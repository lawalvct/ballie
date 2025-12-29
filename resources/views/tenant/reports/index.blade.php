@extends('layouts.tenant')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('page-description', 'Generate and view business reports and analytics.')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <div class="bg-gradient-to-br from-purple-400 to-purple-600 p-2 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Business Reports</h1>
                <p class="text-sm text-gray-500">Generate insights and analytics for your business</p>
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Financial Reports -->
        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Financial Reports</h3>
                    <p class="text-sm text-gray-500">Revenue, expenses, and profit analysis</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('tenant.reports.profit-loss', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Profit & Loss Statement</a>
                <a href="{{ route('tenant.reports.balance-sheet', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Balance Sheet</a>
                <a href="{{ route('tenant.reports.cash-flow', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Cash Flow Statement</a>
                <a href="{{ route('tenant.reports.trial-balance', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Trial Balance</a>
            </div>
        </div>

        <!-- Sales Reports -->
        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Sales Reports</h3>
                    <p class="text-sm text-gray-500">Sales performance and trends</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('tenant.reports.sales-summary', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Sales Summary</a>
                <a href="{{ route('tenant.reports.customer-sales', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Customer Sales Report</a>
                <a href="{{ route('tenant.reports.product-sales', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Product Sales Report</a>
                <a href="{{ route('tenant.reports.sales-by-period', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Sales by Period</a>
            </div>
        </div>

        <!-- Purchase Reports -->
        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Purchase Reports</h3>
                    <p class="text-sm text-gray-500">Purchase performance and trends</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('tenant.reports.purchase-summary', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Purchase Summary</a>
                <a href="{{ route('tenant.reports.vendor-purchases', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Vendor Purchase Report</a>
                <a href="{{ route('tenant.reports.product-purchases', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Product Purchase Report</a>
                <a href="{{ route('tenant.reports.purchases-by-period', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Purchases by Period</a>
            </div>
        </div>

        <!-- Inventory Reports -->
        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Inventory Reports</h3>
                    <p class="text-sm text-gray-500">Stock levels and inventory analysis</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('tenant.reports.stock-summary', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Stock Summary</a>
                <a href="{{ route('tenant.reports.low-stock-alert', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Low Stock Alert</a>
                <a href="{{ route('tenant.reports.stock-valuation', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Stock Valuation</a>
                <a href="{{ route('tenant.reports.stock-movement', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Stock Movement</a>
            </div>
        </div>

        <!-- Payroll Reports -->
        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Payroll Reports</h3>
                    <p class="text-sm text-gray-500">Employee payroll and tax analysis</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('tenant.payroll.reports.summary', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Payroll Summary</a>
                <a href="{{ route('tenant.payroll.reports.tax-report', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Tax Report</a>
                <a href="{{ route('tenant.payroll.reports.employee-summary', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Employee Summary</a>
                <a href="{{ route('tenant.payroll.reports.bank-schedule', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Bank Schedule</a>
            </div>
        </div>

        <!-- CRM Reports -->
        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-pink-400 to-pink-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">CRM Reports</h3>
                    <p class="text-sm text-gray-500">Customer analytics and performance</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('tenant.crm.customers.statements', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Customer Statements</a>
                <a href="{{ route('tenant.crm.payment-reports', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Payment Reports</a>
                <a href="{{ route('tenant.reports.customer-sales', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Customer Sales Analysis</a>
                <a href="{{ route('tenant.crm.activities.index', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Activity Summary</a>
            </div>
        </div>

        <!-- POS Reports -->
        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">POS Reports</h3>
                    <p class="text-sm text-gray-500">Point of sale analytics and trends</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('tenant.pos.reports.daily-sales', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Daily Sales</a>
                <a href="{{ route('tenant.pos.reports.top-products', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Product Performance</a>
                <a href="{{ route('tenant.pos.transactions', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Transaction History</a>
                <a href="{{ route('tenant.pos.reports', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• POS Overview</a>
            </div>
        </div>

        <!-- E-commerce Reports -->
        <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">E-commerce Reports</h3>
                    <p class="text-sm text-gray-500">Online store analytics</p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('tenant.ecommerce.reports.orders', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Order Reports</a>
                <a href="{{ route('tenant.ecommerce.reports.revenue', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Revenue Analysis</a>
                <a href="{{ route('tenant.ecommerce.reports.products', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Product Performance</a>
                <a href="{{ route('tenant.ecommerce.reports.customers', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Customer Analytics</a>
                <a href="{{ route('tenant.ecommerce.reports.abandoned-carts', $tenant->slug) }}" class="block text-sm text-blue-600 hover:text-blue-800 hover:underline">• Abandoned Carts</a>
            </div>
        </div>
    </div>
</div>
@endsection
