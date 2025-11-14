@extends('layouts.tenant')

@section('title', $shift->shift_name)

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center">
                <a href="{{ route('tenant.payroll.shifts.index', $tenant) }}" class="text-gray-600 hover:text-gray-900 mr-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $shift->name }}</h1>
                    <p class="text-gray-600">{{ $shift->code }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tenant.payroll.shifts.edit', [$tenant, $shift->id]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Shift
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Shift Details -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shift Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Status</small>
                        <p class="mb-0">
                            @if($shift->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Working Hours</small>
                        <p class="mb-0">
                            {{ \Carbon\Carbon::parse($shift->start_time)->format('g:i A') }} -
                            {{ \Carbon\Carbon::parse($shift->end_time)->format('g:i A') }}
                        </p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Work Hours</small>
                        <p class="mb-0">{{ $shift->work_hours }} hours</p>
                    </div>

                    @if($shift->late_grace_minutes)
                    <div class="mb-3">
                        <small class="text-muted">Late Grace Period</small>
                        <p class="mb-0">{{ $shift->late_grace_minutes }} minutes</p>
                    </div>
                    @endif

                    @if($shift->shift_allowance > 0)
                    <div class="mb-3">
                        <small class="text-muted">Shift Allowance</small>
                        <p class="mb-0 text-success font-semibold">₦{{ number_format($shift->shift_allowance, 2) }}</p>
                    </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted">Working Days</small>
                        <div class="flex flex-wrap gap-1 mt-1">
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

                    @if($shift->description)
                    <div class="mb-3">
                        <small class="text-muted">Description</small>
                        <p class="mb-0">{{ $shift->description }}</p>
                    </div>
                    @endif

                    <div class="mb-0">
                        <small class="text-muted">Assigned Employees</small>
                        <p class="mb-0">{{ $shift->employee_assignments_count ?? 0 }} employee(s)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Employees -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Assigned Employees</h5>
                    <a href="{{ route('tenant.payroll.shifts.assign-employees', $tenant) }}?shift_id={{ $shift->id }}"
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Assign Employees
                    </a>
                </div>
                <div class="card-body">
                    @if($assignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Effective From</th>
                                    <th>Effective To</th>
                                    <th>Status</th>
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
                                    <td>{{ \Carbon\Carbon::parse($assignment->effective_from)->format('M d, Y') }}</td>
                                    <td>
                                        @if($assignment->effective_to)
                                        {{ \Carbon\Carbon::parse($assignment->effective_to)->format('M d, Y') }}
                                        @else
                                        <span class="text-muted">Ongoing</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$assignment->effective_to || \Carbon\Carbon::parse($assignment->effective_to) >= now())
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Ended</span>
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
                        <i class="fas fa-users text-gray-300" style="font-size: 3rem;"></i>
                        <p class="text-gray-500 mt-3">No employees assigned to this shift yet.</p>
                        <a href="{{ route('tenant.payroll.shifts.assign-employees', $tenant) }}?shift_id={{ $shift->id }}"
                           class="btn btn-primary mt-2">
                            <i class="fas fa-plus mr-2"></i>
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
