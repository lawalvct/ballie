@extends('payroll.portal.layout')

@section('title', 'Payslip Details')
@section('page-title', 'Payslip Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('payroll.portal.payslips', ['tenant' => $tenant, 'token' => $token]) }}"
           class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Payslips
        </a>
    </div>

    <!-- Payslip Header -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Payslip</h2>
                    <p class="text-indigo-100 mt-1">
                        {{ $payslip->payrollPeriod->name ?? 'N/A' }} •
                        {{ \Carbon\Carbon::parse($payslip->payment_date)->format('F Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-indigo-100">Payment Date</p>
                    <p class="text-lg font-bold">{{ \Carbon\Carbon::parse($payslip->payment_date)->format('d M, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Employee & Company Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 border-b border-gray-200">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Employee Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Name:</span>
                        <span class="font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Employee ID:</span>
                        <span class="font-medium text-gray-900">{{ $employee->employee_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Department:</span>
                        <span class="font-medium text-gray-900">{{ $employee->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Position:</span>
                        <span class="font-medium text-gray-900">{{ $employee->job_title }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Company Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Company:</span>
                        <span class="font-medium text-gray-900">{{ $tenant->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Period:</span>
                        <span class="font-medium text-gray-900">{{ $payslip->payrollPeriod->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Status:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $payslip->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($payslip->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            <!-- Earnings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-green-600 mr-2"></i>
                    Earnings
                </h3>
                <div class="bg-green-50 rounded-lg p-4 space-y-3">
                    @php
                        $earnings = $payslip->details->where('type', 'earning');
                        $totalEarnings = $earnings->sum('amount');
                    @endphp

                    @forelse($earnings as $earning)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700">{{ $earning->salaryComponent->name ?? $earning->description }}</span>
                            <span class="font-semibold text-gray-900">₦{{ number_format($earning->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No earnings recorded</p>
                    @endforelse

                    <div class="border-t border-green-200 pt-3 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-900">Total Earnings</span>
                            <span class="font-bold text-green-600 text-lg">₦{{ number_format($totalEarnings, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-minus-circle text-red-600 mr-2"></i>
                    Deductions
                </h3>
                <div class="bg-red-50 rounded-lg p-4 space-y-3">
                    @php
                        $deductions = $payslip->details->where('type', 'deduction');
                        $totalDeductions = $deductions->sum('amount');
                    @endphp

                    @forelse($deductions as $deduction)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700">{{ $deduction->salaryComponent->name ?? $deduction->description }}</span>
                            <span class="font-semibold text-gray-900">₦{{ number_format($deduction->amount, 2) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No deductions recorded</p>
                    @endforelse

                    <div class="border-t border-red-200 pt-3 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-900">Total Deductions</span>
                            <span class="font-bold text-red-600 text-lg">₦{{ number_format($totalDeductions, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 border-t border-gray-200">
            <div class="max-w-md mx-auto space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-700">Gross Salary:</span>
                    <span class="font-semibold text-gray-900">₦{{ number_format($payslip->gross_salary, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-700">Total Deductions:</span>
                    <span class="font-semibold text-red-600">-₦{{ number_format($totalDeductions, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-700">Tax (PAYE):</span>
                    <span class="font-semibold text-red-600">-₦{{ number_format($payslip->monthly_tax, 2) }}</span>
                </div>
                <div class="border-t-2 border-indigo-200 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Net Salary:</span>
                        <span class="text-2xl font-bold text-indigo-600">₦{{ number_format($payslip->net_salary, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
            <p class="text-xs text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                This payslip is generated automatically and does not require a signature.
            </p>
            <div class="flex gap-3">
                <button onclick="window.print()"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors text-sm">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <a href="{{ route('payroll.portal.payslip.download', ['tenant' => $tenant, 'token' => $token, 'payslip' => $payslip->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors text-sm">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Tax & Statutory Information -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-file-invoice text-indigo-600 mr-2"></i>
            Tax & Statutory Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">PAYE Tax</p>
                <p class="text-xl font-bold text-gray-900">₦{{ number_format($payslip->monthly_tax, 2) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Pension Contribution</p>
                <p class="text-xl font-bold text-gray-900">
                    ₦{{ number_format($payslip->details->where('description', 'like', '%Pension%')->sum('amount'), 2) }}
                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Tax Relief</p>
                <p class="text-xl font-bold text-gray-900">₦{{ number_format($employee->annual_relief ?? 200000, 2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .bg-white.rounded-xl, .bg-white.rounded-xl * {
            visibility: visible;
        }
        .bg-white.rounded-xl {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush
