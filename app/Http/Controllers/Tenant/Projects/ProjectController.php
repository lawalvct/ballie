<?php

namespace App\Http\Controllers\Tenant\Projects;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectMilestone;
use App\Models\ProjectNote;
use App\Models\ProjectTask;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    // ─── Index — Project List ─────────────────────────────

    public function index(Request $request, Tenant $tenant)
    {
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

        $projects = $query->withCount(['tasks', 'milestones'])
            ->latest()
            ->paginate(15);

        // Add progress to each project
        $projects->getCollection()->transform(function ($project) {
            $totalTasks = $project->tasks_count;
            $doneTasks = $project->tasks()->where('status', 'done')->count();
            $project->progress = $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0;
            $project->completed_tasks = $doneTasks;
            return $project;
        });

        // Stats
        $stats = [
            'total' => Project::where('tenant_id', $tenant->id)->count(),
            'active' => Project::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
            'completed' => Project::where('tenant_id', $tenant->id)->where('status', 'completed')->count(),
            'overdue' => Project::where('tenant_id', $tenant->id)->overdue()->count(),
        ];

        $customers = Customer::where('tenant_id', $tenant->id)->active()->orderBy('first_name')->get();
        $teamMembers = User::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();

        return view('tenant.projects.index', compact(
            'projects', 'stats', 'customers', 'teamMembers', 'tenant'
        ));
    }

    // ─── Create Form ──────────────────────────────────────

    public function create(Tenant $tenant)
    {
        $customers = Customer::where('tenant_id', $tenant->id)->active()->orderBy('first_name')->get();
        $teamMembers = User::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();
        $roles = \App\Models\Tenant\Role::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('priority')->orderBy('name')->get();

        return view('tenant.projects.create', compact('customers', 'teamMembers', 'tenant', 'roles'));
    }

    // ─── Store ────────────────────────────────────────────

    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:65000',
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:draft,active,on_hold,completed,archived',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['created_by'] = auth()->id();

        $project = Project::create($validated);

        return redirect()
            ->route('tenant.projects.show', [$tenant->slug, $project->id])
            ->with('success', 'Project created successfully.');
    }

    // ─── Show — Project Detail (Tabbed) ───────────────────

    public function show(Request $request, Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $project->load([
            'customer',
            'assignedUser',
            'creator',
            'tasks' => fn($q) => $q->with('assignedUser')->orderBy('sort_order'),
            'milestones' => fn($q) => $q->with('invoice')->orderBy('sort_order'),
            'notes' => fn($q) => $q->with('user')->latest(),
            'attachments' => fn($q) => $q->with('user')->latest(),
        ]);

        $teamMembers = User::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();

        // Task stats
        $taskStats = [
            'total' => $project->tasks->count(),
            'todo' => $project->tasks->where('status', 'todo')->count(),
            'in_progress' => $project->tasks->where('status', 'in_progress')->count(),
            'review' => $project->tasks->where('status', 'review')->count(),
            'done' => $project->tasks->where('status', 'done')->count(),
            'overdue' => $project->tasks->filter(fn($t) => $t->is_overdue)->count(),
        ];

        // Milestone stats
        $milestoneStats = [
            'total' => $project->milestones->count(),
            'completed' => $project->milestones->whereNotNull('completed_at')->count(),
            'billable_total' => $project->milestones->where('is_billable', true)->sum('amount'),
            'billed_total' => $project->milestones->whereNotNull('invoice_id')->sum('amount'),
            'unbilled_total' => $project->milestones->where('is_billable', true)->whereNull('invoice_id')
                ->whereNotNull('completed_at')->sum('amount'),
        ];

        $tab = $request->get('tab', 'overview');

        return view('tenant.projects.show', compact(
            'project', 'tenant', 'teamMembers', 'taskStats', 'milestoneStats', 'tab'
        ));
    }

    // ─── Edit Form ────────────────────────────────────────

    public function edit(Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $customers = Customer::where('tenant_id', $tenant->id)->active()->orderBy('first_name')->get();
        $teamMembers = User::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get();
        $roles = \App\Models\Tenant\Role::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('priority')->orderBy('name')->get();

        return view('tenant.projects.edit', compact('project', 'customers', 'teamMembers', 'tenant', 'roles'));
    }

    // ─── Update ───────────────────────────────────────────

    public function update(Request $request, Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:65000',
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:draft,active,on_hold,completed,archived',
            'priority' => 'required|in:low,medium,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $validated['updated_by'] = auth()->id();

        $project->update($validated);

        return redirect()
            ->route('tenant.projects.show', [$tenant->slug, $project->id])
            ->with('success', 'Project updated successfully.');
    }

    // ─── Quick Status Update ───────────────────────────────

    public function updateStatus(Request $request, Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $validated = $request->validate([
            'status' => 'required|in:draft,active,on_hold,completed,archived',
        ]);

        if ($validated['status'] === 'completed') {
            $total = $project->tasks()->count();
            $done  = $project->tasks()->where('status', 'done')->count();
            if ($total > 0 && $done < $total) {
                return redirect()
                    ->route('tenant.projects.show', [$tenant->slug, $project->id])
                    ->with('error', "Cannot mark project as completed — {$done} of {$total} tasks are done. All tasks must be completed first.");
            }
        }

        $project->update(['status' => $validated['status'], 'updated_by' => auth()->id()]);

        $label = ucfirst(str_replace('_', ' ', $validated['status']));

        return redirect()
            ->route('tenant.projects.show', [$tenant->slug, $project->id])
            ->with('success', "Project status updated to {$label}.");
    }

    // ─── Delete ───────────────────────────────────────────

    public function destroy(Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $project->update(['deleted_by' => auth()->id()]);
        $project->delete();

        return redirect()
            ->route('tenant.projects.index', $tenant->slug)
            ->with('success', 'Project deleted successfully.');
    }

    // ══════════════════════════════════════════════════════
    //  TASKS — AJAX endpoints
    // ══════════════════════════════════════════════════════

    public function storeTask(Request $request, Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $validated['project_id'] = $project->id;
        $validated['tenant_id'] = $tenant->id;
        $validated['sort_order'] = $project->tasks()->count();

        $task = ProjectTask::create($validated);
        $task->load('assignedUser');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return back()->with('success', 'Task added successfully.');
    }

    public function updateTask(Request $request, Tenant $tenant, Project $project, ProjectTask $task)
    {
        $this->authorizeProject($project, $tenant);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'sometimes|in:todo,in_progress,review,done',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer',
        ]);

        $task->update($validated);
        $task->load('assignedUser');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return back()->with('success', 'Task updated.');
    }

    public function destroyTask(Tenant $tenant, Project $project, ProjectTask $task)
    {
        $this->authorizeProject($project, $tenant);
        $task->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Task deleted.');
    }

    // ══════════════════════════════════════════════════════
    //  MILESTONES — AJAX endpoints
    // ══════════════════════════════════════════════════════

    public function storeMilestone(Request $request, Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'due_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'is_billable' => 'boolean',
        ]);

        $validated['project_id'] = $project->id;
        $validated['tenant_id'] = $tenant->id;
        $validated['sort_order'] = $project->milestones()->count();
        $validated['is_billable'] = $validated['is_billable'] ?? true;

        $milestone = ProjectMilestone::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'milestone' => $milestone]);
        }

        return back()->with('success', 'Milestone added successfully.');
    }

    public function updateMilestone(Request $request, Tenant $tenant, Project $project, ProjectMilestone $milestone)
    {
        $this->authorizeProject($project, $tenant);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'due_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'is_billable' => 'boolean',
            'completed_at' => 'nullable|date',
        ]);

        // Handle completing milestone
        if ($request->has('mark_complete') && !$milestone->completed_at) {
            $validated['completed_at'] = now();
        }
        if ($request->has('mark_incomplete')) {
            $validated['completed_at'] = null;
        }

        $milestone->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'milestone' => $milestone->fresh()]);
        }

        return back()->with('success', 'Milestone updated.');
    }

    public function destroyMilestone(Tenant $tenant, Project $project, ProjectMilestone $milestone)
    {
        $this->authorizeProject($project, $tenant);
        $milestone->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Milestone deleted.');
    }

    // ══════════════════════════════════════════════════════
    //  NOTES — AJAX endpoints
    // ══════════════════════════════════════════════════════

    public function storeNote(Request $request, Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'is_internal' => 'boolean',
        ]);

        $validated['project_id'] = $project->id;
        $validated['user_id'] = auth()->id();
        $validated['is_internal'] = $validated['is_internal'] ?? true;

        $note = ProjectNote::create($validated);
        $note->load('user');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'note' => $note]);
        }

        return back()->with('success', 'Note added.');
    }

    public function destroyNote(Tenant $tenant, Project $project, ProjectNote $note)
    {
        $this->authorizeProject($project, $tenant);
        $note->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Note deleted.');
    }

    // ══════════════════════════════════════════════════════
    //  ATTACHMENTS
    // ══════════════════════════════════════════════════════

    public function storeAttachment(Request $request, Tenant $tenant, Project $project)
    {
        $this->authorizeProject($project, $tenant);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store("tenants/{$tenant->id}/projects/{$project->id}", 'public');

        $attachment = ProjectAttachment::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
        ]);

        $attachment->load('user');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'attachment' => $attachment]);
        }

        return back()->with('success', 'File uploaded successfully.');
    }

    public function destroyAttachment(Tenant $tenant, Project $project, ProjectAttachment $attachment)
    {
        $this->authorizeProject($project, $tenant);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Attachment deleted.');
    }

    public function downloadAttachment(Tenant $tenant, Project $project, ProjectAttachment $attachment)
    {
        $this->authorizeProject($project, $tenant);

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    // ══════════════════════════════════════════════════════
    //  REPORTS
    // ══════════════════════════════════════════════════════

    public function reports(Request $request, Tenant $tenant)
    {
        $projects = Project::where('tenant_id', $tenant->id)
            ->with(['customer', 'assignedUser'])
            ->withCount(['tasks', 'milestones'])
            ->get();

        // Summary stats
        $summary = [
            'total' => $projects->count(),
            'active' => $projects->where('status', 'active')->count(),
            'completed' => $projects->where('status', 'completed')->count(),
            'on_hold' => $projects->where('status', 'on_hold')->count(),
            'total_budget' => $projects->sum('budget'),
            'total_cost' => $projects->sum('actual_cost'),
            'overdue' => $projects->filter(fn($p) => $p->is_overdue)->count(),
        ];

        return view('tenant.projects.reports', compact('projects', 'summary', 'tenant'));
    }

    // ─── Authorization Helper ─────────────────────────────

    private function authorizeProject(Project $project, Tenant $tenant): void
    {
        if ($project->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access to this project.');
        }
    }
}
