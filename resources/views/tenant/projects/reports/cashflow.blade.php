@extends('layouts.tenant')

@section('title', 'Project Cashflow')
@section('page-title', 'Project Cashflow')
@section('page-description')
    <span class="hidden md:inline">Cash inflows from milestones and outflows from project expenses.</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.projects.reports.profitability', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-violet-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-violet-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Profitability
            </a>
            <a href="{{ route('tenant.projects.reports.revenue-by-client', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Revenue by Client
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
        <form method="GET" action="{{ route('tenant.projects.reports.cashflow', $tenant->slug) }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select name="project_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-violet-500 focus:border-violet-500">
                    <option value="">All Projects</option>
                    @foreach($projectList as $p)
                        <option value="{{ $p->id }}" {{ $projectId == $p->id ? 'selected' : '' }}>
                            {{ $p->project_number }} — {{ $p->name }}
                        </option>
                    @endforeach
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Total Inflows</p>
            <p class="text-2xl font-bold text-green-600 mt-1">₦{{ number_format($summary['total_inflows'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $summary['inflow_count'] }} transactions</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-red-500">
            <p class="text-sm font-medium text-gray-500">Total Outflows</p>
            <p class="text-2xl font-bold text-red-600 mt-1">₦{{ number_format($summary['total_outflows'], 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $summary['outflow_count'] }} expenses</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-violet-500">
            <p class="text-sm font-medium text-gray-500">Net Cashflow</p>
            <p class="text-2xl font-bold {{ $summary['net_cashflow'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                ₦{{ number_format($summary['net_cashflow'], 2) }}
            </p>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    @if($months->count() > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Monthly Cashflow</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Inflows</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Outflows</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Flow</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($months as $month)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $month->month }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 text-right">
                                    {{ $month->inflows > 0 ? '₦' . number_format($month->inflows, 2) : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right">
                                    {{ $month->outflows > 0 ? '₦' . number_format($month->outflows, 2) : '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold {{ $month->net >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    ₦{{ number_format($month->net, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $maxFlow = $months->max(fn($m) => max(abs($m->inflows), abs($m->outflows)));
                                        $inflowWidth = $maxFlow > 0 ? round(($month->inflows / $maxFlow) * 100) : 0;
                                        $outflowWidth = $maxFlow > 0 ? round(($month->outflows / $maxFlow) * 100) : 0;
                                    @endphp
                                    <div class="flex flex-col space-y-1 w-40">
                                        <div class="bg-gray-100 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $inflowWidth }}%"></div>
                                        </div>
                                        <div class="bg-gray-100 rounded-full h-2">
                                            <div class="bg-red-400 h-2 rounded-full" style="width: {{ $outflowWidth }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr class="font-semibold">
                            <td class="px-6 py-3 text-sm text-gray-900">Totals</td>
                            <td class="px-6 py-3 text-sm text-green-600 text-right">₦{{ number_format($summary['total_inflows'], 2) }}</td>
                            <td class="px-6 py-3 text-sm text-red-600 text-right">₦{{ number_format($summary['total_outflows'], 2) }}</td>
                            <td class="px-6 py-3 text-sm text-right {{ $summary['net_cashflow'] >= 0 ? 'text-green-600' : 'text-red-600' }}">₦{{ number_format($summary['net_cashflow'], 2) }}</td>
                            <td class="px-6 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expense by Category -->
        @if($expenseByCategory->count() > 0)
            <div class="bg-white rounded-2xl p-6 shadow-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Expenses by Category</h3>
                <div class="space-y-3">
                    @foreach($expenseByCategory as $cat)
                        @php
                            $catPct = $summary['total_outflows'] > 0 ? round(($cat->total / $summary['total_outflows']) * 100) : 0;
                            $catColors = [
                                'General' => 'bg-gray-500',
                                'Travel' => 'bg-blue-500',
                                'Materials' => 'bg-yellow-500',
                                'Subcontractor' => 'bg-purple-500',
                                'Software' => 'bg-indigo-500',
                            ];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-700">{{ $cat->category }}</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">₦{{ number_format($cat->total, 2) }}</span>
                                    <span class="text-xs text-gray-400">({{ $catPct }}%)</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $catColors[$cat->category] ?? 'bg-gray-500' }} h-2 rounded-full" style="width: {{ $catPct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h3>
            @php
                $recentItems = $inflows->map(fn($i) => (object)['date' => $i->date, 'amount' => $i->amount, 'desc' => $i->description, 'project' => $i->project_name, 'type' => 'inflow'])
                    ->merge($outflows->map(fn($o) => (object)['date' => $o->date, 'amount' => $o->amount, 'desc' => $o->description, 'project' => $o->project_name, 'type' => 'outflow']))
                    ->sortByDesc('date')
                    ->take(10);
            @endphp
            @if($recentItems->count() > 0)
                <div class="space-y-3">
                    @foreach($recentItems as $item)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $item->type === 'inflow' ? 'bg-green-100' : 'bg-red-100' }}">
                                    @if($item->type === 'inflow')
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->desc }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->project }} · {{ $item->date->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold {{ $item->type === 'inflow' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $item->type === 'inflow' ? '+' : '-' }}₦{{ number_format($item->amount, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400 text-center py-4">No transactions found for this period.</p>
            @endif
        </div>
    </div>
</div>
@endsection
