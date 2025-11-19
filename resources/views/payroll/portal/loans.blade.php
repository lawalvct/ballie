@extends('payroll.portal.layout')

@section('title', 'My Loans')
@section('page-title', 'My Loans')

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Loans</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $loans->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Loans</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $loans->where('status', 'active')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Outstanding</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($loans->where('status', 'active')->sum('balance'), 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Loans List -->
    @if($loans->count() > 0)
        <div class="space-y-4">
            @foreach($loans as $loan)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Header -->
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-pink-600 flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg">{{ $loan->loan_number }}</h3>
                            <p class="text-purple-100 text-sm">{{ $loan->purpose ?? 'Employee Loan' }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $loan->status === 'active' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $loan->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $loan->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Loan Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">
                                    <i class="fas fa-money-bill-wave mr-1 text-gray-400"></i>
                                    Loan Amount
                                </label>
                                <p class="text-lg font-bold text-gray-900">₦{{ number_format($loan->loan_amount, 2) }}</p>
                            </div>

                            <!-- Total Paid -->
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">
                                    <i class="fas fa-check-circle mr-1 text-green-500"></i>
                                    Total Paid
                                </label>
                                <p class="text-lg font-bold text-green-600">₦{{ number_format($loan->total_paid, 2) }}</p>
                            </div>

                            <!-- Balance -->
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">
                                    <i class="fas fa-balance-scale mr-1 text-orange-500"></i>
                                    Outstanding Balance
                                </label>
                                <p class="text-lg font-bold text-orange-600">₦{{ number_format($loan->balance, 2) }}</p>
                            </div>

                            <!-- Monthly Deduction -->
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">
                                    <i class="fas fa-calendar-alt mr-1 text-blue-500"></i>
                                    Monthly Deduction
                                </label>
                                <p class="text-lg font-bold text-blue-600">₦{{ number_format($loan->monthly_deduction, 2) }}</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-600">Repayment Progress</span>
                                <span class="text-sm font-bold text-indigo-600">{{ number_format($loan->progress_percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-3 rounded-full transition-all duration-500"
                                     style="width: {{ min($loan->progress_percentage, 100) }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Start Date:</span>
                                <span class="font-medium text-gray-900 ml-2">{{ $loan->start_date->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Duration:</span>
                                <span class="font-medium text-gray-900 ml-2">{{ $loan->duration_months }} months</span>
                            </div>
                            @if($loan->status === 'active' && $loan->remaining_months > 0)
                                <div>
                                    <span class="text-gray-600">Remaining:</span>
                                    <span class="font-medium text-orange-600 ml-2">{{ $loan->remaining_months }} month{{ $loan->remaining_months > 1 ? 's' : '' }}</span>
                                </div>
                            @endif
                        </div>

                        @if($loan->status === 'completed')
                            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-green-800 flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    This loan has been fully repaid. Thank you for your timely payments!
                                </p>
                            </div>
                        @endif

                        @if($loan->status === 'suspended')
                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-800 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    This loan has been suspended. Please contact HR for more information.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-hand-holding-usd text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No Loans Found</h3>
            <p class="text-gray-600 mb-6">
                You don't have any loan records. If you need financial assistance, please contact HR.
            </p>
            <a href="{{ route('payroll.portal.dashboard', ['tenant' => $tenant, 'token' => $token]) }}"
               class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    @endif

    <!-- Important Information -->
    @if($loans->where('status', 'active')->count() > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Important Information
            </h3>
            <ul class="space-y-2 text-sm text-blue-800">
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    <span>Monthly deductions are automatically processed from your salary during payroll.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    <span>Your loan will be marked as completed once the full amount is repaid.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    <span>For any questions or concerns about your loan, please contact the HR department.</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-blue-600 mr-2 mt-0.5"></i>
                    <span>Keep track of your repayment progress and ensure timely salary processing.</span>
                </li>
            </ul>
        </div>
    @endif
</div>
@endsection
