@extends('layouts.tenant')

@section('title', 'Completed Projects Report')
@section('page-title', 'Completed Projects Report')
@section('page-description')
    <span class="hidden md:inline">Review completed projects, delivery performance, and profitability.</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.projects.reports.active', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Active Projects
            </a>
            <a href="{{ route('tenant.projects.reports.profitability', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Profitability
            </a>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tenant.projects.reports', $tenant->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <form method="GET" action="{{ route('tenant.projects.reports.completed', $tenant->slug) }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Completed From</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Completed To</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500">Completed</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $summary['total_completed'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-green-600 mt-1">₦{{ number_format($summary['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-violet-500">
            <p class="text-sm font-medium text-gray-500">Total Profit</p>
            <p class="text-2xl font-bold {{ $summary['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                ₦{{ number_format($summary['total_profit'], 2) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">Avg Duration</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['avg_duration'] }} days</p>
        </div>
    </div>

    <!-- Delivery Performance -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Performance</h3>
            <div class="space-y-4">
                @php
                    $onTimePct = $summary['total_completed'] > 0 ? round(($summary['on_time'] / $summary['total_completed']) * 100) : 0;
                    $latePct = 100 - $onTimePct;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600">On Time</span>
                        <span class="text-sm font-semibold text-green-600">{{ $summary['on_time'] }} ({{ $onTimePct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $onTimePct }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600">Late</span>
                        <span class="text-sm font-semibold text-red-600">{{ $summary['late'] }} ({{ $latePct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $latePct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Budget Performance</h3>
            <div class="space-y-4">
                @php
                    $onBudgetPct = $summary['total_completed'] > 0 ? round(($summary['on_budget'] / $summary['total_completed']) * 100) : 0;
                    $overBudgetPct = 100 - $onBudgetPct;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600">Within Budget</span>
                        <span class="text-sm font-semibold text-green-600">{{ $summary['on_budget'] }} ({{ $onBudgetPct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $onBudgetPct }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600">Over Budget</span>
                        <span class="text-sm font-semibold text-red-600">{{ $summary['over_budget'] }} ({{ $overBudgetPct }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $overBudgetPct }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Projects Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Completed Projects</h3>
        </div>

        @if($projectData->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projectData as $project)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('tenant.projects.show', [$tenant->slug, $project->id]) }}" class="text-sm font-medium text-violet-600 hover:text-violet-800">
                                        {{ $project->name }}
                                    </a>
                                    <p class="text-xs text-gray-400 font-mono">{{ $project->project_number }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $project->customer ? ($project->customer->company_name ?: $project->customer->first_name . ' ' . $project->customer->last_name) : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $project->completed_at ? $project->completed_at->format('M d, Y') : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                    {{ $project->duration_days !== null ? $project->duration_days . ' days' : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                                    {{ $project->budget ? '₦' . number_format($project->budget, 2) : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-medium">
                                    ₦{{ number_format($project->revenue, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $project->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    ₦{{ number_format($project->profit, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        @if($project->was_late)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Late</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">On Time</span>
                                        @endif
                                        @if($project->was_over_budget)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Over Budget</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr class="font-semibold">
                            <td class="px-6 py-3 text-sm text-gray-900" colspan="4">Totals ({{ $summary['total_completed'] }} projects)</td>
                            <td class="px-6 py-3 text-sm text-gray-900 text-right">₦{{ number_format($projectData->sum('budget'), 2) }}</td>
                            <td class="px-6 py-3 text-sm text-green-600 text-right">₦{{ number_format($summary['total_revenue'], 2) }}</td>
                            <td class="px-6 py-3 text-sm text-right {{ $summary['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">₦{{ number_format($summary['total_profit'], 2) }}</td>
                            <td class="px-6 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-12 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No completed projects found for the selected period.</p>
            </div>
        @endif
    </div>
</div>
@endsection
