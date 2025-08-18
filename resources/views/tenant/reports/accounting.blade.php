@extends('layouts.tenant')

@section('title', 'Accounting Reports')
@section('page-title', 'Accounting Reports')
@section('page-description', 'Comprehensive accounting reports and ledger analysis.')

@section('content')
<div class="space-y-6" x-data="accountingReports()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <div class="bg-gradient-to-br from-slate-400 to-slate-600 p-2 rounded-lg shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Accounting Reports</h1>
                <p class="text-sm text-gray-500">Ledgers, vouchers, and accounting analysis</p>
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
                    <option value="trial_balance">Trial Balance</option>
                    <option value="general_ledger">General Ledger</option>
                    <option value="account_statements">Account Statements</option>
                    <option value="voucher_register">Voucher Register</option>
                    <option value="aging_reports">Aging Reports</option>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Group</label>
                <select x-model="filters.accountGroup" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Groups</option>
                    <option value="#">Assets</option>
                    <option value="#">Liabilities</option>
                    <option value="#">Equity</option>
                    <option value="#">Income</option>
                    <option value="#">Expenses</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Voucher Type</label>
                <select x-model="filters.voucherType" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Types</option>
                    <option value="#">Receipt</option>
                    <option value="#">Payment</option>
                    <option value="#">Journal</option>
                    <option value="#">Sales</option>
                    <option value="#">Purchase</option>
                </select>
            </div>
            <div class="flex items-end">
                <button @click="generateReport" class="w-full bg-gradient-to-r from-slate-600 to-slate-700 text-white px-4 py-2 rounded-xl hover:from-slate-700 hover:to-slate-800 transition-all duration-200">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Accounting Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Assets</p>
                    <p class="text-2xl font-bold text-green-600">₦##,###,###</p>
                    <p class="text-xs text-green-600">Current value</p>
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
                    <p class="text-sm font-medium text-gray-600">Total Liabilities</p>
                    <p class="text-2xl font-bold text-red-600">₦##,###,###</p>
                    <p class="text-xs text-red-600">Outstanding amount</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Vouchers</p>
                    <p class="text-2xl font-bold text-blue-600">#,###</p>
                    <p class="text-xs text-blue-600">This month</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Account Balance</p>
                    <p class="text-2xl font-bold text-purple-600">Match</p>
                    <p class="text-xs text-purple-600">Trial balance status</p>
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
        <!-- Trial Balance Summary -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Trial Balance Summary</h3>
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
                <div class="grid grid-cols-3 gap-4 text-sm font-medium text-gray-500 border-b pb-2">
                    <span>Account Group</span>
                    <span class="text-right">Debit</span>
                    <span class="text-right">Credit</span>
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <div class="grid grid-cols-3 gap-4 text-sm py-1">
                        <span class="text-gray-700">Assets</span>
                        <span class="text-right font-medium">₦##,###,###</span>
                        <span class="text-right">-</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm py-1">
                        <span class="text-gray-700">Liabilities</span>
                        <span class="text-right">-</span>
                        <span class="text-right font-medium">₦##,###,###</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm py-1">
                        <span class="text-gray-700">Equity</span>
                        <span class="text-right">-</span>
                        <span class="text-right font-medium">₦##,###,###</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm py-1">
                        <span class="text-gray-700">Income</span>
                        <span class="text-right">-</span>
                        <span class="text-right font-medium">₦##,###,###</span>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-sm py-1">
                        <span class="text-gray-700">Expenses</span>
                        <span class="text-right font-medium">₦##,###,###</span>
                        <span class="text-right">-</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-sm font-bold border-t pt-2">
                    <span>Total</span>
                    <span class="text-right">₦##,###,###</span>
                    <span class="text-right">₦##,###,###</span>
                </div>
            </div>
        </div>

        <!-- Ledger Activity -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Ledger Activity</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View General Ledger</a>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Cash at Bank</p>
                        <p class="text-xs text-gray-500">Voucher #V####</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600">₦##,###</p>
                        <p class="text-xs text-gray-500">Dr</p>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Sales Revenue</p>
                        <p class="text-xs text-gray-500">Voucher #V####</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-blue-600">₦##,###</p>
                        <p class="text-xs text-gray-500">Cr</p>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Office Expenses</p>
                        <p class="text-xs text-gray-500">Voucher #V####</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-red-600">₦##,###</p>
                        <p class="text-xs text-gray-500">Dr</p>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Accounts Payable</p>
                        <p class="text-xs text-gray-500">Voucher #V####</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-orange-600">₦##,###</p>
                        <p class="text-xs text-gray-500">Cr</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voucher Register -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Voucher Register (This Month)</h3>
                <div class="flex items-center space-x-2">
                    <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Receipt Vouchers</span>
                    <span class="font-semibold text-green-600">### (₦##,###,###)</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Payment Vouchers</span>
                    <span class="font-semibold text-red-600">### (₦##,###,###)</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Journal Vouchers</span>
                    <span class="font-semibold text-blue-600">### (₦##,###,###)</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Sales Vouchers</span>
                    <span class="font-semibold text-purple-600">### (₦##,###,###)</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600">Purchase Vouchers</span>
                    <span class="font-semibold text-orange-600">### (₦##,###,###)</span>
                </div>
                <div class="flex justify-between items-center py-2 border-t-2 border-gray-200 pt-4">
                    <span class="font-semibold text-gray-900">Total Vouchers</span>
                    <span class="font-bold text-green-600 text-lg">#,###</span>
                </div>
            </div>
        </div>

        <!-- Account Groups -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Account Groups Overview</h3>
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
                        <span class="text-sm text-gray-700">Assets</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## accounts</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Liabilities</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## accounts</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Equity</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## accounts</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Income</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## accounts</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm text-gray-700">Expenses</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium">## accounts</p>
                        <p class="text-xs text-gray-500">₦##,###,### total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function accountingReports() {
    return {
        filters: {
            reportType: 'trial_balance',
            dateRange: 'this_month',
            accountGroup: 'all',
            voucherType: 'all'
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
