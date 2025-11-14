@extends('layouts.tenant')

@section('title', 'Shift Management')

@section('page-title', 'Shift Management')
@section('page-description', 'Manage working hours and shift schedules')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Shift Management') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('Manage working hours and shift schedules') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('tenant.payroll.shifts.assignments', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-users mr-2"></i>
                View Assignments
            </a>
            <a href="{{ route('tenant.payroll.shifts.create', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-plus mr-2"></i>
                Create Shift
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-4" role="alert">
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
    <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4" role="alert">
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

    <!-- Shifts Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($shifts as $shift)
        <div class="bg-white overflow-hidden shadow rounded-lg hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <!-- Shift Header -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h5 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $shift->name }}
                        </h5>
                        <div class="flex gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $shift->code }}</span>
                            @if(!$shift->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                            <div class="py-1">
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ route('tenant.payroll.shifts.show', [$tenant, $shift->id]) }}">
                                    <i class="fas fa-eye mr-2"></i> View Details
                                </a>
                                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ route('tenant.payroll.shifts.edit', [$tenant, $shift->id]) }}">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <button class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                                        onclick="deleteShift({{ $shift->id }}, '{{ $shift->name }}')">
                                    <i class="fas fa-trash mr-2"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shift Time -->
                <div class="mb-4">
                    <div class="flex items-center text-gray-700 mb-2">
                        <i class="fas fa-clock w-5 text-gray-400"></i>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} -
                            {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }}
                        </span>
                    </div>
                    <div class="flex items-center text-gray-700 mb-2">
                        <i class="fas fa-hourglass-half w-5 text-gray-400"></i>
                        <span>{{ $shift->work_hours }} hours (Full Day)</span>
                    </div>
                    @if($shift->late_grace_minutes)
                    <div class="flex items-center text-gray-700 mb-2">
                        <i class="fas fa-user-clock w-5 text-gray-400"></i>
                        <span>{{ $shift->late_grace_minutes }} min grace period</span>
                    </div>
                    @endif
                </div>

                <!-- Working Days -->
                <div class="mb-4">
                    <div class="text-xs text-gray-500 mb-2">Working Days</div>
                    <div class="flex flex-wrap gap-1">
                        @php
                            // `working_days` may already be an array (model cast) or a comma-separated string.
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
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">{{ $short }}</span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-500">{{ $short }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Shift Allowance -->
                @if($shift->shift_allowance > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-blue-400 mr-2"></i>
                        <span class="text-sm font-medium text-blue-800">₦{{ number_format($shift->shift_allowance, 2) }} allowance</span>
                    </div>
                </div>
                @endif

                <!-- Employees Count -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">
                            <i class="fas fa-users mr-1"></i>
                            {{ $shift->employee_assignments_count ?? 0 }} employee(s)
                        </span>
                        <a href="{{ route('tenant.payroll.shifts.show', [$tenant, $shift->id]) }}"
                           class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                            View Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-center">
                    <i class="fas fa-clock text-gray-300 text-6xl mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Shifts Found</h3>
                    <p class="text-gray-500 mb-6">Create your first shift to manage employee working hours.</p>
                    <a href="{{ route('tenant.payroll.shifts.create', $tenant) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Shift
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="deleteModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Delete Shift</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-500">Are you sure you want to delete <strong id="shiftNameToDelete" class="text-gray-900"></strong>?</p>
                <p class="text-sm text-red-600 mt-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    This action cannot be undone. Shifts with active assignments cannot be deleted.
                </p>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete Shift
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteShift(shiftId, shiftName) {
    document.getElementById('shiftNameToDelete').textContent = shiftName;
    document.getElementById('deleteForm').action =
        '{{ route("tenant.payroll.shifts.destroy", [$tenant, ":id"]) }}'.replace(':id', shiftId);
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endpush
@endsection
