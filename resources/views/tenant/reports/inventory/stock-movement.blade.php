@extends('layouts.tenant')

@section('title', 'Stock Movement Report')
@section('page-title', 'Stock Movement Report')
@section('page-description')
    <span class="hidden md:inline">Comprehensive transaction history and stock movement analytics with detailed tracking</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Navigation Tabs -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.reports.stock-summary', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Stock Summary
            </a>

            <a href="{{ route('tenant.reports.low-stock-alert', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Low Stock
            </a>

            <a href="{{ route('tenant.reports.stock-valuation', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Valuation
            </a>

            <a href="{{ route('tenant.reports.stock-movement', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150">
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
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150">
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

    <!-- Summary Cards with Gradient Backgrounds -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <!-- Total In Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-100 mb-1">Total In</p>
                    <p class="text-3xl font-bold">{{ number_format($totalIn, 0) }}</p>
                    <p class="text-xs text-green-100 mt-2">Incoming units</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Out Card -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-100 mb-1">Total Out</p>
                    <p class="text-3xl font-bold">{{ number_format(abs($totalOut), 0) }}</p>
                    <p class="text-xs text-red-100 mt-2">Outgoing units</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Net Movement Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-100 mb-1">Net Movement</p>
                    <p class="text-3xl font-bold">{{ number_format($netMovement, 0) }}</p>
                    <p class="text-xs text-blue-100 mt-2">{{ $netMovement >= 0 ? 'Positive' : 'Negative' }} balance</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- In Value Card -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-emerald-100 mb-1">In Value</p>
                    <p class="text-2xl font-bold">₦{{ number_format($totalInValue, 0) }}</p>
                    <p class="text-xs text-emerald-100 mt-2">Purchase value</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Out Value Card -->
        <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-rose-100 mb-1">Out Value</p>
                    <p class="text-2xl font-bold">₦{{ number_format($totalOutValue, 0) }}</p>
                    <p class="text-xs text-rose-100 mt-2">Sales value</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Transactions Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-purple-100 mb-1">Transactions</p>
                    <p class="text-3xl font-bold">{{ number_format($transactionCount) }}</p>
                    <p class="text-xs text-purple-100 mt-2">Total movements</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section with Enhanced Design -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter Movement Data
            </h3>
        </div>

        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- From Date -->
                <div>
                    <label for="from_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        From Date
                    </label>
                    <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                </div>

                <!-- To Date -->
                <div>
                    <label for="to_date" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        To Date
                    </label>
                    <input type="date" name="to_date" id="to_date" value="{{ $toDate }}"
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                </div>

                <!-- Product -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Product
                    </label>
                    <select name="product_id" id="product_id"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Category
                    </label>
                    <select name="category_id" id="category_id"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Movement Type -->
                <div>
                    <label for="movement_type" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                        </svg>
                        Movement Type
                    </label>
                    <select name="movement_type" id="movement_type"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                        <option value="">All Movements</option>
                        <option value="in" {{ $movementType === 'in' ? 'selected' : '' }}>In Only</option>
                        <option value="out" {{ $movementType === 'out' ? 'selected' : '' }}>Out Only</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end gap-2 md:col-span-2 lg:col-span-3">
                    <button type="submit"
                            class="flex-1 inline-flex justify-center items-center px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-wide hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-150 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Apply Filters
                    </button>
                    <a href="{{ route('tenant.reports.stock-movement', $tenant->slug) }}"
                       class="inline-flex justify-center items-center px-6 py-2.5 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 uppercase tracking-wide hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Stock Movement Table with Enhanced Styling -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Movement Details
                </h3>
                <span class="text-sm text-gray-600">
                    Total: <span class="font-bold text-gray-900">{{ number_format($movements->total()) }}</span> movements
                </span>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Created By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-150">
                            <!-- Date -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($movement->transaction_date)->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($movement->transaction_date)->format('h:i A') }}
                                </div>
                            </td>

                            <!-- Product with Avatar -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        {{ strtoupper(substr($movement->product->name, 0, 2)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $movement->product->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $movement->product->category->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Movement Type Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movement->quantity > 0)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-green-500 to-green-600 text-white shadow-md">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                        </svg>
                                        STOCK IN
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-red-500 to-red-600 text-white shadow-md">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                        </svg>
                                        STOCK OUT
                                    </span>
                                @endif
                            </td>

                            <!-- Quantity with Color Coding -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-lg font-bold {{ $movement->quantity > 0 ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 2) }}
                                </span>
                            </td>

                            <!-- Rate -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                ₦{{ number_format($movement->rate, 2) }}
                            </td>

                            <!-- Total Value -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <span class="font-bold text-gray-900">
                                    ₦{{ number_format(abs($movement->quantity) * $movement->rate, 2) }}
                                </span>
                            </td>

                            <!-- Reference -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($movement->reference)
                                    <span class="inline-flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                        </svg>
                                        {{ $movement->reference }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Created By -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($movement->createdBy->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <span class="ml-2 text-sm text-gray-700">{{ $movement->createdBy->name ?? 'System' }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                    <p class="text-base font-semibold text-gray-900 mb-1">No Stock Movements Found</p>
                                    <p class="text-sm text-gray-500">Try adjusting your filters to see movement data</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($movements->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $movements->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
function exportToExcel() {
    // Create workbook
    const wb = XLSX.utils.book_new();

    // Summary data
    const summaryData = [
        ['Stock Movement Report'],
        ['Generated: ' + new Date().toLocaleString()],
        [''],
        ['Summary Statistics'],
        ['Total Stock In', '{{ number_format($totalIn, 2) }}'],
        ['Total Stock Out', '{{ number_format(abs($totalOut), 2) }}'],
        ['Net Movement', '{{ number_format($netMovement, 2) }}'],
        ['In Value', '₦{{ number_format($totalInValue, 2) }}'],
        ['Out Value', '₦{{ number_format($totalOutValue, 2) }}'],
        ['Total Transactions', '{{ number_format($transactionCount) }}'],
        [''],
        ['Movement Details'],
        ['Date', 'Product', 'Category', 'Type', 'Quantity', 'Rate (₦)', 'Value (₦)', 'Reference', 'Created By']
    ];

    // Add movement data
    @foreach($movements as $movement)
    summaryData.push([
        '{{ \Carbon\Carbon::parse($movement->transaction_date)->format("Y-m-d H:i") }}',
        '{{ $movement->product->name }}',
        '{{ $movement->product->category->name ?? "N/A" }}',
        '{{ $movement->quantity > 0 ? "IN" : "OUT" }}',
        {{ $movement->quantity }},
        {{ $movement->rate }},
        {{ abs($movement->quantity) * $movement->rate }},
        '{{ $movement->reference ?? "-" }}',
        '{{ $movement->createdBy->name ?? "System" }}'
    ]);
    @endforeach

    // Create worksheet
    const ws = XLSX.utils.aoa_to_sheet(summaryData);

    // Set column widths
    ws['!cols'] = [
        { wch: 18 },  // Date
        { wch: 30 },  // Product
        { wch: 20 },  // Category
        { wch: 12 },  // Type
        { wch: 12 },  // Quantity
        { wch: 15 },  // Rate
        { wch: 15 },  // Value
        { wch: 20 },  // Reference
        { wch: 20 }   // Created By
    ];

    // Style the header row (row 13, index 12)
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let C = range.s.c; C <= range.e.c; ++C) {
        const address = XLSX.utils.encode_col(C) + "13";
        if (!ws[address]) continue;
        ws[address].s = {
            font: { bold: true },
            fill: { fgColor: { rgb: "4B5563" } },
            alignment: { horizontal: "center" }
        };
    }

    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, 'Stock Movement');

    // Generate filename with date
    const filename = 'stock-movement-report-' + new Date().toISOString().split('T')[0] + '.xlsx';

    // Save file
    XLSX.writeFile(wb, filename);
}
</script>
@endpush

@push('styles')
<style>
@media print {
    /* Hide non-essential elements */
    .no-print,
    nav,
    header,
    footer,
    button,
    .pagination {
        display: none !important;
    }

    /* Optimize page layout */
    body {
        margin: 0;
        padding: 20px;
    }

    /* Remove shadows and backgrounds */
    .shadow-lg,
    .shadow-md,
    .shadow-sm {
        box-shadow: none !important;
    }

    .bg-gradient-to-br,
    .bg-gradient-to-r {
        background: white !important;
        color: black !important;
    }

    /* Adjust summary cards for print */
    .grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        page-break-inside: avoid;
    }

    /* Table optimizations */
    table {
        page-break-inside: auto;
        border-collapse: collapse;
        width: 100%;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group;
    }

    tfoot {
        display: table-footer-group;
    }

    /* Keep important colors */
    .text-green-600,
    .text-green-700,
    .bg-green-100 {
        color: #059669 !important;
        background-color: #d1fae5 !important;
    }

    .text-red-600,
    .text-red-700,
    .bg-red-100 {
        color: #dc2626 !important;
        background-color: #fee2e2 !important;
    }

    /* Ensure badges are visible */
    .rounded-lg {
        border: 1px solid #e5e7eb;
    }
}
</style>
@endpush
