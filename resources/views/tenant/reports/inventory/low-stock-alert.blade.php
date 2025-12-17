@extends('layouts.tenant')

@section('title', 'Low Stock Alert Report')
@section('page-title', 'Low Stock Alert Report')
@section('page-description')
    <span class="hidden md:inline">Critical alerts for products below reorder level requiring immediate attention</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Navigation Tabs -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.reports.stock-summary', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Stock Summary
            </a>

            <a href="{{ route('tenant.reports.low-stock-alert', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Low Stock
            </a>

            <a href="{{ route('tenant.reports.stock-valuation', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Valuation
            </a>

            <a href="{{ route('tenant.reports.stock-movement', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
                Movement
            </a>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2">
            <button onclick="exportToExcel()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </button>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ route('tenant.reports.index', $tenant->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Alert Summary Cards with Gradient Backgrounds -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Alerts Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-orange-100 mb-1">Total Alerts</p>
                    <p class="text-3xl font-bold">{{ number_format($totalAlerts) }}</p>
                    <p class="text-xs text-orange-100 mt-2">Requires attention</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Critical Alerts Card -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-100 mb-1">Critical</p>
                    <p class="text-3xl font-bold">{{ number_format($criticalAlerts) }}</p>
                    <p class="text-xs text-red-100 mt-2">Urgent restock needed</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Warning Alerts Card -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-100 mb-1">Warning</p>
                    <p class="text-3xl font-bold">{{ number_format($warningAlerts) }}</p>
                    <p class="text-xs text-yellow-100 mt-2">Below reorder level</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Out of Stock Card -->
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-100 mb-1">Out of Stock</p>
                    <p class="text-3xl font-bold">{{ number_format($outOfStockCount) }}</p>
                    <p class="text-xs text-gray-100 mt-2">Zero inventory</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Reorder Value Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-100 mb-1">Reorder Value</p>
                    <p class="text-3xl font-bold">₦{{ number_format($estimatedReorderValue, 0) }}</p>
                    <p class="text-xs text-blue-100 mt-2">Estimated cost</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search Product
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by product name..." class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                </div>

                <!-- As of Date -->
                <div>
                    <label for="as_of_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        As of Date
                    </label>
                    <input type="date" name="as_of_date" id="as_of_date" value="{{ $asOfDate }}" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Category
                    </label>
                    <select name="category_id" id="category_id" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Alert Type -->
                <div>
                    <label for="alert_type" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Alert Type
                    </label>
                    <select name="alert_type" id="alert_type" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                        <option value="all" {{ $alertType === 'all' ? 'selected' : '' }}>All Alerts</option>
                        <option value="critical" {{ $alertType === 'critical' ? 'selected' : '' }}>Critical Only</option>
                        <option value="low" {{ $alertType === 'low' ? 'selected' : '' }}>Low Stock Only</option>
                        <option value="out_of_stock" {{ $alertType === 'out_of_stock' ? 'selected' : '' }}>Out of Stock Only</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply Filters
                </button>
                <a href="{{ route('tenant.reports.low-stock-alert', $tenant->slug) }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Low Stock Alert Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Low Stock Alert Details
                </h3>
                <p class="text-sm text-gray-500 mt-1">Showing {{ $paginatedProducts->count() }} of {{ $paginatedProducts->total() }} alerts</p>
            </div>
            <div class="text-sm text-gray-600">
                Report Date: <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($asOfDate)->format('M d, Y') }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="alertTable">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Alert
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Product
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Category
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Reorder Level</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-end">
                                Shortage
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-end">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                Reorder Cost
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paginatedProducts as $product)
                        <tr class="group hover:bg-gradient-to-r hover:from-red-50 hover:to-orange-50 transition-all duration-200 {{ $product->alert_level === 'critical' ? 'bg-red-50/50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center">
                                    @if($product->alert_level === 'critical')
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-red-500 rounded-full opacity-25 animate-ping"></div>
                                            <div class="relative flex items-center justify-center w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full shadow-md">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full shadow-md">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                            {{ strtoupper(substr($product->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate group-hover:text-red-600 transition-colors">
                                            {{ $product->name }}
                                        </p>
                                        <div class="flex items-center mt-1 text-xs text-gray-500">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                            </svg>
                                            <span class="font-medium">{{ $product->sku ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->category)
                                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-gradient-to-r from-purple-100 to-purple-200 border border-purple-300">
                                        <svg class="w-3 h-3 mr-1.5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <span class="text-xs font-semibold text-purple-800">{{ $product->category->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex flex-col items-end">
                                    <span class="text-sm font-bold {{ $product->calculated_stock <= 0 ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ number_format($product->calculated_stock, 2) }}
                                    </span>
                                    @if($product->calculated_stock <= 0)
                                        <span class="text-xs text-red-500 font-medium">Empty</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($product->reorder_level, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex flex-col items-end">
                                    <span class="text-sm font-bold text-red-600">{{ number_format($product->shortage_quantity, 2) }}</span>
                                    @if($product->shortage_percentage > 0)
                                        <span class="text-xs text-gray-500 font-medium">({{ number_format($product->shortage_percentage, 1) }}%)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-100 to-orange-100 rounded-lg border border-red-200">
                                    <svg class="w-4 h-4 mr-1.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    <span class="text-sm font-bold text-red-900">{{ number_format($product->shortage_quantity * ($product->purchase_rate ?? 0), 2) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($product->calculated_stock <= 0)
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-md">
                                        <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Out of Stock
                                    </span>
                                @elseif($product->alert_level === 'critical')
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-red-500 to-red-600 text-white shadow-md">
                                        <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Critical
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-gradient-to-r from-yellow-400 to-yellow-500 text-white shadow-md">
                                        <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Low Stock
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-500 rounded-full flex items-center justify-center mb-4 shadow-lg">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Excellent Stock Management!</h3>
                                    <p class="text-sm text-gray-500">No low stock alerts at this time. All products are well-stocked.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($paginatedProducts->hasPages())
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
                {{ $paginatedProducts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<!-- SheetJS Library for Excel Export -->
<script src="https://cdn.sheetjs.com/xlsx-0.18.5/package/dist/xlsx.full.min.js"></script>

<script>
    // Export to Excel functionality
    function exportToExcel() {
        // Create workbook
        const wb = XLSX.utils.book_new();

        // Summary data
        const summaryData = [
            ['Low Stock Alert Report'],
            ['Report Date:', '{{ \Carbon\Carbon::parse($asOfDate)->format("M d, Y") }}'],
            ['Generated:', '{{ \Carbon\Carbon::now()->format("M d, Y h:i A") }}'],
            [],
            ['Summary Statistics'],
            ['Total Alerts:', {{ $totalAlerts }}],
            ['Critical Alerts:', {{ $criticalAlerts }}],
            ['Warning Alerts:', {{ $warningAlerts }}],
            ['Out of Stock:', {{ $outOfStockCount }}],
            ['Estimated Reorder Value:', '₦{{ number_format($estimatedReorderValue, 2) }}'],
            [],
            []
        ];

        // Product data headers
        const headers = ['Alert Level', 'Product Name', 'SKU', 'Category', 'Current Stock', 'Reorder Level', 'Shortage', 'Shortage %', 'Reorder Cost', 'Status'];

        // Get table data
        const tableRows = [];
        const table = document.getElementById('alertTable');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (!row.querySelector('td[colspan]')) { // Skip empty state row
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    const alertIcon = cells[0].querySelector('svg');
                    const alertLevel = alertIcon?.classList.contains('text-red-600') || alertIcon?.parentElement?.parentElement?.classList.contains('from-red-500') ? 'Critical' : 'Warning';
                    const productName = cells[1].querySelector('.text-sm.font-bold')?.textContent?.trim() || '';
                    const sku = cells[1].querySelector('.text-xs')?.textContent?.replace('SKU:', '')?.trim() || '';
                    const category = cells[2].querySelector('.text-xs')?.textContent?.trim() || cells[2].textContent?.trim() || '-';
                    const currentStock = cells[3].querySelector('span')?.textContent?.trim() || '';
                    const reorderLevel = cells[4].querySelector('span')?.textContent?.trim() || '';
                    const shortage = cells[5].querySelector('.font-bold')?.textContent?.trim() || '';
                    const shortagePercent = cells[5].querySelector('.text-xs')?.textContent?.trim() || '';
                    const reorderCost = cells[6].querySelector('.font-bold')?.textContent?.trim() || '';
                    const status = cells[7].querySelector('span')?.textContent?.trim() || '';

                    tableRows.push([
                        alertLevel,
                        productName,
                        sku,
                        category,
                        currentStock,
                        reorderLevel,
                        shortage,
                        shortagePercent,
                        reorderCost,
                        status
                    ]);
                }
            }
        });

        // Combine all data
        const wsData = [...summaryData, headers, ...tableRows];

        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet(wsData);

        // Set column widths
        ws['!cols'] = [
            { wch: 12 },  // Alert Level
            { wch: 30 },  // Product Name
            { wch: 15 },  // SKU
            { wch: 20 },  // Category
            { wch: 15 },  // Current Stock
            { wch: 15 },  // Reorder Level
            { wch: 12 },  // Shortage
            { wch: 12 },  // Shortage %
            { wch: 15 },  // Reorder Cost
            { wch: 15 }   // Status
        ];

        // Style the header row (make it bold)
        const headerRowIndex = summaryData.length;
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let col = range.s.c; col <= range.e.c; col++) {
            const cellAddress = XLSX.utils.encode_cell({ r: headerRowIndex, c: col });
            if (ws[cellAddress]) {
                ws[cellAddress].s = {
                    font: { bold: true },
                    fill: { fgColor: { rgb: "CCCCCC" } }
                };
            }
        }

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Low Stock Alerts");

        // Generate filename with timestamp
        const timestamp = new Date().toISOString().slice(0, 10);
        const filename = `low-stock-alert-${timestamp}.xlsx`;

        // Save file
        XLSX.writeFile(wb, filename);
    }

    // Print functionality
    function printReport() {
        window.print();
    }
</script>

<!-- Print Styles -->
<style>
    @media print {
        /* Hide non-essential elements */
        .sidebar,
        .navbar,
        nav,
        button,
        .action-buttons,
        .filters-section,
        .pagination,
        .no-print {
            display: none !important;
        }

        /* Optimize table for print */
        body {
            background: white;
            font-size: 10pt;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        table {
            page-break-inside: auto;
            font-size: 9pt;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        /* Adjust colors for print */
        .bg-gradient-to-r,
        .bg-gradient-to-br {
            background: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .shadow-sm,
        .shadow-md,
        .shadow-lg {
            box-shadow: none !important;
        }

        /* Keep important colors */
        .text-red-600,
        .text-red-900 {
            color: #dc2626 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .text-yellow-600 {
            color: #ca8a04 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Add page header */
        @page {
            margin: 1cm;
            @top-center {
                content: "Low Stock Alert Report - {{ \Carbon\Carbon::parse($asOfDate)->format('M d, Y') }}";
            }
        }
    }
</style>
@endpush
