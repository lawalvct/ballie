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

            <!-- BallieAI Interpretation CTA -->
            <div class="mt-6 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-5 no-print">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h4 class="text-lg font-semibold text-purple-800">BallieAI Interpretation</h4>
                        <p class="text-sm text-purple-600">Get an AI-powered explanation of your Profit & Loss performance.</p>
                    </div>
                    <button onclick="openAIInterpretation()" class="inline-flex items-center px-4 py-2 border border-purple-300 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Ask BallieAI to Interpret
                    </button>
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

<!-- BallieAI Interpretation Modal -->
<div id="aiInterpretationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAIInterpretation()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white" id="modal-title">BallieAI Financial Analysis</h3>
                            <p class="text-sm text-purple-200">Powered by AI - Profit & Loss Interpretation</p>
                        </div>
                    </div>
                    <button onclick="closeAIInterpretation()" class="text-white hover:text-purple-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                <!-- Loading State -->
                <div id="aiLoadingState" class="flex flex-col items-center justify-center py-12">
                    <div class="relative">
                        <div class="w-16 h-16 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-gray-600 font-medium">BallieAI is analyzing your financial data...</p>
                    <p class="text-sm text-gray-500 mt-1">This may take a few seconds</p>
                </div>

                <!-- Interpretation Content -->
                <div id="aiInterpretationContent" class="hidden prose prose-purple max-w-none">
                    <!-- AI response will be inserted here -->
                </div>

                <!-- Error State -->
                <div id="aiErrorState" class="hidden">
                    <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-red-800 mb-2">Analysis Unavailable</h4>
                        <p id="aiErrorMessage" class="text-red-600 mb-4">Unable to generate analysis at this time.</p>
                        <button onclick="requestAIInterpretation()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Try Again
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-200">
                <div class="text-xs text-gray-500 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    AI-generated insights. Always verify with professional advice.
                </div>
                <div class="flex gap-2">
                    <button onclick="copyInterpretation()" id="copyBtn" class="hidden px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy
                    </button>
                    <button onclick="downloadInterpretationPdf()" id="downloadPdfBtn" class="hidden px-4 py-2 border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </button>
                    <button onclick="closeAIInterpretation()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                        Close
                    </button>
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

// BallieAI Interpretation Functions
function openAIInterpretation() {
    const modal = document.getElementById('aiInterpretationModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAIInterpretation();
}

function closeAIInterpretation() {
    const modal = document.getElementById('aiInterpretationModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

function requestAIInterpretation() {
    // Show loading, hide others
    document.getElementById('aiLoadingState').classList.remove('hidden');
    document.getElementById('aiInterpretationContent').classList.add('hidden');
    document.getElementById('aiErrorState').classList.add('hidden');
    document.getElementById('copyBtn').classList.add('hidden');
    document.getElementById('downloadPdfBtn').classList.add('hidden');

    // Collect report data from the page
    const reportData = {
        fromDate: '{{ $fromDate }}',
        toDate: '{{ $toDate }}',
        totalIncome: {{ $totalIncome }},
        totalExpenses: {{ $totalExpenses }},
        netProfit: {{ $netProfit }},
        openingStock: {{ $openingStock ?? 0 }},
        closingStock: {{ $closingStock ?? 0 }},
        incomeAccounts: [
            @foreach($incomeData as $item)
            { name: '{{ addslashes($item['account']->name) }}', amount: {{ $item['amount'] }} },
            @endforeach
        ],
        expenseAccounts: [
            @foreach($expenseData as $item)
            { name: '{{ addslashes($item['account']->name) }}', amount: {{ $item['amount'] }} },
            @endforeach
        ],
        @if($compare && $compareData)
        compareData: {
            fromDate: '{{ $compareData['fromDate'] }}',
            toDate: '{{ $compareData['toDate'] }}',
            totalIncome: {{ $compareData['totalIncome'] }},
            totalExpenses: {{ $compareData['totalExpenses'] }},
            netProfit: {{ $compareData['netProfit'] }}
        }
        @else
        compareData: null
        @endif
    };

    // Make API request
    fetch('/api/ai/interpret-profit-loss', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reportData })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('aiLoadingState').classList.add('hidden');

        if (data.success || data.interpretation) {
            const interpretation = data.interpretation || data.message;
            displayInterpretation(interpretation);
        } else {
            showAIError(data.message || 'Unable to generate analysis.');
        }
    })
    .catch(error => {
        console.error('AI Interpretation Error:', error);
        document.getElementById('aiLoadingState').classList.add('hidden');
        showAIError('Network error. Please check your connection and try again.');
    });
}

