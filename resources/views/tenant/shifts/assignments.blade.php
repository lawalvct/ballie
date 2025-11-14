@extends('layouts.tenant')

@section('title', 'Shift Assignments')

@section('page-title', 'Shift Assignments')
@section('page-description', 'Manage employee shift assignments')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Shift Assignments') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('Manage employee shift assignments') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('tenant.payroll.shifts.index', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-clock mr-2"></i>
                {{ __('Manage Shifts') }}
            </a>
            <a href="{{ route('tenant.payroll.shifts.assign-employees', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-user-plus mr-2"></i>
                {{ __('Assign Employees') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('tenant.payroll.shifts.assignments', $tenant) }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Department') }}</label>
                    <select name="department_id" id="department_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">{{ __('All Departments') }}</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Employee') }}</label>
                    <select name="employee_id" id="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">{{ __('All Employees') }}</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="shift_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift') }}</label>
                    <select name="shift_id" id="shift_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">{{ __('All Shifts') }}</option>
                        @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                            {{ $shift->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">{{ __('All') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>{{ __('Ended') }}</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-filter mr-2"></i>
                        {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-6">
            @if($assignments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Employee') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Department') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Shift') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Shift Hours') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Effective From') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Effective To') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $assignment->employee->department->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ $assignment->shift->name }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($assignment->shift->start_time)->format('g:i A') }} -
                                {{ \Carbon\Carbon::parse($assignment->shift->end_time)->format('g:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($assignment->effective_from)->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($assignment->effective_to)
                                {{ \Carbon\Carbon::parse($assignment->effective_to)->format('M d, Y') }}
                                @else
                                <span class="text-gray-500">{{ __('Ongoing') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $isActive = !$assignment->effective_to || \Carbon\Carbon::parse($assignment->effective_to) >= now();
                                @endphp
                                @if($isActive)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('Active') }}</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ __('Ended') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($isActive)
                                <button class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                        onclick="endAssignment({{ $assignment->id }}, '{{ $assignment->employee->first_name }} {{ $assignment->employee->last_name }}')">
                                    <i class="fas fa-stop mr-1"></i>
                                    {{ __('End') }}
                                </button>
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
                <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No Shift Assignments Found') }}</h3>
                <p class="text-gray-500 mb-6">{{ __('Start by assigning shifts to employees.') }}</p>
                <a href="{{ route('tenant.payroll.shifts.assign-employees', $tenant) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-user-plus mr-2"></i>
                    {{ __('Assign Employees') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- End Assignment Modal -->
<div id="endAssignmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">{{ __('End Shift Assignment') }}</h3>
                <button onclick="closeEndModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="endAssignmentForm" method="POST">
                @csrf
                <div class="mb-4">
                    <p class="text-sm text-gray-600">{{ __('Are you sure you want to end the shift assignment for') }} <strong id="employeeNameToEnd"></strong>?</p>

                    <div class="mt-4">
                        <label for="effective_to" class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }} <span class="text-red-500">*</span></label>
                        <input type="date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               id="effective_to"
                               name="effective_to"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeEndModal()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        {{ __('End Assignment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function endAssignment(assignmentId, employeeName) {
    document.getElementById('employeeNameToEnd').textContent = employeeName;
    document.getElementById('endAssignmentForm').action =
        '{{ route("tenant.payroll.shifts.end-assignment", [$tenant, ":id"]) }}'.replace(':id', assignmentId);
    document.getElementById('endAssignmentModal').classList.remove('hidden');
}

function closeEndModal() {
    document.getElementById('endAssignmentModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('endAssignmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEndModal();
    }
});
</script>
@endpush
@endsection
