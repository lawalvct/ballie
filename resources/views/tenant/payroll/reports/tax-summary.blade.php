@extends('layouts.tenant')

@section('title', 'Tax Summary Report - ' . $tenant->name)
@section('page-title', 'Tax Summary Report')
@section('page-description', 'Aggregated tax information and analysis')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Tax Summary Report</h2>
            <p class="mt-1 text-sm text-gray-600">Tax withholding and liability overview</p>
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
            <form method="GET" action="{{ route('tenant.payroll.reports.tax-summary', $tenant) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
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
                    <a href="{{ route('tenant.payroll.reports.tax-summary', $tenant) }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-rose-50 to-red-50 rounded-lg shadow-sm border border-rose-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-rose-600">Total Tax Withheld</p>
                    <p class="text-3xl font-bold text-rose-900 mt-2">₦{{ number_format($summary['total_tax'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg shadow-sm border border-purple-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600">Total Gross Pay</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">₦{{ number_format($summary['total_gross'], 2) }}</p>
                </div>
                <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg shadow-sm border border-blue-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Average Tax Rate</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2">{{ number_format($summary['average_tax_rate'], 2) }}%</p>
                </div>
                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-sm border border-green-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600">Employees</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ number_format($summary['total_employees']) }}</p>
                </div>
                <svg class="w-12 h-12 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    @if($monthlyData->count() > 0)
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-rose-50 to-pink-50">
            <h3 class="text-lg font-semibold text-gray-900">Monthly Tax Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Withheld</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($monthlyData as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($data['month'] . '-01')->format('F Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                            {{ number_format($data['employees']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            ₦{{ number_format($data['gross'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-rose-600 text-right">
                            ₦{{ number_format($data['tax'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            ₦{{ number_format($data['net'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                            {{ $data['gross'] > 0 ? number_format(($data['tax'] / $data['gross']) * 100, 2) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Department Breakdown -->
    @if($departmentData->count() > 0)
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
            <h3 class="text-lg font-semibold text-gray-900">Tax by Department</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Pay</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tax Withheld</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Tax per Employee</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departmentData->sortByDesc('tax') as $deptName => $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $deptName }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                            {{ number_format($data['employees']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            ₦{{ number_format($data['gross'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-rose-600 text-right">
                            ₦{{ number_format($data['tax'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-right">
                            ₦{{ number_format($data['employees'] > 0 ? $data['tax'] / $data['employees'] : 0, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
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
}
</style>
@endsection
