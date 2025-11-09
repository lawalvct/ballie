@extends('layouts.tenant')

@section('title', 'Payslip - ' . $payrollRun->employee->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Employee Payslip</h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $payrollRun->payrollPeriod->name }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.payroll.processing.show', [$tenant, $payrollRun->payrollPeriod]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
            <a href="{{ route('tenant.payroll.payslips.download', [$tenant, $payrollRun->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-red-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m-7 10h10a2 2 0 002-2V9a2 2 0 00-2-2h-3m-3 3l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Download PDF
            </a>
        </div>
    </div>

    <!-- Payslip Card -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <!-- Company Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">{{ $tenant->name }}</h2>
                    <p class="text-indigo-100 mt-1">{{ $tenant->email }}</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-indigo-100">Payslip</div>
                    <div class="text-xl font-bold">{{ $payrollRun->payrollPeriod->name }}</div>
                </div>
            </div>
        </div>

        <!-- Employee Details -->
        <div class="px-8 py-6 border-b border-gray-200 bg-gray-50">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <div class="text-sm text-gray-500">Employee Name</div>
                    <div class="text-base font-semibold text-gray-900">{{ $payrollRun->employee->full_name }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Employee Number</div>
                    <div class="text-base font-semibold text-gray-900">{{ $payrollRun->employee->employee_number ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Department</div>
                    <div class="text-base font-semibold text-gray-900">{{ $payrollRun->employee->department->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Position</div>
                    <div class="text-base font-semibold text-gray-900">{{ $payrollRun->employee->job_title ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Pay Period Info -->
        <div class="px-8 py-4 border-b border-gray-200 bg-blue-50">
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <div class="text-sm text-gray-600">Period Start</div>
                    <div class="text-base font-medium text-gray-900">{{ $payrollRun->payrollPeriod->start_date->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Period End</div>
                    <div class="text-base font-medium text-gray-900">{{ $payrollRun->payrollPeriod->end_date->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-600">Pay Date</div>
                    <div class="text-base font-medium text-gray-900">{{ $payrollRun->payrollPeriod->pay_date->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Earnings -->
        <div class="px-8 py-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Earnings
            </h3>
            <div class="space-y-2">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-700">Basic Salary</span>
                    <span class="font-semibold text-gray-900">₦{{ number_format($payrollRun->basic_salary, 2) }}</span>
                </div>
                @foreach($payrollRun->details->where('component_type', 'earning') as $detail)
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-700">{{ $detail->component_name }}</span>
                    <span class="font-semibold text-gray-900">₦{{ number_format($detail->amount, 2) }}</span>
                </div>
                @endforeach
                <div class="flex items-center justify-between py-3 bg-green-50 px-3 rounded-lg mt-3">
                    <span class="font-semibold text-green-900">Gross Salary</span>
                    <span class="font-bold text-green-900 text-lg">₦{{ number_format($payrollRun->gross_salary, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Deductions -->
        <div class="px-8 py-6 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                </svg>
                Deductions
            </h3>
            <div class="space-y-2">
                @if($payrollRun->monthly_tax > 0)
                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-700">PAYE Tax</span>
                    <span class="font-semibold text-gray-900">₦{{ number_format($payrollRun->monthly_tax, 2) }}</span>
                </div>
                @endif
                @if($payrollRun->nsitf_contribution > 0)
                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-700">NSITF</span>
                    <span class="font-semibold text-gray-900">₦{{ number_format($payrollRun->nsitf_contribution, 2) }}</span>
                </div>
                @endif
                @foreach($payrollRun->details->where('component_type', 'deduction') as $detail)
                <div class="flex items-center justify-between py-2 border-b border-gray-200">
                    <span class="text-gray-700">{{ $detail->component_name }}</span>
                    <span class="font-semibold text-gray-900">₦{{ number_format($detail->amount, 2) }}</span>
                </div>
                @endforeach
                <div class="flex items-center justify-between py-3 bg-red-50 px-3 rounded-lg mt-3">
                    <span class="font-semibold text-red-900">Total Deductions</span>
                    <span class="font-bold text-red-900 text-lg">₦{{ number_format($payrollRun->total_deductions, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Net Pay -->
        <div class="px-8 py-6 bg-gradient-to-r from-indigo-50 to-blue-50 border-t-4 border-indigo-600">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-gray-600 mb-1">Net Pay</div>
                    <div class="text-3xl font-bold text-indigo-900">₦{{ number_format($payrollRun->net_salary, 2) }}</div>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                        @if($payrollRun->payment_status === 'paid') bg-green-100 text-green-800
                        @elseif($payrollRun->payment_status === 'failed') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($payrollRun->payment_status) }}
                    </div>
                    @if($payrollRun->paid_at)
                    <div class="text-xs text-gray-500 mt-1">Paid: {{ $payrollRun->paid_at->format('d M Y H:i') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tax Information -->
        @if($payrollRun->monthly_tax > 0)
        <div class="px-8 py-6 border-t border-gray-200">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Tax Information</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">Annual Gross</div>
                    <div class="font-semibold text-gray-900">₦{{ number_format($payrollRun->annual_gross, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Consolidated Relief</div>
                    <div class="font-semibold text-gray-900">₦{{ number_format($payrollRun->consolidated_relief, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Taxable Income</div>
                    <div class="font-semibold text-gray-900">₦{{ number_format($payrollRun->taxable_income, 2) }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Annual Tax</div>
                    <div class="font-semibold text-gray-900">₦{{ number_format($payrollRun->annual_tax, 2) }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Bank Details -->
        @if($payrollRun->employee->bank_name)
        <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Bank Details</h3>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">Bank Name</div>
                    <div class="font-semibold text-gray-900">{{ $payrollRun->employee->bank_name }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Account Number</div>
                    <div class="font-semibold text-gray-900">{{ $payrollRun->employee->account_number }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Account Name</div>
                    <div class="font-semibold text-gray-900">{{ $payrollRun->employee->account_name }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="px-8 py-4 bg-gray-100 text-center text-xs text-gray-500 border-t border-gray-200">
            This is a computer-generated payslip and does not require a signature.
            <br>
            Generated on {{ now()->format('d M Y H:i:s') }}
        </div>
    </div>
</div>
@endsection
