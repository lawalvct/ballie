@extends('layouts.tenant')

@section('title', 'Balance Sheet')
@section('page-title', 'Balance Sheet')
@section('page-description', 'View your company\'s financial position as of a specific date')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Financial Reports Navigation -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
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
                <a href="{{ route('tenant.accounting.balance-sheet-pdf', ['tenant' => $tenant->slug, 'as_of_date' => $asOfDate ?? now()->toDateString()]) }}" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('tenant.accounting.balance-sheet-excel', ['tenant' => $tenant->slug, 'as_of_date' => $asOfDate ?? now()->toDateString()]) }}" class="inline-flex items-center px-4 py-2 border border-green-300 rounded-lg shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </a>
                <a href="{{ route('tenant.accounting.balance-sheet-table', ['tenant' => $tenant->slug, 'as_of_date' => $asOfDate ?? now()->toDateString()]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Table
                </a>
            </div>
        </div>

        <!-- Date Filter & Quick Presets -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" id="dateFilterForm">
                <!-- Quick Date Presets -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Presets</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setBalanceSheetDate('today')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Today</button>
                        <button type="button" onclick="setBalanceSheetDate('end_of_month')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">End of This Month</button>
                        <button type="button" onclick="setBalanceSheetDate('end_of_last_month')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">End of Last Month</button>
                        <button type="button" onclick="setBalanceSheetDate('end_of_quarter')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">End of This Quarter</button>
                        <button type="button" onclick="setBalanceSheetDate('end_of_last_quarter')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">End of Last Quarter</button>
                        <button type="button" onclick="setBalanceSheetDate('end_of_year')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">End of This Year</button>
                        <button type="button" onclick="setBalanceSheetDate('end_of_last_year')" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">End of Last Year</button>
                    </div>
                </div>

                <!-- Date Input & Compare -->
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="as_of_date" class="block text-sm font-medium text-gray-700 mb-1">As of Date</label>
                        <input type="date" name="as_of_date" id="as_of_date" value="{{ $asOfDate ?? now()->toDateString() }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="compare" value="1" {{ $compare ?? false ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Compare with Previous Period</span>
                        </label>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        @if($compare ?? false && isset($compareData))
        <!-- Comparison Banner -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Comparing with Previous Period</h4>
                    <p class="text-xs text-blue-600">As of {{ date('M d, Y', strtotime($compareData['asOfDate'])) }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Balance Sheet Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Assets & Liabilities -->
            <div class="space-y-6">
                <!-- Assets Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-20 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Assets</h2>
                                <p class="text-emerald-100 text-sm">Resources owned by the business</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if(count($assets) > 0)
                            <div class="space-y-3">
                                @foreach($assets as $account)
                                    <div class="flex items-center justify-between py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors border border-gray-100">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                                <span class="text-emerald-600 font-bold text-sm">{{ substr($account['account']->code, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $account['account']->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $account['account']->code }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-lg text-gray-900">₦{{ number_format($account['balance'], 2) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 pt-6 border-t-2 border-emerald-200">
                                <div class="flex items-center justify-between py-3 px-4 bg-emerald-50 rounded-lg">
                                    <div class="text-lg font-bold text-emerald-800">Total Assets</div>
                                    <div class="text-2xl font-bold text-emerald-600">₦{{ number_format($totalAssets, 2) }}</div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p>No asset accounts found</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Liabilities Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-20 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Liabilities</h2>
                                <p class="text-red-100 text-sm">Debts and obligations</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if(count($liabilities) > 0)
                            <div class="space-y-3">
                                @foreach($liabilities as $account)
                                    <div class="flex items-center justify-between py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors border border-gray-100">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                                <span class="text-red-600 font-bold text-sm">{{ substr($account['account']->code, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $account['account']->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $account['account']->code }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-lg text-gray-900">₦{{ number_format($account['balance'], 2) }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 pt-6 border-t-2 border-red-200">
                                <div class="flex items-center justify-between py-3 px-4 bg-red-50 rounded-lg">
                                    <div class="text-lg font-bold text-red-800">Total Liabilities</div>
                                    <div class="text-2xl font-bold text-red-600">₦{{ number_format($totalLiabilities, 2) }}</div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <p>No liability accounts found</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Equity & Summary -->
            <div class="space-y-6">
                <!-- Equity Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-20 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Owner's Equity</h2>
                                <p class="text-purple-100 text-sm">Owner's interest in the business</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($equity as $account)
                                <div class="flex items-center justify-between py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors border border-gray-100">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-sm">{{ substr($account['account']->code, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $account['account']->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $account['account']->code }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-lg text-gray-900">₦{{ number_format($account['balance'], 2) }}</div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Retained Earnings -->
                            @if(isset($retainedEarnings) && abs($retainedEarnings) >= 0.01)
                                <div class="flex items-center justify-between py-3 px-4 rounded-lg bg-yellow-50 border border-yellow-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <span class="text-yellow-600 font-bold text-sm">RE</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">Retained Earnings</div>
                                            <div class="text-sm text-gray-500">Accumulated profit/loss</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-lg {{ $retainedEarnings >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                            ₦{{ number_format($retainedEarnings, 2) }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 pt-6 border-t-2 border-purple-200">
                            <div class="flex items-center justify-between py-3 px-4 bg-purple-50 rounded-lg">
                                <div class="text-lg font-bold text-purple-800">Total Equity</div>
                                <div class="text-2xl font-bold text-purple-600">₦{{ number_format($totalEquity, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary & Balance Check -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-20 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Balance Verification</h2>
                                <p class="text-indigo-100 text-sm">Assets = Liabilities + Equity</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Assets -->
                            <div class="flex items-center justify-between py-3 px-4 bg-emerald-50 rounded-lg border border-emerald-200">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                    <span class="font-semibold text-emerald-800">Total Assets</span>
                                </div>
                                <span class="text-xl font-bold text-emerald-600">₦{{ number_format($totalAssets, 2) }}</span>
                            </div>

                            <!-- Liabilities + Equity -->
                            <div class="flex items-center justify-between py-3 px-4 bg-indigo-50 rounded-lg border border-indigo-200">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                                    <span class="font-semibold text-indigo-800">Liabilities + Equity</span>
                                </div>
                                <span class="text-xl font-bold text-indigo-600">₦{{ number_format($totalLiabilities + $totalEquity, 2) }}</span>
                            </div>

                            <!-- Balance Status -->
                            <div class="pt-4 border-t border-gray-200">
                                @php $difference = abs($totalAssets - ($totalLiabilities + $totalEquity)); @endphp
                                @if($difference < 0.01)
                                    <div class="flex items-center justify-center py-4 px-6 bg-emerald-50 rounded-xl border-2 border-emerald-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-10 h-10 bg-emerald-500 rounded-full">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-lg font-bold text-emerald-800">Balance Sheet is Ballie</div>
                                                <div class="text-sm text-emerald-600">All accounts are properly balanced</div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center justify-center py-4 px-6 bg-red-50 rounded-xl border-2 border-red-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-10 h-10 bg-red-500 rounded-full">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-lg font-bold text-red-800">Balance Sheet is Out of Balance</div>
                                                <div class="text-sm text-red-600">Difference: ₦{{ number_format($difference, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Ratios & Insights -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-20 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Financial Summary</h2>
                        <p class="text-gray-300 text-sm">Key financial metrics and ratios</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Total Assets -->
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-6 border border-emerald-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-emerald-800">Total Assets</p>
                                <p class="text-3xl font-bold text-emerald-600">₦{{ number_format($totalAssets / 1000, 0) }}K</p>
                            </div>
                            <div class="w-12 h-12 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-emerald-700 mt-2">₦{{ number_format($totalAssets, 2) }}</p>
                    </div>

                    <!-- Total Liabilities -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-red-800">Total Liabilities</p>
                                <p class="text-3xl font-bold text-red-600">₦{{ number_format($totalLiabilities / 1000, 0) }}K</p>
                            </div>
                            <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-red-700 mt-2">₦{{ number_format($totalLiabilities, 2) }}</p>
                    </div>

                    <!-- Total Equity -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-800">Owner's Equity</p>
                                <p class="text-3xl font-bold text-purple-600">₦{{ number_format($totalEquity / 1000, 0) }}K</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-purple-700 mt-2">₦{{ number_format($totalEquity, 2) }}</p>
                    </div>

                    <!-- Debt-to-Equity Ratio -->
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 border border-indigo-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-indigo-800">Debt-to-Equity</p>
                                <p class="text-3xl font-bold text-indigo-600">
                                    @if($totalEquity > 0)
                                        {{ number_format($totalLiabilities / $totalEquity, 2) }}
                                    @else
                                        ∞
                                    @endif
                                </p>
                            </div>
                            <div class="w-12 h-12 bg-indigo-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm text-indigo-700 mt-2">
                            {{ $totalLiabilities > $totalEquity ? 'High leverage' : 'Conservative' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Sheet Equation Visualization -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Balance Sheet Equation</h3>
                <p class="text-sm text-gray-600">The fundamental accounting equation in action</p>
            </div>
            <div class="p-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center space-x-6 text-center">
                        <!-- Assets -->
                        <div class="bg-emerald-50 rounded-xl p-6 border-2 border-emerald-200">
                            <div class="text-2xl font-bold text-emerald-600 mb-2">₦{{ number_format($totalAssets, 2) }}</div>
                            <div class="text-sm font-semibold text-emerald-800">ASSETS</div>
                        </div>

                        <!-- Equals -->
                        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full">
                            <span class="text-2xl font-bold text-gray-600">=</span>
                        </div>

                        <!-- Liabilities -->
                        <div class="bg-red-50 rounded-xl p-6 border-2 border-red-200">
                            <div class="text-2xl font-bold text-red-600 mb-2">₦{{ number_format($totalLiabilities, 2) }}</div>
                            <div class="text-sm font-semibold text-red-800">LIABILITIES</div>
                        </div>

                        <!-- Plus -->
                        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full">
                            <span class="text-2xl font-bold text-gray-600">+</span>
                        </div>

                        <!-- Equity -->
                        <div class="bg-purple-50 rounded-xl p-6 border-2 border-purple-200">
                            <div class="text-2xl font-bold text-purple-600 mb-2">₦{{ number_format($totalEquity, 2) }}</div>
                            <div class="text-sm font-semibold text-purple-800">EQUITY</div>
                        </div>
                    </div>
                </div>

                <!-- Balance Status Indicator -->
                <div class="mt-8 text-center">
                    @if(abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01)
                        <div class="inline-flex items-center px-6 py-3 bg-emerald-100 text-emerald-800 rounded-full font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ✓ Balance Sheet is Ballie
                        </div>
                    @else
                        <div class="inline-flex items-center px-6 py-3 bg-red-100 text-red-800 rounded-full font-semibold">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            ⚠ Out of Balance by ₦{{ number_format($difference, 2) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Additional Insights -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Liquidity Analysis -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Liquidity</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Current Assets</span>
                        <span class="font-semibold text-blue-600">₦{{ number_format($totalAssets, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Current Liabilities</span>
                        <span class="font-semibold text-red-600">₦{{ number_format($totalLiabilities, 2) }}</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between">
                        <span class="text-sm font-bold text-gray-900">Net Working Capital</span>
                        <span class="font-bold {{ ($totalAssets - $totalLiabilities) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            ₦{{ number_format($totalAssets - $totalLiabilities, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Solvency Analysis -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Solvency</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Debt Ratio</span>
                        <span class="font-semibold text-purple-600">
                            @if($totalAssets > 0)
                                {{ number_format(($totalLiabilities / $totalAssets) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Equity Ratio</span>
                        <span class="font-semibold text-emerald-600">
                            @if($totalAssets > 0)
                                {{ number_format(($totalEquity / $totalAssets) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between">
                        <span class="text-sm font-bold text-gray-900">Financial Health</span>
                        <span class="font-bold {{ ($totalLiabilities / ($totalAssets ?: 1)) < 0.5 ? 'text-emerald-600' : 'text-yellow-600' }}">
                            {{ ($totalLiabilities / ($totalAssets ?: 1)) < 0.3 ? 'Excellent' : (($totalLiabilities / ($totalAssets ?: 1)) < 0.5 ? 'Good' : 'Fair') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Growth Metrics -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Performance</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Book Value</span>
                        <span class="font-semibold text-indigo-600">₦{{ number_format($totalEquity, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Retained Earnings</span>
                        <span class="font-semibold {{ ($retainedEarnings ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            ₦{{ number_format($retainedEarnings ?? 0, 2) }}
                        </span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between">
                        <span class="text-sm font-bold text-gray-900">ROE Potential</span>
                        <span class="font-bold text-indigo-600">
                            {{ $totalEquity > 0 ? 'Measurable' : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- BallieAI Interpretation CTA -->
        <div class="mt-6 bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-5 no-print">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h4 class="text-lg font-semibold text-purple-800">BallieAI Interpretation</h4>
                    <p class="text-sm text-purple-600">Get an AI-powered explanation of your Balance Sheet performance.</p>
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

<!-- AI Interpretation Modal -->
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
                            <p class="text-sm text-purple-200">Powered by AI - Balance Sheet Interpretation</p>
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

<!-- Print Styles -->
<style>
@media print {
    body {
        background: white !important;
    }

    .min-h-screen {
        min-height: auto !important;
    }

    .bg-gray-50 {
        background: white !important;
    }

    .shadow-lg, .shadow-md {
        box-shadow: none !important;
        border: 1px solid #e5e7eb !important;
    }

    .rounded-2xl, .rounded-xl {
        border-radius: 8px !important;
    }

    .gradient-to-r, .bg-gradient-to-br {
        background: #374151 !important;
        color: white !important;
    }

    .print-hide {
        display: none !important;
    }

    /* Ensure proper page formatting */
    .invoice-container {
        page-break-inside: avoid;
    }

    .grid {
        display: block !important;
    }

    .grid > div {
        margin-bottom: 20px !important;
        break-inside: avoid;
    }
}
</style>

<script>
function setBalanceSheetDate(preset) {
    const today = new Date();
    let asOfDate;

    switch(preset) {
        case 'today':
            asOfDate = today;
            break;
        case 'end_of_month':
            asOfDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'end_of_last_month':
            asOfDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'end_of_quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            asOfDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
            break;
        case 'end_of_last_quarter':
            const lastQuarter = Math.floor(today.getMonth() / 3) - 1;
            const year = lastQuarter < 0 ? today.getFullYear() - 1 : today.getFullYear();
            const q = lastQuarter < 0 ? 3 : lastQuarter;
            asOfDate = new Date(year, q * 3 + 3, 0);
            break;
        case 'end_of_year':
            asOfDate = new Date(today.getFullYear(), 11, 31);
            break;
        case 'end_of_last_year':
            asOfDate = new Date(today.getFullYear() - 1, 11, 31);
            break;
    }

    document.getElementById('as_of_date').value = asOfDate.toISOString().split('T')[0];
    document.getElementById('dateFilterForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.bg-white');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

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

async function requestAIInterpretation() {
    // Show loading, hide others
    document.getElementById('aiLoadingState').classList.remove('hidden');
    document.getElementById('aiInterpretationContent').classList.add('hidden');
    document.getElementById('aiErrorState').classList.add('hidden');
    document.getElementById('copyBtn').classList.add('hidden');
    document.getElementById('downloadPdfBtn').classList.add('hidden');

    try {
        // Prepare balance sheet data
        const reportData = {
            asOfDate: '{{ $asOfDate ?? "N/A" }}',
            totalAssets: {{ $totalAssets ?? 0 }},
            totalLiabilities: {{ $totalLiabilities ?? 0 }},
            totalEquity: {{ $totalEquity ?? 0 }},
            assets: @json($assets ?? []),
            liabilities: @json($liabilities ?? []),
            equity: @json($equity ?? []),
            companyName: '{{ $tenant->business_name ?? "Your Business" }}',
            isBalanced: {{ abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01 ? 'true' : 'false' }},
            debtToEquityRatio: {{ $totalEquity > 0 ? ($totalLiabilities / $totalEquity) : 0 }},
            debtRatio: {{ $totalAssets > 0 ? (($totalLiabilities / $totalAssets) * 100) : 0 }},
            equityRatio: {{ $totalAssets > 0 ? (($totalEquity / $totalAssets) * 100) : 0 }},
            netWorkingCapital: {{ $totalAssets - $totalLiabilities }}
        };

        // Make API request
        const response = await fetch('/api/ai/interpret-balance-sheet', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reportData })
        });

        const data = await response.json();

        document.getElementById('aiLoadingState').classList.add('hidden');

        if (data.success || data.interpretation) {
            const interpretation = data.interpretation || data.message;
            displayInterpretation(interpretation);
        } else {
            showAIError(data.message || 'Unable to generate analysis.');
        }
    } catch (error) {
        console.error('AI Interpretation Error:', error);
        document.getElementById('aiLoadingState').classList.add('hidden');
        showAIError('Network error. Please check your connection and try again.');
    }
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
                    <p class="text-sm text-purple-600">As of: {{ $asOfDate ?? 'N/A' }}</p>
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

// Download Interpretation as PDF
function downloadInterpretationPdf() {
    if (!currentInterpretation) return;

    // Create a form and submit it to download PDF
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/api/ai/export-balance-sheet-interpretation-pdf';
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
    interpretationInput.value = currentInterpretation;
    form.appendChild(interpretationInput);

    // Add report data
    const reportDataInput = document.createElement('input');
    reportDataInput.type = 'hidden';
    reportDataInput.name = 'reportData';
    const reportData = {
        asOfDate: '{{ $asOfDate ?? "N/A" }}',
        totalAssets: {{ $totalAssets ?? 0 }},
        totalLiabilities: {{ $totalLiabilities ?? 0 }},
        totalEquity: {{ $totalEquity ?? 0 }},
        companyName: '{{ $tenant->business_name ?? "Your Business" }}'
    };
    reportDataInput.value = JSON.stringify(reportData);
    form.appendChild(reportDataInput);

    // Submit form
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAIInterpretation();
    }
});
</script>
@endsection
