@extends('layouts.tenant')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Modern Header -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Balance Sheet</h1>
                        <p class="text-lg text-gray-600">
                            {{ $tenant->name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            As of {{ \Carbon\Carbon::parse($asOfDate ?? now())->format('F d, Y') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Date Filter -->
                    <form method="GET" class="flex items-center space-x-2">
                        <input type="date"
                               name="as_of_date"
                               value="{{ $asOfDate ?? now()->toDateString() }}"
                               class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Update
                        </button>
                    </form>

                    <button onclick="window.print()"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>

                    <a href="{{ route('tenant.accounting.index', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back
                    </a>
                    <a href="{{ route('balance-sheet-table', ['tenant' => $tenant->slug, 'as_of_date' => $asOfDate ?? now()->toDateString()]) }}"
                       class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg shadow-sm text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Standard Table View
                    </a>
                </div>
            </div>
        </div>

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
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth animations
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
</script>
@endsection
