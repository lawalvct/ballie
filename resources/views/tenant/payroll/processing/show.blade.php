@extends('tenant.layouts.app')

@section('title', 'Payroll Period Details')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">{{ $period->name }}</h1>
                    <p class="text-cyan-100 text-lg">
                        {{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}
                        (Pay Date: {{ $period->pay_date->format('M d, Y') }})
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium
                        {{ $period->status === 'draft' ? 'bg-gray-500/20 text-gray-100' :
                           ($period->status === 'processing' ? 'bg-yellow-500/20 text-yellow-100' :
                           ($period->status === 'completed' ? 'bg-green-500/20 text-green-100' :
                           ($period->status === 'approved' ? 'bg-blue-500/20 text-blue-100' : 'bg-red-500/20 text-red-100'))) }}">
                        <span class="w-2 h-2 rounded-full mr-2
                            {{ $period->status === 'draft' ? 'bg-gray-400' :
                               ($period->status === 'processing' ? 'bg-yellow-400' :
                               ($period->status === 'completed' ? 'bg-green-400' :
                               ($period->status === 'approved' ? 'bg-blue-400' : 'bg-red-400'))) }}"></span>
                        {{ ucfirst($period->status) }}
                    </span>
                    <a href="{{ route('tenant.payroll.processing.index', $tenant) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Processing
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg mb-8">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-8">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Payroll Summary -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Payroll Summary</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-users text-blue-500 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-600">Employees</p>
                                    <p class="text-xl font-bold text-blue-900">{{ $period->payrollRuns->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-money-bill-wave text-green-500 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-green-600">Gross Salary</p>
                                    <p class="text-xl font-bold text-green-900">₦{{ number_format($period->total_gross_salary ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-minus-circle text-red-500 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-red-600">Deductions</p>
                                    <p class="text-xl font-bold text-red-900">₦{{ number_format($period->total_deductions ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-hand-holding-usd text-purple-500 text-2xl mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-purple-600">Net Salary</p>
                                    <p class="text-xl font-bold text-purple-900">₦{{ number_format($period->total_net_salary ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Payroll Details -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900">Employee Payroll Details</h3>
                        <p class="text-gray-600 mt-1">Individual payroll calculations for this period</p>
                    </div>

                    @if($period->payrollRuns && $period->payrollRuns->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowances</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($period->payrollRuns as $run)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                                        <i class="fas fa-user text-gray-500"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $run->employee->first_name }} {{ $run->employee->last_name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $run->employee->employee_number }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $run->employee->department->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                ₦{{ number_format($run->basic_salary, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                                ₦{{ number_format($run->total_allowances, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                                ₦{{ number_format($run->total_deductions, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600">
                                                ₦{{ number_format($run->monthly_tax, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-600">
                                                ₦{{ number_format($run->net_salary, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-2">
                                                    <button onclick="viewPayslip({{ $run->id }})"
                                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                            title="View Payslip">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button onclick="downloadPayslip({{ $run->id }})"
                                                            class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                                            title="Download Payslip">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button onclick="emailPayslip({{ $run->id }})"
                                                            class="text-purple-600 hover:text-purple-900 transition-colors duration-200"
                                                            title="Email Payslip">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <i class="fas fa-calculator text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">No payroll calculated yet</h3>
                            <p class="text-gray-500 mb-6">Generate payroll calculations for all active employees.</p>
                            @if($period->status === 'draft')
                                <button onclick="generatePayroll()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                                    <i class="fas fa-play mr-2"></i>Generate Payroll
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Period Information -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Period Information</h3>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Type</p>
                            <p class="text-gray-900 capitalize">{{ $period->type }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created By</p>
                            <p class="text-gray-900">{{ $period->createdBy->name ?? 'System' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created On</p>
                            <p class="text-gray-900">{{ $period->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        @if($period->approved_by)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Approved By</p>
                                <p class="text-gray-900">{{ $period->approvedBy->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Approved On</p>
                                <p class="text-gray-900">{{ $period->approved_at->format('M d, Y g:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>

                    <div class="space-y-3">
                        @if($period->status === 'draft')
                            <button onclick="generatePayroll()"
                                    class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                                <i class="fas fa-play mr-2"></i>Generate Payroll
                            </button>
                        @endif

                        @if($period->status === 'completed')
                            <button onclick="approvePayroll()"
                                    class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                                <i class="fas fa-check mr-2"></i>Approve Payroll
                            </button>
                        @endif

                        @if($period->status === 'approved')
                            <a href="{{ route('tenant.payroll.processing.export-bank-file', [$tenant, $period]) }}"
                               class="block w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                                <i class="fas fa-university mr-2"></i>Export Bank File
                            </a>
                        @endif

                        <button onclick="exportPayrollReport()"
                                class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-file-export mr-2"></i>Export Report
                        </button>

                        <button onclick="printPayrollSummary()"
                                class="block w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-print mr-2"></i>Print Summary
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generatePayroll() {
    if (confirm('Are you sure you want to generate payroll for this period? This will calculate salaries for all active employees.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tenant/{{ $tenant->id }}/payroll/processing/{{ $period->id }}/generate`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function approvePayroll() {
    if (confirm('Are you sure you want to approve this payroll? This will create accounting entries and finalize the payroll.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tenant/{{ $tenant->id }}/payroll/processing/{{ $period->id }}/approve`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function viewPayslip(runId) {
    // Implement payslip view modal or redirect
    window.open(`/tenant/{{ $tenant->id }}/payroll/payslips/${runId}`, '_blank');
}

function downloadPayslip(runId) {
    window.open(`/tenant/{{ $tenant->id }}/payroll/payslips/${runId}/download`, '_blank');
}

function emailPayslip(runId) {
    if (confirm('Send payslip to employee via email?')) {
        // Implement email functionality
        fetch(`/tenant/{{ $tenant->id }}/payroll/payslips/${runId}/email`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            }
        }).then(response => {
            if (response.ok) {
                alert('Payslip sent successfully!');
            } else {
                alert('Failed to send payslip.');
            }
        });
    }
}

function exportPayrollReport() {
    window.open(`/tenant/{{ $tenant->id }}/payroll/processing/{{ $period->id }}/export-report`, '_blank');
}

function printPayrollSummary() {
    window.print();
}
</script>
@endsection
