@extends('payroll.portal.layout')

@section('title', 'My Payslips')
@section('page-title', 'My Payslips')

@section('content')
<div class="space-y-6">
    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filter Payslips</h2>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" id="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                <select name="month" id="month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Months</option>
                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m => $monthName)
                        <option value="{{ $m + 1 }}" {{ request('month') == ($m + 1) ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Payslips Grid -->
    @if($payslips->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($payslips as $payslip)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                        <h3 class="text-white font-bold text-lg">
                            {{ $payslip->payrollPeriod->name ?? 'N/A' }}
                        </h3>
                        <p class="text-indigo-100 text-sm">
                            {{ $payslip->payrollPeriod->pay_date ? $payslip->payrollPeriod->pay_date->format('F Y') : 'N/A' }}
                        </p>
                    </div>

                    <!-- Content -->
                    <div class="p-6 space-y-4">
                        <!-- Pay Date -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                Pay Date
                            </span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $payslip->payrollPeriod->pay_date ? $payslip->payrollPeriod->pay_date->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>

                        <!-- Period -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                                Period
                            </span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $payslip->payrollPeriod->start_date ? $payslip->payrollPeriod->start_date->format('M d') : '' }} -
                                {{ $payslip->payrollPeriod->end_date ? $payslip->payrollPeriod->end_date->format('M d, Y') : '' }}
                            </span>
                        </div>

                        <!-- Gross Salary -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-700">Gross Salary</span>
                            <span class="text-sm font-bold text-green-600">
                                ₦{{ number_format($payslip->gross_salary ?? 0, 2) }}
                            </span>
                        </div>

                        <!-- Deductions -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Total Deductions</span>
                            <span class="text-sm font-bold text-red-600">
                                ₦{{ number_format($payslip->total_deductions ?? 0, 2) }}
                            </span>
                        </div>

                        <!-- Net Salary -->
                        <div class="flex items-center justify-between pt-2 border-t-2 border-indigo-200 bg-indigo-50 -mx-6 px-6 py-3">
                            <span class="text-base font-bold text-gray-900">Net Salary</span>
                            <span class="text-lg font-bold text-indigo-600">
                                ₦{{ number_format($payslip->net_salary ?? 0, 2) }}
                            </span>
                        </div>

                        <!-- Payment Status -->
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $payslip->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payslip->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payslip->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($payslip->payment_status ?? 'pending') }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 pb-6 flex gap-2">
                        <a href="{{ route('payroll.portal.payslip', ['tenant' => $tenant, 'token' => $token, 'payslip' => $payslip->id]) }}"
                           class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-center rounded-lg font-medium transition-colors">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('payroll.portal.payslip.download', ['tenant' => $tenant, 'token' => $token, 'payslip' => $payslip->id]) }}"
                           class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-center rounded-lg font-medium transition-colors">
                            <i class="fas fa-download mr-1"></i>PDF
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $payslips->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-invoice-dollar text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Payslips Found</h3>
            <p class="text-gray-600 mb-6">
                You don't have any payslips yet. Payslips will appear here once payroll is processed.
            </p>
            <a href="{{ route('payroll.portal.dashboard', ['tenant' => $tenant, 'token' => $token]) }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    @endif

    <!-- Summary Stats -->
    @if($payslips->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Payslips</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $payslips->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Paid Payslips</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $payslips->where('payment_status', 'paid')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pending Payment</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ $payslips->where('payment_status', 'pending')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
