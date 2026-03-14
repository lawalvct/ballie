<?php

namespace App\Http\Controllers\Tenant\Projects;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectMilestone;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectReportController extends Controller
{
    // ─── Project Profitability Report ─────────────────────

    public function profitability(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', null);
        $toDate = $request->get('to_date', null);
        $status = $request->get('status', 'all');

        $query = Project::where('tenant_id', $tenant->id)
            ->with(['customer', 'expenses', 'milestones']);

        if ($fromDate) {
            $query->where('start_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where(function ($q) use ($toDate) {
                $q->where('end_date', '<=', $toDate)
                  ->orWhereNull('end_date');
            });
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $projects = $query->get();

        // Calculate profitability for each project
        $projectData = $projects->map(function ($project) {
            $revenue = $project->milestones
                ->where('is_billable', true)
                ->whereNotNull('invoice_id')
                ->sum('amount');

            $unbilledRevenue = $project->milestones
                ->where('is_billable', true)
                ->whereNull('invoice_id')
                ->whereNotNull('completed_at')
                ->sum('amount');

            $totalExpenses = $project->expenses->sum('amount');
            $profit = $revenue - $totalExpenses;
            $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

            return (object) [
                'id' => $project->id,
                'name' => $project->name,
                'project_number' => $project->project_number,
                'status' => $project->status,
                'customer' => $project->customer,
                'budget' => (float) $project->budget,
                'actual_cost' => (float) $project->actual_cost,
                'revenue' => (float) $revenue,
                'unbilled_revenue' => (float) $unbilledRevenue,
                'total_expenses' => (float) $totalExpenses,
                'profit' => (float) $profit,
                'margin' => $margin,
                'budget_used_percent' => $project->budget > 0 ? round(($project->actual_cost / $project->budget) * 100, 1) : 0,
            ];
        })->sortByDesc('profit')->values();

        // Summary
        $summary = [
            'total_revenue' => $projectData->sum('revenue'),
            'total_expenses' => $projectData->sum('total_expenses'),
            'total_profit' => $projectData->sum('profit'),
            'total_budget' => $projectData->sum('budget'),
            'total_unbilled' => $projectData->sum('unbilled_revenue'),
            'avg_margin' => $projectData->count() > 0
                ? round($projectData->avg('margin'), 1) : 0,
            'profitable_count' => $projectData->where('profit', '>', 0)->count(),
            'loss_count' => $projectData->where('profit', '<', 0)->count(),
        ];

        return view('tenant.projects.reports.profitability', compact(
            'tenant', 'projectData', 'summary', 'fromDate', 'toDate', 'status'
        ));
    }

    // ─── Revenue by Client ───────────────────────────────

    public function revenueByClient(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', null);
        $toDate = $request->get('to_date', null);

        $query = Project::where('tenant_id', $tenant->id)
            ->whereNotNull('customer_id')
            ->with(['customer', 'milestones', 'expenses']);

        if ($fromDate) {
            $query->where('start_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where(function ($q) use ($toDate) {
                $q->where('end_date', '<=', $toDate)
                  ->orWhereNull('end_date');
            });
        }

        $projects = $query->get();

        // Group by customer
        $clientData = $projects->groupBy('customer_id')->map(function ($customerProjects, $customerId) {
            $customer = $customerProjects->first()->customer;
            $totalRevenue = 0;
            $totalExpenses = 0;
            $totalBudget = 0;

            foreach ($customerProjects as $project) {
                $totalRevenue += $project->milestones
                    ->where('is_billable', true)
                    ->whereNotNull('invoice_id')
                    ->sum('amount');
                $totalExpenses += $project->expenses->sum('amount');
                $totalBudget += (float) $project->budget;
            }

            return (object) [
                'customer_id' => $customerId,
                'customer_name' => $customer
                    ? ($customer->company_name ?: $customer->first_name . ' ' . $customer->last_name)
                    : 'Unknown',
                'project_count' => $customerProjects->count(),
                'active_count' => $customerProjects->where('status', 'active')->count(),
                'completed_count' => $customerProjects->where('status', 'completed')->count(),
                'total_revenue' => (float) $totalRevenue,
                'total_expenses' => (float) $totalExpenses,
                'total_budget' => (float) $totalBudget,
                'profit' => (float) ($totalRevenue - $totalExpenses),
            ];
        })->sortByDesc('total_revenue')->values();

