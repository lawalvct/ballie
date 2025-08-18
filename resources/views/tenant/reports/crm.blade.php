@extends('layouts.tenant')

@section('title', 'CRM Reports')
@section('page-title', 'CRM Reports')
@section('page-description', 'Customer relationship management analytics and performance reports.')

@section('content')
<div class="space-y-6" x-data="crmReports()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <div class="bg-gradient-to-br from-pink-400 to-pink-600 p-2 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">CRM Reports</h1>
                <p class="text-sm text-gray-500">Customer analytics, sales performance, and relationship insights</p>
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
                    <option value="customer_overview">Customer Overview</option>
                    <option value="sales_performance">Sales Performance</option>
                    <option value="lead_conversion">Lead Conversion</option>
                    <option value="customer_lifetime">Customer Lifetime Value</option>
                    <option value="activity_summary">Activity Summary</option>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Customer Segment</label>
                <select x-model="filters.segment" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Customers</option>
                    <option value="new">New Customers</option>
                    <option value="returning">Returning Customers</option>
                    <option value="vip">VIP Customers</option>
                    <option value="inactive">Inactive Customers</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sales Rep</label>
                <select x-model="filters.salesRep" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Sales Reps</option>
                    <option value="#">John Doe</option>
                    <option value="#">Jane Smith</option>
                    <option value="#">Mike Johnson</option>
                </select>
            </div>
            <div class="flex items-end">
                <button @click="generateReport" class="w-full bg-gradient-to-r from-pink-600 to-pink-700 text-white px-4 py-2 rounded-xl hover:from-pink-700 hover:to-pink-800 transition-all duration-200">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- CRM Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Customers</p>
                    <p class="text-2xl font-bold text-blue-600">#,###</p>
                    <p class="text-xs text-blue-600">+## this month</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Leads</p>
                    <p class="text-2xl font-bold text-yellow-600">###</p>
                    <p class="text-xs text-yellow-600">## this week</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                    <p class="text-2xl font-bold text-green-600">##.#%</p>
                    <p class="text-xs text-green-600">+#.#% from last month</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Customer LTV</p>
                    <p class="text-2xl font-bold text-purple-600">₦###,###</p>
                    <p class="text-xs text-purple-600">Average lifetime value</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Customer Acquisition -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Customer Acquisition</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Details</a>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">This Month</span>
                    <span class="font-semibold text-green-600">## customers</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Last Month</span>
                    <span class="font-semibold text-gray-600">## customers</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Growth Rate</span>
                    <span class="font-semibold text-green-600">+##.#%</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Acquisition Cost</span>
                    <span class="font-semibold text-blue-600">₦##,###</span>
                </div>
            </div>
        </div>

        <!-- Lead Performance -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Lead Performance</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Pipeline</a>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">New Leads</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 70%"></div>
                        </div>
                        <span class="text-sm font-medium">## leads</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Qualified</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 50%"></div>
                        </div>
                        <span class="text-sm font-medium">## leads</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Proposal</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: 30%"></div>
                        </div>
                        <span class="text-sm font-medium">## leads</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Closed Won</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-20 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 20%"></div>
                        </div>
                        <span class="text-sm font-medium">## leads</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Top Customers by Revenue</h3>
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
                            <p class="text-sm font-medium text-gray-900">Customer Name A</p>
                            <p class="text-xs text-gray-500">## orders this month</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦###,###</p>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-green-600">2</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Customer Name B</p>
                            <p class="text-xs text-gray-500">## orders this month</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦###,###</p>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <span class="text-xs font-bold text-yellow-600">3</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Customer Name C</p>
                            <p class="text-xs text-gray-500">## orders this month</p>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-green-600">₦###,###</p>
                </div>
            </div>
        </div>

        <!-- Sales Rep Performance -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Sales Rep Performance</h3>
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
                    <span>Rep</span>
                    <span class="text-right">Leads</span>
                    <span class="text-right">Closed</span>
                    <span class="text-right">Revenue</span>
                </div>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">John Doe</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                    </div>
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">Jane Smith</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                    </div>
                    <div class="grid grid-cols-4 gap-4 text-sm py-2 hover:bg-gray-50 rounded-lg px-2">
                        <span class="text-gray-700">Mike Johnson</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium">##</span>
                        <span class="text-right font-medium text-green-600">₦##,###</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent CRM Activities</h3>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Activities</a>
        </div>
        <div class="space-y-4">
            <div class="flex items-start space-x-4">
                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900">New lead <span class="font-medium">Lead Name</span> added by <span class="font-medium">John Doe</span></p>
                    <p class="text-xs text-gray-500">2 hours ago</p>
                </div>
            </div>
            <div class="flex items-start space-x-4">
                <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900">Deal <span class="font-medium">Deal Name</span> marked as won - ₦##,###</p>
                    <p class="text-xs text-gray-500">4 hours ago</p>
                </div>
            </div>
            <div class="flex items-start space-x-4">
                <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900">Follow-up scheduled with <span class="font-medium">Customer Name</span></p>
                    <p class="text-xs text-gray-500">6 hours ago</p>
                </div>
            </div>
            <div class="flex items-start space-x-4">
                <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900">New customer <span class="font-medium">Customer Name</span> registered</p>
                    <p class="text-xs text-gray-500">8 hours ago</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function crmReports() {
    return {
        filters: {
            reportType: 'customer_overview',
            dateRange: 'this_month',
            segment: 'all',
            salesRep: 'all'
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