function displayInterpretation(interpretation) {
    // Store the original interpretation text for PDF export
    currentInterpretation = interpretation;

    const contentDiv = document.getElementById('aiInterpretationContent');

    // Convert markdown-like formatting to HTML
    let html = interpretation
        // Headers
        .replace(/^### (.*$)/gim, '<h3 class="text-lg font-bold text-gray-900 mt-6 mb-3 flex items-center"><span class="mr-2">$1</span></h3>')
        .replace(/^## (.*$)/gim, '<h2 class="text-xl font-bold text-purple-800 mt-6 mb-4 pb-2 border-b border-purple-200">$1</h2>')
        .replace(/^# (.*$)/gim, '<h1 class="text-2xl font-bold text-purple-900 mb-4">$1</h1>')
        // Bold
        .replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold text-gray-900">$1</strong>')
        // Lists
        .replace(/^\d+\. (.*$)/gim, '<li class="ml-4 mb-2">$1</li>')
        .replace(/^- (.*$)/gim, '<li class="ml-4 mb-1 list-disc">$1</li>')
        .replace(/^• (.*$)/gim, '<li class="ml-4 mb-1 list-disc">$1</li>')
        // Paragraphs
        .replace(/\n\n/g, '</p><p class="mb-4 text-gray-700">')
        // Line breaks
        .replace(/\n/g, '<br>');

    // Wrap in paragraph tags
    html = '<p class="mb-4 text-gray-700">' + html + '</p>';

    // Fix list items
    html = html.replace(/<\/p><li/g, '</p><ul class="list-disc ml-6 mb-4"><li');
    html = html.replace(/<\/li><p/g, '</li></ul><p');

    contentDiv.innerHTML = `
        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h4 class="text-lg font-semibold text-purple-800">BallieAI Analysis</h4>
                    <p class="text-sm text-purple-600">Period: {{ date('M d, Y', strtotime($fromDate)) }} to {{ date('M d, Y', strtotime($toDate)) }}</p>
                </div>
            </div>
        </div>
        <div class="interpretation-content">
            ${html}
        </div>
    `;

    contentDiv.classList.remove('hidden');
    document.getElementById('copyBtn').classList.remove('hidden');
    document.getElementById('downloadPdfBtn').classList.remove('hidden');
}

function downloadInterpretationPdf() {
    const content = document.getElementById('aiInterpretationContent');
    if (!content || content.classList.contains('hidden')) {
        return;
    }

    // Get the interpretation text (remove HTML formatting for API)
    const interpretationText = currentInterpretation || content.innerText;

    // Prepare report data
    const reportData = {
        fromDate: '{{ $fromDate }}',
        toDate: '{{ $toDate }}',
        totalIncome: {{ $totalIncome }},
        totalExpenses: {{ $totalExpenses }},
        netProfit: {{ $netProfit }}
    };

    // Create a form and submit to trigger download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/api/ai/export-profit-loss-interpretation-pdf';
    form.target = '_blank';

    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    // Add interpretation
    const interpretationInput = document.createElement('input');
    interpretationInput.type = 'hidden';
    interpretationInput.name = 'interpretation';
    interpretationInput.value = interpretationText;
    form.appendChild(interpretationInput);

    // Add report data
    const reportDataInput = document.createElement('input');
    reportDataInput.type = 'hidden';
    reportDataInput.name = 'reportData';
    reportDataInput.value = JSON.stringify(reportData);
    form.appendChild(reportDataInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function showAIError(message) {
    document.getElementById('aiErrorMessage').textContent = message;
    document.getElementById('aiErrorState').classList.remove('hidden');
}

function copyInterpretation() {
    const content = document.getElementById('aiInterpretationContent');
    const text = content.innerText || content.textContent;

    navigator.clipboard.writeText(text).then(() => {
        const btn = document.getElementById('copyBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Copied!';
        btn.classList.add('bg-green-50', 'text-green-700', 'border-green-300');

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('bg-green-50', 'text-green-700', 'border-green-300');
        }, 2000);
    });
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAIInterpretation();
    }
});
</script>
@endsection
