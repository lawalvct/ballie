@extends('layouts.tenant')

@section('title', 'Employees - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Employees</h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage your workforce and employee records
            </p>
        </div>
        <div class="mt-4 lg:mt-0 flex items-center space-x-3">
            <a href="{{ route('tenant.payroll.employees.create', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Employee
            </a>
            <a href="{{ route('tenant.payroll.employees.export-all', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Export
            </a>
            <button type="button" onclick="openImportModal()"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Import
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Employee name, ID, email..."
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                </div>

                <!-- Department -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department"
                            id="department"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                            id="status"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                    <input type="text"
                           name="position"
                           id="position"
                           value="{{ request('position') }}"
                           placeholder="Job position..."
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Employees</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $employees->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $employees->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Inactive</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $employees->where('status', 'inactive')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Payroll</p>
                    <p class="text-2xl font-semibold text-gray-900">₦{{ number_format($employees->sum(function($employee) { return $employee->currentSalary->base_salary ?? 0; }), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Employees</h3>
        </div>

        @if($employees->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Position
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hire Date
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Salary
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employees as $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-primary-600">
                                                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $employee->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee->employee_number ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee->department->name ?? 'No Department' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($employee->position)
                                        {{ $employee->position->name }}
                                        <span class="text-xs text-gray-500">({{ $employee->position->code }})</span>
                                    @else
                                        <span class="text-gray-400">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                    ₦{{ number_format($employee->currentSalary->base_salary ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' :
                                           ($employee->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' :
                                            'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- View -->
                                        <a href="{{ route('tenant.payroll.employees.show', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                           title="View Employee">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        <!-- Edit -->
                                        <a href="{{ route('tenant.payroll.employees.edit', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                                           class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50"
                                           title="Edit Employee">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        <!-- Payslip -->
                                        <a href="{{ route('tenant.payroll.employees.payslip', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                                           class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50"
                                           title="Generate Payslip">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>

                                        <!-- More Actions Dropdown -->
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                    class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-50"
                                                    title="More Actions">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                </svg>
                                            </button>

                                            <div x-show="open"
                                                 @click.away="open = false"
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    <!-- Edit Employee -->
                                                    <a href="{{ route('tenant.payroll.employees.edit', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                                                       class="flex items-center px-4 py-2 text-sm text-blue-700 hover:bg-blue-50">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit Employee
                                                    </a>

                                                    <!-- Toggle Status -->
                                                    <form method="POST" action="{{ route('tenant.payroll.employees.toggle-status', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}" class="block">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                onclick="return confirm('Are you sure you want to {{ $employee->status === 'active' ? 'deactivate' : 'activate' }} this employee?')"
                                                                class="w-full text-left px-4 py-2 text-sm text-{{ $employee->status === 'active' ? 'yellow' : 'green' }}-700 hover:bg-{{ $employee->status === 'active' ? 'yellow' : 'green' }}-50 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                @if($employee->status === 'active')
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                @else
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                @endif
                                                            </svg>
                                                            {{ $employee->status === 'active' ? 'Deactivate' : 'Activate' }} Employee
                                                        </button>
                                                    </form>

                                                    <!-- Reset Portal Link -->
                                                    <form method="POST" action="{{ route('tenant.payroll.employees.reset-portal-link', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}" class="block">
                                                        @csrf
                                                        <button type="submit"
                                                                onclick="return confirm('Are you sure you want to reset the portal link for this employee?')"
                                                                class="w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                            Reset Portal Link
                                                        </button>
                                                    </form>

                                                    <!-- Delete Employee -->
                                                    @if($employee->status !== 'active')
                                                        <form method="POST" action="{{ route('tenant.payroll.employees.destroy', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}" class="block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    onclick="return confirm('Are you sure you want to delete this employee? This action cannot be undone.')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                                Delete Employee
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $employees->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No employees found</h3>
                <p class="text-gray-500 mb-6">
                    @if(request()->hasAny(['search', 'department', 'status', 'position']))
                        No employees match your current filters. Try adjusting your search criteria.
                    @else
                        You haven't added any employees yet. Add your first employee to get started.
                    @endif
                </p>
                <a href="{{ route('tenant.payroll.employees.create', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Your First Employee
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Import Employees Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Import Employees</h3>
            <button type="button" onclick="closeImportModal()" class="text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <!-- Instructions -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="font-medium text-blue-900 mb-2">Import Instructions:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>✓ Download the template below to see the required format</li>
                    <li>✓ Fill in employee data in the template</li>
                    <li>✓ Save as CSV file</li>
                    <li>✓ Upload the file to import all employees at once</li>
                </ul>
            </div>

            <!-- Download Template Button -->
            <div class="mb-6">
                <a href="{{ route('tenant.payroll.employees.template', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium text-sm transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Download CSV Template
                </a>
            </div>

            <!-- File Upload Form -->
            <form id="importForm" enctype="multipart/form-data">
                @csrf
                <div class="mb-6">
                    <label for="importFile" class="block text-sm font-medium text-gray-700 mb-2">Select CSV File</label>
                    <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-500 transition-colors duration-200">
                        <input type="file"
                               id="importFile"
                               name="file"
                               accept=".csv,.xlsx"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                               onchange="handleFileSelect(event)">
                        <div class="pointer-events-none">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600">Drag and drop your file here, or click to select</p>
                            <p class="text-xs text-gray-500 mt-1">CSV or Excel files only</p>
                        </div>
                    </div>
                    <p id="fileName" class="mt-2 text-sm text-gray-600"></p>
                </div>

                <!-- Preview Section -->
                <div id="previewSection" class="mb-6 hidden">
                    <h4 class="font-medium text-gray-900 mb-2">Preview (First 5 rows)</h4>
                    <div class="overflow-x-auto bg-gray-50 rounded-lg border border-gray-200">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 border-b">
                                <tr id="previewHeader"></tr>
                            </thead>
                            <tbody id="previewBody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Import Statistics -->
                <div id="statsSection" class="mb-6 hidden p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-700">
                        <span class="font-medium" id="rowCount">0</span> employees ready to import
                    </p>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <button type="button"
                            onclick="closeImportModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            id="importSubmitBtn"
                            disabled
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-400 text-white rounded-lg font-medium transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Import Employees
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
// Import Modal Functions
function openImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('importForm').reset();
    document.getElementById('previewSection').classList.add('hidden');
    document.getElementById('statsSection').classList.add('hidden');
    document.getElementById('fileName').textContent = '';
    document.getElementById('importSubmitBtn').disabled = true;
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Show file name
    document.getElementById('fileName').textContent = `Selected: ${file.name}`;

    // Read and parse CSV
    const reader = new FileReader();
    reader.onload = function(e) {
        const text = e.target.result;
        const rows = text.trim().split('\n');

        if (rows.length < 2) {
            alert('CSV file must contain at least a header and one data row');
            return;
        }

        // Parse header
        const headers = rows[0].split(',').map(h => h.trim());

        // Parse first 5 data rows for preview
        const previewHeader = document.getElementById('previewHeader');
        const previewBody = document.getElementById('previewBody');
        previewHeader.innerHTML = '';
        previewBody.innerHTML = '';

        headers.forEach(header => {
            const th = document.createElement('th');
            th.className = 'px-4 py-2 text-left text-gray-700 font-medium';
            th.textContent = header;
            previewHeader.appendChild(th);
        });

        for (let i = 1; i < Math.min(6, rows.length); i++) {
            const cells = rows[i].split(',');
            const tr = document.createElement('tr');
            tr.className = 'border-b hover:bg-gray-100';

            cells.forEach(cell => {
                const td = document.createElement('td');
                td.className = 'px-4 py-2 text-gray-600';
                td.textContent = cell.trim();
                tr.appendChild(td);
            });
            previewBody.appendChild(tr);
        }

        // Show preview and stats
        document.getElementById('previewSection').classList.remove('hidden');
        document.getElementById('statsSection').classList.remove('hidden');
        document.getElementById('rowCount').textContent = rows.length - 1;
        document.getElementById('importSubmitBtn').disabled = false;
    };

    reader.readAsText(file);
}

// Handle form submission - wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    const importForm = document.getElementById('importForm');
    if (!importForm) {
        console.error('Import form not found');
        return;
    }

    importForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select a file to import');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                         document.querySelector('input[name="_token"]')?.value;

        if (!csrfToken) {
            alert('CSRF token not found. Please refresh the page and try again.');
            console.error('CSRF token missing');
            return;
        }

        formData.append('_token', csrfToken);

        const submitBtn = document.getElementById('importSubmitBtn');
        const originalText = submitBtn.textContent;

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Importing...';

            const response = await fetch('{{ route('tenant.payroll.employees.import', ['tenant' => $tenant->slug]) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
                credentials: 'same-origin'
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                throw new Error('Server returned an error. Please check if you are logged in and try again.');
            }

            const data = await response.json();

            if (response.ok) {
                alert(`Import successful!\n✓ ${data.imported} employees imported\n✗ ${data.errors} errors`);
                closeImportModal();
                location.reload();
            } else {
                let errorMsg = data.message || 'Unknown error';
                if (data.error_details && data.error_details.length > 0) {
                    errorMsg += '\n\nFirst few errors:\n' + data.error_details.join('\n');
                }
                alert(`Import failed: ${errorMsg}`);
            }
        } catch (error) {
            console.error('Upload error:', error);
            alert('Error uploading file: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
});
</script>
@endsection

```
