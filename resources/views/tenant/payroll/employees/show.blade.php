@extends('tenant.layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header with Employee Info -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-circle text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </h1>
                        <p class="text-blue-100 text-lg">{{ $employee->job_title }}</p>
                        <p class="text-blue-200 text-sm">
                            <i class="fas fa-id-card mr-1"></i>{{ $employee->employee_number }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium
                        {{ $employee->status === 'active' ? 'bg-green-500/20 text-green-100' : 'bg-red-500/20 text-red-100' }}">
                        <span class="w-2 h-2 rounded-full mr-2
                            {{ $employee->status === 'active' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                        {{ ucfirst($employee->status) }}
                    </span>
                    <a href="{{ route('tenant.payroll.employees', $tenant) }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Employees
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Personal Information -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Personal Details Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-500"></i>
                        Personal Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-gray-900">{{ $employee->email ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                            <p class="text-gray-900">{{ $employee->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Date of Birth</label>
                            <p class="text-gray-900">
                                {{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'Not provided' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Gender</label>
                            <p class="text-gray-900">{{ $employee->gender ? ucfirst($employee->gender) : 'Not specified' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                            <p class="text-gray-900">{{ $employee->address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Employment Details Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-briefcase mr-2 text-green-500"></i>
                        Employment Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Department</label>
                            <p class="text-gray-900">{{ $employee->department->name ?? 'Not assigned' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Employment Type</label>
                            <p class="text-gray-900">{{ $employee->employment_type ? ucfirst($employee->employment_type) : 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Hire Date</label>
                            <p class="text-gray-900">{{ $employee->hire_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Pay Frequency</label>
                            <p class="text-gray-900">{{ $employee->pay_frequency ? ucfirst($employee->pay_frequency) : 'Not set' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bank Information Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-university mr-2 text-purple-500"></i>
                        Bank Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Bank Name</label>
                            <p class="text-gray-900">{{ $employee->bank_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Account Number</label>
                            <p class="text-gray-900">{{ $employee->account_number ?? 'Not provided' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Account Name</label>
                            <p class="text-gray-900">{{ $employee->account_name ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Payroll Activity -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-history mr-2 text-orange-500"></i>
                        Recent Payroll Activity
                    </h3>

                    @if($employee->payrollRuns && $employee->payrollRuns->count() > 0)
                        <div class="space-y-4">
                            @foreach($employee->payrollRuns as $payrollRun)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $payrollRun->payrollPeriod->name ?? 'Payroll Run' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $payrollRun->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-emerald-600">
                                            ₦{{ number_format($payrollRun->net_pay, 2) }}
                                        </p>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $payrollRun->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($payrollRun->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No payroll activity yet.</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Current Salary Card -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>
                        Current Salary
                    </h3>

                    @if($employee->currentSalary)
                        <div class="space-y-4">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-emerald-600">
                                    ₦{{ number_format($employee->currentSalary->basic_salary, 2) }}
                                </p>
                                <p class="text-sm text-gray-500">Basic Salary</p>
                            </div>

                            @if($employee->currentSalary->salaryComponents && $employee->currentSalary->salaryComponents->count() > 0)
                                <div class="border-t pt-4">
                                    <h4 class="font-medium text-gray-900 mb-2">Salary Components</h4>
                                    <div class="space-y-2">
                                        @foreach($employee->currentSalary->salaryComponents as $component)
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-600">{{ $component->salaryComponent->name }}</span>
                                                <span class="font-medium text-gray-900">
                                                    @if($component->amount)
                                                        ₦{{ number_format($component->amount, 2) }}
                                                    @else
                                                        {{ $component->percentage }}%
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No salary information set</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>

                    <div class="space-y-3">
                        <a href="#" class="block w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-edit mr-2"></i>Edit Employee
                        </a>
                        <a href="#" class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-money-check-alt mr-2"></i>Generate Payslip
                        </a>
                        <a href="#" class="block w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-calculator mr-2"></i>Update Salary
                        </a>
                        <a href="#" class="block w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-3 rounded-lg font-medium transition-colors duration-300 text-center">
                            <i class="fas fa-hand-holding-usd mr-2"></i>Manage Loans
                        </a>
                    </div>
                </div>

                <!-- Employment Summary -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Employment Summary</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Years of Service</span>
                            <span class="font-medium">
                                {{ $employee->hire_date->diffInYears(now()) }} years
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Payslips</span>
                            <span class="font-medium">{{ $employee->payrollRuns->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Active Loans</span>
                            <span class="font-medium">{{ $employee->loans ? $employee->loans->where('status', 'active')->count() : 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
