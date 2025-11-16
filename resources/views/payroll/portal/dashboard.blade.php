<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Portal - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-circle text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Welcome, {{ $employee->first_name }}!</h1>
                        <p class="text-sm text-gray-600">{{ $employee->employee_number }} • {{ $employee->department->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('payroll.portal.profile', ['tenant' => $tenant, 'token' => $token]) }}"
                       class="text-gray-600 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-user-cog text-xl"></i>
                    </a>
                    <form action="{{ route('payroll.portal.logout', ['tenant' => $tenant, 'token' => $token]) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600 transition-colors">
                            <i class="fas fa-sign-out-alt text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Payslips -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-6">
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

            <!-- Quick Links & Loans -->
            <div class="space-y-6">
                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-link mr-2 text-indigo-600"></i>
                        Quick Links
                    </h2>
                    <div class="space-y-2">
                        <a href="{{ route('payroll.portal.payslips', ['tenant' => $tenant, 'token' => $token]) }}"
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <span class="flex items-center text-gray-700 group-hover:text-indigo-600">
                                <i class="fas fa-file-invoice-dollar w-5 mr-3"></i>
                                My Payslips
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                        </a>
                        <a href="{{ route('payroll.portal.profile', ['tenant' => $tenant, 'token' => $token]) }}"
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <span class="flex items-center text-gray-700 group-hover:text-indigo-600">
                                <i class="fas fa-user w-5 mr-3"></i>
                                My Profile
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                        </a>
                        <a href="{{ route('payroll.portal.loans', ['tenant' => $tenant, 'token' => $token]) }}"
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <span class="flex items-center text-gray-700 group-hover:text-indigo-600">
                                <i class="fas fa-hand-holding-usd w-5 mr-3"></i>
                                My Loans
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                        </a>
                        <a href="{{ route('payroll.portal.tax-certificate', ['tenant' => $tenant, 'token' => $token]) }}"
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <span class="flex items-center text-gray-700 group-hover:text-indigo-600">
                                <i class="fas fa-file-alt w-5 mr-3"></i>
                                Tax Certificate
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                        </a>
                        <a href="{{ route('payroll.portal.attendance', ['tenant' => $tenant, 'token' => $token]) }}"
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                            <span class="flex items-center text-gray-700 group-hover:text-indigo-600">
                                <i class="fas fa-calendar-check w-5 mr-3"></i>
                                Attendance
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-indigo-600"></i>
                        </a>
                    </div>
                </div>

                <!-- Active Loans -->
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
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-600">
                <p>© {{ date('Y') }} Employee Self-Service Portal. All rights reserved.</p>
                <p class="mt-1">
                    <i class="fas fa-shield-alt text-green-600"></i>
                    Your data is secure and encrypted
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
