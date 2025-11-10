@extends('layouts.tenant')

@section('title', 'Detailed Payroll Report - ' . $tenant->name)
@section('page-title', 'Detailed Payroll Report')
@section('page-description', 'Comprehensive payroll breakdown')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Detailed Payroll Report</h2>
            <p class="mt-1 text-sm text-gray-600">Complete payroll information with all components</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Report
            </button>
            <a href="{{ route('tenant.payroll.index', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Payroll
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <form method="GET" action="{{ route('tenant.payroll.reports.detailed', $tenant) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <select id="year" name="year"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                    <select id="month" name="month"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('tenant.payroll.reports.detailed', $tenant) }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Employees</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($totals['employees']) }}</p>
                </div>
                <svg class="w-12 h-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Gross</p>
                    <p class="text-2xl font-bold mt-1">₦{{ number_format($totals['gross'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm font-medium">Basic Pay</p>
                    <p class="text-2xl font-bold mt-1">₦{{ number_format($totals['basic'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-cyan-100 text-sm font-medium">Allowances</p>
                    <p class="text-2xl font-bold mt-1">₦{{ number_format($totals['allowances'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Deductions</p>
                    <p class="text-2xl font-bold mt-1">₦{{ number_format($totals['deductions'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Net Pay</p>
                    <p class="text-2xl font-bold mt-1">₦{{ number_format($totals['net'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Detailed Payroll Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Detailed Payroll Records</h3>
        </div>

        @if($payrollRuns->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Date</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Basic</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Allowances</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payrollRuns as $run)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $run->employee->full_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $run->employee->employee_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $run->employee->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div>{{ \Carbon\Carbon::parse($run->payrollPeriod->start_date)->format('M d') }}</div>
                                <div class="text-xs text-gray-400">to {{ \Carbon\Carbon::parse($run->payrollPeriod->end_date)->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($run->payrollPeriod->pay_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₦{{ number_format($run->basic_salary, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                                ₦{{ number_format($run->total_allowances, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-purple-600 text-right">
                                ₦{{ number_format($run->gross_pay, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                                ₦{{ number_format($run->total_deductions, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                                ₦{{ number_format($run->tax_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600 text-right">
                                ₦{{ number_format($run->net_pay, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm font-bold text-gray-900">
                                TOTALS ({{ number_format($totals['employees']) }} Employees)
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                ₦{{ number_format($totals['basic'], 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                ₦{{ number_format($totals['allowances'], 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-purple-600 text-right">
                                ₦{{ number_format($totals['gross'], 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                ₦{{ number_format($totals['deductions'], 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">
                                ₦{{ number_format($totals['tax'], 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600 text-right">
                                ₦{{ number_format($totals['net'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Payroll Records Found</h3>
                <p class="text-gray-500">No payroll records match your current filters.</p>
            </div>
        @endif
    </div>
</div>

<style>
@media print {
    .no-print, button, a {
        display: none !important;
    }
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
    .bg-gradient-to-br {
        background: linear-gradient(to bottom right, var(--tw-gradient-stops)) !important;
    }
}
</style>
@endsection
