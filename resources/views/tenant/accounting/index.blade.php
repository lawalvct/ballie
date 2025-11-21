@extends('layouts.tenant')

@section('title', 'Accounting Dashboard')

@section('page-title', 'Accounting')
@section('page-description')
    <span class="hidden md:inline">Manage all your business finances: invoices, vouchers, revenue and expenses in one place.</span>
@endsection


@section('content')
<div x-data="{
    moreActionsExpanded: false,
    toggleMoreActions() {
        this.moreActionsExpanded = !this.moreActionsExpanded;
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">


        <!-- Action Buttons -->
        <div class="mt-4 lg:mt-0 grid grid-cols-2 sm:flex sm:flex-wrap gap-2 sm:gap-3">
            <a href="{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="hidden sm:inline ml-2">Sales Invoice</span>
                <span class="sm:hidden">Sales</span>
            </a>

            <a href="{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug]) }}?type=pur"
               class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="hidden sm:inline ml-2">Purchase Invoice</span>
                <span class="sm:hidden">Purchase</span>
            </a>

            <a href="{{ route('tenant.accounting.invoices.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline ml-2">View Invoices</span>
                <span class="sm:hidden">Invoices</span>
            </a>

            <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="hidden sm:inline ml-2">View Vouchers</span>
                <span class="sm:hidden">Vouchers</span>
            </a>

            <!-- More Actions Button -->
            <button @click="toggleMoreActions()"
                    class="col-span-2 sm:col-span-1 inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                </svg>
                <span class="ml-2" x-text="moreActionsExpanded ? 'Hide Actions' : 'More Actions'"></span>
                <svg class="w-4 h-4 ml-2 transition-transform duration-200"
                     :class="{ 'rotate-180': moreActionsExpanded }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Include the More Actions Expandable Section -->
    @include('tenant.accounting.partials.more-actions-section')

    <!-- Financial Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600">Total Revenue</p>
                    <p class="text-base sm:text-xl font-bold text-gray-900">₦{{ number_format($totalRevenue ?? 0, 2) }}</p>
                    <p class="text-xs sm:text-sm {{ $revenueChange['direction'] === 'up' ? 'text-green-600' : ($revenueChange['direction'] === 'down' ? 'text-red-600' : 'text-gray-600') }} mt-1">
                        <span class="inline-flex items-center">
                            @if($revenueChange['direction'] === 'up')
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @elseif($revenueChange['direction'] === 'down')
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            @else
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                                </svg>
                            @endif
                            <span class="hidden sm:inline">{{ $revenueChange['direction'] === 'up' ? '+' : ($revenueChange['direction'] === 'down' ? '-' : '') }}{{ $revenueChange['percentage'] }}% from last month</span>
                            <span class="sm:hidden">{{ $revenueChange['direction'] === 'up' ? '+' : ($revenueChange['direction'] === 'down' ? '-' : '') }}{{ $revenueChange['percentage'] }}%</span>
                        </span>
                    </p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600">Total Expenses</p>
                    <p class="text-base sm:text-xl font-bold text-gray-900">₦{{ number_format($totalExpenses ?? 0, 2) }}</p>
                    <p class="text-xs sm:text-sm {{ $expenseChange['direction'] === 'up' ? 'text-red-600' : ($expenseChange['direction'] === 'down' ? 'text-green-600' : 'text-gray-600') }} mt-1">
                        <span class="inline-flex items-center">
                            @if($expenseChange['direction'] === 'up')
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @elseif($expenseChange['direction'] === 'down')
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            @else
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                                </svg>
                            @endif
                            <span class="hidden sm:inline">{{ $expenseChange['direction'] === 'up' ? '+' : ($expenseChange['direction'] === 'down' ? '-' : '') }}{{ $expenseChange['percentage'] }}% from last month</span>
                            <span class="sm:hidden">{{ $expenseChange['direction'] === 'up' ? '+' : ($expenseChange['direction'] === 'down' ? '-' : '') }}{{ $expenseChange['percentage'] }}%</span>
                        </span>
                    </p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600">Outstanding Invoices</p>
                    <p class="text-base sm:text-xl font-bold text-gray-900">₦{{ number_format($outstandingInvoices ?? 0, 2) }}</p>
                    <p class="text-xs sm:text-sm text-orange-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $pendingInvoicesCount ?? 0 }} pending
                        </span>
                    </p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg border-l-4 border-teal-500">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-600">Net Profit</p>
                    <p class="text-base sm:text-xl font-bold text-gray-900">₦{{ number_format(($totalRevenue ?? 0) - ($totalExpenses ?? 0), 2) }}</p>
                    <p class="text-xs sm:text-sm {{ $profitChange['direction'] === 'up' ? 'text-green-600' : ($profitChange['direction'] === 'down' ? 'text-red-600' : 'text-gray-600') }} mt-1">
                        <span class="inline-flex items-center">
                            @if($profitChange['direction'] === 'up')
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @elseif($profitChange['direction'] === 'down')
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            @else
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"></path>
                                </svg>
                            @endif
                            <span class="hidden sm:inline">{{ $profitChange['direction'] === 'up' ? '+' : ($profitChange['direction'] === 'down' ? '-' : '') }}{{ $profitChange['percentage'] }}% from last month</span>
                            <span class="sm:hidden">{{ $profitChange['direction'] === 'up' ? '+' : ($profitChange['direction'] === 'down' ? '-' : '') }}{{ $profitChange['percentage'] }}%</span>
                        </span>
                    </p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Recent Transactions</h3>
                <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            <div class="space-y-4">
                @forelse($recentTransactions ?? [] as $transaction)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-{{ $transaction->type === 'income' ? 'green' : 'red' }}-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-{{ $transaction->type === 'income' ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($transaction->type === 'income')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                    @endif
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $transaction->description }}</p>
                                <p class="text-sm text-gray-500">{{ $transaction->date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-{{ $transaction->type === 'income' ? 'green' : 'red' }}-600">
                                {{ $transaction->type === 'income' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500">No recent transactions</p>
                        <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">Create your first voucher</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Voucher Summary -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Voucher Summary</h3>
                <a href="{{ route('tenant.accounting.voucher-types.index', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manage Types</a>
            </div>
            <div class="space-y-4">
                @forelse($voucherSummary ?? [] as $summary)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-{{ $summary['color'] }}-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-{{ $summary['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $summary['type'] }}</p>
                                <p class="text-sm text-gray-500">{{ $summary['count'] }} voucher{{ $summary['count'] !== 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">₦{{ number_format($summary['total'], 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $summary['code'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500">No vouchers this month</p>
                        <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">Create your first voucher</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h3 class="text-xl font-bold text-gray-900">Monthly Financial Overview</h3>
            <div class="flex space-x-2">
                <button onclick="updateChartPeriod('6m')" id="btn-6m" class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200">6M</button>
                <button onclick="updateChartPeriod('1y')" id="btn-1y" class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200">1Y</button>
            </div>
        </div>
        <div class="relative h-64 sm:h-80">
            <canvas id="financialChart"></canvas>
        </div>

        <!-- Chart Legend -->
        <div class="mt-6 flex flex-wrap justify-center gap-4 text-sm">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                <span class="text-gray-700">Revenue</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                <span class="text-gray-700">Expenses</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                <span class="text-gray-700">Profit</span>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
.quick-action-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.quick-action-btn:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.quick-action-btn:active {
    transform: translateY(0) scale(1.02);
}

/* Modal Action Cards Styles */
.modal-action-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.modal-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.8s;
}

.modal-action-card:hover::before {
    left: 100%;
}

.modal-action-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.modal-action-card:active {
    transform: translateY(-2px) scale(1.01);
}

/* Custom scrollbar for modal */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Animation for dropdown items */
.dropdown-item {
    transition: all 0.2s ease-in-out;
}

.dropdown-item:hover {
    transform: translateX(4px);
}

/* Modal backdrop blur effect */
.modal-backdrop {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* Pulse animation for modal cards */
@keyframes pulse-subtle {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.modal-action-card:hover .w-14 {
    animation: pulse-subtle 2s infinite;
}

/* Gradient animations */
.gradient-bg-primary {
    background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 400% 400%;
    animation: gradient-shift 15s ease infinite;
}

@keyframes gradient-shift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Hover effects for transaction items */
.transaction-item {
    transition: all 0.3s ease;
}

.transaction-item:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Loading skeleton animation */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Modal entrance animation */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

/* Card hover glow effect */
.modal-action-card:hover {
    box-shadow: 0 0 30px rgba(255, 255, 255, 0.2);
}

/* Responsive modal adjustments */
@media (max-width: 768px) {
    .modal-action-card {
        padding: 1rem;
    }

    .modal-action-card .w-14 {
        width: 2.5rem;
        height: 2.5rem;
    }

    .modal-action-card .w-7 {
        width: 1.25rem;
        height: 1.25rem;
    }
}

/* Ripple effect */
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>

@push('scripts')
<script>
let financialChart = null;
let currentPeriod = '6m';

// Initialize chart with data from backend
const chartData = @json($chartData);

function initializeChart(data) {
    const ctx = document.getElementById('financialChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (financialChart) {
        financialChart.destroy();
    }

    financialChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: data.revenue,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Expenses',
                    data: data.expenses,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(239, 68, 68)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Profit',
                    data: data.profit,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '₦' + context.parsed.y.toLocaleString('en-NG', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString('en-NG');
                        },
                        color: '#6B7280',
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

function updateChartPeriod(period) {
    currentPeriod = period;

    // Update button styles
    document.getElementById('btn-6m').classList.toggle('bg-blue-600', period === '6m');
    document.getElementById('btn-6m').classList.toggle('text-white', period === '6m');
    document.getElementById('btn-6m').classList.toggle('bg-gray-100', period !== '6m');
    document.getElementById('btn-6m').classList.toggle('text-gray-700', period !== '6m');

    document.getElementById('btn-1y').classList.toggle('bg-blue-600', period === '1y');
    document.getElementById('btn-1y').classList.toggle('text-white', period === '1y');
    document.getElementById('btn-1y').classList.toggle('bg-gray-100', period !== '1y');
    document.getElementById('btn-1y').classList.toggle('text-gray-700', period !== '1y');

    // Fetch new data (you can implement AJAX call here if needed)
    // For now, we'll use the initial data
    // In production, you'd want to fetch from: /tenant/{tenant}/accounting/chart-data?period={period}
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the chart
    initializeChart(chartData);

    // Add smooth hover effects to cards
    const cards = document.querySelectorAll('.bg-white.rounded-2xl');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        });
    });

    // Add click animation to quick action buttons
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add click animation to modal action cards
    const modalActionCards = document.querySelectorAll('.modal-action-card');
    modalActionCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Add click feedback
            this.style.transform = 'translateY(-2px) scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Alt + I for Create Invoice
        if (e.altKey && e.key === 'i') {
            e.preventDefault();
            window.location.href = "{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug]) }}";
        }

        // Alt + V for Create Voucher
        if (e.altKey && e.key === 'v') {
            e.preventDefault();
            window.location.href = "{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug]) }}";
        }

        // Alt + R for Reports
        if (e.altKey && e.key === 'r') {
            e.preventDefault();
            window.location.href = "{{ route('tenant.reports.index', ['tenant' => $tenant->slug]) }}";
        }

        // Alt + M for More Actions Modal
        if (e.altKey && e.key === 'm') {
            e.preventDefault();
            // Trigger the Alpine.js modal
            const moreActionsBtn = document.querySelector('[x-data] button');
            if (moreActionsBtn) {
                moreActionsBtn.click();
            }
        }

        // Escape to close modal
        if (e.key === 'Escape') {
            // This will be handled by Alpine.js automatically
        }
    });

    // Auto-refresh data every 5 minutes
    setInterval(function() {
        // You can implement AJAX refresh here if needed
        console.log('Auto-refresh triggered');
    }, 300000); // 5 minutes
});
</script>
@endpush
@endsection