        $summary = [
            'total_clients' => $clientData->count(),
            'total_revenue' => $clientData->sum('total_revenue'),
            'total_profit' => $clientData->sum('profit'),
            'total_projects' => $clientData->sum('project_count'),
            'avg_revenue_per_client' => $clientData->count() > 0
                ? round($clientData->sum('total_revenue') / $clientData->count(), 2) : 0,
        ];

        return view('tenant.projects.reports.revenue-by-client', compact(
            'tenant', 'clientData', 'summary', 'fromDate', 'toDate'
        ));
    }

    // ─── Active Projects Report ──────────────────────────

    public function activeProjects(Request $request, Tenant $tenant)
    {
        $sortBy = $request->get('sort', 'end_date');

        $projects = Project::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with(['customer', 'assignedUser', 'tasks', 'milestones', 'expenses'])
            ->get();

        $projectData = $projects->map(function ($project) {
            $totalTasks = $project->tasks->count();
            $doneTasks = $project->tasks->where('status', 'done')->count();
            $progress = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

            $billedRevenue = $project->milestones
                ->where('is_billable', true)
                ->whereNotNull('invoice_id')
                ->sum('amount');

            return (object) [
                'id' => $project->id,
                'name' => $project->name,
                'project_number' => $project->project_number,
                'customer' => $project->customer,
                'assigned_user' => $project->assignedUser,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'budget' => (float) $project->budget,
                'actual_cost' => (float) $project->actual_cost,
                'revenue' => (float) $billedRevenue,
                'progress' => $progress,
                'total_tasks' => $totalTasks,
                'done_tasks' => $doneTasks,
                'is_overdue' => $project->is_overdue,
                'days_remaining' => $project->days_remaining,
                'priority' => $project->priority,
            ];
        });

        // Sort
        $projectData = match ($sortBy) {
            'progress' => $projectData->sortByDesc('progress'),
            'budget' => $projectData->sortByDesc('budget'),
            'priority' => $projectData->sortBy(function ($p) {
                return ['urgent' => 0, 'high' => 1, 'medium' => 2, 'low' => 3][$p->priority] ?? 4;
            }),
            default => $projectData->sortBy('end_date'),
        };

        $projectData = $projectData->values();

        $summary = [
            'total_active' => $projectData->count(),
            'overdue' => $projectData->where('is_overdue', true)->count(),
            'total_budget' => $projectData->sum('budget'),
            'total_spent' => $projectData->sum('actual_cost'),
            'avg_progress' => $projectData->count() > 0 ? round($projectData->avg('progress')) : 0,
        ];

        return view('tenant.projects.reports.active-projects', compact(
            'tenant', 'projectData', 'summary', 'sortBy'
        ));
    }

    // ─── Completed Projects Report ───────────────────────

    public function completedProjects(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());

        $projects = Project::where('tenant_id', $tenant->id)
            ->where('status', 'completed')
            ->with(['customer', 'tasks', 'milestones', 'expenses'])
            ->when($fromDate, fn($q) => $q->where('completed_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->where('completed_at', '<=', Carbon::parse($toDate)->endOfDay()))
            ->orderByDesc('completed_at')
            ->get();

        $projectData = $projects->map(function ($project) {
            $revenue = $project->milestones
                ->where('is_billable', true)
                ->whereNotNull('invoice_id')
                ->sum('amount');

            $totalExpenses = $project->expenses->sum('amount');
            $durationDays = $project->start_date && $project->completed_at
                ? $project->start_date->diffInDays($project->completed_at) : null;

            $wasOverBudget = $project->budget > 0 && $project->actual_cost > $project->budget;
            $wasLate = $project->end_date && $project->completed_at && $project->completed_at->gt($project->end_date);

            return (object) [
                'id' => $project->id,
                'name' => $project->name,
                'project_number' => $project->project_number,
                'customer' => $project->customer,
                'budget' => (float) $project->budget,
                'actual_cost' => (float) $project->actual_cost,
                'revenue' => (float) $revenue,
                'profit' => (float) ($revenue - $totalExpenses),
                'completed_at' => $project->completed_at,
                'duration_days' => $durationDays,
                'was_over_budget' => $wasOverBudget,
                'was_late' => $wasLate,
                'total_tasks' => $project->tasks->count(),
            ];
        });

        $summary = [
            'total_completed' => $projectData->count(),
            'total_revenue' => $projectData->sum('revenue'),
            'total_profit' => $projectData->sum('profit'),
            'on_budget' => $projectData->where('was_over_budget', false)->count(),
            'over_budget' => $projectData->where('was_over_budget', true)->count(),
            'on_time' => $projectData->where('was_late', false)->count(),
            'late' => $projectData->where('was_late', true)->count(),
            'avg_duration' => $projectData->count() > 0
                ? round($projectData->whereNotNull('duration_days')->avg('duration_days')) : 0,
        ];

        return view('tenant.projects.reports.completed-projects', compact(
            'tenant', 'projectData', 'summary', 'fromDate', 'toDate'
        ));
    }

    // ─── Project Cashflow Report ─────────────────────────

    public function cashflow(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $projectId = $request->get('project_id', null);

        $query = Project::where('tenant_id', $tenant->id)
            ->with(['milestones.invoice', 'expenses']);

        if ($projectId) {
            $query->where('id', $projectId);
        }

        $projects = $query->get();

        // Collect all cash inflows (billed milestones) and outflows (expenses)
        $inflows = collect();
        $outflows = collect();

        foreach ($projects as $project) {
            // Inflows from billed milestones
            foreach ($project->milestones->where('is_billable', true)->whereNotNull('invoice_id') as $milestone) {
                $date = $milestone->invoice ? $milestone->invoice->voucher_date : $milestone->completed_at;
                $inflows->push((object) [
                    'date' => $date ? Carbon::parse($date) : now(),
                    'amount' => (float) $milestone->amount,
                    'description' => $milestone->title,
                    'project_name' => $project->name,
                    'project_id' => $project->id,
                    'type' => 'milestone',
                ]);
            }

            // Outflows from expenses
            foreach ($project->expenses as $expense) {
                $outflows->push((object) [
                    'date' => $expense->expense_date ?? $expense->created_at,
                    'amount' => (float) $expense->amount,
                    'description' => $expense->title,
                    'project_name' => $project->name,
                    'project_id' => $project->id,
                    'category' => $expense->category,
                    'type' => 'expense',
                ]);
            }
        }

        // Filter by date range
        $inflows = $inflows->filter(function ($item) use ($fromDate, $toDate) {
            return $item->date->between($fromDate, Carbon::parse($toDate)->endOfDay());
        })->sortBy('date')->values();

        $outflows = $outflows->filter(function ($item) use ($fromDate, $toDate) {
            return $item->date->between($fromDate, Carbon::parse($toDate)->endOfDay());
        })->sortBy('date')->values();

        // Monthly breakdown
        $allDates = $inflows->pluck('date')->merge($outflows->pluck('date'));
        $months = collect();

        if ($allDates->isNotEmpty()) {
            $startMonth = Carbon::parse($fromDate)->startOfMonth();
            $endMonth = Carbon::parse($toDate)->endOfMonth();

            while ($startMonth->lte($endMonth)) {
                $monthKey = $startMonth->format('Y-m');
                $monthInflows = $inflows->filter(fn($i) => $i->date->format('Y-m') === $monthKey)->sum('amount');
                $monthOutflows = $outflows->filter(fn($o) => $o->date->format('Y-m') === $monthKey)->sum('amount');

                $months->push((object) [
                    'month' => $startMonth->format('M Y'),
                    'month_key' => $monthKey,
                    'inflows' => (float) $monthInflows,
                    'outflows' => (float) $monthOutflows,
                    'net' => (float) ($monthInflows - $monthOutflows),
                ]);

                $startMonth->addMonth();
            }
        }

        // Expense breakdown by category
        $expenseByCategory = $outflows->groupBy('category')->map(function ($items, $category) {
            return (object) [
                'category' => ucfirst($category ?: 'General'),
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->sortByDesc('total')->values();

        $summary = [
            'total_inflows' => $inflows->sum('amount'),
            'total_outflows' => $outflows->sum('amount'),
            'net_cashflow' => $inflows->sum('amount') - $outflows->sum('amount'),
            'inflow_count' => $inflows->count(),
            'outflow_count' => $outflows->count(),
        ];

        // Project list for filter dropdown
        $projectList = Project::where('tenant_id', $tenant->id)
            ->select('id', 'name', 'project_number')
            ->orderBy('name')
            ->get();

        return view('tenant.projects.reports.cashflow', compact(
            'tenant', 'summary', 'inflows', 'outflows', 'months',
            'expenseByCategory', 'fromDate', 'toDate', 'projectId', 'projectList'
        ));
    }
}
