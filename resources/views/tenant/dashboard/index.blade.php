@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('page-title')
    <span class="md:hidden">Dashboard</span>
    <span class="hidden md:inline">Dashboard Overview</span>
@endsection

@section('page-description')
    <span class="hidden md:inline">
        @php
            $catLabels = [
                'trading' => 'Trading Business Metrics',
                'manufacturing' => 'Manufacturing Business Metrics',
                'service' => 'Service Business Metrics',
                'hybrid' => 'Business Metrics & Insights',
            ];
        @endphp
        {{ $catLabels[$businessCategory] ?? 'Business Metrics & Insights' }}
    </span>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
    .metric-card { transition: all 0.2s ease; }
    .metric-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1); }
    .sparkline-container { height: 40px; }
    .chart-container { position: relative; }
    .gradient-card { background: linear-gradient(135deg, var(--from) 0%, var(--to) 100%); }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="dashboardApp()">

    {{-- ═══════════════════════════════════════════════════════
         TOUR BANNER
         ═══════════════════════════════════════════════════════ --}}
    @if(isset($showTour) && $showTour)
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-start space-x-4 mb-4 md:mb-0">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="text-white">
                        <h3 class="text-2xl md:text-3xl font-bold mb-2">Welcome to @brand!</h3>
                        <p class="text-blue-100 text-base md:text-lg mb-1">Need help getting started?</p>
                        <p class="text-blue-200 text-sm">Check out our comprehensive documentation and guides.</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('tenant.tour.skip', $tenant->slug) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-xl transition-all">
                            Maybe Later
                        </button>
                    </form>
                    <a href="{{ route('tenant.help', $tenant->slug) }}" class="px-8 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-blue-50 transition-all text-center">
                        Help & Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         ALERTS
         ═══════════════════════════════════════════════════════ --}}
    @if(count($alerts) > 0)
    <div class="space-y-3">
        @foreach($alerts as $alert)
        <div id="alert-{{ $alert['type'] }}" class="bg-{{ $alert['color'] }}-50 border-l-4 border-{{ $alert['color'] }}-400 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-{{ $alert['color'] }}-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-{{ $alert['color'] }}-800">{{ $alert['title'] }}</h4>
                        <p class="text-sm text-{{ $alert['color'] }}-700">{{ $alert['message'] }}</p>
                    </div>
                </div>
                <button onclick="dismissAlert('{{ $alert['type'] }}')" class="text-{{ $alert['color'] }}-400 hover:text-{{ $alert['color'] }}-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         PRIMARY METRICS ROW
         ═══════════════════════════════════════════════════════ --}}
    @php
        // Category-aware metric cards
        $primaryMetrics = [];

        // Revenue (all categories)
        $primaryMetrics[] = [
            'label' => $term->label('revenue') ?: 'Revenue',
            'value' => '₦' . number_format($monthlyRevenue, 2),
            'subtitle' => 'This month',
            'change' => $revenueGrowth,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
            'color' => 'emerald',
            'gradient_from' => '#10b981',
            'gradient_to' => '#059669',
        ];

        // Service: Outstanding Invoices │ Trading/Mfg: Sales Count
        if ($businessCategory === 'service') {
            $primaryMetrics[] = [
                'label' => 'Outstanding Invoices',
                'value' => '₦' . number_format($outstandingInvoices['total'] ?? 0, 2),
                'subtitle' => ($outstandingInvoices['count'] ?? 0) . ' invoices',
                'change' => null,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                'color' => 'amber',
                'gradient_from' => '#f59e0b',
                'gradient_to' => '#d97706',
            ];
        } else {
            $primaryMetrics[] = [
                'label' => $term->label('sales') ?: 'Sales',
                'value' => number_format($totalSalesCount),
                'subtitle' => 'This month',
                'change' => null,
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>',
                'color' => 'blue',
                'gradient_from' => '#3b82f6',
                'gradient_to' => '#2563eb',
            ];
        }

        // Expenses
        $primaryMetrics[] = [
            'label' => 'Expenses',
            'value' => '₦' . number_format($monthlyExpenses + $monthlyPurchase, 2),
            'subtitle' => 'This month',
            'change' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m0 0l6-6m-6 6l6 6"/>',
            'color' => 'red',
            'gradient_from' => '#ef4444',
            'gradient_to' => '#dc2626',
        ];

        // Clients/Customers
        $primaryMetrics[] = [
            'label' => $term->label('customers') ?: 'Customers',
            'value' => number_format($totalCustomers),
            'subtitle' => $newCustomersThisMonth > 0 ? "+{$newCustomersThisMonth} this month" : 'Total registered',
            'change' => null,
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
            'color' => 'violet',
            'gradient_from' => '#8b5cf6',
            'gradient_to' => '#7c3aed',
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($primaryMetrics as $metric)
        <div class="metric-card bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
            {{-- Background decoration --}}
            <div class="absolute top-0 right-0 w-24 h-24 opacity-5">
                <svg class="w-full h-full text-{{ $metric['color'] }}-500" fill="currentColor" viewBox="0 0 24 24">
                    {!! $metric['icon'] !!}
                </svg>
            </div>

            <div class="flex items-start justify-between relative z-10">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500 mb-1">{{ $metric['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-900 tracking-tight">{{ $metric['value'] }}</p>
                    <div class="flex items-center mt-2 gap-2">
                        @if($metric['change'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $metric['change'] >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                <svg class="w-3 h-3 mr-0.5 {{ $metric['change'] >= 0 ? '' : 'rotate-180' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $metric['change'] >= 0 ? '+' : '' }}{{ number_format($metric['change'], 1) }}%
                            </span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $metric['subtitle'] }}</span>
                    </div>
                </div>
                <div class="w-11 h-11 bg-{{ $metric['color'] }}-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-{{ $metric['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $metric['icon'] !!}
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════════════
         FINANCIAL SUMMARY BAR (Net Profit, Cash, Receivables, Payables)
         ═══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-5 text-white">
            <p class="text-sm text-emerald-100 font-medium">Net Profit</p>
            <p class="text-xl font-bold mt-1">₦{{ number_format($netProfit, 2) }}</p>
            <p class="text-xs text-emerald-200 mt-1">This month</p>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-5 text-white">
            <p class="text-sm text-blue-100 font-medium">Cash Balance</p>
            <p class="text-xl font-bold mt-1">₦{{ number_format($cashBalance, 2) }}</p>
            <p class="text-xs text-blue-200 mt-1">Cash in Hand</p>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl p-5 text-white">
            <p class="text-sm text-amber-100 font-medium">Receivables</p>
            <p class="text-xl font-bold mt-1">₦{{ number_format(abs($receivables), 2) }}</p>
            <p class="text-xs text-amber-200 mt-1">Debtors owe you</p>
        </div>
        <div class="bg-gradient-to-br from-rose-500 to-red-600 rounded-xl p-5 text-white">
            <p class="text-sm text-rose-100 font-medium">Payables</p>
            <p class="text-xl font-bold mt-1">₦{{ number_format(abs($payables), 2) }}</p>
            <p class="text-xs text-rose-200 mt-1">You owe creditors</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         CHARTS ROW
         ═══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Revenue vs Expenses — Line/Bar Chart (2 cols) --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Revenue vs Expenses</h3>
                    <p class="text-sm text-gray-500">Last 6 months performance</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Revenue
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span> Expenses
                    </span>
                </div>
            </div>
            <div class="chart-container" style="height: 300px;">
                <canvas id="revenueExpenseChart"></canvas>
            </div>
        </div>

        {{-- Revenue Breakdown — Doughnut (1 col) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Monthly Breakdown</h3>
                <p class="text-sm text-gray-500">This month by transaction type</p>
            </div>
            <div class="chart-container flex items-center justify-center" style="height: 220px;">
                <canvas id="breakdownChart"></canvas>
            </div>
            {{-- Legend --}}
            <div class="mt-4 space-y-2" id="breakdownLegend"></div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         DAILY REVENUE SPARKLINE
         ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-2">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Daily {{ $term->label('revenue') ?: 'Revenue' }}</h3>
                <p class="text-sm text-gray-500">Last 14 days trend</p>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900">₦{{ number_format($monthlyRevenue, 2) }}</p>
                <p class="text-xs text-gray-500">Month total so far</p>
            </div>
        </div>
        <div class="chart-container" style="height: 180px;">
            <canvas id="dailyRevenueChart"></canvas>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         POS TODAY'S SALES (only if POS module is enabled)
         ═══════════════════════════════════════════════════════ --}}
    @if(($enabledModules['pos'] ?? false) && ($todaySales > 0 || $todaySalesCount > 0))
    <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-xl shadow-lg p-6 text-white">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-white/80 font-medium">{{ $term->label('todays_sales') ?: "Today's Sales" }} (POS)</p>
                    <p class="text-3xl font-bold">₦{{ number_format($todaySales, 2) }}</p>
                </div>
            </div>
            <div class="text-center sm:text-right">
                <p class="text-4xl font-bold">{{ $todaySalesCount }}</p>
                <p class="text-sm text-white/80">transactions today</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         INVENTORY ROW (only if inventory module is enabled)
         ═══════════════════════════════════════════════════════ --}}
    @if(($enabledModules['inventory'] ?? false) && !$term->isHidden('stock'))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="metric-card bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total {{ $term->label('products') ?: 'Products' }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Active items</p>
                </div>
                <div class="w-11 h-11 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="metric-card bg-white rounded-xl shadow-sm border {{ $lowStockCount > 0 ? 'border-yellow-200 bg-yellow-50/30' : 'border-gray-100' }} p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ $term->label('low_stock') ?: 'Low Stock' }}</p>
                    <p class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-yellow-600' : 'text-gray-900' }}">{{ $lowStockCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Need reorder</p>
                </div>
                <div class="w-11 h-11 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="metric-card bg-white rounded-xl shadow-sm border {{ $outOfStockCount > 0 ? 'border-red-200 bg-red-50/30' : 'border-gray-100' }} p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Out of Stock</p>
                    <p class="text-2xl font-bold {{ $outOfStockCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $outOfStockCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Urgent restock</p>
                </div>
                <div class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         TABLES ROW: Recent Transactions + Top Products/Customers
         ═══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent Transactions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Recent Transactions</h3>
                <a href="{{ route('tenant.accounting.vouchers.index', $tenant->slug) }}" class="text-xs text-brand-blue hover:underline font-medium">View All</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentTransactions as $txn)
                @php
                    $typeColors = [
                        'SV' => 'bg-green-100 text-green-700',
                        'PUR' => 'bg-orange-100 text-orange-700',
                        'RV' => 'bg-blue-100 text-blue-700',
                        'PV' => 'bg-red-100 text-red-700',
                        'JV' => 'bg-gray-100 text-gray-700',
                        'EXP' => 'bg-rose-100 text-rose-700',
                    ];
                    $badgeClass = $typeColors[$txn['type_code']] ?? 'bg-gray-100 text-gray-600';
                @endphp
                <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $badgeClass }} flex-shrink-0">
                            {{ $txn['type_code'] }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $txn['number'] }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Str::limit($txn['narration'] ?? $txn['type'], 40) }}</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        <p class="text-sm font-semibold text-gray-900">₦{{ number_format($txn['amount'], 2) }}</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($txn['date'])->format('M d') }}</p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p class="text-sm text-gray-400">No transactions yet</p>
                    <a href="{{ route('tenant.accounting.vouchers.create', $tenant->slug) }}" class="text-xs text-brand-blue hover:underline mt-1 inline-block">Create your first voucher</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Top Products or Top Customers (context-dependent) --}}
        @if(($enabledModules['inventory'] ?? false) && count($topProducts) > 0)
        {{-- Top Products card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">{{ $term->label('top_products') ?: 'Top Products' }}</h3>
                <a href="{{ route('tenant.inventory.products.index', $tenant->slug) }}" class="text-xs text-brand-blue hover:underline font-medium">View All</a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($topProducts as $idx => $product)
                <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 bg-brand-blue/10 text-brand-blue rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ $idx + 1 }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $product['name'] }}</p>
                            <p class="text-xs text-gray-400">{{ number_format($product['sales']) }} {{ $term->label('units_sold') ?: 'sold' }}</p>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">₦{{ number_format($product['revenue'], 2) }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @else
        {{-- Top Customers card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Top {{ $term->label('customers') ?: 'Customers' }}</h3>
                @if($enabledModules['crm'] ?? false)
                <a href="{{ route('tenant.crm.customers.index', $tenant->slug) }}" class="text-xs text-brand-blue hover:underline font-medium">View All</a>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topCustomers as $idx => $customer)
                <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-violet-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-violet-600">{{ strtoupper(substr($customer['name'], 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $customer['name'] }}</p>
                            @if($customer['outstanding'] > 0)
                            <p class="text-xs text-amber-600">₦{{ number_format($customer['outstanding'], 2) }} outstanding</p>
                            @else
                            <p class="text-xs text-gray-400">No outstanding</p>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">₦{{ number_format($customer['spent'], 2) }}</p>
                </div>
                @empty
                <div class="px-6 py-10 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-sm text-gray-400">No customer data yet</p>
                </div>
                @endforelse
            </div>
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════
         TOP CUSTOMERS GRID (shown alongside top products for trading/manufacturing)
         ═══════════════════════════════════════════════════════ --}}
    @if(($enabledModules['inventory'] ?? false) && count($topProducts) > 0 && count($topCustomers) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Top {{ $term->label('customers') ?: 'Customers' }}</h3>
            @if($enabledModules['crm'] ?? false)
            <a href="{{ route('tenant.crm.customers.index', $tenant->slug) }}" class="text-xs text-brand-blue hover:underline font-medium">View All</a>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @foreach($topCustomers as $customer)
            <div class="border border-gray-100 rounded-xl p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-violet-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-violet-600">{{ strtoupper(substr($customer['name'], 0, 2)) }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $customer['name'] }}</p>
                </div>
                <p class="text-lg font-bold text-gray-900">₦{{ number_format($customer['spent'], 0) }}</p>
                @if($customer['outstanding'] > 0)
                <p class="text-xs text-amber-600 mt-1">₦{{ number_format($customer['outstanding'], 0) }} owed</p>
                @else
                <p class="text-xs text-green-600 mt-1">All paid up</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         QUICK ACTIONS
         ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            {{-- Create Invoice (always) --}}
            <a href="{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug, 'type' => 'sv']) }}" class="group p-4 border border-gray-100 rounded-xl hover:border-brand-blue/30 hover:bg-blue-50/50 transition-all text-center">
                <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center mx-auto mb-2 transition-colors">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-700 group-hover:text-brand-blue">New {{ $term->label('sales_invoice') ?: 'Invoice' }}</p>
            </a>

            {{-- Add Customer/Client --}}
            @if($enabledModules['crm'] ?? false)
            <a href="{{ route('tenant.crm.customers.create', $tenant->slug) }}" class="group p-4 border border-gray-100 rounded-xl hover:border-green-300 hover:bg-green-50/50 transition-all text-center">
                <div class="w-10 h-10 bg-green-100 group-hover:bg-green-200 rounded-xl flex items-center justify-center mx-auto mb-2 transition-colors">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-700 group-hover:text-green-700">Add {{ $term->label('customer') ?: 'Customer' }}</p>
            </a>
            @endif

            {{-- Add Product (only if inventory enabled) --}}
            @if($enabledModules['inventory'] ?? false)
            <a href="{{ route('tenant.inventory.products.create', $tenant->slug) }}" class="group p-4 border border-gray-100 rounded-xl hover:border-purple-300 hover:bg-purple-50/50 transition-all text-center">
                <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-200 rounded-xl flex items-center justify-center mx-auto mb-2 transition-colors">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-700 group-hover:text-purple-700">Add {{ $term->label('product') ?: 'Product' }}</p>
            </a>
            @endif

            {{-- P&L Report --}}
            <a href="{{ route('tenant.reports.financial', $tenant->slug) }}" class="group p-4 border border-gray-100 rounded-xl hover:border-orange-300 hover:bg-orange-50/50 transition-all text-center">
                <div class="w-10 h-10 bg-orange-100 group-hover:bg-orange-200 rounded-xl flex items-center justify-center mx-auto mb-2 transition-colors">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-700 group-hover:text-orange-700">{{ $term->label('pnl_title') ?: 'P&L Report' }}</p>
            </a>

            {{-- Sales/Revenue Reports --}}
            <a href="{{ route('tenant.reports.sales', $tenant->slug) }}" class="group p-4 border border-gray-100 rounded-xl hover:border-red-300 hover:bg-red-50/50 transition-all text-center">
                <div class="w-10 h-10 bg-red-100 group-hover:bg-red-200 rounded-xl flex items-center justify-center mx-auto mb-2 transition-colors">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-700 group-hover:text-red-700">{{ $term->label('sales_reports') ?: 'Sales Reports' }}</p>
            </a>

            {{-- New Voucher --}}
            <a href="{{ route('tenant.accounting.vouchers.create', $tenant->slug) }}" class="group p-4 border border-gray-100 rounded-xl hover:border-indigo-300 hover:bg-indigo-50/50 transition-all text-center">
                <div class="w-10 h-10 bg-indigo-100 group-hover:bg-indigo-200 rounded-xl flex items-center justify-center mx-auto mb-2 transition-colors">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                    </svg>
                </div>
                <p class="text-xs font-medium text-gray-700 group-hover:text-indigo-700">New Voucher</p>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $chartLabels = json_encode($chartData['labels']);
    $chartRevenue = json_encode($chartData['revenue']);
    $chartExpenses = json_encode($chartData['expenses']);
    $breakdownData = json_encode($revenueBreakdown);
    $dailyData = json_encode($dailyRevenue);
