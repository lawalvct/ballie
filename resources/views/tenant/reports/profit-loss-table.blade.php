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

    <!-- BallieAI Interpretation CTA -->
    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-5 no-print">
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
// Store current interpretation for PDF export
let currentInterpretation = '';

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

    // Collect report data - flatten the grouped data
    const incomeAccounts = [];
    @foreach($incomeByGroup as $groupName => $groupData)
        @foreach($groupData['accounts'] as $item)
        incomeAccounts.push({ name: '{{ addslashes($item['account']->name) }}', amount: {{ $item['amount'] }} });
        @endforeach
    @endforeach

    const expenseAccounts = [];
    @foreach($expenseByGroup as $groupName => $groupData)
        @foreach($groupData['accounts'] as $item)
        expenseAccounts.push({ name: '{{ addslashes($item['account']->name) }}', amount: {{ $item['amount'] }} });
        @endforeach
    @endforeach

    const reportData = {
        fromDate: '{{ $fromDate }}',
        toDate: '{{ $toDate }}',
        totalIncome: {{ $totalIncome }},
        totalExpenses: {{ $totalExpenses }},
        netProfit: {{ $netProfit }},
        openingStock: 0,
        closingStock: 0,
        incomeAccounts: incomeAccounts,
        expenseAccounts: expenseAccounts,
        compareData: null
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
                    <p class="text-sm text-purple-600">Period: {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</p>
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
