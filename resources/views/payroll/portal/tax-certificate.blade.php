@extends('payroll.portal.layout')

@section('title', 'Tax Certificate')
@section('page-title', 'Annual Tax Certificate')

@section('content')
<div class="space-y-6">
    <!-- Year Selection -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Select Tax Year</h2>
            <form method="GET" class="flex items-center gap-3">
                <select name="year"
                        onchange="this.form.submit()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    <!-- Tax Certificate -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden" id="tax-certificate">
        <!-- Certificate Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6 text-white">
            <div class="text-center">
                <h1 class="text-3xl font-bold mb-2">TAX CERTIFICATE</h1>
                <p class="text-lg text-indigo-100">Year {{ $year }}</p>
            </div>
        </div>

        <!-- Certificate Body -->
        <div class="p-8 space-y-6">
            <!-- Company Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employer Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Company Name:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $tenant->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Address:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $tenant->address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Employee Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Full Name:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $employee->first_name }} {{ $employee->last_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Employee Number:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $employee->employee_number }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Tax ID (TIN):</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $employee->tin ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Department:</span>
                        <span class="font-medium text-gray-900 ml-2">{{ $employee->department->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Tax Summary -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-invoice-dollar mr-2 text-indigo-600"></i>
                    Annual Tax Summary for {{ $year }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Gross Income -->
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Gross Income</p>
                                <p class="text-2xl font-bold text-gray-900">₦{{ number_format($taxData->total_gross ?? 0, 2) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Tax -->
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Tax Paid (PAYE)</p>
                                <p class="text-2xl font-bold text-red-600">₦{{ number_format($taxData->total_tax ?? 0, 2) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-receipt text-red-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Pension Contribution -->
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Pension Contribution</p>
                                <p class="text-2xl font-bold text-purple-600">₦{{ number_format($taxData->total_pension ?? 0, 2) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-piggy-bank text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Net Income -->
                    <div class="bg-white rounded-lg p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Net Income</p>
                                <p class="text-2xl font-bold text-blue-600">₦{{ number_format($taxData->total_net ?? 0, 2) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-wallet text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax Relief Information -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tax Relief Applied</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Annual Tax Relief:</span>
                        <span class="font-bold text-gray-900">₦{{ number_format($employee->annual_relief ?? 200000, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Pension PIN:</span>
                        <span class="font-medium text-gray-900">{{ $employee->pension_pin ?? 'Not provided' }}</span>
                    </div>
                </div>
            </div>

            <!-- Certificate Statement -->
            <div class="border-t border-gray-200 pt-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <p class="text-sm text-gray-800 leading-relaxed">
                        This is to certify that <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                        (Employee Number: <strong>{{ $employee->employee_number }}</strong>) was employed by
                        <strong>{{ $tenant->name ?? 'our organization' }}</strong> during the year <strong>{{ $year }}</strong>
                        and earned a total gross income of <strong>₦{{ number_format($taxData->total_gross ?? 0, 2) }}</strong>.
                        A total of <strong>₦{{ number_format($taxData->total_tax ?? 0, 2) }}</strong> was deducted
                        as Pay-As-You-Earn (PAYE) tax in accordance with Nigerian tax regulations.
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div>
                        <p>Generated on: <strong>{{ now()->format('F d, Y') }}</strong></p>
                        <p class="mt-1">Certificate ID: <strong>TAX-{{ $employee->employee_number }}-{{ $year }}</strong></p>
                    </div>
                    <div class="text-right">
                        <p>Valid for tax filing purposes</p>
                        <p class="mt-1">Contact HR for verification</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt text-green-600 mr-1"></i>
                    This is an official tax certificate generated from the payroll system.
                </p>
                <div class="flex gap-3">
                    <button onclick="window.print()"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors text-sm">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <a href="{{ route('payroll.portal.tax-certificate.download', ['tenant' => $tenant, 'token' => $token, 'year' => $year]) }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors text-sm">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Notice -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            Important Information
        </h3>
        <ul class="space-y-2 text-sm text-blue-800">
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span>This certificate shows your total income and tax paid for the selected year.</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span>Use this certificate when filing your annual tax returns with the Federal Inland Revenue Service (FIRS).</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span>Keep this certificate for your records and provide copies to relevant tax authorities when required.</span>
            </li>
            <li class="flex items-start">
                <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                <span>Contact the HR department if you notice any discrepancies in the tax information.</span>
            </li>
        </ul>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #tax-certificate, #tax-certificate * {
            visibility: visible;
        }
        #tax-certificate {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Tax certificate is now downloadable directly via the download link
</script>
@endpush
