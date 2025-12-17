@extends('layouts.tenant')

@section('title', 'Stock Summary Report')
@section('page-title', 'Stock Summary Report')
@section('page-description')
    <span class="hidden md:inline">Comprehensive overview of all products with real-time stock levels and valuations</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Navigation Tabs -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.reports.stock-summary', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-lg hover:from-yellow-600 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Stock Summary
            </a>

            <a href="{{ route('tenant.reports.low-stock-alert', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Low Stock
            </a>

            <a href="{{ route('tenant.reports.stock-valuation', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Valuation
            </a>

            <a href="{{ route('tenant.reports.stock-movement', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-150">
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
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-150">
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Products Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-100 mb-1">Total Products</p>
                    <p class="text-3xl font-bold">{{ number_format($totalProducts) }}</p>
                    <p class="text-xs text-blue-100 mt-2">Active inventory items</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stock Value Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-100 mb-1">Stock Value</p>
                    <p class="text-3xl font-bold">₦{{ number_format($totalStockValue, 0) }}</p>
                    <p class="text-xs text-green-100 mt-2">Total inventory value</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Quantity Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-purple-100 mb-1">Total Quantity</p>
                    <p class="text-3xl font-bold">{{ number_format($totalStockQuantity, 0) }}</p>
                    <p class="text-xs text-purple-100 mt-2">Combined stock units</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-yellow-100 mb-1">Low Stock</p>
                    <p class="text-3xl font-bold">{{ number_format($lowStockCount) }}</p>
                    <p class="text-xs text-yellow-100 mt-2">Needs restocking soon</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Out of Stock Card -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-100 mb-1">Out of Stock</p>
                    <p class="text-3xl font-bold">{{ number_format($outOfStockCount) }}</p>
                    <p class="text-xs text-red-100 mt-2">Requires immediate action</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search Product
                    </label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by product name..." class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                </div>

                <!-- As of Date -->
                <div>
                    <label for="as_of_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        As of Date
                    </label>
                    <input type="date" name="as_of_date" id="as_of_date" value="{{ $asOfDate }}" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Category
                    </label>
                    <select name="category_id" id="category_id" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Stock Status -->
                <div>
                    <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Stock Status
                    </label>
                    <select name="stock_status" id="stock_status" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                        <option value="all" {{ $stockStatus === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="in_stock" {{ $stockStatus === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ $stockStatus === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ $stockStatus === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                        </svg>
                        Sort By
                    </label>
                    <select name="sort_by" id="sort_by" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                        <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Product Name</option>
                        <option value="stock_value" {{ $sortBy === 'stock_value' ? 'selected' : '' }}>Stock Value</option>
                        <option value="current_stock" {{ $sortBy === 'current_stock' ? 'selected' : '' }}>Stock Quantity</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 border border-transparent rounded-lg text-sm font-medium text-white hover:from-yellow-600 hover:to-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply Filters
                </button>
                <a href="{{ route('tenant.reports.stock-summary', $tenant->slug) }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Stock Summary Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Stock Details
                </h3>
                <p class="text-sm text-gray-500 mt-1">Showing {{ $paginatedProducts->count() }} of {{ $paginatedProducts->total() }} products</p>
            </div>
            <div class="text-sm text-gray-600">
                Report Date: <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($asOfDate)->format('M d, Y') }}</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="stockTable">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                    <tr>
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
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-end">
                                Current Stock
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Purchase Rate</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Selling Rate</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-end">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                Purchase Value
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                            <div class="flex items-center justify-end">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                Sales Value
                            </div>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Reorder Level</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($paginatedProducts as $index => $product)
                        <tr class="hover:bg-yellow-50 transition-colors duration-150">
                            <!-- Product Name -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($product->name, 0, 2)) }}
                                    </div>
                                    <div class="ml-3">
                                        <a href="{{ route('tenant.reports.bin-card', ['tenant' => $tenant->slug, 'product_id' => $product->id]) }}" target="_blank" class="text-sm font-semibold text-gray-900 hover:text-yellow-600 transition-colors">
                                            {{ $product->name }}
                                        </a>
                                        @if($product->sku)
                                            <div class="text-xs text-gray-500">SKU: {{ $product->sku }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>

                            <!-- Current Stock -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-gray-900">
                                    {{ number_format($product->calculated_stock, 2) }}
                                </span>
                            </td>

                            <!-- Unit -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                                {{ $product->primaryUnit->abbreviation ?? 'Unit' }}
                            </td>

                            <!-- Purchase Rate -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₦{{ number_format($product->purchase_rate ?? 0, 2) }}
                            </td>

                            <!-- Selling Rate -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₦{{ number_format($product->sales_rate ?? 0, 2) }}
                            </td>

                            <!-- Purchase Value -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-semibold text-green-700">
                                    ₦{{ number_format($product->calculated_stock * ($product->purchase_rate ?? 0), 2) }}
                                </span>
                            </td>

                            <!-- Sales Value -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-bold text-blue-700">
                                    ₦{{ number_format($product->calculated_stock * ($product->sales_rate ?? 0), 2) }}
                                </span>
                            </td>

                            <!-- Reorder Level -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                                {{ $product->reorder_level ? number_format($product->reorder_level, 2) : '-' }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($product->status_flag === 'out_of_stock')
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Out of Stock
                                    </span>
                                @elseif($product->status_flag === 'low_stock')
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Low Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-full bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        In Stock
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('tenant.reports.bin-card', ['tenant' => $tenant->slug, 'product_id' => $product->id]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent rounded-lg text-xs font-semibold text-white hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-150">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Bin Card
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-900 mb-1">No stock data available</p>
                                    <p class="text-sm text-gray-500">Try adjusting your filters or add products to your inventory</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($paginatedProducts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $paginatedProducts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    // Export to Excel functionality
    function exportToExcel() {
        // Get table data
        const table = document.getElementById('stockTable');
        const rows = [];

        // Add title row
        rows.push(['Stock Summary Report']);
        rows.push(['Report Date: {{ \Carbon\Carbon::parse($asOfDate)->format("M d, Y") }}']);
        rows.push(['']);

        // Add summary data
        rows.push(['Summary Statistics']);
        rows.push(['Total Products', '{{ number_format($totalProducts) }}']);
        rows.push(['Stock Value', '₦{{ number_format($totalStockValue, 2) }}']);
        rows.push(['Total Quantity', '{{ number_format($totalStockQuantity, 2) }}']);
        rows.push(['Low Stock Count', '{{ number_format($lowStockCount) }}']);
        rows.push(['Out of Stock Count', '{{ number_format($outOfStockCount) }}']);
        rows.push(['']);

        // Add headers
        const headers = [
            'Product',
            'Category',
            'Current Stock',
            'Unit',
            'Purchase Rate',
            'Selling Rate',
            'Purchase Value',
            'Sales Value',
            'Reorder Level',
            'Status'
        ];
        rows.push(headers);

        // Add data rows
        @foreach($paginatedProducts as $product)
            rows.push([
                '{{ $product->name }}',
                '{{ $product->category->name ?? "Uncategorized" }}',
                {{ $product->calculated_stock }},
                '{{ $product->primaryUnit->abbreviation ?? "Unit" }}',
                {{ $product->purchase_rate ?? 0 }},
                {{ $product->sales_rate ?? 0 }},
                {{ $product->calculated_stock * ($product->purchase_rate ?? 0) }},
                {{ $product->calculated_stock * ($product->sales_rate ?? 0) }},
                '{{ $product->reorder_level ? number_format($product->reorder_level, 2) : "-" }}',
                '{{ ucfirst(str_replace("_", " ", $product->status_flag ?? "in_stock")) }}'
            ]);
        @endforeach

        // Create workbook and worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(rows);

        // Set column widths
        ws['!cols'] = [
            {wch: 30}, // Product
            {wch: 20}, // Category
            {wch: 15}, // Current Stock
            {wch: 10}, // Unit
            {wch: 15}, // Purchase Rate
            {wch: 15}, // Selling Rate
            {wch: 18}, // Purchase Value
            {wch: 18}, // Sales Value
            {wch: 15}, // Reorder Level
            {wch: 15}  // Status
        ];

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Stock Summary');

        // Generate filename with timestamp
        const filename = 'Stock_Summary_Report_{{ \Carbon\Carbon::parse($asOfDate)->format("Y_m_d") }}.xlsx';

        // Save file
        XLSX.writeFile(wb, filename);
    }

    // Auto-submit form on filter change for better UX (optional)
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('#category_id, #stock_status, #sort_by');
        const dateInput = document.getElementById('as_of_date');

        // Optional: Auto-submit on select change
        // Uncomment if you want instant filtering
        /*
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });

        dateInput.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        */
    });

    // Print styles
    const printStyles = `
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .gradient-bg {
                background: linear-gradient(135deg, #2b6399 0%, #3c2c64 50%, #4a3570 100%) !important;
            }
        }
    `;

    const styleSheet = document.createElement("style");
    styleSheet.textContent = printStyles;
    document.head.appendChild(styleSheet);
</script>
@endpush
@endsection
