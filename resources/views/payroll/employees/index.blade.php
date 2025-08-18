@extends('layouts.tenant')

@section('title', 'Employees')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Employees</h1>
                    <p class="text-emerald-100 text-lg">Manage your workforce and employee records</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('payroll.employees.create') }}"
                       class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                        <i class="fas fa-user-plus mr-2"></i>Add Employee
                    </a>
                    <a href="{{ route('payroll.employees.export') }}"
                       class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-download mr-2"></i>Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
            <form method="GET" action="{{ route('payroll.employees.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Employee name or ID..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-300 mr-2">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                    <a href="{{ route('payroll.employees.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-300">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Employee Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($employees as $employee)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-bold text-gray-900">{{ $employee->full_name }}</h3>
                                <p class="text-gray-600">{{ $employee->position }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($employee->status === 'active') bg-emerald-100 text-emerald-800
                                    @elseif($employee->status === 'inactive') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-id-card text-gray-400 w-4 mr-2"></i>
                                <span class="text-gray-600">ID: {{ $employee->employee_id }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-building text-gray-400 w-4 mr-2"></i>
                                <span class="text-gray-600">{{ $employee->department->name ?? 'No Department' }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-calendar text-gray-400 w-4 mr-2"></i>
                                <span class="text-gray-600">Joined {{ $employee->hire_date->format('M Y') }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-money-bill-wave text-gray-400 w-4 mr-2"></i>
                                <span class="text-gray-600">₦{{ number_format($employee->base_salary, 2) }}/month</span>
                            </div>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('payroll.employees.show', $employee) }}"
                               class="flex-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-4 py-2 rounded-lg text-center font-medium transition-colors duration-300 border border-emerald-200">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('payroll.employees.edit', $employee) }}"
                               class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-center font-medium transition-colors duration-300 border border-blue-200">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <a href="{{ route('payroll.employees.payslip', $employee) }}"
                               class="flex-1 bg-purple-50 hover:bg-purple-100 text-purple-700 px-4 py-2 rounded-lg text-center font-medium transition-colors duration-300 border border-purple-200">
                                <i class="fas fa-file-alt mr-1"></i>Payslip
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12 bg-white rounded-2xl shadow-lg border border-gray-100">
                        <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Employees Found</h3>
                        <p class="text-gray-500 mb-6">
                            @if(request()->hasAny(['search', 'department', 'status']))
                                Try adjusting your filters or search terms.
                            @else
                                Start by adding your first employee to the system.
                            @endif
                        </p>
                        <a href="{{ route('payroll.employees.create') }}"
                           class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300">
                            <i class="fas fa-user-plus mr-2"></i>Add Employee
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
