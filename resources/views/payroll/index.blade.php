@extends('layouts.tenant')

@section('title', 'Payroll Management')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Payroll Management</h1>
                    <p class="text-blue-100 text-lg">Manage employee payroll, salaries, and benefits</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('payroll.employees.create') }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-user-plus mr-2"></i>Add Employee
                    </a>
                    <a href="{{ route('payroll.periods.create') }}"
                       class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-calendar-plus mr-2"></i>New Payroll Period
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Employees -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-blue-500 p-3 rounded-xl">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Total Employees</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_employees'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Payroll -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-emerald-500 p-3 rounded-xl">
                        <i class="fas fa-money-bill-wave text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Current Period</p>
                        <p class="text-lg font-bold text-gray-900">{{ $currentPeriod?->name ?? 'No Active Period' }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Payroll -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-purple-500 p-3 rounded-xl">
                        <i class="fas fa-calculator text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Last Payroll</p>
                        <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['last_payroll_total'] ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-orange-500 p-3 rounded-xl">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm font-medium">Pending Approval</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_approvals'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Payroll Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tasks mr-2 text-blue-500"></i>
                    Payroll Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('payroll.runs.create') }}"
                       class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 border border-blue-200">
                        <i class="fas fa-play-circle text-blue-600 text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Process Payroll</h4>
                            <p class="text-gray-600 text-sm">Calculate and process current period payroll</p>
                        </div>
                    </a>

                    <a href="{{ route('payroll.runs.index') }}"
                       class="flex items-center p-4 bg-gradient-to-r from-emerald-50 to-green-50 rounded-xl hover:from-emerald-100 hover:to-green-100 transition-all duration-300 border border-emerald-200">
                        <i class="fas fa-list text-emerald-600 text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">View Payroll Runs</h4>
                            <p class="text-gray-600 text-sm">Review completed and pending payroll runs</p>
                        </div>
                    </a>

                    <a href="{{ route('payroll.reports.index') }}"
                       class="flex items-center p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl hover:from-purple-100 hover:to-pink-100 transition-all duration-300 border border-purple-200">
                        <i class="fas fa-chart-bar text-purple-600 text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Payroll Reports</h4>
                            <p class="text-gray-600 text-sm">Generate payroll reports and analytics</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Employee Management -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-users-cog mr-2 text-emerald-500"></i>
                    Employee Management
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('payroll.employees.index') }}"
                       class="flex items-center p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-xl hover:from-emerald-100 hover:to-teal-100 transition-all duration-300 border border-emerald-200">
                        <i class="fas fa-users text-emerald-600 text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">All Employees</h4>
                            <p class="text-gray-600 text-sm">Manage employee records and details</p>
                        </div>
                    </a>

                    <a href="{{ route('payroll.departments.index') }}"
                       class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl hover:from-blue-100 hover:to-cyan-100 transition-all duration-300 border border-blue-200">
                        <i class="fas fa-building text-blue-600 text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Departments</h4>
                            <p class="text-gray-600 text-sm">Organize employees by departments</p>
                        </div>
                    </a>

                    <a href="{{ route('payroll.salary-components.index') }}"
                       class="flex items-center p-4 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl hover:from-orange-100 hover:to-red-100 transition-all duration-300 border border-orange-200">
                        <i class="fas fa-cogs text-orange-600 text-xl mr-4"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Salary Components</h4>
                            <p class="text-gray-600 text-sm">Configure allowances and deductions</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-history mr-2 text-indigo-500"></i>
                    Recent Payroll Activity
                </h3>
            </div>
            <div class="p-6">
                @if(isset($recentRuns) && $recentRuns->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentRuns as $run)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-4
                                        @if($run->status === 'completed') bg-emerald-500
                                        @elseif($run->status === 'pending') bg-orange-500
                                        @else bg-gray-400 @endif"></div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $run->payrollPeriod->name }}</h4>
                                        <p class="text-gray-600 text-sm">{{ $run->created_at->format('M d, Y') }} • {{ ucfirst($run->status) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">₦{{ number_format($run->total_net_pay, 2) }}</p>
                                    <p class="text-gray-600 text-sm">{{ $run->employee_count }} employees</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-money-bill-wave text-gray-300 text-4xl mb-4"></i>
                        <h4 class="text-lg font-semibold text-gray-600 mb-2">No Payroll Activity Yet</h4>
                        <p class="text-gray-500 mb-4">Start by adding employees and creating your first payroll period</p>
                        <a href="{{ route('payroll.employees.create') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-300">
                            Add First Employee
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