@endphp
<script>
function dashboardApp() {
    return {};
}

function dismissAlert(alertType) {
    const el = document.getElementById('alert-' + alertType);
    if (el) {
        el.style.transition = 'all 0.3s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateX(-20px)';
        setTimeout(() => el.style.display = 'none', 300);
    }
    localStorage.setItem('dismissed_alert_' + alertType, Date.now());
}

document.addEventListener('DOMContentLoaded', function() {
    // Restore dismissed alerts
    ['low_stock', 'out_of_stock', 'receivables'].forEach(function(type) {
        const dismissed = localStorage.getItem('dismissed_alert_' + type);
        if (dismissed && (Date.now() - parseInt(dismissed)) < 86400000) {
            const el = document.getElementById('alert-' + type);
            if (el) el.style.display = 'none';
        }
    });

    // ─── Shared chart defaults ──────────────────────────
    Chart.defaults.font.family = "'Inter', 'system-ui', '-apple-system', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#9ca3af';

    const fmtNaira = (val) => {
        if (val >= 1e6) return '₦' + (val / 1e6).toFixed(1) + 'M';
        if (val >= 1e3) return '₦' + (val / 1e3).toFixed(0) + 'K';
        return '₦' + val.toLocaleString();
    };

    // ╔══════════════════════════════════════════════════════╗
    // ║  Revenue vs Expenses — Bar + Line Chart             ║
    // ╚══════════════════════════════════════════════════════╝
    const revExpCtx = document.getElementById('revenueExpenseChart');
    if (revExpCtx) {
        new Chart(revExpCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [
                    {
                        label: 'Revenue',
                        data: {!! $chartRevenue !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Expenses',
                        data: {!! $chartExpenses !!},
                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#f3f4f6',
                        bodyColor: '#d1d5db',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': ' + fmtNaira(ctx.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: { callback: val => fmtNaira(val), maxTicksLimit: 6 },
                        border: { display: false },
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                    }
                }
            }
        });
    }

    // ╔══════════════════════════════════════════════════════╗
    // ║  Revenue Breakdown — Doughnut Chart                 ║
    // ╚══════════════════════════════════════════════════════╝
    const breakdownCtx = document.getElementById('breakdownChart');
    const breakdownRaw = {!! $breakdownData !!};

    if (breakdownCtx) {
        const labels = breakdownRaw.length > 0 ? breakdownRaw.map(b => b.name) : ['No Data'];
        const values = breakdownRaw.length > 0 ? breakdownRaw.map(b => b.total) : [1];
        const colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1'];
        const bgColors = breakdownRaw.length > 0
            ? breakdownRaw.map((_, i) => colors[i % colors.length])
            : ['#e5e7eb'];

        new Chart(breakdownCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: bgColors,
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => ctx.label + ': ' + fmtNaira(ctx.raw)
                        }
                    }
                }
            }
        });

        // Custom legend
        const legendEl = document.getElementById('breakdownLegend');
        if (legendEl && breakdownRaw.length > 0) {
            legendEl.innerHTML = breakdownRaw.map((b, i) => `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${colors[i % colors.length]}"></span>
                        <span class="text-xs text-gray-600">${b.name}</span>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">${fmtNaira(b.total)}</span>
                </div>
            `).join('');
        } else if (legendEl) {
            legendEl.innerHTML = '<p class="text-xs text-gray-400 text-center">No transactions this month</p>';
        }
    }

    // ╔══════════════════════════════════════════════════════╗
    // ║  Daily Revenue — Area/Line Chart                    ║
    // ╚══════════════════════════════════════════════════════╝
    const dailyCtx = document.getElementById('dailyRevenueChart');
    const dailyRaw = {!! $dailyData !!};

    if (dailyCtx) {
        const ctx2d = dailyCtx.getContext('2d');
        const gradient = ctx2d.createLinearGradient(0, 0, 0, 180);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.15)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(ctx2d, {
            type: 'line',
            data: {
                labels: dailyRaw.map(d => d.date),
                datasets: [{
                    label: 'Revenue',
                    data: dailyRaw.map(d => d.amount),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#3b82f6',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => fmtNaira(ctx.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                        ticks: { callback: val => fmtNaira(val), maxTicksLimit: 5 },
                        border: { display: false },
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 7 },
                    }
                }
            }
        });
    }
});
</script>
@endpush
