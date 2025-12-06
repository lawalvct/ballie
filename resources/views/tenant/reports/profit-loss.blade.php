@extends('layouts.tenant')

@section('title', 'Profit & Loss Statement')
@section('page-title', 'Profit & Loss Statement')
@section('page-description', 'View your profit and loss statement for the selected period')

@section('content')
<div class="space-y-6">
    <!-- Financial Reports Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.reports.profit-loss', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-emerald-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Profit & Loss
            </a>

            <a href="{{ route('tenant.reports.balance-sheet', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-blue-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                Balance Sheet
            </a>

            <a href="{{ route('tenant.reports.trial-balance', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-purple-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a1 1 0 011-1h3M7 3v18"></path>
                </svg>
                Trial Balance
            </a>

            <a href="{{ route('tenant.reports.cash-flow', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-indigo-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6M9 10.5h6"></path>
                </svg>
                Cash Flow
            </a>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            <a href="{{ route('tenant.accounting.profit-loss-pdf', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                PDF
            </a>
            <a href="{{ route('tenant.accounting.profit-loss-excel', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" class="inline-flex items-center px-4 py-2 border border-green-300 rounded-lg shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Excel
            </a>
            <a href="{{ route('tenant.accounting.profit-loss-table', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Table
            </a>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" id="dateFilterForm">
            <!-- Quick Date Presets -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quick Presets</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setDateRange('today')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Today</button>
                    <button type="button" onclick="setDateRange('this_month')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">This Month</button>
                    <button type="button" onclick="setDateRange('last_month')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Last Month</button>
                    <button type="button" onclick="setDateRange('this_quarter')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">This Quarter</button>
                    <button type="button" onclick="setDateRange('last_quarter')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Last Quarter</button>
                    <button type="button" onclick="setDateRange('this_year')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">This Year</button>
                    <button type="button" onclick="setDateRange('last_year')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Last Year</button>
                </div>
            </div>
            
            <!-- Date Inputs -->
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="compare" value="1" {{ $compare ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="ml-2 text-sm text-gray-700">Compare</span>
                    </label>
                </div>
                <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @if($compare && $compareData)
        <div class="col-span-full bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Comparing with Previous Period</h4>
                    <p class="text-xs text-blue-600">{{ date('M d, Y', strtotime($compareData['fromDate'])) }} to {{ date('M d, Y', strtotime($compareData['toDate'])) }}</p>
                </div>
            </div>
        </div>
        @endif
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-6 border border-emerald-200">
            <div class="text-sm font-medium text-emerald-600 mb-1">Total Income</div>
            <div class="text-3xl font-bold text-emerald-700">₦{{ number_format($totalIncome, 2) }}</div>
            @if($compare && $compareData)
            @php
                $change = $compareData['totalIncome'] > 0 ? (($totalIncome - $compareData['totalIncome']) / $compareData['totalIncome']) * 100 : 0;
            @endphp
            <div class="mt-2 text-xs {{ $change >= 0 ? 'text-emerald-600' : 'text-red-600' }} font-medium">
                {{ $change >= 0 ? '↑' : '↓' }} {{ number_format(abs($change), 1) }}% vs previous
            </div>
            @endif
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
            <div class="text-sm font-medium text-red-600 mb-1">Total Expenses</div>
            <div class="text-3xl font-bold text-red-700">₦{{ number_format($totalExpenses, 2) }}</div>
            @if($compare && $compareData)
            @php
                $change = $compareData['totalExpenses'] > 0 ? (($totalExpenses - $compareData['totalExpenses']) / $compareData['totalExpenses']) * 100 : 0;
            @endphp
            <div class="mt-2 text-xs {{ $change <= 0 ? 'text-emerald-600' : 'text-red-600' }} font-medium">
                {{ $change >= 0 ? '↑' : '↓' }} {{ number_format(abs($change), 1) }}% vs previous
            </div>
            @endif
        </div>
        <div class="bg-gradient-to-br {{ $netProfit >= 0 ? 'from-blue-50 to-blue-100 border-blue-200' : 'from-orange-50 to-orange-100 border-orange-200' }} rounded-xl p-6 border">
            <div class="text-sm font-medium {{ $netProfit >= 0 ? 'text-blue-600' : 'text-orange-600' }} mb-1">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            <div class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-blue-700' : 'text-orange-700' }}">₦{{ number_format(abs($netProfit), 2) }}</div>
            @if($compare && $compareData)
            @php
                $prevProfit = $compareData['netProfit'];
                $change = $prevProfit != 0 ? (($netProfit - $prevProfit) / abs($prevProfit)) * 100 : 0;
            @endphp
            <div class="mt-2 text-xs {{ $change >= 0 ? 'text-emerald-600' : 'text-red-600' }} font-medium">
                {{ $change >= 0 ? '↑' : '↓' }} {{ number_format(abs($change), 1) }}% vs previous
            </div>
            @endif
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
            <div class="text-sm font-medium text-purple-600 mb-1">Profit Margin</div>
            <div class="text-3xl font-bold text-purple-700">
                @if($totalIncome > 0)
                    {{ number_format(($netProfit / $totalIncome) * 100, 2) }}%
                @else
                    0.00%
                @endif
            </div>
        </div>
    </div>

    <!-- Report -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Profit & Loss Statement</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Period: {{ date('M d, Y', strtotime($fromDate)) }} to {{ date('M d, Y', strtotime($toDate)) }}
                </p>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Income -->
                <div>
                    <div class="bg-emerald-50 px-4 py-3 rounded-lg mb-4">
                        <h4 class="text-lg font-bold text-emerald-800">Income</h4>
                    </div>
                    <div class="space-y-2">
                        @forelse($incomeData as $item)
                            <div class="flex justify-between items-center py-2 px-3 hover:bg-gray-50 rounded-lg group">
                                <a href="{{ route('tenant.accounting.ledger-accounts.show', ['tenant' => $tenant->slug, 'ledgerAccount' => $item['account']->id]) }}"
                                   class="text-gray-700 hover:text-emerald-600 flex items-center gap-2">
                                    <span>{{ $item['account']->name }}</span>
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                <span class="font-semibold text-emerald-600">₦{{ number_format($item['amount'], 2) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic py-4 text-center">No income recorded</p>
                        @endforelse

                        <div class="border-t-2 border-emerald-200 pt-3 mt-4">
                            <div class="flex justify-between items-center font-bold text-emerald-800 text-lg px-3">
                                <span>Total Income</span>
                                <span>₦{{ number_format($totalIncome, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expenses -->
                <div>
                    <div class="bg-red-50 px-4 py-3 rounded-lg mb-4">
                        <h4 class="text-lg font-bold text-red-800">Expenses</h4>
                    </div>
                    <div class="space-y-2">
                        @forelse($expenseData as $item)
                            <div class="flex justify-between items-center py-2 px-3 hover:bg-gray-50 rounded-lg group">
                                <a href="{{ route('tenant.accounting.ledger-accounts.show', ['tenant' => $tenant->slug, 'ledgerAccount' => $item['account']->id]) }}"
                                   class="text-gray-700 hover:text-red-600 flex items-center gap-2">
                                    <span>{{ $item['account']->name }}</span>
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                                <span class="font-semibold text-red-600">₦{{ number_format($item['amount'], 2) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic py-4 text-center">No expenses recorded</p>
                        @endforelse

                        <div class="border-t-2 border-red-200 pt-3 mt-4">
                            <div class="flex justify-between items-center font-bold text-red-800 text-lg px-3">
                                <span>Total Expenses</span>
                                <span>₦{{ number_format($totalExpenses, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Summary -->
            @if(isset($openingStock) || isset($closingStock))
            <div class="mt-8 pt-6 border-t-2 border-gray-200">
                <h4 class="text-lg font-bold text-gray-800 mb-4">Stock Summary</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-5 rounded-lg border border-blue-200">
                        <div class="text-sm text-blue-600 font-semibold mb-1">Opening Stock</div>
                        <div class="text-2xl font-bold text-blue-800">₦{{ number_format($openingStock ?? 0, 2) }}</div>
                    </div>
                    <div class="bg-emerald-50 p-5 rounded-lg border border-emerald-200">
                        <div class="text-sm text-emerald-600 font-semibold mb-1">Closing Stock</div>
                        <div class="text-2xl font-bold text-emerald-800">₦{{ number_format($closingStock ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Net Profit/Loss -->
            <div class="mt-8 pt-6 border-t-2 border-gray-300">
                <div class="flex justify-between items-center px-3 py-2 bg-gray-50 rounded-lg">
                    <span class="text-xl font-bold text-gray-900">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
                    <span class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        ₦{{ number_format(abs($netProfit), 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setDateRange(preset) {
    const today = new Date();
    let fromDate, toDate;
    
    switch(preset) {
        case 'today':
            fromDate = toDate = today;
            break;
        case 'this_month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'last_month':
            fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            toDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'this_quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            fromDate = new Date(today.getFullYear(), quarter * 3, 1);
            toDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
            break;
        case 'last_quarter':
            const lastQuarter = Math.floor(today.getMonth() / 3) - 1;
            const year = lastQuarter < 0 ? today.getFullYear() - 1 : today.getFullYear();
            const q = lastQuarter < 0 ? 3 : lastQuarter;
            fromDate = new Date(year, q * 3, 1);
            toDate = new Date(year, q * 3 + 3, 0);
            break;
        case 'this_year':
            fromDate = new Date(today.getFullYear(), 0, 1);
            toDate = new Date(today.getFullYear(), 11, 31);
            break;
        case 'last_year':
            fromDate = new Date(today.getFullYear() - 1, 0, 1);
            toDate = new Date(today.getFullYear() - 1, 11, 31);
            break;
    }
    
    document.getElementById('from_date').value = fromDate.toISOString().split('T')[0];
    document.getElementById('to_date').value = toDate.toISOString().split('T')[0];
    document.getElementById('dateFilterForm').submit();
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
