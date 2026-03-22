<?php

namespace App\Http\Controllers\Api\Tenant\Projects;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectExpense;
use App\Models\ProjectMilestone;
use App\Models\ProjectNote;
use App\Models\ProjectTask;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ProjectAccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProjectApiController extends Controller
{
    // ══════════════════════════════════════════════════════
    //  PROJECTS — CRUD
    // ══════════════════════════════════════════════════════

    /**
     * List projects with filters, search, and pagination.
     */
    public function index(Request $request, Tenant $tenant)
    {
        try {
            $query = Project::where('tenant_id', $tenant->id)
                ->with(['customer', 'assignedUser']);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('project_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Filter by priority
            if ($request->filled('priority') && $request->priority !== 'all') {
                $query->where('priority', $request->priority);
            }

            // Filter by client
            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            // Filter by assigned user
            if ($request->filled('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            $projects = $query->withCount(['tasks', 'milestones', 'expenses', 'notes', 'attachments'])
                ->latest()
                ->paginate($request->get('per_page', 15));

            // Add progress to each project
            $projects->getCollection()->transform(function ($project) {
                $totalTasks = $project->tasks_count;
                $doneTasks  = $project->tasks()->where('status', 'done')->count();
                $project->progress        = $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0;
                $project->completed_tasks = $doneTasks;
                return $project;
            });

            // Stats
            $stats = [
                'total'     => Project::where('tenant_id', $tenant->id)->count(),
                'active'    => Project::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
                'completed' => Project::where('tenant_id', $tenant->id)->where('status', 'completed')->count(),
                'overdue'   => Project::where('tenant_id', $tenant->id)->overdue()->count(),
            ];

            return response()->json([
                'success'  => true,
                'data'     => $projects->items(),
                'stats'    => $stats,
                'meta'     => [
                    'current_page' => $projects->currentPage(),
                    'last_page'    => $projects->lastPage(),
                    'per_page'     => $projects->perPage(),
                    'total'        => $projects->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Project index error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to load projects.'], 500);
        }
    }

    /**
     * Get data needed for the create/edit forms (customers, team members).
     */
    public function formData(Request $request, Tenant $tenant)
    {
        try {
            $customers   = Customer::where('tenant_id', $tenant->id)->active()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'company_name']);
            $teamMembers = User::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);

            return response()->json([
                'success' => true,
                'data'    => [
                    'customers'    => $customers,
                    'team_members' => $teamMembers,
                    'statuses'     => ['draft', 'active', 'on_hold', 'completed', 'archived'],
                    'priorities'   => ['low', 'medium', 'high', 'urgent'],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Project formData error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load form data.'], 500);
        }
    }

    /**
     * Create a new project.
     */
    public function store(Request $request, Tenant $tenant)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string|max:65000',
                'customer_id' => 'nullable|exists:customers,id',
                'status'      => 'required|in:draft,active,on_hold,completed,archived',
                'priority'    => 'required|in:low,medium,high,urgent',
                'start_date'  => 'nullable|date',
                'end_date'    => 'nullable|date|after_or_equal:start_date',
                'budget'      => 'nullable|numeric|min:0',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            // Tenant-scope customer
            if (!empty($validated['customer_id'])) {
                $customer = Customer::where('id', $validated['customer_id'])->where('tenant_id', $tenant->id)->first();
                if (!$customer) {
                    throw ValidationException::withMessages(['customer_id' => ['Customer not found in this tenant.']]);
                }
            }

            // Tenant-scope assigned user
            if (!empty($validated['assigned_to'])) {
                $user = User::where('id', $validated['assigned_to'])->where('tenant_id', $tenant->id)->first();
                if (!$user) {
                    throw ValidationException::withMessages(['assigned_to' => ['Team member not found in this tenant.']]);
                }
            }

            $validated['tenant_id']  = $tenant->id;
            $validated['slug']       = Str::slug($validated['name']) . '-' . Str::random(5);
            $validated['created_by'] = auth()->id();

            $project = Project::create($validated);
            $project->load(['customer', 'assignedUser', 'creator']);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully.',
                'data'    => $project,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Project store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to create project.'], 500);
        }
    }

    /**
     * Show project detail with all relations.
     */
    public function show(Request $request, Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);

            $project->load([
                'customer',
                'assignedUser',
                'creator',
                'tasks'       => fn($q) => $q->with('assignedUser')->orderBy('sort_order'),
                'milestones'  => fn($q) => $q->with('invoice')->orderBy('sort_order'),
                'notes'       => fn($q) => $q->with('user')->latest(),
                'attachments' => fn($q) => $q->with('user')->latest(),
                'expenses'    => fn($q) => $q->with(['creator', 'voucher'])->latest(),
            ]);

            // Task stats
            $taskStats = [
                'total'       => $project->tasks->count(),
                'todo'        => $project->tasks->where('status', 'todo')->count(),
                'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
                'review'      => $project->tasks->where('status', 'review')->count(),
                'done'        => $project->tasks->where('status', 'done')->count(),
                'overdue'     => $project->tasks->filter(fn($t) => $t->is_overdue)->count(),
            ];

            // Milestone stats
            $milestoneStats = [
                'total'          => $project->milestones->count(),
                'completed'      => $project->milestones->whereNotNull('completed_at')->count(),
                'billable_total' => (float) $project->milestones->where('is_billable', true)->sum('amount'),
                'billed_total'   => (float) $project->milestones->whereNotNull('invoice_id')->sum('amount'),
                'unbilled_total' => (float) $project->milestones->where('is_billable', true)->whereNull('invoice_id')
                    ->whereNotNull('completed_at')->sum('amount'),
            ];

            // Progress
            $totalTasks = $taskStats['total'];
            $doneTasks  = $taskStats['done'];
            $progress   = $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0;

            return response()->json([
                'success' => true,
                'data'    => [
                    'project'         => $project,
                    'task_stats'      => $taskStats,
                    'milestone_stats' => $milestoneStats,
                    'progress'        => $progress,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Project show error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load project.'], 500);
        }
    }

    /**
     * Update a project.
     */
    public function update(Request $request, Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);

            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string|max:65000',
                'customer_id' => 'nullable|exists:customers,id',
                'status'      => 'required|in:draft,active,on_hold,completed,archived',
                'priority'    => 'required|in:low,medium,high,urgent',
                'start_date'  => 'nullable|date',
                'end_date'    => 'nullable|date|after_or_equal:start_date',
                'budget'      => 'nullable|numeric|min:0',
                'assigned_to' => 'nullable|exists:users,id',
            ]);

            // Tenant-scope customer
            if (!empty($validated['customer_id'])) {
                $customer = Customer::where('id', $validated['customer_id'])->where('tenant_id', $tenant->id)->first();
                if (!$customer) {
                    throw ValidationException::withMessages(['customer_id' => ['Customer not found in this tenant.']]);
                }
            }

            // Tenant-scope assigned user
            if (!empty($validated['assigned_to'])) {
                $user = User::where('id', $validated['assigned_to'])->where('tenant_id', $tenant->id)->first();
                if (!$user) {
                    throw ValidationException::withMessages(['assigned_to' => ['Team member not found in this tenant.']]);
                }
            }

            $validated['updated_by'] = auth()->id();
            $project->update($validated);
            $project->load(['customer', 'assignedUser']);

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully.',
                'data'    => $project,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Project update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update project.'], 500);
        }
    }

    /**
     * Update project status.
     */
    public function updateStatus(Request $request, Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);

            $validated = $request->validate([
                'status' => 'required|in:draft,active,on_hold,completed,archived',
            ]);

            // Prevent marking as completed if tasks remain
            if ($validated['status'] === 'completed') {
                $total = $project->tasks()->count();
                $done  = $project->tasks()->where('status', 'done')->count();
                if ($total > 0 && $done < $total) {
                    throw ValidationException::withMessages([
                        'status' => ["Cannot mark project as completed — {$done} of {$total} tasks are done. All tasks must be completed first."],
                    ]);
                }
            }

            $project->update(['status' => $validated['status'], 'updated_by' => auth()->id()]);

            return response()->json([
                'success' => true,
                'message' => 'Project status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.',
                'data'    => ['status' => $project->status],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Project updateStatus error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update project status.'], 500);
        }
    }

    /**
     * Delete a project (soft delete).
     */
    public function destroy(Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);
            $project->update(['deleted_by' => auth()->id()]);
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Project destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete project.'], 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  TASKS
    // ══════════════════════════════════════════════════════

    /**
     * Add a task to a project.
     */
    public function storeTask(Request $request, Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);

            $validated = $request->validate([
                'title'           => 'required|string|max:255',
                'description'     => 'nullable|string|max:2000',
                'priority'        => 'required|in:low,medium,high,urgent',
                'assigned_to'     => 'nullable|exists:users,id',
                'due_date'        => 'nullable|date',
                'estimated_hours' => 'nullable|numeric|min:0',
            ]);

            $validated['project_id'] = $project->id;
            $validated['tenant_id']  = $tenant->id;
            $validated['sort_order'] = $project->tasks()->count();

            $task = ProjectTask::create($validated);
            $task->load('assignedUser');

            return response()->json([
                'success' => true,
                'message' => 'Task added successfully.',
                'data'    => $task,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Task store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add task.'], 500);
        }
    }

    /**
     * Update a task.
     */
    public function updateTask(Request $request, Tenant $tenant, Project $project, ProjectTask $task)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($task->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Task does not belong to this project.'], 403);
            }

            $validated = $request->validate([
                'title'           => 'sometimes|required|string|max:255',
                'description'     => 'nullable|string|max:2000',
                'status'          => 'sometimes|in:todo,in_progress,review,done',
                'priority'        => 'sometimes|in:low,medium,high,urgent',
                'assigned_to'     => 'nullable|exists:users,id',
                'due_date'        => 'nullable|date',
                'estimated_hours' => 'nullable|numeric|min:0',
                'actual_hours'    => 'nullable|numeric|min:0',
                'sort_order'      => 'nullable|integer',
            ]);

            $task->update($validated);
            $task->load('assignedUser');

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully.',
                'data'    => $task,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Task update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update task.'], 500);
        }
    }

    /**
     * Delete a task.
     */
    public function destroyTask(Tenant $tenant, Project $project, ProjectTask $task)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($task->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Task does not belong to this project.'], 403);
            }

            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Task destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete task.'], 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  MILESTONES
    // ══════════════════════════════════════════════════════

    /**
     * Add a milestone to a project.
     */
    public function storeMilestone(Request $request, Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);

            $validated = $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string|max:2000',
                'due_date'    => 'nullable|date',
                'amount'      => 'nullable|numeric|min:0',
                'is_billable' => 'boolean',
            ]);

            $validated['project_id']  = $project->id;
            $validated['tenant_id']   = $tenant->id;
            $validated['sort_order']  = $project->milestones()->count();
            $validated['is_billable'] = $validated['is_billable'] ?? true;

            $milestone = ProjectMilestone::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Milestone added successfully.',
                'data'    => $milestone,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Milestone store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add milestone.'], 500);
        }
    }

    /**
     * Update a milestone.
     */
    public function updateMilestone(Request $request, Tenant $tenant, Project $project, ProjectMilestone $milestone)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($milestone->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Milestone does not belong to this project.'], 403);
            }

            $validated = $request->validate([
                'title'        => 'sometimes|required|string|max:255',
                'description'  => 'nullable|string|max:2000',
                'due_date'     => 'nullable|date',
                'amount'       => 'nullable|numeric|min:0',
                'is_billable'  => 'boolean',
                'completed_at' => 'nullable|date',
            ]);

            // Handle mark complete / incomplete
            if ($request->has('mark_complete') && !$milestone->completed_at) {
                $validated['completed_at'] = now();
            }
            if ($request->has('mark_incomplete')) {
                $validated['completed_at'] = null;
            }

            $milestone->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Milestone updated successfully.',
                'data'    => $milestone->fresh(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Milestone update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update milestone.'], 500);
        }
    }

    /**
     * Delete a milestone.
     */
    public function destroyMilestone(Tenant $tenant, Project $project, ProjectMilestone $milestone)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($milestone->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Milestone does not belong to this project.'], 403);
            }

            $milestone->delete();

            return response()->json([
                'success' => true,
                'message' => 'Milestone deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Milestone destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete milestone.'], 500);
        }
    }

    /**
     * Invoice a completed billable milestone.
     */
    public function invoiceMilestone(Request $request, Tenant $tenant, Project $project, ProjectMilestone $milestone)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($milestone->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Milestone does not belong to this project.'], 403);
            }

            $service = new ProjectAccountingService($tenant->id);
            $voucher = $service->invoiceMilestone($milestone);

            return response()->json([
                'success'        => true,
                'message'        => 'Milestone invoiced successfully.',
                'voucher_number' => $voucher->voucher_number,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Milestone invoice error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to invoice milestone.'], 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  EXPENSES
    // ══════════════════════════════════════════════════════

    /**
     * Record a project expense (with accounting).
     */
    public function storeExpense(Request $request, Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);

            $validated = $request->validate([
                'title'        => 'required|string|max:255',
                'description'  => 'nullable|string|max:2000',
                'amount'       => 'required|numeric|min:0.01',
                'expense_date' => 'required|date',
                'category'     => 'nullable|string|max:50',
            ]);

            $service = new ProjectAccountingService($tenant->id);
            $expense = $service->recordExpense($project, $validated);
            $expense->load(['creator', 'voucher']);
            $project->refresh();

            return response()->json([
                'success'              => true,
                'message'              => 'Expense recorded and posted to accounting.',
                'data'                 => $expense,
                'project_actual_cost'  => (float) $project->actual_cost,
                'budget_used_percent'  => $project->budget_used_percent,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Expense store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to record expense.'], 500);
        }
    }

    /**
     * Delete a project expense (reverses accounting).
     */
    public function destroyExpense(Tenant $tenant, Project $project, ProjectExpense $expense)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($expense->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Expense does not belong to this project.'], 403);
            }

            DB::transaction(function () use ($project, $expense) {
                $project->decrement('actual_cost', $expense->amount);

                if ($expense->voucher_id && $expense->voucher) {
                    $expense->voucher->entries()->delete();
                    $expense->voucher->delete();
                }

                $expense->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Expense deleted and reversed from accounting.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Expense destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete expense.'], 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  NOTES
    // ══════════════════════════════════════════════════════

    /**
     * Add a note to a project.
     */
    public function storeNote(Request $request, Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);

            $validated = $request->validate([
                'content'     => 'required|string|max:5000',
                'is_internal' => 'boolean',
            ]);

            $validated['project_id']  = $project->id;
            $validated['user_id']     = auth()->id();
            $validated['is_internal'] = $validated['is_internal'] ?? true;

            $note = ProjectNote::create($validated);
            $note->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully.',
                'data'    => $note,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Note store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add note.'], 500);
        }
    }

    /**
     * Delete a note.
     */
    public function destroyNote(Tenant $tenant, Project $project, ProjectNote $note)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($note->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Note does not belong to this project.'], 403);
            }

            $note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Note destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete note.'], 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  ATTACHMENTS
    // ══════════════════════════════════════════════════════

    /**
     * Upload a file attachment to a project.
     */
    public function storeAttachment(Request $request, Tenant $tenant, Project $project)
    {
        try {
            $this->authorizeProject($project, $tenant);

            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
            ]);

            $file = $request->file('file');
            $path = $file->store("tenants/{$tenant->id}/projects/{$project->id}", 'public');

            $attachment = ProjectAttachment::create([
                'project_id' => $project->id,
                'user_id'    => auth()->id(),
                'file_name'  => $file->getClientOriginalName(),
                'file_path'  => $path,
                'file_size'  => $file->getSize(),
                'mime_type'  => $file->getClientMimeType(),
            ]);

            $attachment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'data'    => $attachment,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Attachment store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to upload file.'], 500);
        }
    }

    /**
     * Delete a file attachment.
     */
    public function destroyAttachment(Tenant $tenant, Project $project, ProjectAttachment $attachment)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($attachment->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Attachment does not belong to this project.'], 403);
            }

            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Attachment destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete attachment.'], 500);
        }
    }

    /**
     * Download a file attachment.
     */
    public function downloadAttachment(Tenant $tenant, Project $project, ProjectAttachment $attachment)
    {
        try {
            $this->authorizeProject($project, $tenant);

            if ($attachment->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Attachment does not belong to this project.'], 403);
            }

            return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
        } catch (\Throwable $e) {
            Log::error('Attachment download error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to download attachment.'], 500);
        }
    }

    // ══════════════════════════════════════════════════════
    //  REPORTS
    // ══════════════════════════════════════════════════════

    /**
     * Get project reports data.
     */
    public function reports(Request $request, Tenant $tenant)
    {
        try {
            $projects = Project::where('tenant_id', $tenant->id)
                ->with(['customer', 'assignedUser'])
                ->withCount(['tasks', 'milestones', 'expenses'])
                ->get();

            // Summary stats
            $summary = [
                'total'      => $projects->count(),
                'active'     => $projects->where('status', 'active')->count(),
                'completed'  => $projects->where('status', 'completed')->count(),
                'on_hold'    => $projects->where('status', 'on_hold')->count(),
                'total_budget' => (float) $projects->sum('budget'),
                'total_cost'   => (float) $projects->sum('actual_cost'),
                'overdue'      => $projects->filter(fn($p) => $p->is_overdue)->count(),
            ];

            // Status distribution
            $statusDistribution = [
                'draft'     => $projects->where('status', 'draft')->count(),
                'active'    => $projects->where('status', 'active')->count(),
                'on_hold'   => $projects->where('status', 'on_hold')->count(),
                'completed' => $projects->where('status', 'completed')->count(),
                'archived'  => $projects->where('status', 'archived')->count(),
            ];

            // Add progress to each project
            $projectList = $projects->map(function ($project) {
                $totalTasks = $project->tasks_count;
                $doneTasks  = $project->tasks()->where('status', 'done')->count();
                return [
                    'id'              => $project->id,
                    'name'            => $project->name,
                    'project_number'  => $project->project_number,
                    'status'          => $project->status,
                    'priority'        => $project->priority,
                    'customer'        => $project->customer ? $project->customer->first_name . ' ' . $project->customer->last_name : null,
                    'assigned_to'     => $project->assignedUser ? $project->assignedUser->name : null,
                    'budget'          => (float) $project->budget,
                    'actual_cost'     => (float) $project->actual_cost,
                    'tasks_count'     => $project->tasks_count,
                    'milestones_count' => $project->milestones_count,
                    'progress'        => $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0,
                    'start_date'      => $project->start_date?->toDateString(),
                    'end_date'        => $project->end_date?->toDateString(),
                    'is_overdue'      => $project->is_overdue,
                    'days_remaining'  => $project->days_remaining,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => [
                    'summary'             => $summary,
                    'status_distribution' => $statusDistribution,
                    'projects'            => $projectList,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Project reports error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to load reports.'], 500);
        }
    }

    // ─── Authorization Helper ─────────────────────────────

    private function authorizeProject(Project $project, Tenant $tenant): void
    {
        if ($project->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access to this project.');
        }
    }
}
