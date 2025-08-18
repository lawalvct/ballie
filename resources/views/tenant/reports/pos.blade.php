@extends('layouts.tenant')

@section('title', 'POS Reports')
@section('page-title', 'POS Reports')
@section('page-description', 'Point of sale analytics, daily sales, and transaction reports.')

@section('content')
<div class="space-y-6" x-data="posReports()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <div class="bg-gradient-to-br from-emerald-400 to-emerald-600 p-2 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">POS Reports</h1>
                <p class="text-sm text-gray-500">Point of sale analytics, daily sales, and transaction reports</p>
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
                    <option value="daily_sales">Daily Sales</option>
                    <option value="hourly_sales">Hourly Sales</option>
                    <option value="product_performance">Product Performance</option>
                    <option value="payment_methods">Payment Methods</option>
                    <option value="cashier_performance">Cashier Performance</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <select x-model="filters.dateRange" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Terminal</label>
                <select x-model="filters.terminal" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Terminals</option>
                    <option value="#">POS Terminal 1</option>
                    <option value="#">POS Terminal 2</option>
                    <option value="#">Mobile POS</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cashier</label>
                <select x-model="filters.cashier" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Cashiers</option>
                    <option value="#">John Doe</option>
                    <option value="#">Jane Smith</option>
                    <option value="#">Mike Johnson</option>
                </select>
            </div>
            <div class="flex items-end">
                <button @click="generateReport" class="w-full bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-4 py-2 rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- POS Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today's Sales</p>
                    <p class="text-2xl font-bold text-green-600">₦###,###</p>
                    <p class="text-xs text-green-600">## transactions</p>
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
                    <p class="text-sm font-medium text-gray-600">Avg Transaction</p>
                    <p class="text-2xl font-bold text-blue-600">₦#,###</p>
                    <p class="text-xs text-blue-600">Per sale amount</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Items Sold</p>
                    <p class="text-2xl font-bold text-purple-600">##,###</p>
                    <p class="text-xs text-purple-600">Total quantity</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Terminals</p>
                    <p class="text-2xl font-bold text-orange-600">##</p>
                    <p class="text-xs text-orange-600">Currently open</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Hourly Sales -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Hourly Sales (Today)</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Chart</a>
                </div>
            </div>
            <div class="space-y-3">
                <div class="grid grid-cols-3 gap-4 text-sm font-medium text-gray-500 border-b pb-2">
                    <span>Hour</span>
                    <span class="text-right">Sales</span>
                    <span class="text-right">Transactions</span>
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <div class="grid grid-cols-3 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">9:00 AM</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                        <span class="text-right font-medium">##</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">10:00 AM</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                        <span class="text-right font-medium">##</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">11:00 AM</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                        <span class="text-right font-medium">##</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">12:00 PM</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                        <span class="text-right font-medium">##</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Payment Methods</h3>
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
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Cash</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">₦##,###</p>
                        <p class="text-xs text-gray-500">##% of sales</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Credit Card</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">₦##,###</p>
                        <p class="text-xs text-gray-500">##% of sales</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Mobile Payment</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">₦##,###</p>
                        <p class="text-xs text-gray-500">##% of sales</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Bank Transfer</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">₦##,###</p>
                        <p class="text-xs text-gray-500">##% of sales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Top Selling Products</h3>
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
                            <p class="text-sm font-medium text-gray-900">Product Name A</p>
                            <p class="text-xs text-gray-500">## units sold</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦##,###</p>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-green-600">2</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Product Name B</p>
                            <p class="text-xs text-gray-500">## units sold</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦##,###</p>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-yellow-600">3</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Product Name C</p>
                            <p class="text-xs text-gray-500">## units sold</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦##,###</p>
                </div>
            </div>
        </div>

        <!-- Cashier Performance -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Cashier Performance</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="space-y-3">
                <div class="grid grid-cols-4 gap-4 text-sm font-medium text-gray-500 border-b pb-2">
                    <span>Cashier</span>
                    <span class="text-right">Sales</span>
                    <span class="text-right">Transactions</span>
                    <span class="text-right">Avg/Sale</span>
                </div>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">John Doe</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">₦#,###</span>
                    </div>
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">Jane Smith</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">₦#,###</span>
                    </div>
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">Mike Johnson</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">₦#,###</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Transactions</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Transaction ID</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Time</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Items</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Payment</th>
                        <th class="text-left py-3 px-4 font-medium text-gray-500">Cashier</th>
                        <th class="text-right py-3 px-4 font-medium text-gray-500">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-900">#TXN####</td>
                        <td class="py-3 px-4 text-sm text-gray-600">##:## AM</td>
                        <td class="py-3 px-4 text-sm text-gray-600">## items</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Cash</td>
                        <td class="py-3 px-4 text-sm text-gray-600">John Doe</td>
                        <td class="py-3 px-4 text-sm font-medium text-green-600 text-right">₦#,###</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-900">#TXN####</td>
                        <td class="py-3 px-4 text-sm text-gray-600">##:## AM</td>
                        <td class="py-3 px-4 text-sm text-gray-600">## items</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Card</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Jane Smith</td>
                        <td class="py-3 px-4 text-sm font-medium text-green-600 text-right">₦#,###</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm text-gray-900">#TXN####</td>
                        <td class="py-3 px-4 text-sm text-gray-600">##:## AM</td>
                        <td class="py-3 px-4 text-sm text-gray-600">## items</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Mobile</td>
                        <td class="py-3 px-4 text-sm text-gray-600">Mike Johnson</td>
                        <td class="py-3 px-4 text-sm font-medium text-green-600 text-right">₦#,###</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function posReports() {
    return {
        filters: {
            reportType: 'daily_sales',
            dateRange: 'today',
            terminal: 'all',
            cashier: 'all'
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
