@extends('layouts.tenant')

@section('title', 'Payroll Reports')
@section('page-title', 'Payroll Reports')
@section('page-description', 'Employee payroll analysis, tax reports, and salary summaries.')

@section('content')
<div class="space-y-6" x-data="payrollReports()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <div class="bg-gradient-to-br from-indigo-400 to-indigo-600 p-2 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payroll Reports</h1>
                <p class="text-sm text-gray-500">Employee payroll analysis, tax reports, and salary summaries</p>
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
                    <option value="summary">Payroll Summary</option>
                    <option value="detailed">Detailed Report</option>
                    <option value="tax_report">Tax Report</option>
                    <option value="bank_schedule">Bank Schedule</option>
                    <option value="department_analysis">Department Analysis</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                <select x-model="filters.period" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="current">Current Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_quarter">This Quarter</option>
                    <option value="this_year">This Year</option>
                    <option value="custom">Custom Period</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select x-model="filters.department" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Departments</option>
                    <option value="#">Administration</option>
                    <option value="#">Human Resources</option>
                    <option value="#">Finance & Accounting</option>
                    <option value="#">Operations</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Employee Status</label>
                <select x-model="filters.status" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Employees</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="terminated">Terminated</option>
                </select>
            </div>
            <div class="flex items-end">
                <button @click="generateReport" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-4 py-2 rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Payroll Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-blue-600">###</p>
                    <p class="text-xs text-blue-600">## active this month</p>
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
                    <p class="text-sm font-medium text-gray-600">Total Gross Pay</p>
                    <p class="text-2xl font-bold text-green-600">₦##,###,###</p>
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
                    <p class="text-sm font-medium text-gray-600">Total Deductions</p>
                    <p class="text-2xl font-bold text-red-600">₦##,###,###</p>
                    <p class="text-xs text-red-600">Tax, pension, etc.</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Net Pay</p>
                    <p class="text-2xl font-bold text-purple-600">₦##,###,###</p>
                    <p class="text-xs text-purple-600">To be paid out</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Payroll Summary -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Current Month Summary</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Detailed</a>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Basic Salaries</span>
                    <span class="font-semibold text-green-600">₦##,###,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Allowances</span>
                    <span class="font-semibold text-green-600">₦##,###,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">PAYE Tax</span>
                    <span class="font-semibold text-red-600">₦##,###,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Pension Contributions</span>
                    <span class="font-semibold text-red-600">₦##,###,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Other Deductions</span>
                    <span class="font-semibold text-red-600">₦##,###,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-t-2 border-gray-200 pt-4">
                    <span class="font-semibold text-gray-900">Net Payroll</span>
                    <span class="font-bold text-green-600 text-lg">₦##,###,###</span>
                </div>
            </div>
        </div>

        <!-- Department Breakdown -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Department Breakdown</h3>
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
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Administration</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## employees</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Finance & Accounting</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## employees</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Operations</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## employees</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Sales & Marketing</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## employees</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Summary -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Tax & Statutory Deductions</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Generate Tax Report</a>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">PAYE Tax</span>
                    <span class="font-semibold text-red-600">₦##,###,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Employee Pension (8%)</span>
                    <span class="font-semibold text-red-600">₦##,###,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Employer Pension (10%)</span>
                    <span class="font-semibold text-red-600">₦##,###,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">NSITF (1%)</span>
                    <span class="font-semibold text-red-600">₦##,###</span>
                </div>
                <div class="flex justify-between items-center py-2 border-t-2 border-gray-200 pt-4">
                    <span class="font-semibold text-gray-900">Total Tax & Statutory</span>
                    <span class="font-bold text-red-600 text-lg">₦##,###,###</span>
                </div>
            </div>
        </div>

        <!-- Recent Payroll Runs -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Payroll Runs</h3>
                <div class="flex items-center space-x-2">
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">January 2025 Payroll</p>
                        <p class="text-xs text-gray-500">Processed on DD/MM/YYYY</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600">₦##,###,###</p>
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Completed</span>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">December 2024 Payroll</p>
                        <p class="text-xs text-gray-500">Processed on DD/MM/YYYY</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600">₦##,###,###</p>
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Completed</span>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">November 2024 Payroll</p>
                        <p class="text-xs text-gray-500">Processed on DD/MM/YYYY</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600">₦##,###,###</p>
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Completed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function payrollReports() {
    return {
        filters: {
            reportType: 'summary',
            period: 'current',
            department: 'all',
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
