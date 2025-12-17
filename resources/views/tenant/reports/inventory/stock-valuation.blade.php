@extends('layouts.tenant')

@section('title', 'Stock Valuation Report')
@section('page-title', 'Stock Valuation Report')
@section('page-description')
    <span class="hidden md:inline">Comprehensive inventory valuation with multiple methods and detailed analytics</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header with Navigation Tabs -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.reports.stock-summary', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Stock Summary
            </a>

            <a href="{{ route('tenant.reports.low-stock-alert', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Low Stock
            </a>

            <a href="{{ route('tenant.reports.stock-valuation', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Valuation
            </a>

            <a href="{{ route('tenant.reports.stock-movement', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-150">
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
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-150">
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Products Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-100 mb-1">Total Products</p>
                    <p class="text-3xl font-bold">{{ number_format($totalProducts) }}</p>
                    <p class="text-xs text-blue-100 mt-2">Inventory items</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Value Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-100 mb-1">Total Value</p>
                    <p class="text-3xl font-bold">₦{{ number_format($totalStockValue, 0) }}</p>
                    <p class="text-xs text-green-100 mt-2">Stock valuation</p>
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
                    <p class="text-3xl font-bold">{{ number_format($totalQuantity, 0) }}</p>
                    <p class="text-xs text-purple-100 mt-2">Combined units</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Value Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-orange-100 mb-1">Average Value</p>
                    <p class="text-3xl font-bold">₦{{ number_format($averageValue, 0) }}</p>
                    <p class="text-xs text-orange-100 mt-2">Per product</p>
                </div>
                <div class="flex-shrink-0 w-14 h-14 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
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
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search by product name..." class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                </div>

                <!-- As of Date -->
                <div>
                    <label for="as_of_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        As of Date
                    </label>
                    <input type="date" name="as_of_date" id="as_of_date" value="{{ $asOfDate }}" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        Category
                    </label>
                    <select name="category_id" id="category_id" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Valuation Method -->
                <div>
                    <label for="valuation_method" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Valuation Method
                    </label>
                    <select name="valuation_method" id="valuation_method" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                        <option value="weighted_average" {{ $valuationMethod === 'weighted_average' ? 'selected' : '' }}>Weighted Average</option>
                        <option value="fifo" {{ $valuationMethod === 'fifo' ? 'selected' : '' }}>FIFO</option>
                    </select>
                </div>

                <!-- Group By -->
                <div>
                    <label for="group_by" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        Group By
                    </label>
                    <select name="group_by" id="group_by" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
                        <option value="product" {{ $groupBy === 'product' ? 'selected' : '' }}>Product</option>
                        <option value="category" {{ $groupBy === 'category' ? 'selected' : '' }}>Category</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 border border-transparent rounded-lg text-sm font-medium text-white hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply Filters
                </button>
                <a href="{{ route('tenant.reports.stock-valuation', $tenant->slug) }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Stock Valuation Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Stock Valuation Details ({{ ucfirst(str_replace('_', ' ', $valuationMethod)) }})</h3>
        </div>
        <div class="overflow-x-auto">
            @if($groupBy === 'product')
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Rate</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($paginatedData as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item['product']->name }}</div>
                                    <div class="text-xs text-gray-500">SKU: {{ $item['product']->sku ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['category_name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['quantity'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₦{{ number_format($item['average_rate'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">₦{{ number_format($item['value'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <p class="text-sm text-gray-500">No stock valuation data available</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($paginatedData as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['category_name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['product_count']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['total_quantity'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">₦{{ number_format($item['total_value'], 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                    {{ $totalStockValue > 0 ? number_format(($item['total_value'] / $totalStockValue) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <p class="text-sm text-gray-500">No stock valuation data available</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Pagination -->
        @if($paginatedData->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $paginatedData->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Top 10 Most Valuable Items -->
    @if($topValueProducts->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top 10 Most Valuable Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topValueProducts as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item['product']->name }}</div>
                                <div class="text-xs text-gray-500">{{ $item['category_name'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($item['quantity'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">₦{{ number_format($item['value'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                {{ $totalStockValue > 0 ? number_format(($item['value'] / $totalStockValue) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
