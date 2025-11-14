@extends('layouts.tenant')

@section('title', $shift->name)

@section('page-title', $shift->name)
@section('page-description', 'Shift ' . $shift->code)

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <a href="{{ route('tenant.payroll.shifts.index', $tenant) }}" class="text-gray-600 hover:text-gray-900 mr-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $shift->name }}</h1>
                    <p class="text-gray-600 mt-1"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $shift->code }}</span></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tenant.payroll.shifts.edit', [$tenant, $shift->id]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Shift
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Shift Details -->
        <div class="lg:col-span-1">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900">Shift Details</h5>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Status</p>
                        <div class="mt-1">
                            @if($shift->is_active)
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Inactive
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Working Hours</p>
                        <p class="mt-1 text-lg font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} -
                            {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Total Work Hours</p>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $shift->work_hours }} hours</p>
                    </div>

                    @if($shift->late_grace_minutes)
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Late Grace Period</p>
                        <p class="mt-1 text-lg font-medium text-gray-900">{{ $shift->late_grace_minutes }} minutes</p>
                    </div>
                    @endif

                    @if($shift->shift_allowance > 0)
                    <div class="mb-4 bg-blue-50 border border-blue-200 rounded-md p-4">
                        <p class="text-xs text-blue-600 uppercase tracking-wide font-semibold">Shift Allowance</p>
                        <p class="mt-1 text-2xl font-bold text-blue-900">₦{{ number_format($shift->shift_allowance, 2) }}</p>
                    </div>
                    @endif

                    <div class="mb-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-3">Working Days</p>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $days = is_array($shift->working_days) ? $shift->working_days : explode(',', (string) $shift->working_days);
                                $dayMap = [
                                    'monday' => 'Mon',
                                    'tuesday' => 'Tue',
                                    'wednesday' => 'Wed',
                                    'thursday' => 'Thu',
                                    'friday' => 'Fri',
                                    'saturday' => 'Sat',
                                    'sunday' => 'Sun'
                                ];
                            @endphp
                            @foreach($dayMap as $day => $short)
                                @if(in_array($day, $days))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ $short }}</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ $short }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    @if($shift->description)
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Description</p>
                        <p class="text-sm text-gray-600">{{ $shift->description }}</p>
                    </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200 mt-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Employees Assigned</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $shift->employee_assignments_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Employees -->
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h5 class="text-lg font-semibold text-gray-900">Assigned Employees</h5>
                    <a href="{{ route('tenant.payroll.shifts.assign-employees', $tenant) }}?shift_id={{ $shift->id }}"
                       class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-user-plus mr-1"></i>
                        Assign Employees
                    </a>
                </div>
                <div class="p-6">
                    @if($assignments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Effective From</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Effective To</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assignments as $assignment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $assignment->employee->first_name }} {{ $assignment->employee->last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $assignment->employee->employee_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $assignment->employee->department->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($assignment->effective_from)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($assignment->effective_to)
                                        {{ \Carbon\Carbon::parse($assignment->effective_to)->format('M d, Y') }}
                                        @else
                                        <span class="text-gray-500 italic">Ongoing</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(!$assignment->effective_to || \Carbon\Carbon::parse($assignment->effective_to) >= now())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Active
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Ended
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $assignments->links() }}
                    </div>
                    @else
                    <div class="text-center py-12">
                        <i class="fas fa-users text-gray-300" style="font-size: 3rem;"></i>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No Employees Assigned</h3>
                        <p class="mt-2 text-gray-500">Start assigning employees to this shift.</p>
                        <a href="{{ route('tenant.payroll.shifts.assign-employees', $tenant) }}?shift_id={{ $shift->id }}"
                           class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-user-plus mr-2"></i>
                            Assign Employees
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
