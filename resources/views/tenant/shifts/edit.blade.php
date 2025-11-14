@extends('layouts.tenant')

@section('title', 'Edit Shift')

@section('page-title', 'Edit Shift')
@section('page-description', 'Update shift configuration')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('tenant.payroll.shifts.show', [$tenant, $shift->id]) }}"
               class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Back to Shift Details') }}
            </a>
        </div>
        <div class="ml-0">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Edit Shift') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('Update shift configuration') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('tenant.payroll.shifts.update', [$tenant, $shift->id]) }}">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">{{ __('Basic Information') }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift Name') }} <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $shift->name) }}"
                                           required>
                                    @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift Code') }} <span class="text-red-500">*</span></label>
                                    <input type="text"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('code') border-red-300 @enderror"
                                           id="code"
                                           name="code"
                                           value="{{ old('code', $shift->code) }}"
                                           required>
                                    @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">{{ __('Working Hours') }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Time') }} <span class="text-red-500">*</span></label>
                                    <input type="time"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('start_time') border-red-300 @enderror"
                                           id="start_time"
                                           name="start_time"
                                           value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}"
                                           required>
                                    @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Time') }} <span class="text-red-500">*</span></label>
                                    <input type="time"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('end_time') border-red-300 @enderror"
                                           id="end_time"
                                           name="end_time"
                                           value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}"
                                           required>
                                    @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="work_hours" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Work Hours') }} <span class="text-red-500">*</span></label>
                                    <input type="number"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('work_hours') border-red-300 @enderror"
                                           id="work_hours"
                                           name="work_hours"
                                           value="{{ old('work_hours', $shift->work_hours) }}"
                                           step="0.5"
                                           min="0"
                                           max="24"
                                           required>
                                    @error('work_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="late_grace_minutes" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Late Grace Period (min)') }}</label>
                                    <input type="number"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('late_grace_minutes') border-red-300 @enderror"
                                           id="late_grace_minutes"
                                           name="late_grace_minutes"
                                           value="{{ old('late_grace_minutes', $shift->late_grace_minutes) }}"
                                           min="0"
                                           max="60">
                                    @error('late_grace_minutes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Working Days -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">{{ __('Working Days') }}</h3>

                            <div class="mb-6">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @php
                                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                        // Handle working_days as array (JSON cast) or CSV string
                                        $workingDays = is_array($shift->working_days) ? $shift->working_days : explode(',', (string) $shift->working_days);
                                        $oldDays = old('working_days', $workingDays);
                                    @endphp
                                    @foreach($days as $day)
                                    <div class="flex items-center">
                                        <input class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                               type="checkbox"
                                               name="working_days[]"
                                               value="{{ $day }}"
                                               id="day_{{ $day }}"
                                               {{ in_array($day, $oldDays) ? 'checked' : '' }}>
                                        <label class="ml-2 block text-sm text-gray-900" for="day_{{ $day }}">
                                            {{ ucfirst($day) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @error('working_days')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Additional Settings -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">{{ __('Additional Settings') }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="shift_allowance" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift Allowance (₦)') }}</label>
                                    <input type="number"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('shift_allowance') border-red-300 @enderror"
                                           id="shift_allowance"
                                           name="shift_allowance"
                                           value="{{ old('shift_allowance', $shift->shift_allowance) }}"
                                           step="0.01"
                                           min="0">
                                    @error('shift_allowance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div></div>
                            </div>

                            <div class="mb-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-300 @enderror"
                                          id="description"
                                          name="description"
                                          rows="3">{{ old('description', $shift->description) }}</textarea>
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <div class="flex items-center">
                                    <input class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                           type="checkbox"
                                           name="is_active"
                                           value="1"
                                           id="is_active"
                                           {{ old('is_active', $shift->is_active) ? 'checked' : '' }}>
                                    <label class="ml-2 block text-sm text-gray-900" for="is_active">
                                        {{ __('Active (can be assigned to employees)') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                            <a href="{{ route('tenant.payroll.shifts.show', [$tenant, $shift->id]) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-times mr-2"></i>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-save mr-2"></i>
                                {{ __('Update Shift') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                        {{ __('Shift Information') }}
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Created') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shift->created_at->format('M d, Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Last Updated') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shift->updated_at->format('M d, Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Assigned Employees') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $shift->employee_assignments_count ?? 0 }} {{ __('employee(s)') }}</dd>
                        </div>
                    </div>

                    @if($shift->employee_assignments_count > 0)
                    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">
                                    {{ __('Changes to shift hours will affect') }} {{ $shift->employee_assignments_count }} {{ __('assigned employee(s).') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
