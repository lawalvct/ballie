@extends('layouts.tenant')

@section('title', 'Statement of Changes in Equity')
@section('page-title', 'Statement of Changes in Equity')
@section('page-description', 'Track owner contributions, drawings, retained earnings, and profit movement for the selected period')

@php
    $formatMoney = fn ($amount) => ($amount < 0 ? '(' : '') . '₦' . number_format(abs($amount), 2) . ($amount < 0 ? ')' : '');
@endphp

@section('content')
<div class="space-y-6 equity-statement-container">
    <!-- Financial Reports Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 no-print">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.reports.profit-loss', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-emerald-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                Profit &amp; Loss
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
            <a href="{{ route('tenant.reports.statement-of-changes-in-equity', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-rose-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-all duration-200 transform hover:scale-105">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Equity
            </a>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            <a href="{{ route('tenant.reports.statement-of-changes-in-equity-pdf', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate]) }}"
               class="inline-flex items-center px-4 py-2 border border-rose-200 rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M4 6h16M4 6a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2"></path>
                </svg>
                Download PDF
            </a>
        </div>
    </div>

    <!-- Professional Header -->
    <div class="bg-white rounded-lg shadow-sm border-2 border-gray-200 p-8">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 uppercase tracking-wide">{{ $tenant->name }}</h1>
            <h2 class="text-xl font-semibold text-gray-700 mt-2">@term('equity_report')</h2>
            <p class="text-sm text-gray-600 mt-3">
                For the Period from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
            </p>
        </div>
        <div class="border-t-2 border-gray-300 pt-4 mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-gray-600">
                <span class="font-medium">Report Date:</span> {{ now()->format('F d, Y') }}
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-600 mb-1">Closing Equity</div>
                <div class="text-2xl font-bold {{ $totalClosingEquity >= 0 ? 'text-rose-600' : 'text-red-600' }}">
                    {{ $formatMoney($totalClosingEquity) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 no-print">
        <form method="GET" id="equityDateFilterForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quick Presets</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="setEquityDateRange('today')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Today</button>
                    <button type="button" onclick="setEquityDateRange('this_month')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">This Month</button>
                    <button type="button" onclick="setEquityDateRange('last_month')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Last Month</button>
                    <button type="button" onclick="setEquityDateRange('this_quarter')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">This Quarter</button>
                    <button type="button" onclick="setEquityDateRange('this_year')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">This Year</button>
                    <button type="button" onclick="setEquityDateRange('last_year')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Last Year</button>
                </div>
            </div>
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-rose-500 focus:border-rose-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 font-medium">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl p-6 border border-slate-200">
            <div class="text-xs font-medium text-slate-600 mb-1 sm:text-sm">Opening Equity</div>
            <div class="text-lg font-bold leading-tight tracking-tight text-slate-800 sm:text-xl xl:text-2xl break-all">{{ $formatMoney($totalOpeningEquity) }}</div>
            <p class="text-xs text-slate-500 mt-2">As of {{ \Carbon\Carbon::parse($openingDate)->format('M d, Y') }}</p>
        </div>
        <div class="bg-gradient-to-br {{ $totalDirectEquityMovement >= 0 ? 'from-emerald-50 to-emerald-100 border-emerald-200' : 'from-red-50 to-red-100 border-red-200' }} rounded-xl p-6 border">
            <div class="text-xs font-medium {{ $totalDirectEquityMovement >= 0 ? 'text-emerald-600' : 'text-red-600' }} mb-1 sm:text-sm">Direct Equity Movement</div>
            <div class="text-lg font-bold leading-tight tracking-tight {{ $totalDirectEquityMovement >= 0 ? 'text-emerald-700' : 'text-red-700' }} sm:text-xl xl:text-2xl break-all">{{ $formatMoney($totalDirectEquityMovement) }}</div>
            <p class="text-xs {{ $totalDirectEquityMovement >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-2">Capital, drawings, and other equity entries</p>
        </div>
        <div class="bg-gradient-to-br {{ $profitForPeriod >= 0 ? 'from-blue-50 to-blue-100 border-blue-200' : 'from-orange-50 to-orange-100 border-orange-200' }} rounded-xl p-6 border">
            <div class="text-xs font-medium {{ $profitForPeriod >= 0 ? 'text-blue-600' : 'text-orange-600' }} mb-1 sm:text-sm">Profit / Loss for Period</div>
            <div class="text-lg font-bold leading-tight tracking-tight {{ $profitForPeriod >= 0 ? 'text-blue-700' : 'text-orange-700' }} sm:text-xl xl:text-2xl break-all">{{ $formatMoney($profitForPeriod) }}</div>
            <p class="text-xs {{ $profitForPeriod >= 0 ? 'text-blue-600' : 'text-orange-600' }} mt-2">From income and expense accounts</p>
        </div>
        <div class="bg-gradient-to-br from-rose-50 to-rose-100 rounded-xl p-6 border border-rose-200">
            <div class="text-xs font-medium text-rose-600 mb-1 sm:text-sm">Closing Equity</div>
            <div class="text-lg font-bold leading-tight tracking-tight text-rose-700 sm:text-xl xl:text-2xl break-all">{{ $formatMoney($totalClosingEquity) }}</div>
            <p class="text-xs text-rose-600 mt-2">Net movement: {{ $formatMoney($totalEquityMovement) }}</p>
        </div>
    </div>

    <!-- Statement Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-rose-600 to-rose-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white">Statement Details</h3>
                    <p class="text-sm text-rose-100">Opening balances, equity changes, retained earnings, and closing balances</p>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Opening</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Additions</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Closing</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($equityMovements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $movement['account']->name }}</div>
                                <div class="text-xs text-gray-500">{{ $movement['account']->code }}{{ $movement['account']->accountGroup ? ' · ' . $movement['account']->accountGroup->name : '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-700">{{ $formatMoney($movement['opening_balance']) }}</td>
                            <td class="px-6 py-4 text-right font-medium text-emerald-600">{{ $movement['additions'] > 0 ? $formatMoney($movement['additions']) : '-' }}</td>
                            <td class="px-6 py-4 text-right font-medium text-red-600">{{ $movement['deductions'] > 0 ? $formatMoney($movement['deductions']) : '-' }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">{{ $formatMoney($movement['closing_balance']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                No direct equity account movement found for this period.
                            </td>
                        </tr>
                    @endforelse

                    <tr class="bg-blue-50">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-blue-900">Retained Earnings / Current Period Result</div>
                            <div class="text-xs text-blue-600">Opening retained earnings plus profit or loss for this period</div>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-blue-800">{{ $formatMoney($openingRetainedEarnings) }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-emerald-700">{{ $profitForPeriod > 0 ? $formatMoney($profitForPeriod) : '-' }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-red-700">{{ $profitForPeriod < 0 ? $formatMoney(abs($profitForPeriod)) : '-' }}</td>
                        <td class="px-6 py-4 text-right font-bold text-blue-900">{{ $formatMoney($closingRetainedEarnings) }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-900 text-white">
                    <tr>
                        <td class="px-6 py-4 font-bold">Total Equity</td>
                        <td class="px-6 py-4 text-right font-bold">{{ $formatMoney($totalOpeningEquity) }}</td>
                        <td class="px-6 py-4 text-right font-bold">{{ $formatMoney($totalEquityAdditions + max($profitForPeriod, 0)) }}</td>
                        <td class="px-6 py-4 text-right font-bold">{{ $formatMoney($totalEquityDeductions + abs(min($profitForPeriod, 0))) }}</td>
                        <td class="px-6 py-4 text-right font-bold">{{ $formatMoney($totalClosingEquity) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Reconciliation -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Equity Reconciliation</h3>
            <p class="text-sm text-gray-600">How opening equity becomes closing equity for the selected period</p>
        </div>
        <div class="p-6">
            <div class="max-w-3xl mx-auto space-y-3">
                <div class="flex items-center justify-between py-3 px-4 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-sm font-medium text-slate-700">Opening Equity</span>
                    <span class="font-bold text-slate-900">{{ $formatMoney($totalOpeningEquity) }}</span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 {{ $totalDirectEquityMovement >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }} rounded-lg border">
                    <span class="text-sm font-medium {{ $totalDirectEquityMovement >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Direct Equity Movement</span>
                    <span class="font-bold {{ $totalDirectEquityMovement >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ $formatMoney($totalDirectEquityMovement) }}</span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 {{ $profitForPeriod >= 0 ? 'bg-blue-50 border-blue-200' : 'bg-orange-50 border-orange-200' }} rounded-lg border">
                    <span class="text-sm font-medium {{ $profitForPeriod >= 0 ? 'text-blue-700' : 'text-orange-700' }}">Profit / Loss for Period</span>
                    <span class="font-bold {{ $profitForPeriod >= 0 ? 'text-blue-700' : 'text-orange-700' }}">{{ $formatMoney($profitForPeriod) }}</span>
                </div>
                <div class="flex items-center justify-between py-4 px-5 bg-rose-100 rounded-lg border-2 border-rose-300">
                    <span class="text-base font-bold text-rose-900">Closing Equity</span>
                    <span class="text-xl font-bold text-rose-900">{{ $formatMoney($totalClosingEquity) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page { size: A4 portrait; margin: 12mm; }
    .no-print, nav, header, footer, aside, .sidebar, .topbar { display: none !important; }
    body { background: #fff !important; color: #000 !important; }
    .equity-statement-container { margin: 0 !important; padding: 0 !important; }
    .shadow, .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl { box-shadow: none !important; }
    .rounded-lg, .rounded-xl, .rounded-2xl { border-radius: 0 !important; }
    .bg-gradient-to-br, .bg-gradient-to-r { background: #fff !important; }
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
    .grid { display: grid !important; }
    .text-white { color: #000 !important; }
    a { text-decoration: none !important; color: inherit !important; }
}
</style>

<script>
function setEquityDateRange(preset) {
    const today = new Date();
    let fromDate = new Date(today);
    let toDate = new Date(today);

    switch (preset) {
        case 'today':
            break;
        case 'this_month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            break;
        case 'last_month':
            fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            toDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'this_quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            fromDate = new Date(today.getFullYear(), quarter * 3, 1);
            break;
        case 'this_year':
            fromDate = new Date(today.getFullYear(), 0, 1);
            break;
        case 'last_year':
            fromDate = new Date(today.getFullYear() - 1, 0, 1);
            toDate = new Date(today.getFullYear() - 1, 11, 31);
            break;
    }

    document.getElementById('from_date').value = fromDate.toISOString().split('T')[0];
    document.getElementById('to_date').value = toDate.toISOString().split('T')[0];
    document.getElementById('equityDateFilterForm').submit();
}
</script>
@endsection
