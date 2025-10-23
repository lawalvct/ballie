@extends('layouts.tenant')

@section('title', 'Product Sales Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Product Sales Report</h1>
            <p class="text-sm text-gray-500 mt-1">Detailed sales analysis by product with profit metrics</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ route('tenant.reports.index', $tenant->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Products Sold</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($totalRevenue, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Quantity</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalQuantitySold, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                <select name="product_id" id="product_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" id="category_id" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select name="sort_by" id="sort_by" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="total_revenue" {{ $sortBy === 'total_revenue' ? 'selected' : '' }}>Revenue</option>
                    <option value="quantity_sold" {{ $sortBy === 'quantity_sold' ? 'selected' : '' }}>Quantity</option>
                    <option value="gross_profit" {{ $sortBy === 'gross_profit' ? 'selected' : '' }}>Profit</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Product Sales Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Product Sales Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Sold</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Price</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Margin %</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Invoices</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($productSales as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $product->product_name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($product->quantity_sold, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">₦{{ number_format($product->total_revenue, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₦{{ number_format($product->avg_selling_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₦{{ number_format($product->total_cost, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <span class="font-semibold {{ $product->gross_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    ₦{{ number_format($product->gross_profit, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $product->profit_margin >= 20 ? 'bg-green-100 text-green-800' : ($product->profit_margin >= 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($product->profit_margin, 1) }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($product->invoice_count) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">No product sales data found for the selected period</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($productSales->count() > 0)
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-3 text-sm font-bold text-gray-900">Total</td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">{{ number_format($productSales->sum('quantity_sold'), 2) }}</td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">₦{{ number_format($productSales->sum('total_revenue'), 2) }}</td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">-</td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">₦{{ number_format($productSales->sum('total_cost'), 2) }}</td>
                            <td class="px-6 py-3 text-sm font-bold text-right">
                                <span class="{{ $productSales->sum('gross_profit') >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    ₦{{ number_format($productSales->sum('gross_profit'), 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">
                                {{ number_format($productSales->sum('total_revenue') > 0 ? ($productSales->sum('gross_profit') / $productSales->sum('total_revenue')) * 100 : 0, 1) }}%
                            </td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">{{ number_format($productSales->sum('invoice_count')) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <!-- Pagination -->
        @if($productSales->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $productSales->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
