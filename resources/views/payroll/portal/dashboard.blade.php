@extends('payroll.portal.layout')

@section('title', 'Dashboard')
@section('page-title', 'Welcome, ' . $employee->first_name . '!')

@section('content')
        <!-- Year-to-Date Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">YTD Gross</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($ytdStats['ytd_gross'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">YTD Tax</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($ytdStats['ytd_tax'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-receipt text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">YTD Net</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($ytdStats['ytd_net'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Payroll Runs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $ytdStats['payroll_count'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Payslips -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-file-invoice-dollar mr-2 text-indigo-600"></i>
                        Recent Payslips
                    </h2>
                    <a href="{{ route('payroll.portal.payslips', ['tenant' => $tenant, 'token' => $token]) }}"
                       class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                @if($recentPayslips->isEmpty())
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>No payslips available yet</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentPayslips as $payslip)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $payslip->payrollPeriod->name ?? 'N/A' }}</h3>
                                        <div class="flex items-center space-x-4 mt-1 text-sm text-gray-600">
                                            <span><i class="fas fa-calendar mr-1"></i>{{ $payslip->payrollPeriod->pay_date ? $payslip->payrollPeriod->pay_date->format('M d, Y') : 'N/A' }}</span>
                                            <span><i class="fas fa-money-bill mr-1"></i>₦{{ number_format($payslip->net_salary, 2) }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('payroll.portal.payslip', ['tenant' => $tenant, 'token' => $token, 'payslip' => $payslip->id]) }}"
                                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Active Loans -->
            <div>
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-hand-holding-usd mr-2 text-indigo-600"></i>
                        Active Loans
                    </h2>
                    @if($activeLoans->isEmpty())
                        <div class="text-center py-4 text-gray-500 text-sm">
                            <i class="fas fa-check-circle text-2xl mb-2 text-green-500"></i>
                            <p>No active loans</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($activeLoans as $loan)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $loan->loan_type }}</span>
                                        <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">Active</span>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <div class="flex justify-between mb-1">
                                            <span>Amount:</span>
                                            <span class="font-medium">₦{{ number_format($loan->amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Balance:</span>
                                            <span class="font-medium text-red-600">₦{{ number_format($loan->balance, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
            </div>
        </div>
@endsection
