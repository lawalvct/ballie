@extends('layouts.tenant')

@section('title', 'Create Shift')

@section('page-title', 'Create Shift')
@section('page-description', 'Define working hours and shift configuration')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('tenant.payroll.shifts.index', $tenant) }}" class="text-gray-600 hover:text-gray-900 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Create New Shift') }}</h1>
        </div>
        <p class="text-gray-600 ml-9">{{ __('Define working hours and shift configuration') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('tenant.payroll.shifts.store', $tenant) }}">
                        @csrf

                        <!-- Basic Information -->
                        <h5 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">{{ __('Basic Information') }}</h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift Name') }} <span class="text-red-500">*</span></label>
                                <input type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="e.g., Morning Shift, Night Shift"
                                       required>
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift Code') }} <span class="text-red-500">*</span></label>
                                <input type="text"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('code') border-red-300 @enderror"
                                       id="code"
                                       name="code"
                                       value="{{ old('code') }}"
                                       placeholder="e.g., MS, NS"
                                       required>
                                @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <h5 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 mt-6">{{ __('Working Hours') }}</h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Time') }} <span class="text-red-500">*</span></label>
                                <input type="time"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('start_time') border-red-300 @enderror"
                                       id="start_time"
                                       name="start_time"
                                       value="{{ old('start_time', '08:00') }}"
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
                                       value="{{ old('end_time', '17:00') }}"
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
                                       value="{{ old('work_hours', 8) }}"
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
                                       value="{{ old('late_grace_minutes', 15) }}"
                                       min="0"
                                       max="60">
                                <p class="mt-1 text-sm text-gray-500">{{ __('Tolerance for late arrivals') }}</p>
                                @error('late_grace_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Working Days -->
                        <h5 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 mt-6">{{ __('Working Days') }}</h5>

                        <div class="mb-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @php
                                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                    $oldDays = old('working_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
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

                        <!-- Additional Settings -->
                        <h5 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200 mt-6">{{ __('Additional Settings') }}</h5>

                        <div class="mb-6">
                            <label for="shift_allowance" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Shift Allowance (₦)') }}</label>
                            <input type="number"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('shift_allowance') border-red-300 @enderror"
                                   id="shift_allowance"
                                   name="shift_allowance"
                                   value="{{ old('shift_allowance', 0) }}"
                                   step="0.01"
                                   min="0">
                            <p class="mt-1 text-sm text-gray-500">{{ __('Extra pay for special shifts (evening/night)') }}</p>
                            @error('shift_allowance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-300 @enderror"
                                      id="description"
                                      name="description"
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                            <a href="{{ route('tenant.payroll.shifts.index', $tenant) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-times mr-2"></i>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-save mr-2"></i>
                                {{ __('Create Shift') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-gray-50 overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                        {{ __('Shift Configuration Guide') }}
                    </h5>

                    <div class="mb-4">
                        <h6 class="font-semibold text-gray-900">{{ __('Shift Name & Code') }}</h6>
                        <p class="text-sm text-gray-600">
                            {{ __('Use descriptive names (e.g., "Morning Shift", "Night Shift"). Code should be short (e.g., "MS", "NS").') }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-semibold text-gray-900">{{ __('Working Hours') }}</h6>
                        <p class="text-sm text-gray-600">
                            {{ __('Full day hours: Expected work hours (8, 12, etc.)') }}<br>
                            {{ __('Half day hours: Used for half-day attendance') }}<br>
                            {{ __('Grace period: Tolerance before marking late') }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-semibold text-gray-900">{{ __('Late Determination') }}</h6>
                        <p class="text-sm text-gray-600">
                            {{ __('Example: Shift starts 8:00 AM, Grace: 15 min') }}<br>
                            • {{ __('Clock in 8:10 AM → Present') }}<br>
                            • {{ __('Clock in 8:20 AM → Late (20 min)') }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="font-semibold text-gray-900">{{ __('Shift Allowance') }}</h6>
                        <p class="text-sm text-gray-600">
                            {{ __('Optional extra pay for special shifts (evening/night). Added to employee\'s monthly salary.') }}
                        </p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-lightbulb text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800">{{ __('Tip: Most companies use 8-hour shifts with 15-minute grace period.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
