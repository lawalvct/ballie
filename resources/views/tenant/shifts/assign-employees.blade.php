@extends('layouts.tenant')

@section('title', 'Assign Employees to Shifts')

@section('page-title', 'Assign Employees to Shifts')
@section('page-description', 'Assign or update employee shift schedules')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('tenant.payroll.shifts.assignments', $tenant) }}"
               class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Assignments
            </a>
        </div>
        <div class="ml-0">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Assign Employees to Shifts') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('Assign or update employee shift schedules') }}</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Single Assignment Form -->
        <div class="lg:col-span-1">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900">{{ __('Single Assignment') }}</h5>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('tenant.payroll.shifts.store-assignment', $tenant) }}">
                        @csrf

                        <div class="mb-6">
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Select Employee') }}</label>
                            <select name="employee_id" id="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('employee_id') border-red-300 @enderror" required>
                                <option value="">{{ __('Select Employee') }}</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                        data-current-shift="{{ $employee->currentShiftAssignment?->shiftSchedule?->shift_name ?? 'None' }}">
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                    ({{ $employee->employee_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p id="currentShiftInfo" class="mt-1 text-sm text-gray-500"></p>
                        </div>

                        <div class="mb-6">
                            <label for="shift_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift') }} <span class="text-red-500">*</span></label>
                            <select name="shift_id" id="shift_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('shift_id') border-red-300 @enderror" required>
                                <option value="">{{ __('Select Shift') }}</option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}"
                                        data-shift-time="{{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }}"
                                        data-shift-hours="{{ $shift->work_hours }}">
                                    {{ $shift->name }}
                                    ({{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }})
                                </option>
                                @endforeach
                            </select>
                            @error('shift_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="effective_from" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Effective From') }} <span class="text-red-500">*</span></label>
                            <input type="date"
                                   name="effective_from"
                                   id="effective_from"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('effective_from') border-red-300 @enderror"
                                   value="{{ old('effective_from', date('Y-m-d')) }}"
                                   required>
                            @error('effective_from')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center">
                                <input class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       type="checkbox"
                                       name="is_permanent"
                                       value="1"
                                       id="is_permanent_single"
                                       checked>
                                <label class="ml-2 block text-sm text-gray-900" for="is_permanent_single">
                                    {{ __('Permanent Assignment (no end date)') }}
                                </label>
                            </div>
                        </div>

                        <div class="mb-6" id="effective_to_wrapper_single" style="display: none;">
                            <label for="effective_to_single" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Effective To') }}</label>
                            <input type="date"
                                   name="effective_to"
                                   id="effective_to_single"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="mt-1 text-sm text-gray-500">{{ __('Leave empty for permanent assignment') }}</p>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-user-check mr-2"></i>
                            {{ __('Assign Employee') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bulk Assignment Form -->
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h5 class="text-lg font-semibold text-gray-900">{{ __('Bulk Assignment') }}</h5>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('tenant.payroll.shifts.bulk-assign', $tenant) }}" id="bulkAssignForm">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Select Employees') }} <span class="text-red-500">*</span></label>
                            <div class="border border-gray-300 rounded-md p-4 max-h-80 overflow-y-auto">
                                <div class="mb-3 flex gap-2">
                                    <button type="button"
                                            onclick="selectAll()"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ __('Select All') }}
                                    </button>
                                    <button type="button"
                                            onclick="deselectAll()"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ __('Deselect All') }}
                                    </button>
                                </div>
                                @foreach($employees as $employee)
                                <div class="flex items-center mb-2">
                                    <input class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded employee-checkbox"
                                           type="checkbox"
                                           name="employee_ids[]"
                                           value="{{ $employee->id }}"
                                           id="emp_{{ $employee->id }}">
                                    <label class="ml-2 block text-sm text-gray-900" for="emp_{{ $employee->id }}">
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                        <span class="text-gray-500">({{ $employee->employee_id }})</span>
                                        @if($employee->currentShiftAssignment)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                            {{ __('Current:') }} {{ $employee->currentShiftAssignment->shift->name }}
                                        </span>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Selected:') }} <span id="selectedCount">0</span> {{ __('employee(s)') }}
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="shift_id_bulk" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift') }} <span class="text-red-500">*</span></label>
                            <select name="shift_id" id="shift_id_bulk" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">{{ __('Select Shift') }}</option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->name }}
                                    ({{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="effective_from_bulk" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Effective From') }} <span class="text-red-500">*</span></label>
                                <input type="date"
                                       name="effective_from"
                                       id="effective_from_bulk"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ date('Y-m-d') }}"
                                       required>
                            </div>
                            <div>
                                <label for="effective_to_bulk" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Effective To') }}</label>
                                <input type="date"
                                       name="effective_to"
                                       id="effective_to_bulk"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">{{ __('Leave empty for permanent') }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-center">
                                <input class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       type="checkbox"
                                       name="is_permanent"
                                       value="1"
                                       id="is_permanent_bulk"
                                       checked>
                                <label class="ml-2 block text-sm text-gray-900" for="is_permanent_bulk">
                                    {{ __('Permanent Assignment') }}
                                </label>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">{{ __('Note: This will end current shift assignments for selected employees.') }}</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-users mr-2"></i>
                            {{ __('Assign Selected Employees') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Show current shift when employee selected
document.getElementById('employee_id')?.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const currentShift = option.getAttribute('data-current-shift');
    const infoEl = document.getElementById('currentShiftInfo');
    if (currentShift && currentShift !== 'None') {
        infoEl.textContent = '{{ __("Current Shift:") }} ' + currentShift;
        infoEl.classList.add('text-yellow-600');
        infoEl.classList.remove('text-gray-500');
    } else {
        infoEl.textContent = '{{ __("No current shift assigned") }}';
        infoEl.classList.remove('text-yellow-600');
        infoEl.classList.add('text-gray-500');
    }
});

// Toggle effective_to based on is_permanent (single)
document.getElementById('is_permanent_single')?.addEventListener('change', function() {
    const wrapper = document.getElementById('effective_to_wrapper_single');
    wrapper.style.display = this.checked ? 'none' : 'block';
});

// Toggle effective_to based on is_permanent (bulk)
document.getElementById('is_permanent_bulk')?.addEventListener('change', function() {
    const input = document.getElementById('effective_to_bulk');
    input.disabled = this.checked;
    if (this.checked) input.value = '';
});

// Select/Deselect all
function selectAll() {
    document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = true);
    updateSelectedCount();
}

function deselectAll() {
    document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = false);
    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    const count = document.querySelectorAll('.employee-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

// Listen for checkbox changes
document.querySelectorAll('.employee-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

// Validate bulk form
document.getElementById('bulkAssignForm')?.addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.employee-checkbox:checked').length;
    if (checked === 0) {
        e.preventDefault();
        alert('{{ __("Please select at least one employee") }}');
        return false;
    }
});
</script>
@endpush
@endsection
