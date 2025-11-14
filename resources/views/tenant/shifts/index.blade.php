@extends('layouts.tenant')

@section('title', 'Shift Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Shift Management</h1>
            <p class="text-gray-600 mt-1">Manage working hours and shift schedules</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('tenant.payroll.shifts.assignments', $tenant) }}"
               class="btn btn-outline-primary">
                <i class="fas fa-users mr-2"></i>
                View Assignments
            </a>
            <a href="{{ route('tenant.payroll.shifts.create', $tenant) }}"
               class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Create Shift
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Shifts Grid -->
    <div class="row">
        @forelse($shifts as $shift)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="card-body">
                    <!-- Shift Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h5 class="text-lg font-semibold text-gray-900 mb-1">
                                {{ $shift->name }}
                            </h5>
                            <span class="badge bg-secondary">{{ $shift->code }}</span>
                            @if(!$shift->is_active)
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-ghost" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('tenant.payroll.shifts.show', [$tenant, $shift->id]) }}">
                                        <i class="fas fa-eye mr-2"></i> View Details
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('tenant.payroll.shifts.edit', [$tenant, $shift->id]) }}">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item text-danger"
                                            onclick="deleteShift({{ $shift->id }}, '{{ $shift->name }}')">
                                        <i class="fas fa-trash mr-2"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Shift Time -->
                    <div class="mb-3">
                        <div class="flex items-center text-gray-700 mb-2">
                            <i class="fas fa-clock w-5"></i>
                            <span class="font-medium">
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} -
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }}
                            </span>
                        </div>
                        <div class="flex items-center text-gray-700 mb-2">
                            <i class="fas fa-hourglass-half w-5"></i>
                            <span>{{ $shift->work_hours }} hours (Full Day)</span>
                        </div>
                        @if($shift->late_grace_minutes)
                        <div class="flex items-center text-gray-700 mb-2">
                            <i class="fas fa-user-clock w-5"></i>
                            <span>{{ $shift->late_grace_minutes }} min grace period</span>
                        </div>
                        @endif
                    </div>

                    <!-- Working Days -->
                    <div class="mb-3">
                        <div class="text-xs text-gray-500 mb-1">Working Days</div>
                        <div class="flex flex-wrap gap-1">
                            @php
                                $days = explode(',', $shift->working_days);
                                $dayMap = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed',
                                          'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'];
                            @endphp
                            @foreach($dayMap as $day => $short)
                                @if(in_array($day, $days))
                                <span class="badge bg-primary">{{ $short }}</span>
                                @else
                                <span class="badge bg-light text-muted">{{ $short }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Shift Allowance -->
                    @if($shift->shift_allowance > 0)
                    <div class="alert alert-info py-2 mb-3">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        <strong>₦{{ number_format($shift->shift_allowance, 2) }}</strong> allowance
                    </div>
                    @endif

                    <!-- Employees Count -->
                    <div class="border-t pt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-users mr-1"></i>
                                {{ $shift->employee_assignments_count ?? 0 }} employee(s)
                            </span>
                            <a href="{{ route('tenant.payroll.shifts.show', [$tenant, $shift->id]) }}"
                               class="text-sm text-primary hover:underline">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clock text-gray-300" style="font-size: 4rem;"></i>
                    <h5 class="text-gray-600 mt-3 mb-2">No Shifts Found</h5>
                    <p class="text-gray-500 mb-4">Create your first shift to manage employee working hours.</p>
                    <a href="{{ route('tenant.payroll.shifts.create', $tenant) }}" class="btn btn-primary">
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
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="shiftNameToDelete"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    This action cannot be undone. Shifts with active assignments cannot be deleted.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Shift</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteShift(shiftId, shiftName) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    document.getElementById('shiftNameToDelete').textContent = shiftName;
    document.getElementById('deleteForm').action =
        '{{ route("tenant.payroll.shifts.destroy", [$tenant, ":id"]) }}'.replace(':id', shiftId);
    modal.show();
}
</script>
@endpush
@endsection
