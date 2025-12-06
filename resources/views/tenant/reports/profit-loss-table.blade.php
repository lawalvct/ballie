@extends('layouts.tenant')

@section('title', 'Profit & Loss Account (Tabular)')
@section('page-title', 'Profit & Loss Account')
@section('page-description', 'Tabular view of your profit and loss statement')

@section('content')
<div class="space-y-6">
    <!-- Header with Mode Toggle -->
    <div class="flex items-center justify-between no-print">
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
        <div class="flex space-x-2">
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
            <a href="{{ route('tenant.accounting.profit-loss', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Modern View
            </a>
        </div>
    </div>

    <!-- Date Range & Mode Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 no-print">
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
            <div class="flex-1 min-w-[200px]">
                <label for="mode" class="block text-sm font-medium text-gray-700 mb-1">Display Mode</label>
                <select name="mode" id="mode" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="detailed" {{ $mode === 'detailed' ? 'selected' : '' }}>Detailed</option>
                    <option value="condensed" {{ $mode === 'condensed' ? 'selected' : '' }}>Condensed</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium">
                Generate Report
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 no-print">
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

    <!-- Print Header (only visible when printing) -->
    <div class="print-only" style="display: none;">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Profit & Loss Account</h1>
            <p class="text-lg text-gray-700">
                Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}
            </p>
            <p class="text-sm text-gray-600 mt-1">{{ $mode === 'condensed' ? 'Condensed' : 'Detailed' }} View</p>
        </div>
    </div>

    <!-- Tabular P&L Statement -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden print-container">
        <div class="px-6 py-5 border-b border-gray-200 no-print">
            <h3 class="text-xl font-bold text-gray-900">Profit & Loss Account</h3>
            <p class="text-sm text-gray-600 mt-1">{{ $mode === 'condensed' ? 'Condensed' : 'Detailed' }} View</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Particulars</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount (₦)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- INCOME SECTION -->
                    <tr class="bg-emerald-50">
                        <td colspan="2" class="px-6 py-3 font-bold text-emerald-800 text-lg">INCOME</td>
                    </tr>

                    @forelse($incomeByGroup as $groupName => $groupData)
                        @if($mode === 'detailed')
                            <!-- Group Header -->
                            <tr class="bg-emerald-25">
                                <td colspan="2" class="px-6 py-2 font-semibold text-emerald-700">{{ $groupName }}</td>
                            </tr>
                            <!-- Individual Accounts -->
                            @foreach($groupData['accounts'] as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-2 pl-12">
                                    <a href="{{ route('tenant.accounting.ledger-accounts.show', ['tenant' => $tenant->slug, 'ledgerAccount' => $item['account']->id]) }}"
                                       class="text-gray-700 hover:text-emerald-600 flex items-center gap-2">
                                        <span>{{ $item['account']->name }}</span>
                                        <span class="text-xs text-gray-500">({{ $item['account']->code }})</span>
                                    </a>
                                </td>
                                <td class="px-6 py-2 text-right text-gray-900">{{ number_format($item['amount'], 2) }}</td>
                            </tr>
                            @endforeach
                            <!-- Group Total -->
                            <tr class="bg-emerald-50">
                                <td class="px-6 py-2 pl-12 font-semibold text-emerald-700">Total {{ $groupName }}</td>
                                <td class="px-6 py-2 text-right font-semibold text-emerald-700">{{ number_format($groupData['total'], 2) }}</td>
                            </tr>
                        @else
                            <!-- Condensed Mode: Show only group totals -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-2 pl-8 font-medium text-gray-800">{{ $groupName }}</td>
                                <td class="px-6 py-2 text-right font-medium text-gray-900">{{ number_format($groupData['total'], 2) }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-500 italic">No income recorded</td>
                        </tr>
                    @endforelse

                    <!-- Total Income -->
                    <tr class="bg-emerald-100 font-bold text-emerald-900">
                        <td class="px-6 py-3 text-lg">TOTAL INCOME</td>
                        <td class="px-6 py-3 text-right text-lg">{{ number_format($totalIncome, 2) }}</td>
                    </tr>

                    <!-- Spacer -->
                    <tr class="bg-gray-100">
                        <td colspan="2" class="py-2"></td>
                    </tr>

                    <!-- EXPENSES SECTION -->
                    <tr class="bg-red-50">
                        <td colspan="2" class="px-6 py-3 font-bold text-red-800 text-lg">EXPENSES</td>
                    </tr>

                    @forelse($expenseByGroup as $groupName => $groupData)
                        @if($mode === 'detailed')
                            <!-- Group Header -->
                            <tr class="bg-red-25">
                                <td colspan="2" class="px-6 py-2 font-semibold text-red-700">{{ $groupName }}</td>
                            </tr>
                            <!-- Individual Accounts -->
                            @foreach($groupData['accounts'] as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-2 pl-12">
                                    <a href="{{ route('tenant.accounting.ledger-accounts.show', ['tenant' => $tenant->slug, 'ledgerAccount' => $item['account']->id]) }}"
                                       class="text-gray-700 hover:text-red-600 flex items-center gap-2">
                                        <span>{{ $item['account']->name }}</span>
                                        <span class="text-xs text-gray-500">({{ $item['account']->code }})</span>
                                    </a>
                                </td>
                                <td class="px-6 py-2 text-right text-gray-900">{{ number_format($item['amount'], 2) }}</td>
                            </tr>
                            @endforeach
                            <!-- Group Total -->
                            <tr class="bg-red-50">
                                <td class="px-6 py-2 pl-12 font-semibold text-red-700">Total {{ $groupName }}</td>
                                <td class="px-6 py-2 text-right font-semibold text-red-700">{{ number_format($groupData['total'], 2) }}</td>
                            </tr>
                        @else
                            <!-- Condensed Mode: Show only group totals -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-2 pl-8 font-medium text-gray-800">{{ $groupName }}</td>
                                <td class="px-6 py-2 text-right font-medium text-gray-900">{{ number_format($groupData['total'], 2) }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-500 italic">No expenses recorded</td>
                        </tr>
                    @endforelse

                    <!-- Total Expenses -->
                    <tr class="bg-red-100 font-bold text-red-900">
                        <td class="px-6 py-3 text-lg">TOTAL EXPENSES</td>
                        <td class="px-6 py-3 text-right text-lg">{{ number_format($totalExpenses, 2) }}</td>
                    </tr>

                    <!-- Spacer -->
                    <tr class="bg-gray-100">
                        <td colspan="2" class="py-2"></td>
                    </tr>

                    <!-- NET PROFIT/LOSS -->
                    <tr class="bg-{{ $netProfit >= 0 ? 'blue' : 'orange' }}-100 font-bold text-{{ $netProfit >= 0 ? 'blue' : 'orange' }}-900">
                        <td class="px-6 py-4 text-xl">NET {{ $netProfit >= 0 ? 'PROFIT' : 'LOSS' }}</td>
                        <td class="px-6 py-4 text-right text-xl">{{ number_format(abs($netProfit), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print/Export Actions -->
    <div class="flex justify-end space-x-3 no-print">
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print
        </button>
    </div>
</div>

@push('styles')
<style>
    @media print {
        /* Hide all non-essential elements */
        .no-print,
        nav,
        header,
        footer,
        .sidebar,
        button,
        a {
            display: none !important;
        }

        /* Show print-only elements */
        .print-only {
            display: block !important;
        }

        /* Reset body and container styles for print */
        body {
            background: white !important;
            margin: 0;
            padding: 20px;
        }

        .print-container {
            box-shadow: none !important;
            border: 1px solid #000 !important;
            border-radius: 0 !important;
        }

        /* Preserve table colors */
        .bg-emerald-50,
        .bg-emerald-100,
        .bg-red-50,
        .bg-red-100,
        .bg-blue-100,
        .bg-orange-100 {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Adjust table for print */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
        }

        /* Page break settings */
        tr {
            page-break-inside: avoid;
        }
    }
</style>
@endpush
@endsection
