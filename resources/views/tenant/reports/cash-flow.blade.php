@extends('layouts.tenant')

@section('title', 'Cash Flow Statement - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <!-- Enhanced Header with Summary Cards -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg text-white p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">Cash Flow Statement</h1>
                <p class="mt-2 text-blue-100">
                    Comprehensive cash flow analysis from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-sm text-blue-200">Net Cash Flow</div>
                <div class="text-3xl font-bold {{ $netCashFlow >= 0 ? 'text-green-300' : 'text-red-300' }}">
                    {{ $netCashFlow >= 0 ? '+' : '' }}{{ number_format($netCashFlow, 2) }}
                </div>
                <div class="text-xs text-blue-200">
                    {{ $netCashFlow >= 0 ? 'Cash Increase' : 'Cash Decrease' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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

    <!-- Enhanced Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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
                    <button onclick="setDateRange('this_month')"
                            class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md border border-gray-200">
                        This Month
                    </button>
                    <button onclick="setDateRange('last_month')"
                            class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md border border-gray-200">
                        Last Month
                    </button>
                    <button onclick="setDateRange('this_quarter')"
                            class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md border border-gray-200">
                        This Quarter
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="window.print()"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    <button onclick="exportToCSV()"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                    <button onclick="exportToPDF()"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        PDF
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

    <!-- Enhanced Cash Flow Statement -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Cash Flow Statement
                    </h3>
                    <p class="mt-1 text-sm text-gray-600">
                        Statement of cash flows for the period from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="toggleAllSections()" class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors">
                        <svg id="expand-all-icon" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <button onclick="toggleSection('operating')" class="flex items-center text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors group section-header-button">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 transition-colors section-icon">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <span>Cash Flow from Operating Activities</span>
                            <svg id="operating-chevron" class="w-5 h-5 ml-2 text-gray-400 transform transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-500">{{ count($operatingActivities) }} transactions</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $operatingTotal >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $operatingTotal >= 0 ? '+' : '' }}{{ number_format($operatingTotal, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div id="operating-content" class="p-6 transition-all duration-300 overflow-hidden">

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

                        <div class="border-t-2 border-gray-300 pt-4 mt-6">
                            <div class="flex justify-between items-center py-3 px-4 rounded-lg {{ $operatingTotal >= 0 ? 'bg-green-50' : 'bg-red-50' }}">
                                <span class="text-lg font-semibold text-gray-900">Net Cash Flow from Operating Activities</span>
                                <span class="text-2xl font-mono font-bold {{ $operatingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $operatingTotal >= 0 ? '+' : '' }}{{ number_format($operatingTotal, 2) }}
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
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <button onclick="toggleSection('investing')" class="flex items-center text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors group section-header-button">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 transition-colors section-icon">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <span>Cash Flow from Investing Activities</span>
                            <svg id="investing-chevron" class="w-5 h-5 ml-2 text-gray-400 transform transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-500">{{ count($investingActivities) }} transactions</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $investingTotal >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $investingTotal >= 0 ? '+' : '' }}{{ number_format($investingTotal, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div id="investing-content" class="p-6 transition-all duration-300 overflow-hidden">

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

                        <div class="border-t-2 border-gray-300 pt-4 mt-6">
                            <div class="flex justify-between items-center py-3 px-4 rounded-lg {{ $investingTotal >= 0 ? 'bg-green-50' : 'bg-red-50' }}">
                                <span class="text-lg font-semibold text-gray-900">Net Cash Flow from Investing Activities</span>
                                <span class="text-2xl font-mono font-bold {{ $investingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $investingTotal >= 0 ? '+' : '' }}{{ number_format($investingTotal, 2) }}
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
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <button onclick="toggleSection('financing')" class="flex items-center text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors group section-header-button">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-200 transition-colors section-icon">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6M9 10.5h6"></path>
                                </svg>
                            </div>
                            <span>Cash Flow from Financing Activities</span>
                            <svg id="financing-chevron" class="w-5 h-5 ml-2 text-gray-400 transform transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-500">{{ count($financingActivities) }} transactions</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $financingTotal >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $financingTotal >= 0 ? '+' : '' }}{{ number_format($financingTotal, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div id="financing-content" class="p-6 transition-all duration-300 overflow-hidden">

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

                        <div class="border-t-2 border-gray-300 pt-4 mt-6 mb-5">
                            <div class="flex justify-between items-center py-3 px-4 rounded-lg {{ $financingTotal >= 0 ? 'bg-green-50' : 'bg-red-50' }}">
                                <span class="text-lg font-semibold text-gray-900">Net Cash Flow from Financing Activities</span>
                                <span class="text-2xl font-mono font-bold {{ $financingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $financingTotal >= 0 ? '+' : '' }}{{ number_format($financingTotal, 2) }}
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

            <!-- Enhanced Net Cash Flow Summary -->
            <div class="border-t-2 border-gray-300 pt-6">
                <div class="bg-gradient-to-r from-gray-50 to-white rounded-lg p-6 space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Cash Flow Summary
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex justify-between items-center py-3 px-4 rounded-lg bg-white border border-gray-200 hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Operating Activities</span>
                                <div class="text-xs text-gray-500">Core business operations</div>
                            </div>
                            <span class="text-lg font-mono font-semibold {{ $operatingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $operatingTotal >= 0 ? '+' : '' }}{{ number_format($operatingTotal, 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-3 px-4 rounded-lg bg-white border border-gray-200 hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Investing Activities</span>
                                <div class="text-xs text-gray-500">Asset investments</div>
                            </div>
                            <span class="text-lg font-mono font-semibold {{ $investingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $investingTotal >= 0 ? '+' : '' }}{{ number_format($investingTotal, 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center py-3 px-4 rounded-lg bg-white border border-gray-200 hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Financing Activities</span>
                                <div class="text-xs text-gray-500">Capital structure</div>
                            </div>
                            <span class="text-lg font-mono font-semibold {{ $financingTotal >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $financingTotal >= 0 ? '+' : '' }}{{ number_format($financingTotal, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t-2 border-gray-300 pt-4">
                        <div class="flex justify-between items-center py-4 px-6 rounded-lg {{ $netCashFlow >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border-2">
                            <div>
                                <span class="text-xl font-bold text-gray-900">Net Change in Cash</span>
                                <div class="text-sm text-gray-600">
                                    @if($netCashFlow > 0)
                                        Cash position improved
                                    @elseif($netCashFlow == 0)
                                        No change in cash position
                                    @else
                                        Cash position declined
                                    @endif
                                </div>
                            </div>
                            <span class="text-3xl font-mono font-bold {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $netCashFlow >= 0 ? '+' : '' }}{{ number_format($netCashFlow, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Cash Position -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-3-6h6M9 10.5h6"></path>
                    </svg>
                    Cash Position Analysis
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-blue-600 bg-blue-200 px-2 py-1 rounded-full">Opening</span>
                        </div>
                        <div class="text-sm font-medium text-blue-900 mb-1">Cash at Start</div>
                        <div class="text-2xl font-bold text-blue-700 font-mono mb-2">{{ number_format($openingCash, 2) }}</div>
                        <div class="text-xs text-blue-600">As of {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }}</div>
                    </div>

                    <div class="bg-gradient-to-br from-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-50 to-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-100 rounded-xl p-6 border border-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-200 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($netCashFlow >= 0)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                    @endif
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-600 bg-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-200 px-2 py-1 rounded-full">
                                {{ $netCashFlow >= 0 ? 'Inflow' : 'Outflow' }}
                            </span>
                        </div>
                        <div class="text-sm font-medium text-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-900 mb-1">Net Cash Flow</div>
                        <div class="text-2xl font-bold text-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-700 font-mono mb-2">
                            {{ $netCashFlow >= 0 ? '+' : '' }}{{ number_format($netCashFlow, 2) }}
                        </div>
                        <div class="text-xs text-{{ $netCashFlow >= 0 ? 'green' : 'red' }}-600">For the period</div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-purple-600 bg-purple-200 px-2 py-1 rounded-full">Closing</span>
                        </div>
                        <div class="text-sm font-medium text-purple-900 mb-1">Cash at End</div>
                        <div class="text-2xl font-bold text-purple-700 font-mono mb-2">{{ number_format($closingCash, 2) }}</div>
                        <div class="text-xs text-purple-600">As of {{ \Carbon\Carbon::parse($toDate)->format('M d, Y') }}</div>
                    </div>
                </div>

                <!-- Cash Flow Health Indicator -->
                <div class="mt-6 p-4 rounded-lg border-2 {{ $netCashFlow > 0 ? 'border-green-200 bg-green-50' : ($netCashFlow == 0 ? 'border-yellow-200 bg-yellow-50' : 'border-red-200 bg-red-50') }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 {{ $netCashFlow > 0 ? 'bg-green-500' : ($netCashFlow == 0 ? 'bg-yellow-500' : 'bg-red-500') }}">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($netCashFlow > 0)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                                    @elseif($netCashFlow == 0)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    @endif
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-lg font-semibold {{ $netCashFlow > 0 ? 'text-green-800' : ($netCashFlow == 0 ? 'text-yellow-800' : 'text-red-800') }}">
                                    @if($netCashFlow > 0) Healthy Cash Flow
                                    @elseif($netCashFlow == 0) Balanced Cash Flow
                                    @else Declining Cash Flow
                                    @endif
                                </h5>
                                <p class="text-sm {{ $netCashFlow > 0 ? 'text-green-600' : ($netCashFlow == 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    @if($netCashFlow > 0)
                                        Your business generated positive cash flow this period, indicating strong operational performance.
                                    @elseif($netCashFlow == 0)
                                        Cash inflows exactly matched outflows for this period, maintaining stable liquidity.
                                    @else
                                        Cash outflows exceeded inflows this period. Consider reviewing operational efficiency and cash management.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium {{ $netCashFlow > 0 ? 'text-green-700' : ($netCashFlow == 0 ? 'text-yellow-700' : 'text-red-700') }}">
                                Change: {{ number_format(($closingCash > 0 ? ($netCashFlow / $closingCash) : 0) * 100, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash Accounts Detail -->
            @if(count($cashAccounts) > 0)
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Cash Accounts Detail</h4>
                    <div class="space-y-2">
                        @foreach($cashAccounts as $account)
                            <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded">
                                <span class="text-sm text-gray-900">{{ $account->name }} ({{ $account->code }})</span>
                                <span class="text-sm font-mono text-gray-700">
                                    {{ number_format($account->current_balance ?? 0, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
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
<script>
// Enhanced interactivity for cash flow statement - Section collapse/expand
let sectionsExpanded = {
    operating: true,
    investing: true,
    financing: true
};

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
function exportToCSV() {
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
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="animate-spin h-4 w-4 inline mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Exporting...';
    button.disabled = true;

    setTimeout(() => {
        link.click();
        document.body.removeChild(link);
        button.innerHTML = originalText;
        button.disabled = false;

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
function setQuickDate(preset) {
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
        const button = event.target;
        const originalClasses = button.className;
        button.className = button.className.replace('text-blue-600', 'text-white').replace('hover:bg-blue-50', 'bg-blue-600');

        setTimeout(() => {
            button.className = originalClasses;
            showNotification(`Date range set to ${preset.replace(/[-_]/g, ' ')}`, 'success');
        }, 200);
    }
}

// Alias function for compatibility
function setDateRange(preset) {
    setQuickDate(preset);
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
</script>
@endpush
@endsection
