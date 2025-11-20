@extends('layouts.tenant')

@section('title', 'Profit & Loss Statement')
@section('page-title', 'Profit & Loss Statement')
@section('page-description', 'View your profit and loss statement for the selected period')

@section('content')
<div class="space-y-6">
    <!-- Header with View Toggle -->
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Profit & Loss Statement</h1>
        <a href="{{ route('tenant.accounting.profit-loss-table', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate]) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Tabular View
        </a>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
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
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
                Generate Report
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-6 border border-emerald-200">
            <div class="text-sm font-medium text-emerald-600 mb-1">Total Income</div>
            <div class="text-3xl font-bold text-emerald-700">₦{{ number_format($totalIncome, 2) }}</div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
            <div class="text-sm font-medium text-red-600 mb-1">Total Expenses</div>
            <div class="text-3xl font-bold text-red-700">₦{{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="bg-gradient-to-br {{ $netProfit >= 0 ? 'from-blue-50 to-blue-100 border-blue-200' : 'from-orange-50 to-orange-100 border-orange-200' }} rounded-xl p-6 border">
            <div class="text-sm font-medium {{ $netProfit >= 0 ? 'text-blue-600' : 'text-orange-600' }} mb-1">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            <div class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-blue-700' : 'text-orange-700' }}">₦{{ number_format(abs($netProfit), 2) }}</div>
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
@endsection
