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
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Profit & Loss Statement</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Balance Sheet</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Cash Flow Statement</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Trial Balance</a>
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
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Sales Summary</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Customer Sales Report</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Product Sales Report</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Sales by Period</a>
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
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Stock Summary</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Low Stock Alert</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Stock Valuation</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Stock Movement</a>
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
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Payroll Summary</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Tax Report</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Department Analysis</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Bank Schedule</a>
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
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Customer Overview</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Lead Conversion</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Sales Performance</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Activity Summary</a>
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
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Daily Sales</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Product Performance</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Payment Methods</a>
                <a href="#" class="block text-sm text-blue-600 hover:text-blue-800">• Cashier Performance</a>
            </div>
        </div>
    </div>

    <!-- Quick Report Generation -->
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Quick Report Generation</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                    <select class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option>Profit & Loss</option>
                        <option>Sales Summary</option>
                        <option>Customer Report</option>
                        <option>Inventory Report</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Quarter</option>
                        <option>This Year</option>
                        <option>Custom Range</option>
                    </select>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <select class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option>PDF</option>
                        <option>Excel</option>
                        <option>CSV</option>
                    </select>
                </div>
                <div class="flex space-x-3 pt-6">
                    <button class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                        Generate Report
                    </button>
                    <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        Preview
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
