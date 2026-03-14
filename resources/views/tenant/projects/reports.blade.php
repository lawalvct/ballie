@extends('layouts.tenant')

@section('title', 'Project Reports')
@section('page-title', 'Project Reports')
@section('page-description')
    <span class="hidden md:inline">
        Overview of all project performance, budgets, and status.
    </span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.projects.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Projects
            </a>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.projects.reports.profitability', $tenant->slug) }}" class="inline-flex items-center px-3 py-2 bg-violet-600 rounded-lg text-xs font-semibold text-white uppercase hover:bg-violet-700 transition">Profitability</a>
            <a href="{{ route('tenant.projects.reports.revenue-by-client', $tenant->slug) }}" class="inline-flex items-center px-3 py-2 bg-blue-600 rounded-lg text-xs font-semibold text-white uppercase hover:bg-blue-700 transition">Revenue by Client</a>
            <a href="{{ route('tenant.projects.reports.active', $tenant->slug) }}" class="inline-flex items-center px-3 py-2 bg-green-600 rounded-lg text-xs font-semibold text-white uppercase hover:bg-green-700 transition">Active Projects</a>
            <a href="{{ route('tenant.projects.reports.completed', $tenant->slug) }}" class="inline-flex items-center px-3 py-2 bg-blue-500 rounded-lg text-xs font-semibold text-white uppercase hover:bg-blue-600 transition">Completed Projects</a>
            <a href="{{ route('tenant.projects.reports.cashflow', $tenant->slug) }}" class="inline-flex items-center px-3 py-2 bg-green-500 rounded-lg text-xs font-semibold text-white uppercase hover:bg-green-600 transition">Cashflow</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-violet-500">
            <p class="text-sm font-medium text-gray-500">Total Projects</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $summary['total'] }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Active</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $summary['active'] }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500">Completed</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $summary['completed'] }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-red-500">
            <p class="text-sm font-medium text-gray-500">Overdue</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $summary['overdue'] }}</p>
        </div>
    </div>

    <!-- Budget Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Budget Overview</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm text-gray-500">Total Budget</p>
                        <p class="text-sm font-semibold text-gray-900">₦{{ number_format($summary['total_budget'], 2) }}</p>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm text-gray-500">Total Actual Cost</p>
                        <p class="text-sm font-semibold text-gray-900">₦{{ number_format($summary['total_cost'], 2) }}</p>
                    </div>
                </div>
                @php
                    $budgetUsed = $summary['total_budget'] > 0 ? round(($summary['total_cost'] / $summary['total_budget']) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm text-gray-500">Budget Utilization</p>
                        <p class="text-sm font-semibold {{ $budgetUsed > 100 ? 'text-red-600' : 'text-gray-900' }}">{{ $budgetUsed }}%</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $budgetUsed > 100 ? 'bg-red-500' : ($budgetUsed > 80 ? 'bg-yellow-500' : 'bg-violet-500') }}"
                             style="width: {{ min($budgetUsed, 100) }}%"></div>
                    </div>
                </div>
                <div class="pt-3 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">Remaining</p>
                        <p class="text-sm font-semibold {{ ($summary['total_budget'] - $summary['total_cost']) < 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₦{{ number_format($summary['total_budget'] - $summary['total_cost'], 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Status Distribution</h3>
            @php
                $statuses = [
                    'active' => ['label' => 'Active', 'color' => 'bg-green-500', 'bg' => 'bg-green-100 text-green-700'],
                    'completed' => ['label' => 'Completed', 'color' => 'bg-blue-500', 'bg' => 'bg-blue-100 text-blue-700'],
                    'on_hold' => ['label' => 'On Hold', 'color' => 'bg-yellow-500', 'bg' => 'bg-yellow-100 text-yellow-700'],
                    'draft' => ['label' => 'Draft', 'color' => 'bg-gray-400', 'bg' => 'bg-gray-100 text-gray-700'],
                    'archived' => ['label' => 'Archived', 'color' => 'bg-gray-300', 'bg' => 'bg-gray-100 text-gray-500'],
                ];
            @endphp
            <div class="space-y-3">
                @foreach($statuses as $statusKey => $statusInfo)
                    @php
                        $count = $projects->where('status', $statusKey)->count();
                        $pct = $summary['total'] > 0 ? round(($count / $summary['total']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusInfo['bg'] }}">
                                    {{ $statusInfo['label'] }}
                                </span>
                            </div>
                            <span class="text-sm text-gray-600">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="{{ $statusInfo['color'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Project Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Projects</h3>
        </div>

        @if($projects->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actual Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $project)
                            @php
                                $projectStatusColors = [
                                    'draft' => 'bg-gray-100 text-gray-700',
                                    'active' => 'bg-green-100 text-green-700',
                                    'on_hold' => 'bg-yellow-100 text-yellow-700',
                                    'completed' => 'bg-blue-100 text-blue-700',
                                    'archived' => 'bg-gray-100 text-gray-500',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('tenant.projects.show', [$tenant->slug, $project->id]) }}" class="text-sm font-medium text-violet-600 hover:text-violet-800">
                                        {{ $project->name }}
                                    </a>
                                    <p class="text-xs text-gray-400 font-mono">{{ $project->project_number }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $projectStatusColors[$project->status] ?? '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $project->customer ? $project->customer->first_name . ' ' . $project->customer->last_name : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $project->tasks_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $project->budget ? '₦' . number_format($project->budget, 2) : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $project->actual_cost ? '₦' . number_format($project->actual_cost, 2) : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm {{ $project->is_overdue ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                    {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : '—' }}
                                    @if($project->is_overdue)
                                        <span class="text-xs">(Overdue)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center text-gray-400">
                <p>No projects to report on.</p>
            </div>
        @endif
    </div>
</div>
@endsection
