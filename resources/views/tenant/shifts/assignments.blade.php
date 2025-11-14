@extends('layouts.tenant')

@section('title', 'Shift Assignments')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Shift Assignments</h1>
            <p class="text-gray-600 mt-1">Manage employee shift assignments</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('tenant.payroll.shifts.index', $tenant) }}" class="btn btn-outline-secondary">
                <i class="fas fa-clock mr-2"></i>
                Manage Shifts
            </a>
            <a href="{{ route('tenant.payroll.shifts.assign-employees', $tenant) }}" class="btn btn-primary">
                <i class="fas fa-user-plus mr-2"></i>
                Assign Employees
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tenant.payroll.shifts.assignments', $tenant) }}" class="row g-3">
                <div class="col-md-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select name="employee_id" id="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="shift_id" class="form-label">Shift</label>
                    <select name="shift_id" id="shift_id" class="form-select">
                        <option value="">All Shifts</option>
                        @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                            {{ $shift->shift_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>Ended</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($assignments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Shift</th>
                            <th>Shift Hours</th>
                            <th>Effective From</th>
                            <th>Effective To</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                        <tr>
                            <td>
                                <div class="font-medium">
                                    {{ $assignment->employee->first_name }} {{ $assignment->employee->last_name }}
                                </div>
                                <small class="text-muted">{{ $assignment->employee->employee_id }}</small>
                            </td>
                            <td>{{ $assignment->employee->department->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $assignment->shift->name }}</span>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($assignment->shift->start_time)->format('g:i A') }} -
                                {{ \Carbon\Carbon::parse($assignment->shift->end_time)->format('g:i A') }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($assignment->effective_from)->format('M d, Y') }}</td>
                            <td>
                                @if($assignment->effective_to)
                                {{ \Carbon\Carbon::parse($assignment->effective_to)->format('M d, Y') }}
                                @else
                                <span class="text-muted">Ongoing</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $isActive = !$assignment->effective_to || \Carbon\Carbon::parse($assignment->effective_to) >= now();
                                @endphp
                                @if($isActive)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Ended</span>
                                @endif
                            </td>
                            <td>
                                @if($isActive)
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="endAssignment({{ $assignment->id }}, '{{ $assignment->employee->first_name }} {{ $assignment->employee->last_name }}')">
                                    <i class="fas fa-stop"></i>
                                    End
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $assignments->links() }}
            @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times text-gray-300" style="font-size: 4rem;"></i>
                <h5 class="text-gray-600 mt-3 mb-2">No Shift Assignments Found</h5>
                <p class="text-gray-500 mb-4">Start by assigning shifts to employees.</p>
                <a href="{{ route('tenant.payroll.shifts.assign-employees', $tenant) }}" class="btn btn-primary">
                    <i class="fas fa-user-plus mr-2"></i>
                    Assign Employees
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- End Assignment Modal -->
<div class="modal fade" id="endAssignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">End Shift Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="endAssignmentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to end the shift assignment for <strong id="employeeNameToEnd"></strong>?</p>

                    <div class="mb-3">
                        <label for="effective_to" class="form-label required">End Date</label>
                        <input type="date"
                               class="form-control"
                               id="effective_to"
                               name="effective_to"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">End Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function endAssignment(assignmentId, employeeName) {
    const modal = new bootstrap.Modal(document.getElementById('endAssignmentModal'));
    document.getElementById('employeeNameToEnd').textContent = employeeName;
    document.getElementById('endAssignmentForm').action =
        '{{ route("tenant.payroll.shifts.end-assignment", [$tenant, ":id"]) }}'.replace(':id', assignmentId);
    modal.show();
}
</script>
@endpush
@endsection
