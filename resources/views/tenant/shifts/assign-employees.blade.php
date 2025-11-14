@extends('layouts.tenant')

@section('title', 'Assign Employees to Shifts')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('tenant.payroll.shifts.assignments', $tenant) }}" class="text-gray-600 hover:text-gray-900 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Assign Employees to Shifts</h1>
        </div>
        <p class="text-gray-600 ml-9">Assign or update employee shift schedules</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Single Assignment Form -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Single Assignment</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tenant.payroll.shifts.store-assignment', $tenant) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Select Employee</label>
                            <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                        data-current-shift="{{ $employee->currentShiftAssignment?->shiftSchedule?->shift_name ?? 'None' }}">
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                    ({{ $employee->employee_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small id="currentShiftInfo" class="text-muted"></small>
                        </div>

                        <div class="mb-3">
                            <label for="shift_id" class="form-label required">Shift</label>
                            <select name="shift_id" id="shift_id" class="form-select @error('shift_id') is-invalid @enderror" required>
                                <option value="">Select Shift</option>
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
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="effective_from" class="form-label required">Effective From</label>
                            <input type="date"
                                   name="effective_from"
                                   id="effective_from"
                                   class="form-control @error('effective_from') is-invalid @enderror"
                                   value="{{ old('effective_from', date('Y-m-d')) }}"
                                   required>
                            @error('effective_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_permanent"
                                       value="1"
                                       id="is_permanent_single"
                                       checked>
                                <label class="form-check-label" for="is_permanent_single">
                                    Permanent Assignment (no end date)
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="effective_to_wrapper_single" style="display: none;">
                            <label for="effective_to_single" class="form-label">Effective To</label>
                            <input type="date"
                                   name="effective_to"
                                   id="effective_to_single"
                                   class="form-control">
                            <small class="text-muted">Leave empty for permanent assignment</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-check mr-2"></i>
                            Assign Employee
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bulk Assignment Form -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Bulk Assignment</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('tenant.payroll.shifts.bulk-assign', $tenant) }}" id="bulkAssignForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label required">Select Employees</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                        Select All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                        Deselect All
                                    </button>
                                </div>
                                @foreach($employees as $employee)
                                <div class="form-check">
                                    <input class="form-check-input employee-checkbox"
                                           type="checkbox"
                                           name="employee_ids[]"
                                           value="{{ $employee->id }}"
                                           id="emp_{{ $employee->id }}">
                                    <label class="form-check-label" for="emp_{{ $employee->id }}">
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                        <span class="text-muted">({{ $employee->employee_id }})</span>
                                        @if($employee->currentShiftAssignment)
                                        <span class="badge bg-secondary">
                                            Current: {{ $employee->currentShiftAssignment->shift->name }}
                                        </span>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <small class="text-muted">
                                Selected: <span id="selectedCount">0</span> employee(s)
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="shift_id_bulk" class="form-label required">Shift</label>
                            <select name="shift_id" id="shift_id_bulk" class="form-select" required>
                                <option value="">Select Shift</option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->name }}
                                    ({{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="effective_from_bulk" class="form-label required">Effective From</label>
                                <input type="date"
                                       name="effective_from"
                                       id="effective_from_bulk"
                                       class="form-control"
                                       value="{{ date('Y-m-d') }}"
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label for="effective_to_bulk" class="form-label">Effective To</label>
                                <input type="date"
                                       name="effective_to"
                                       id="effective_to_bulk"
                                       class="form-control">
                                <small class="text-muted">Leave empty for permanent</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_permanent"
                                       value="1"
                                       id="is_permanent_bulk"
                                       checked>
                                <label class="form-check-label" for="is_permanent_bulk">
                                    Permanent Assignment
                                </label>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Note:</strong> This will end current shift assignments for selected employees.
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-users mr-2"></i>
                            Assign Selected Employees
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
        infoEl.textContent = 'Current Shift: ' + currentShift;
        infoEl.classList.add('text-warning');
    } else {
        infoEl.textContent = 'No current shift assigned';
        infoEl.classList.remove('text-warning');
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
        alert('Please select at least one employee');
        return false;
    }
});
</script>
@endpush
@endsection
