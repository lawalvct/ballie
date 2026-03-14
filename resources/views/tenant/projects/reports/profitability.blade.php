@extends('layouts.tenant')

@section('title', 'Project Profitability Report')
@section('page-title', 'Project Profitability Report')
@section('page-description')
    <span class="hidden md:inline">Analyze which projects are making money and which are not.</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.projects.reports.revenue-by-client', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Revenue by Client
            </a>
            <a href="{{ route('tenant.projects.reports.cashflow', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Cashflow
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
        <form method="GET" action="{{ route('tenant.projects.reports.profitability', $tenant->slug) }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="on_hold" {{ $status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
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
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-green-600 mt-1">₦{{ number_format($summary['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-red-500">
            <p class="text-sm font-medium text-gray-500">Total Expenses</p>
            <p class="text-2xl font-bold text-red-600 mt-1">₦{{ number_format($summary['total_expenses'], 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-violet-500">
            <p class="text-sm font-medium text-gray-500">Net Profit</p>
            <p class="text-2xl font-bold {{ $summary['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                ₦{{ number_format($summary['total_profit'], 2) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-500">Avg Margin</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['avg_margin'] }}%</p>
            <div class="flex items-center space-x-4 mt-2 text-xs">
                <span class="text-green-600">{{ $summary['profitable_count'] }} profitable</span>
                <span class="text-red-600">{{ $summary['loss_count'] }} at loss</span>
            </div>
        </div>
    </div>

    <!-- Unbilled Revenue Alert -->
    @if($summary['total_unbilled'] > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex items-center space-x-3">
            <svg class="w-6 h-6 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <p class="text-sm text-yellow-700">
                You have <strong>₦{{ number_format($summary['total_unbilled'], 2) }}</strong> in completed but unbilled milestones. Consider invoicing these.
            </p>
        </div>
    @endif

    <!-- Profitability Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Project Profitability Breakdown</h3>
            <span class="text-sm text-gray-500">{{ $projectData->count() }} projects</span>
        </div>

        @if($projectData->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Margin</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projectData as $project)
                            @php
                                $statusColors = [
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $project->customer ? ($project->customer->company_name ?: $project->customer->first_name . ' ' . $project->customer->last_name) : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$project->status] ?? '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                                    {{ $project->budget ? '₦' . number_format($project->budget, 2) : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right font-medium">
                                    ₦{{ number_format($project->revenue, 2) }}
                                    @if($project->unbilled_revenue > 0)
                                        <p class="text-xs text-yellow-500">+₦{{ number_format($project->unbilled_revenue, 2) }} unbilled</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right">
                                    ₦{{ number_format($project->total_expenses, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $project->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    ₦{{ number_format($project->profit, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $project->margin >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $project->margin }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr class="font-semibold">
                            <td class="px-6 py-3 text-sm text-gray-900" colspan="3">Totals</td>
                            <td class="px-6 py-3 text-sm text-gray-900 text-right">₦{{ number_format($summary['total_budget'], 2) }}</td>
                            <td class="px-6 py-3 text-sm text-green-600 text-right">₦{{ number_format($summary['total_revenue'], 2) }}</td>
                            <td class="px-6 py-3 text-sm text-red-600 text-right">₦{{ number_format($summary['total_expenses'], 2) }}</td>
                            <td class="px-6 py-3 text-sm text-right {{ $summary['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">₦{{ number_format($summary['total_profit'], 2) }}</td>
                            <td class="px-6 py-3 text-sm text-gray-900 text-right">{{ $summary['avg_margin'] }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-12 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p>No projects found for the selected filters.</p>
            </div>
        @endif
    </div>
</div>
@endsection
