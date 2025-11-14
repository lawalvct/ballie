@extends('layouts.tenant')

@section('title', 'Create Shift')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('tenant.payroll.shifts.index', $tenant) }}" class="text-gray-600 hover:text-gray-900 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Create New Shift</h1>
        </div>
        <p class="text-gray-600 ml-9">Define working hours and shift configuration</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('tenant.payroll.shifts.store', $tenant) }}">
                        @csrf

                        <!-- Basic Information -->
                        <h5 class="mb-3 pb-2 border-bottom">Basic Information</h5>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label required">Shift Name</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="e.g., Morning Shift, Night Shift"
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
                                       value="{{ old('code') }}"
                                       placeholder="e.g., MS, NS"
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
                                       value="{{ old('start_time', '08:00') }}"
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
                                       value="{{ old('end_time', '17:00') }}"
                                       required>
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="work_hours" class="form-label required">Work Hours</label>
                                <input type="number"
                                       class="form-control @error('work_hours') is-invalid @enderror"
                                       id="work_hours"
                                       name="work_hours"
                                       value="{{ old('work_hours', 8) }}"
                                       step="0.5"
                                       min="0"
                                       max="24"
                                       required>
                                @error('work_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="late_grace_minutes" class="form-label">Late Grace Period (min)</label>
                                <input type="number"
                                       class="form-control @error('late_grace_minutes') is-invalid @enderror"
                                       id="late_grace_minutes"
                                       name="late_grace_minutes"
                                       value="{{ old('late_grace_minutes', 15) }}"
                                       min="0"
                                       max="60">
                                <small class="text-muted">Tolerance for late arrivals</small>
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
                                    $oldDays = old('working_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
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
                                   value="{{ old('shift_allowance', 0) }}"
                                   step="0.01"
                                   min="0">
                            <small class="text-muted">Extra pay for special shifts (evening/night)</small>
                            @error('shift_allowance')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('tenant.payroll.shifts.index', $tenant) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Create Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="mb-3">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        Shift Configuration Guide
                    </h5>

                    <div class="mb-3">
                        <h6 class="font-semibold">Shift Name & Code</h6>
                        <p class="text-sm text-gray-600">
                            Use descriptive names (e.g., "Morning Shift", "Night Shift").
                            Code should be short (e.g., "MS", "NS").
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-semibold">Working Hours</h6>
                        <p class="text-sm text-gray-600">
                            Full day hours: Expected work hours (8, 12, etc.)<br>
                            Half day hours: Used for half-day attendance<br>
                            Grace period: Tolerance before marking late
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-semibold">Late Determination</h6>
                        <p class="text-sm text-gray-600">
                            Example: Shift starts 8:00 AM, Grace: 15 min<br>
                            • Clock in 8:10 AM → Present<br>
                            • Clock in 8:20 AM → Late (20 min)
                        </p>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-semibold">Shift Allowance</h6>
                        <p class="text-sm text-gray-600">
                            Optional extra pay for special shifts (evening/night).
                            Added to employee's monthly salary.
                        </p>
                    </div>

                    <div class="alert alert-info small">
                        <i class="fas fa-lightbulb mr-2"></i>
                        <strong>Tip:</strong> Most companies use 8-hour shifts with 15-minute grace period.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
