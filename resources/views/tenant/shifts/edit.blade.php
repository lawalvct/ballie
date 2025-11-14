@extends('layouts.tenant')

@section('title', 'Edit Shift')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('tenant.payroll.shifts.show', [$tenant, $shift->id]) }}" class="text-gray-600 hover:text-gray-900 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Shift</h1>
        </div>
        <p class="text-gray-600 ml-9">Update shift configuration</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('tenant.payroll.shifts.update', [$tenant, $shift->id]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <h5 class="mb-3 pb-2 border-bottom">Basic Information</h5>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label required">Shift Name</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $shift->name) }}"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="code" class="form-label required">Shift Code</label>
                                <input type="text"
                                       class="form-control @error('code') is-invalid @enderror"
                                       id="code"
                                       name="code"
                                       value="{{ old('code', $shift->code) }}"
                                       required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <h5 class="mb-3 pb-2 border-bottom mt-4">Working Hours</h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label required">Start Time</label>
                                <input type="time"
                                       class="form-control @error('start_time') is-invalid @enderror"
                                       id="start_time"
                                       name="start_time"
                                       value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}"
                                       required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_time" class="form-label required">End Time</label>
                                <input type="time"
                                       class="form-control @error('end_time') is-invalid @enderror"
                                       id="end_time"
                                       name="end_time"
                                       value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}"
                                       required>
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="work_hours" class="form-label required">Work Hours</label>
                                <input type="number"
                                       class="form-control @error('work_hours') is-invalid @enderror"
                                       id="work_hours"
                                       name="work_hours"
                                       value="{{ old('work_hours', $shift->work_hours) }}"
                                       step="0.5"
                                       min="0"
                                       max="24"
                                       required>
                                @error('work_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="late_grace_minutes" class="form-label">Late Grace Period (min)</label>
                                <input type="number"
                                       class="form-control @error('late_grace_minutes') is-invalid @enderror"
                                       id="late_grace_minutes"
                                       name="late_grace_minutes"
                                       value="{{ old('late_grace_minutes', $shift->late_grace_minutes) }}"
                                       min="0"
                                       max="60">
                                @error('late_grace_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Working Days -->
                        <h5 class="mb-3 pb-2 border-bottom mt-4">Working Days</h5>

                        <div class="mb-3">
                            <div class="form-check-group">
                                @php
                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                    $workingDays = explode(',', $shift->working_days);
                                    $oldDays = old('working_days', $workingDays);
                                @endphp
                                @foreach($days as $day)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="working_days[]"
                                           value="{{ $day }}"
                                           id="day_{{ $day }}"
                                           {{ in_array($day, $oldDays) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="day_{{ $day }}">
                                        {{ ucfirst($day) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('working_days')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Additional Settings -->
                        <h5 class="mb-3 pb-2 border-bottom mt-4">Additional Settings</h5>

                        <div class="mb-3">
                            <label for="shift_allowance" class="form-label">Shift Allowance (₦)</label>
                            <input type="number"
                                   class="form-control @error('shift_allowance') is-invalid @enderror"
                                   id="shift_allowance"
                                   name="shift_allowance"
                                   value="{{ old('shift_allowance', $shift->shift_allowance) }}"
                                   step="0.01"
                                   min="0">
                            @error('shift_allowance')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3">{{ old('description', $shift->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_active"
                                       value="1"
                                       id="is_active"
                                       {{ old('is_active', $shift->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (can be assigned to employees)
                                </label>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('tenant.payroll.shifts.show', [$tenant, $shift->id]) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Update Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="mb-3">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Shift Information
                    </h5>

                    <div class="mb-3">
                        <small class="text-muted">Created</small>
                        <p class="mb-0">{{ $shift->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Last Updated</small>
                        <p class="mb-0">{{ $shift->updated_at->format('M d, Y') }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Assigned Employees</small>
                        <p class="mb-0">{{ $shift->employee_assignments_count ?? 0 }} employee(s)</p>
                    </div>

                    @if($shift->employee_assignments_count > 0)
                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Changes to shift hours will affect {{ $shift->employee_assignments_count }} assigned employee(s).
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
