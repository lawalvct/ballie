@extends('layouts.tenant')

@section('title', 'Cash Flow Statement - ' . $tenant->name)
@section('page-title', 'Cash Flow Statement')
@section('page-description', 'Analysis of cash inflows and outflows from operating, investing, and financing activities')

@section('content')
<div class="space-y-6 cash-flow-container">
    <!-- Navigation Buttons -->
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

    <!-- Professional Header -->
    <div class="bg-white rounded-lg shadow-sm border-2 border-gray-200 p-8">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 uppercase tracking-wide">{{ $tenant->name }}</h1>
            <h2 class="text-xl font-semibold text-gray-700 mt-2">Statement of Cash Flows</h2>
            <p class="text-sm text-gray-600 mt-3">
                For the Period from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
            </p>
        </div>

        <div class="border-t-2 border-gray-300 pt-4 mt-4">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <span class="font-medium">Report Date:</span> {{ now()->format('F d, Y') }}
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600 mb-1">Net Change in Cash and Cash Equivalents</div>
                    <div class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ₦{{ number_format(abs($netCashFlow), 2) }} {{ $netCashFlow >= 0 ? 'Increase' : 'Decrease' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 min-w-0 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Operating Activities</h3>
                    <p class="text-lg font-bold {{ $operatingTotal >= 0 ? 'text-green-600' : 'text-red-600' }} truncate">
                        {{ number_format($operatingTotal, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 min-w-0 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Investing Activities</h3>
                    <p class="text-lg font-bold {{ $investingTotal >= 0 ? 'text-green-600' : 'text-red-600' }} truncate">
                        {{ number_format($investingTotal, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6M9 10.5h6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4 min-w-0 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Financing Activities</h3>
                    <p class="text-lg font-bold {{ $financingTotal >= 0 ? 'text-green-600' : 'text-red-600' }} truncate">
                        {{ number_format($financingTotal, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 {{ $netCashFlow >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-lg flex items-center justify-center">
                        @if($netCashFlow >= 0)
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        @endif
                    </div>
                </div>
                <div class="ml-4 min-w-0 flex-1">
                    <h3 class="text-sm font-medium text-gray-500">Cash Position</h3>
                    <p class="text-lg font-bold {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }} truncate">
                        {{ number_format($closingCash, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Summary View (Hidden by default) -->
    <div id="simple-view" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8" style="display: none;">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Cash Flow Summary</h2>
            <p class="text-gray-600 mt-2">{{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</p>
        </div>

        <div class="max-w-2xl mx-auto space-y-4">
            <div class="flex justify-between items-center py-4 px-6 bg-gray-50 rounded-lg">
                <span class="text-lg font-medium text-gray-700">Opening Cash Balance</span>
                <span class="text-xl font-bold text-gray-900">₦{{ number_format($openingCash, 2) }}</span>
            </div>

            <div class="flex justify-between items-center py-4 px-6 bg-green-50 rounded-lg">
                <span class="text-lg font-medium text-gray-700">Operating Activities</span>
                <span class="text-xl font-bold {{ $operatingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">₦{{ number_format($operatingTotal, 2) }}</span>
            </div>

            <div class="flex justify-between items-center py-4 px-6 bg-blue-50 rounded-lg">
                <span class="text-lg font-medium text-gray-700">Investing Activities</span>
                <span class="text-xl font-bold {{ $investingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">₦{{ number_format($investingTotal, 2) }}</span>
            </div>

            <div class="flex justify-between items-center py-4 px-6 bg-purple-50 rounded-lg">
                <span class="text-lg font-medium text-gray-700">Financing Activities</span>
                <span class="text-xl font-bold {{ $financingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">₦{{ number_format($financingTotal, 2) }}</span>
            </div>

            <div class="flex justify-between items-center py-4 px-6 {{ $netCashFlow >= 0 ? 'bg-green-100 border-2 border-green-300' : 'bg-red-100 border-2 border-red-300' }} rounded-lg">
                <span class="text-lg font-bold text-gray-900">Net Cash Change</span>
                <span class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-green-700' : 'text-red-700' }}">₦{{ number_format($netCashFlow, 2) }}</span>
            </div>

            <div class="flex justify-between items-center py-4 px-6 bg-gray-100 rounded-lg border-2 border-gray-300">
                <span class="text-lg font-bold text-gray-900">Closing Cash Balance</span>
                <span class="text-2xl font-bold text-gray-900">₦{{ number_format($closingCash, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Detailed View (Visible by default) -->
    <div id="detailed-view">
    <!-- Small chart that visualizes the period cash flow contributions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-lg font-semibold">Cash Flow Visualization</h4>
            <div class="text-sm text-gray-500">A quick visual summary of operating, investing and financing</div>
        </div>
        <div class="w-full">
            <canvas id="cashFlowChart" class="w-full h-48"></canvas>
        </div>
    </div>

    <!-- Enhanced Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 no-print">
        <div class="space-y-4">
            <!-- First Row: Header and Date Range Controls -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div class="flex items-center space-x-4">
                    <h3 class="text-lg font-medium text-gray-900">Report Controls</h3>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($fromDate)->diffInDays(\Carbon\Carbon::parse($toDate)) + 1 }} days
                        </span>
                    </div>
                </div>

                <!-- Date Range Form -->
                <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3">
                        <div>
                            <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                            <input type="date"
                                   name="from_date"
                                   id="from_date"
                                   value="{{ $fromDate }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                        </div>
                        <div>
                            <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                            <input type="date"
                                   name="to_date"
                                   id="to_date"
                                   value="{{ $toDate }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Second Row: Quick Date Presets and Action Buttons -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <!-- Quick Date Presets -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-600 mr-2">Quick Filters:</span>
                    <button onclick="setDateRange('this_month', this)"
                            class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md border border-gray-200">
                        This Month
                    </button>
                    <button onclick="setDateRange('last_month', this)"
                            class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md border border-gray-200">
                        Last Month
                    </button>
                    <button onclick="setDateRange('this_quarter', this)"
                            class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md border border-gray-200">
                        This Quarter
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="printCashFlow()"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    <button onclick="exportToCSV(this)"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                    <a href="{{ route('tenant.reports.cash-flow', ['tenant' => $tenant->slug, 'from_date' => $fromDate, 'to_date' => $toDate, 'download' => 'pdf']) }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                    <button onclick="toggleSimpleView()"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span id="simple-view-text">Simple View</span>
                    </button>
                    <a href="{{ route('tenant.reports.index', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Cash Flow Statement -->
    <div class="bg-white shadow-sm rounded-lg border-2 border-gray-300 overflow-hidden">
        <div class="px-8 py-6 border-b-2 border-gray-300 bg-gray-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 uppercase tracking-wide">
                        Detailed Cash Flow Analysis
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 font-medium">
                        All amounts in Nigerian Naira (₦)
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="toggleAllSections()" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 border border-gray-300 rounded-md transition-colors">
                        <svg id="expand-all-icon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                        </svg>
                        <span id="expand-all-text">Collapse All</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            <!-- Enhanced Operating Activities -->
            <div id="operating-section" class="transition-all duration-300">
                <div class="px-8 py-6 bg-white border-b-2 border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <button onclick="toggleSection('operating')" class="flex items-center text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors group section-header-button uppercase tracking-wide">
                            <span>Cash Flows from Operating Activities</span>
                            <svg id="operating-chevron" class="w-6 h-6 ml-3 text-gray-400 transform transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="text-sm text-gray-500 font-medium">
                            {{ count($operatingActivities) }} transaction(s)
                        </div>
                    </div>
                </div>

                <div id="operating-content" class="px-8 py-6 transition-all duration-300 overflow-hidden bg-gray-50">

                @if(count($operatingActivities) > 0)
                    <div class="space-y-3">
                        @foreach($operatingActivities as $index => $activity)
                            <div class="flex justify-between items-center py-3 px-4 rounded-lg border border-gray-100 activity-row cursor-pointer">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-medium
                                            @if($activity['type'] == 'income') bg-green-100 text-green-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3
                                                @if($activity['type'] == 'income') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($activity['type']) }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $activity['type'] == 'income' ? 'Cash inflow from revenue' : 'Cash outflow for expenses' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-mono font-semibold {{ $activity['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $activity['amount'] >= 0 ? '+' : '' }}{{ number_format($activity['amount'], 2) }}
                                    </span>
                                    <div class="text-xs text-gray-500">
                                        {{ number_format(abs($activity['amount']) / max(abs($operatingTotal), 1) * 100, 1) }}%
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="border-t-4 border-gray-900 pt-4 mt-6">
                            <div class="flex justify-between items-center py-4 px-6 bg-white border-2 border-gray-300 rounded-lg">
                                <span class="text-lg font-bold text-gray-900 uppercase">Net Cash Provided by (Used in) Operating Activities</span>
                                <span class="text-2xl font-mono font-bold {{ $operatingTotal >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                    {{ $operatingTotal >= 0 ? '' : '(' }}₦{{ number_format(abs($operatingTotal), 2) }}{{ $operatingTotal >= 0 ? '' : ')' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Operating Activities</h3>
                        <p class="mt-1 text-sm text-gray-500">No operating activities found for this period</p>
                    </div>
                @endif
                </div>
            </div>

            <!-- Enhanced Investing Activities -->
            <div id="investing-section" class="transition-all duration-300">
                <div class="px-8 py-6 bg-white border-b-2 border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <button onclick="toggleSection('investing')" class="flex items-center text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors group section-header-button uppercase tracking-wide">
                            <span>Cash Flows from Investing Activities</span>
                            <svg id="investing-chevron" class="w-6 h-6 ml-3 text-gray-400 transform transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="text-sm text-gray-500 font-medium">
                            {{ count($investingActivities) }} transaction(s)
                        </div>
                    </div>
                </div>

                <div id="investing-content" class="px-8 py-6 transition-all duration-300 overflow-hidden bg-gray-50">

                @if(count($investingActivities) > 0)
                    <div class="space-y-3">
                        @foreach($investingActivities as $index => $activity)
                            <div class="flex justify-between items-center py-3 px-4 rounded-lg border border-gray-100 activity-row cursor-pointer">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3 bg-blue-100 text-blue-800">
                                                Investing
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Capital expenditure or asset investment
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-mono font-semibold {{ $activity['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $activity['amount'] >= 0 ? '+' : '' }}{{ number_format($activity['amount'], 2) }}
                                    </span>
                                    <div class="text-xs text-gray-500">
                                        {{ number_format(abs($activity['amount']) / max(abs($investingTotal), 1) * 100, 1) }}%
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="border-t-4 border-gray-900 pt-4 mt-6">
                            <div class="flex justify-between items-center py-4 px-6 bg-white border-2 border-gray-300 rounded-lg">
                                <span class="text-lg font-bold text-gray-900 uppercase">Net Cash Provided by (Used in) Investing Activities</span>
                                <span class="text-2xl font-mono font-bold {{ $investingTotal >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                    {{ $investingTotal >= 0 ? '' : '(' }}₦{{ number_format(abs($investingTotal), 2) }}{{ $investingTotal >= 0 ? '' : ')' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Investing Activities</h3>
                        <p class="mt-1 text-sm text-gray-500">No investing activities found for this period</p>
                    </div>
                @endif
                </div>
            </div>

            <!-- Enhanced Financing Activities -->
            <div id="financing-section" class="transition-all duration-300">
                <div class="px-8 py-6 bg-white border-b-2 border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <button onclick="toggleSection('financing')" class="flex items-center text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors group section-header-button uppercase tracking-wide">
                            <span>Cash Flows from Financing Activities</span>
                            <svg id="financing-chevron" class="w-6 h-6 ml-3 text-gray-400 transform transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="text-sm text-gray-500 font-medium">
                            {{ count($financingActivities) }} transaction(s)
                        </div>
                    </div>
                </div>

                <div id="financing-content" class="px-8 py-6 transition-all duration-300 overflow-hidden bg-gray-50">

                @if(count($financingActivities) > 0)
                    <div class="space-y-3">
                        @foreach($financingActivities as $index => $activity)
                            <div class="flex justify-between items-center py-3 px-4 rounded-lg border border-gray-100 activity-row cursor-pointer">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-4">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-medium
                                            @if($activity['type'] == 'equity') bg-purple-100 text-purple-700
                                            @else bg-orange-100 text-orange-700
                                            @endif">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3
                                                @if($activity['type'] == 'equity') bg-purple-100 text-purple-800
                                                @else bg-orange-100 text-orange-800
                                                @endif">
                                                {{ ucfirst($activity['type']) }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $activity['type'] == 'equity' ? 'Capital investment or distribution' : 'Borrowing or debt payment' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-mono font-semibold {{ $activity['amount'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $activity['amount'] >= 0 ? '+' : '' }}{{ number_format($activity['amount'], 2) }}
                                    </span>
                                    <div class="text-xs text-gray-500">
                                        {{ number_format(abs($activity['amount']) / max(abs($financingTotal), 1) * 100, 1) }}%
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="border-t-4 border-gray-900 pt-4 mt-6 mb-5">
                            <div class="flex justify-between items-center py-4 px-6 bg-white border-2 border-gray-300 rounded-lg">
                                <span class="text-lg font-bold text-gray-900 uppercase">Net Cash Provided by (Used in) Financing Activities</span>
                                <span class="text-2xl font-mono font-bold {{ $financingTotal >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                    {{ $financingTotal >= 0 ? '' : '(' }}₦{{ number_format(abs($financingTotal), 2) }}{{ $financingTotal >= 0 ? '' : ')' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6M9 10.5h6" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Financing Activities</h3>
                        <p class="mt-1 text-sm text-gray-500">No financing activities found for this period</p>
                    </div>
                @endif
                </div>
            </div>
        </div>

            <!-- Professional Net Cash Flow Summary -->
            <div class="border-t-4 border-gray-900 mt-8">
                <div class="bg-white px-8 py-6 space-y-6">
                    <h4 class="text-xl font-bold text-gray-900 uppercase tracking-wide border-b-2 border-gray-300 pb-3">
                        Reconciliation of Cash and Cash Equivalents
                    </h4>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-3 px-6 bg-gray-50 border-l-4 border-blue-500">
                            <span class="text-base font-semibold text-gray-900">Net Cash from Operating Activities</span>
                            <span class="text-lg font-mono font-bold {{ $operatingTotal >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ $operatingTotal >= 0 ? '' : '(' }}₦{{ number_format(abs($operatingTotal), 2) }}{{ $operatingTotal >= 0 ? '' : ')' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-3 px-6 bg-gray-50 border-l-4 border-purple-500">
                            <span class="text-base font-semibold text-gray-900">Net Cash from Investing Activities</span>
                            <span class="text-lg font-mono font-bold {{ $investingTotal >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ $investingTotal >= 0 ? '' : '(' }}₦{{ number_format(abs($investingTotal), 2) }}{{ $investingTotal >= 0 ? '' : ')' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-3 px-6 bg-gray-50 border-l-4 border-orange-500">
                            <span class="text-base font-semibold text-gray-900">Net Cash from Financing Activities</span>
                            <span class="text-lg font-mono font-bold {{ $financingTotal >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ $financingTotal >= 0 ? '' : '(' }}₦{{ number_format(abs($financingTotal), 2) }}{{ $financingTotal >= 0 ? '' : ')' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t-4 border-gray-900 pt-4 mt-4">
                        <div class="flex justify-between items-center py-5 px-6 bg-gray-100 border-2 border-gray-400 rounded-lg">
                            <span class="text-xl font-bold text-gray-900 uppercase">Net Increase (Decrease) in Cash and Cash Equivalents</span>
                            <span class="text-3xl font-mono font-bold {{ $netCashFlow >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ $netCashFlow >= 0 ? '' : '(' }}₦{{ number_format(abs($netCashFlow), 2) }}{{ $netCashFlow >= 0 ? '' : ')' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Cash Position Statement -->
            <div class="border-t-2 border-gray-300 mt-6">
                <div class="bg-white px-8 py-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-4 px-6 bg-gray-50 border-2 border-gray-300 rounded-lg">
                            <span class="text-lg font-bold text-gray-900">Cash and Cash Equivalents at Beginning of Period</span>
                            <span class="text-2xl font-mono font-bold text-gray-900">₦{{ number_format($openingCash, 2) }}</span>
                        </div>

                        <div class="flex justify-between items-center py-4 px-6 bg-blue-50 border-2 border-blue-300 rounded-lg">
                            <span class="text-lg font-bold text-gray-900">Net Increase (Decrease) in Cash and Cash Equivalents</span>
                            <span class="text-2xl font-mono font-bold {{ $netCashFlow >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ $netCashFlow >= 0 ? '' : '(' }}₦{{ number_format(abs($netCashFlow), 2) }}{{ $netCashFlow >= 0 ? '' : ')' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-5 px-6 bg-green-50 border-4 border-green-600 rounded-lg shadow-md">
                            <span class="text-xl font-bold text-gray-900 uppercase">Cash and Cash Equivalents at End of Period</span>
                            <span class="text-3xl font-mono font-bold text-green-700">₦{{ number_format($closingCash, 2) }}</span>
                        </div>
                    </div>

                    <!-- Professional Note -->
                    <div class="mt-6 p-4 bg-gray-50 border-l-4 border-blue-600 rounded">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold">Note:</span> This statement has been prepared using the indirect method and shows the cash flows from operating, investing, and financing activities for the period from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Professional Cash Accounts Detail -->
            @if(count($cashAccounts) > 0)
                <div class="border-t-2 border-gray-300 mt-6">
                    <div class="bg-white px-8 py-6">
                        <h4 class="text-lg font-bold text-gray-900 uppercase tracking-wide mb-6 border-b-2 border-gray-300 pb-3">
                            Supplemental Disclosure - Cash and Cash Equivalents
                        </h4>
                        <div class="space-y-3">
                            @foreach($cashAccounts as $account)
                                <div class="flex justify-between items-center py-3 px-6 bg-gray-50 border-l-4 border-blue-500 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div>
                                        <span class="text-base font-semibold text-gray-900">{{ $account->name }}</span>
                                        <span class="text-sm text-gray-600 ml-2">(Account #{{ $account->code }})</span>
                                    </div>
                                    <span class="text-lg font-mono font-bold text-gray-900">
                                        ₦{{ number_format($account->current_balance ?? 0, 2) }}
                                    </span>
                                </div>
                            @endforeach

                            <div class="border-t-2 border-gray-400 pt-3 mt-4">
                                <div class="flex justify-between items-center py-3 px-6 bg-blue-50 border-2 border-blue-400 rounded-lg">
                                    <span class="text-lg font-bold text-gray-900 uppercase">Total Cash and Cash Equivalents</span>
                                    <span class="text-2xl font-mono font-bold text-blue-700">
                                        ₦{{ number_format($cashAccounts->sum('current_balance'), 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div><!-- End detailed-view -->
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .bg-gray-50 {
            background-color: #f9f9f9 !important;
        }
    }

    /* Enhanced section collapse/expand animations */
    .section-content {
        transition: max-height 0.3s ease-in-out, padding 0.3s ease-in-out;
        overflow: hidden;
    }

    .section-header-button:hover .section-icon {
        transform: scale(1.05);
    }

    .chevron-icon {
        transition: transform 0.2s ease-in-out;
    }

    .activity-row {
        transition: transform 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    }

    .activity-row:hover {
        transform: translateX(4px);
        background-color: #f9fafb;
        border-color: #d1d5db;
    }

    /* Smooth animations for summary cards */
    @keyframes slideInFromTop {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .summary-card {
        animation: slideInFromTop 0.5s ease-out;
    }

    /* Loading state animations */
    .loading-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@push('scripts')
@php
    $netCashColor = $netCashFlow >= 0 ? 'rgba(16,185,129,0.85)' : 'rgba(239,68,68,0.85)';
    $chartValues = [$operatingTotal, $investingTotal, $financingTotal, $netCashFlow];
    $chartColors = [
        'rgba(34,197,94,0.85)',
        'rgba(59,130,246,0.85)',
        'rgba(139,92,246,0.85)',
        $netCashColor
    ];
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Enhanced interactivity for cash flow statement - Section collapse/expand
let sectionsExpanded = {
    operating: true,
    investing: true,
    financing: true
};

let isSimpleView = false;

function toggleSimpleView() {
    const simpleView = document.getElementById('simple-view');
    const detailedView = document.getElementById('detailed-view');
    const buttonText = document.getElementById('simple-view-text');

    isSimpleView = !isSimpleView;

    if (isSimpleView) {
        simpleView.style.display = 'block';
        detailedView.style.display = 'none';
        buttonText.textContent = 'Detailed View';
        showNotification('Switched to simple view', 'info');
    } else {
        simpleView.style.display = 'none';
        detailedView.style.display = 'block';
        buttonText.textContent = 'Simple View';
        showNotification('Switched to detailed view', 'info');
    }
}

function toggleSection(sectionName) {
    const content = document.getElementById(sectionName + '-content');
    const chevron = document.getElementById(sectionName + '-chevron');

    if (!content || !chevron) return;

    const isExpanded = sectionsExpanded[sectionName];

    if (isExpanded) {
        // Collapse
        content.style.maxHeight = '0px';
        content.style.paddingTop = '0px';
        content.style.paddingBottom = '0px';
        chevron.style.transform = 'rotate(-90deg)';
        sectionsExpanded[sectionName] = false;
    } else {
        // Expand
        content.style.maxHeight = content.scrollHeight + 'px';
        content.style.paddingTop = '1.5rem';
        content.style.paddingBottom = '1.5rem';
        chevron.style.transform = 'rotate(0deg)';
        sectionsExpanded[sectionName] = true;
    }

    updateExpandAllButton();
}

function toggleAllSections() {
    const allExpanded = Object.values(sectionsExpanded).every(expanded => expanded);
    const newState = !allExpanded;

    ['operating', 'investing', 'financing'].forEach(sectionName => {
        if (sectionsExpanded[sectionName] !== newState) {
            toggleSection(sectionName);
        }
    });
}

function updateExpandAllButton() {
    const allExpanded = Object.values(sectionsExpanded).every(expanded => expanded);
    const expandAllIcon = document.getElementById('expand-all-icon');
    const expandAllText = document.getElementById('expand-all-text');

    if (expandAllIcon && expandAllText) {
        if (allExpanded) {
            expandAllIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m0 0l7-7m7 7H3"></path>';
            expandAllText.textContent = 'Collapse All';
        } else {
            expandAllIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>';
            expandAllText.textContent = 'Expand All';
        }
    }
}

// Enhanced CSV export with better formatting
function exportToCSV(el) {
    let csvContent = "data:text/csv;charset=utf-8,";

    // Add header with better formatting
    csvContent += "CASH FLOW STATEMENT\n";
    csvContent += "Company: {{ $tenant->name }}\n";
    csvContent += "Period: {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}\n";
    csvContent += "Generated: " + new Date().toLocaleDateString() + "\n\n";

    // Operating Activities
    csvContent += "CASH FLOWS FROM OPERATING ACTIVITIES\n";
    csvContent += "Description,Type,Amount\n";
    @foreach($operatingActivities as $activity)
        csvContent += "\"{{ addslashes($activity['description']) }}\",{{ ucfirst($activity['type']) }},{{ number_format($activity['amount'], 2) }}\n";
    @endforeach
    csvContent += ",,\n";
    csvContent += "Net Cash Flow from Operating Activities,,{{ number_format($operatingTotal, 2) }}\n\n";

    // Investing Activities
    csvContent += "CASH FLOWS FROM INVESTING ACTIVITIES\n";
    csvContent += "Description,Type,Amount\n";
    @foreach($investingActivities as $activity)
        csvContent += "\"{{ addslashes($activity['description']) }}\",Investing,{{ number_format($activity['amount'], 2) }}\n";
    @endforeach
    csvContent += ",,\n";
    csvContent += "Net Cash Flow from Investing Activities,,{{ number_format($investingTotal, 2) }}\n\n";

    // Financing Activities
    csvContent += "CASH FLOWS FROM FINANCING ACTIVITIES\n";
    csvContent += "Description,Type,Amount\n";
    @foreach($financingActivities as $activity)
        csvContent += "\"{{ addslashes($activity['description']) }}\",{{ ucfirst($activity['type']) }},{{ number_format($activity['amount'], 2) }}\n";
    @endforeach
    csvContent += ",,\n";
    csvContent += "Net Cash Flow from Financing Activities,,{{ number_format($financingTotal, 2) }}\n\n";

    // Summary
    csvContent += "CASH FLOW SUMMARY\n";
    csvContent += "Item,Amount\n";
    csvContent += "Cash at Beginning of Period,{{ number_format($openingCash, 2) }}\n";
    csvContent += "Net Cash Flow from Operating Activities,{{ number_format($operatingTotal, 2) }}\n";
    csvContent += "Net Cash Flow from Investing Activities,{{ number_format($investingTotal, 2) }}\n";
    csvContent += "Net Cash Flow from Financing Activities,{{ number_format($financingTotal, 2) }}\n";
    csvContent += "Net Increase (Decrease) in Cash,{{ number_format($netCashFlow, 2) }}\n";
    csvContent += "Cash at End of Period,{{ number_format($closingCash, 2) }}\n";

    // Create and trigger download
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "cash_flow_statement_{{ $fromDate }}_to_{{ $toDate }}.csv");
    document.body.appendChild(link);

    // Add visual feedback
    const button = el || (typeof event !== 'undefined' ? event.target : null);
    let originalText = null;
    if (button instanceof Element) {
        originalText = button.innerHTML;
        button.innerHTML = '<svg class="animate-spin h-4 w-4 inline mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Exporting...';
        button.disabled = true;
    }

    setTimeout(() => {
        link.click();
        document.body.removeChild(link);
        if (originalText !== null && button instanceof Element) {
            button.innerHTML = originalText;
            button.disabled = false;
        }

        // Show success notification
        showNotification('CSV exported successfully!', 'success');
    }, 500);
}

// Enhanced date validation with better UX
function validateDateRange() {
    const fromDate = new Date(document.getElementById('from_date').value);
    const toDate = new Date(document.getElementById('to_date').value);
    const today = new Date();

    let isValid = true;
    let message = '';

    if (fromDate > today) {
        message = 'From date cannot be in the future';
        isValid = false;
    } else if (toDate > today) {
        message = 'To date cannot be in the future';
        isValid = false;
    } else if (fromDate > toDate) {
        message = 'From date cannot be later than To date';
        isValid = false;
    }

    if (!isValid) {
        showNotification(message, 'error');
        return false;
    }

    return true;
}

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };

    notification.className += ` ${colors[type] || colors.info}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                  type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                  type === 'warning' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>' :
                  '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'}
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// Quick date presets with animation
function setQuickDate(preset, el) {
    const today = new Date();
    let fromDate, toDate;

    switch(preset) {
        case 'this-month':
        case 'this_month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'last-month':
        case 'last_month':
            fromDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            toDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        case 'this-quarter':
        case 'this_quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            fromDate = new Date(today.getFullYear(), quarter * 3, 1);
            toDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
            break;
        case 'this-year':
        case 'this_year':
            fromDate = new Date(today.getFullYear(), 0, 1);
            toDate = new Date(today.getFullYear(), 11, 31);
            break;
        case 'last-year':
        case 'last_year':
            fromDate = new Date(today.getFullYear() - 1, 0, 1);
            toDate = new Date(today.getFullYear() - 1, 11, 31);
            break;
    }

    if (fromDate && toDate) {
        document.getElementById('from_date').value = fromDate.toISOString().split('T')[0];
        document.getElementById('to_date').value = toDate.toISOString().split('T')[0];

        // Visual feedback
        const button = (el instanceof Element) ? el : (typeof event !== 'undefined' ? event.target : null);
        let originalClasses = null;
        if (button instanceof Element) {
            originalClasses = button.className;
            button.className = button.className.replace('text-blue-600', 'text-white').replace('hover:bg-blue-50', 'bg-blue-600');
        }

        setTimeout(() => {
            if (originalClasses !== null && button instanceof Element) {
                button.className = originalClasses;
            }
            showNotification(`Date range set to ${preset.replace(/[-_]/g, ' ')}`, 'success');
        }, 200);
    }
}

// Alias function for compatibility
function setDateRange(preset, el) {
    setQuickDate(preset, el);
}

// Enhanced print functionality
function printCashFlow() {
    // Hide non-printable elements
    const noPrintElements = document.querySelectorAll('.no-print');
    noPrintElements.forEach(el => el.style.display = 'none');

    // Add print-specific styles
    const printStyles = document.createElement('style');
    printStyles.innerHTML = `
        @media print {
            body * { visibility: hidden; }
            .cash-flow-container, .cash-flow-container * { visibility: visible; }
            .cash-flow-container { position: absolute; left: 0; top: 0; width: 100%; }
            .bg-gradient-to-r, .bg-gradient-to-br { background: #f9f9f9 !important; }
            .shadow-lg, .shadow-md { box-shadow: none !important; }
        }
    `;
    document.head.appendChild(printStyles);

    // Trigger print
    window.print();

    // Restore elements after print
    setTimeout(() => {
        noPrintElements.forEach(el => el.style.display = '');
        printStyles.remove();
    }, 1000);
}

// Enhanced event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Initialize section states and set up content containers
    ['operating', 'investing', 'financing'].forEach(sectionName => {
        const content = document.getElementById(sectionName + '-content');
        if (content) {
            content.style.maxHeight = content.scrollHeight + 'px';
            content.style.transition = 'max-height 0.3s ease, padding 0.3s ease';
        }
    });

    // Initialize expand/collapse button state
    updateExpandAllButton();

    // Date field validation
    const fromDateField = document.getElementById('from_date');
    const toDateField = document.getElementById('to_date');

    if (fromDateField) {
        fromDateField.addEventListener('change', validateDateRange);
    }

    if (toDateField) {
        toDateField.addEventListener('change', validateDateRange);
    }

    // Add hover effects to activity rows
    const activityRows = document.querySelectorAll('[class*="hover:border-gray-200"]');
    activityRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
            this.style.transition = 'transform 0.2s ease';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });

    // Add loading animation to form submissions
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.innerHTML = '<svg class="animate-spin h-4 w-4 inline mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Generating...';
                submitButton.disabled = true;
            }
        });
    }
});

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + P for print
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printCashFlow();
    }

    // Ctrl/Cmd + E for export
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportToCSV();
    }
});

// Initialize the cash flow chart if present
function initCashFlowChart() {
    const ctxEl = document.getElementById('cashFlowChart');
    if (!ctxEl || typeof Chart === 'undefined') return;

    const values = @json($chartValues);
    const labels = ['Operating', 'Investing', 'Financing', 'Net Change'];
    const backgroundColors = @json($chartColors);

    const ctx = ctxEl.getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cash Flow (₦)',
                data: values,
                backgroundColor: backgroundColors,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function(ctx) { return '₦ ' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}); } } }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    ticks: { callback: function(val){ return '₦ ' + Number(val).toLocaleString(); } }
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initCashFlowChart();
});
</script>
@endpush
@endsection
