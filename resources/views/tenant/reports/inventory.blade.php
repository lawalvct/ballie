@extends('layouts.tenant')

@section('title', 'Inventory Reports')
@section('page-title', 'Inventory Reports')
@section('page-description', 'Stock analysis, movements, and inventory management reports.')

@section('content')
<div class="space-y-6" x-data="inventoryReports()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 p-2 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Inventory Reports</h1>
                <p class="text-sm text-gray-500">Stock levels, movements, and inventory analysis</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button @click="exportAll" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export All
            </button>
            <a href="{{ route('tenant.reports.index', $tenant) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <select x-model="filters.reportType" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="stock_summary">Stock Summary</option>
                    <option value="low_stock">Low Stock Alert</option>
                    <option value="stock_movement">Stock Movement</option>
                    <option value="valuation">Stock Valuation</option>
                    <option value="category_analysis">Category Analysis</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <select x-model="filters.dateRange" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_quarter">This Quarter</option>
                    <option value="this_year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select x-model="filters.category" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Categories</option>
                    <option value="#">Electronics</option>
                    <option value="#">Clothing</option>
                    <option value="#">Food & Beverages</option>
                    <option value="#">Office Supplies</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Items</option>
                    <option value="in_stock">In Stock</option>
                    <option value="low_stock">Low Stock</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>
            <div class="flex items-end">
                <button @click="generateReport" class="w-full bg-gradient-to-r from-yellow-600 to-yellow-700 text-white px-4 py-2 rounded-xl hover:from-yellow-700 hover:to-yellow-800 transition-all duration-200">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Inventory Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-blue-600">#,###</p>
                    <p class="text-xs text-blue-600">+## new this month</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Value</p>
                    <p class="text-2xl font-bold text-green-600">₦#,###,###</p>
                    <p class="text-xs text-green-600">+#.#% from last month</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                    <p class="text-2xl font-bold text-red-600">##</p>
                    <p class="text-xs text-red-600">Need attention</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                    <p class="text-2xl font-bold text-orange-600">##</p>
                    <p class="text-xs text-orange-600">Items unavailable</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Stock Summary -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Stock Summary</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Full Report</a>
                </div>
            </div>
            <div class="space-y-3">
                <div class="grid grid-cols-4 gap-4 text-sm font-medium text-gray-500 border-b pb-2">
                    <span>Product</span>
                    <span class="text-right">Qty</span>
                    <span class="text-right">Value</span>
                    <span class="text-right">Status</span>
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">Product Name A</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">₦##,###</span>
                        <span class="text-right">
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">In Stock</span>
                        </span>
                    </div>
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">Product Name B</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">₦##,###</span>
                        <span class="text-right">
                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Low Stock</span>
                        </span>
                    </div>
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">Product Name C</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">₦##,###</span>
                        <span class="text-right">
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Out of Stock</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Movement -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Stock Movements</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Movements</a>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Stock In - Product A</p>
                        <p class="text-xs text-gray-500">Purchase Order #PO####</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600">+## units</p>
                        <p class="text-xs text-gray-500">Yesterday</p>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Stock Out - Product B</p>
                        <p class="text-xs text-gray-500">Sales Order #SO####</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-red-600">-## units</p>
                        <p class="text-xs text-gray-500">2 days ago</p>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Adjustment - Product C</p>
                        <p class="text-xs text-gray-500">Stock count adjustment</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-blue-600">+## units</p>
                        <p class="text-xs text-gray-500">3 days ago</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products by Value -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Top Products by Value</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-blue-600">1</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">High Value Product A</p>
                            <p class="text-xs text-gray-500">## units in stock</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦##,###,###</p>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-green-600">2</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">High Value Product B</p>
                            <p class="text-xs text-gray-500">## units in stock</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦##,###,###</p>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-yellow-600">3</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">High Value Product C</p>
                            <p class="text-xs text-gray-500">## units in stock</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦##,###,###</p>
                </div>
            </div>
        </div>

        <!-- Category Analysis -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Category Analysis</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Electronics</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                        <span class="text-sm font-medium">45%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Clothing</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                        <span class="text-sm font-medium">30%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Food & Beverages</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 15%"></div>
                        </div>
                        <span class="text-sm font-medium">15%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Office Supplies</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 10%"></div>
                        </div>
                        <span class="text-sm font-medium">10%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function inventoryReports() {
    return {
        filters: {
            reportType: 'stock_summary',
            dateRange: 'this_month',
            category: 'all',
            status: 'all'
        },

        generateReport() {
            // Generate report logic
        },

        exportAll() {
            // Export logic
        }
    }
}
</script>
@endsection
