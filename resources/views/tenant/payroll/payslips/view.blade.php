@extends('layouts.tenant')

@section('title', 'Payslip - ' . $payrollRun->employee->full_name)
@section('page-title', 'Employee Payslip')
@section('page-description', $payrollRun->payrollPeriod->name . ' - ' . $payrollRun->employee->full_name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    @if($payrollRun->payment_status === 'paid') bg-green-100 text-green-800
                    @elseif($payrollRun->payment_status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($payrollRun->payment_status ?? 'pending') }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('tenant.payroll.processing.show', [$tenant, $payrollRun->payrollPeriod]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
            @if($payrollRun->payment_status !== 'paid')
            <button onclick="markPayslipAsPaid()" class="inline-flex items-center px-4 py-2 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Mark as Paid
            </button>
            @endif
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            <a href="{{ route('tenant.payroll.payslips.download', [$tenant, $payrollRun->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </a>
        </div>
    </div>

    <!-- Payslip Card -->
    <div class="bg-white shadow-lg rounded-2xl border border-gray-200 overflow-hidden">
        <!-- Company Header -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 sm:px-8 py-8 text-white">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <h2 class="text-2xl sm:text-3xl font-bold">{{ $tenant->name }}</h2>
                    <p class="text-blue-100 mt-2 text-sm">{{ $tenant->email }}</p>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-sm text-blue-100 uppercase tracking-wide">Payslip</div>
                    <div class="text-xl sm:text-2xl font-bold mt-1">{{ $payrollRun->payrollPeriod->name }}</div>
                </div>
            </div>
        </div>

        <!-- Employee Details -->
        <div class="px-6 sm:px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Employee Name</div>
                    <div class="text-base font-bold text-gray-900">{{ $payrollRun->employee->full_name }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Employee Number</div>
                    <div class="text-base font-bold text-gray-900">{{ $payrollRun->employee->employee_number ?? 'N/A' }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Department</div>
                    <div class="text-base font-bold text-gray-900">{{ $payrollRun->employee->department->name ?? 'N/A' }}</div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Position</div>
                    <div class="text-base font-bold text-gray-900">{{ $payrollRun->employee->job_title ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Pay Period Info -->
        <div class="px-6 sm:px-8 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 uppercase tracking-wide">Period Start</div>
                        <div class="text-sm font-bold text-gray-900">{{ $payrollRun->payrollPeriod->start_date->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 uppercase tracking-wide">Period End</div>
                        <div class="text-sm font-bold text-gray-900">{{ $payrollRun->payrollPeriod->end_date->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 uppercase tracking-wide">Pay Date</div>
                        <div class="text-sm font-bold text-gray-900">{{ $payrollRun->payrollPeriod->pay_date->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">
            <!-- Earnings -->
            <div class="px-6 sm:px-8 py-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    Earnings
                </h3>
                <div class="space-y-1">
                    <div class="flex items-center justify-between py-3 hover:bg-gray-50 px-2 rounded transition-colors">
                        <span class="text-sm text-gray-700">Basic Salary</span>
                        <span class="font-semibold text-gray-900">₦{{ number_format($payrollRun->basic_salary, 2) }}</span>
                    </div>
                    @foreach($payrollRun->details->where('component_type', 'earning') as $detail)
                    <div class="flex items-center justify-between py-3 hover:bg-gray-50 px-2 rounded transition-colors">
                        <span class="text-sm text-gray-700">{{ $detail->component_name }}</span>
                        <span class="font-semibold text-gray-900">₦{{ number_format($detail->amount, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between py-4 bg-gradient-to-r from-green-50 to-emerald-50 px-4 rounded-xl mt-4 border-2 border-green-200">
                    <span class="font-bold text-green-900">Gross Salary</span>
                    <span class="font-bold text-green-900 text-xl">₦{{ number_format($payrollRun->gross_salary, 2) }}</span>
                </div>
            </div>

            <!-- Deductions -->
            <div class="px-6 sm:px-8 py-6 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </div>
                    Deductions
                </h3>
                <div class="space-y-1">
                    @if($payrollRun->monthly_tax > 0)
                    <div class="flex items-center justify-between py-3 hover:bg-white px-2 rounded transition-colors">
                        <span class="text-sm text-gray-700">PAYE Tax</span>
                        <span class="font-semibold text-gray-900">₦{{ number_format($payrollRun->monthly_tax, 2) }}</span>
                    </div>
                    @endif
                    @if($payrollRun->nsitf_contribution > 0)
                    <div class="flex items-center justify-between py-3 hover:bg-white px-2 rounded transition-colors">
                        <span class="text-sm text-gray-700">NSITF</span>
                        <span class="font-semibold text-gray-900">₦{{ number_format($payrollRun->nsitf_contribution, 2) }}</span>
                    </div>
                    @endif
                    @foreach($payrollRun->details->where('component_type', 'deduction') as $detail)
                    <div class="flex items-center justify-between py-3 hover:bg-white px-2 rounded transition-colors">
                        <span class="text-sm text-gray-700">{{ $detail->component_name }}</span>
                        <span class="font-semibold text-gray-900">₦{{ number_format($detail->amount, 2) }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between py-4 bg-gradient-to-r from-red-50 to-rose-50 px-4 rounded-xl mt-4 border-2 border-red-200">
                    <span class="font-bold text-red-900">Total Deductions</span>
                    <span class="font-bold text-red-900 text-xl">₦{{ number_format($payrollRun->total_deductions, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Net Pay -->
        <div class="px-6 sm:px-8 py-8 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 border-t-4 border-yellow-400">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="text-sm text-blue-100 uppercase tracking-wide mb-2">Net Pay (Take Home)</div>
                    <div class="text-4xl sm:text-5xl font-bold text-white">₦{{ number_format($payrollRun->net_salary, 2) }}</div>
                </div>
                <div class="text-left sm:text-right">
                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold shadow-lg
                        @if($payrollRun->payment_status === 'paid') bg-green-500 text-white
                        @elseif($payrollRun->payment_status === 'pending') bg-yellow-500 text-white
                        @else bg-gray-500 text-white @endif">
                        {{ ucfirst($payrollRun->payment_status ?? 'pending') }}
                    </div>
                    @if($payrollRun->paid_at)
                    <div class="text-xs text-blue-100 mt-2">Paid: {{ $payrollRun->paid_at->format('d M Y H:i') }}</div>
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

<script>
function markPayslipAsPaid() {
    if (confirm('Are you sure you want to mark this payslip as paid? This action cannot be undone easily.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('tenant.payroll.payslips.mark-paid', [$tenant, $payrollRun->id]) }}';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
