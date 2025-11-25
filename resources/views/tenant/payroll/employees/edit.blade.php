@extends('layouts.tenant')

@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')
@section('page-description', 'Update employee information and salary details.')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.payroll.employees.show', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Employee
            </a>
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-edit text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $employee->employee_number }}</p>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Editing employee</span>
            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
        </div>
    </div>

    <!-- Display any validation errors at the top of the form -->
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} error(s) with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Display success message if available -->
    @if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('tenant.payroll.employees.update', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
        @csrf
        @method('PUT')

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Form Completion</span>
                <span class="text-sm font-medium text-gray-700" id="progress-indicator">0% Complete</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Section 1: Personal Information (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-blue-500"></i>
                Personal Information
            </h3>

            <!-- Employee Photo -->
            <div class="mb-6 flex items-start space-x-6">
                <div class="flex-shrink-0">
                    <div class="relative">
                        <div id="avatar-preview" class="w-32 h-32 rounded-full border-4 border-gray-200 overflow-hidden bg-gray-100 flex items-center justify-center">
                            @if($employee->avatar)
                                <img id="avatar-image" src="{{ asset($employee->avatar) }}" alt="{{ $employee->full_name }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <img id="avatar-image" src="" alt="Employee Photo" class="hidden w-full h-full object-cover">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Employee Photo (Optional)
                    </label>
                    <div class="flex items-center space-x-3">
                        <label for="avatar" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $employee->avatar ? 'Change Photo' : 'Choose Photo' }}
                        </label>
                        @if($employee->avatar)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-700">Remove photo</span>
                            </label>
                        @endif
                        <button type="button" id="remove-avatar-preview" class="hidden inline-flex items-center px-4 py-2 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Cancel
                        </button>
                    </div>
                    <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/jpg" class="hidden">
                    <p class="mt-2 text-xs text-gray-500">
                        Accepted formats: JPG, JPEG, PNG. Maximum size: 2MB.
                    </p>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" required
                           value="{{ old('first_name', $employee->first_name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter first name">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" required
                           value="{{ old('last_name', $employee->last_name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Enter last name">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" required
                           value="{{ old('email', $employee->email) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="employee@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <input type="tel" name="phone" id="phone"
                           value="{{ old('phone', $employee->phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="+234 xxx xxx xxxx">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Birth
                    </label>
                    <input type="date" name="date_of_birth" id="date_of_birth"
                           value="{{ old('date_of_birth', $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Gender
                    </label>
                    <select name="gender" id="gender"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $employee->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $employee->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $employee->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Employment Details (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl mb-6">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="section-toggle w-full flex items-center justify-between" data-target="employment-section" aria-expanded="false">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-briefcase mr-2 text-green-500"></i>
                        Employment Details
                    </h3>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="employment-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <div class="flex space-x-2">
                            <select name="department_id" id="department_id" required
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openDepartmentModal()"
                                    class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Position
                        </label>
                        <div class="flex space-x-2">
                            <select name="position_id" id="position_id"
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Select Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}"
                                            data-department="{{ $position->department_id }}"
                                            {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }} ({{ $position->code }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openPositionModal()"
                                    class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        @error('position_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="job_title" class="block text-sm font-medium text-gray-700 mb-2">
                            Job Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="job_title" id="job_title" required
                               value="{{ old('job_title', $employee->job_title) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="e.g., Senior Accountant">
                        @error('job_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Hire Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="hire_date" id="hire_date" required
                               value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        @error('hire_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Employment Type <span class="text-red-500">*</span>
                        </label>
                        <select name="employment_type" id="employment_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Select Type</option>
                            <option value="full_time" {{ old('employment_type', $employee->employment_type) === 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('employment_type', $employee->employment_type) === 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('employment_type', $employee->employment_type) === 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="casual" {{ old('employment_type', $employee->employment_type) === 'casual' ? 'selected' : '' }}>Casual</option>
                            <option value="intern" {{ old('employment_type', $employee->employment_type) === 'intern' ? 'selected' : '' }}>Intern</option>
                        </select>
                        @error('employment_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pay_frequency" class="block text-sm font-medium text-gray-700 mb-2">
                            Pay Frequency <span class="text-red-500">*</span>
                        </label>
                        <select name="pay_frequency" id="pay_frequency" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="">Select Frequency</option>
                            <option value="monthly" {{ old('pay_frequency', $employee->pay_frequency) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly" {{ old('pay_frequency', $employee->pay_frequency) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="contract" {{ old('pay_frequency', $employee->pay_frequency) === 'contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                        @error('pay_frequency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attendance Deduction Exemption -->
                    <div class="form-group md:col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" name="attendance_deduction_exempt" id="attendance_deduction_exempt"
                                       value="1"
                                       {{ old('attendance_deduction_exempt', $employee->attendance_deduction_exempt) ? 'checked' : '' }}
                                       onchange="toggleAttendanceExemptionReason()"
                                       class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3">
                                    <span class="text-sm font-medium text-gray-900">
                                        <i class="fas fa-shield-alt text-blue-600 mr-1"></i>
                                        Exempt from Attendance Deductions
                                    </span>
                                    <p class="text-xs text-gray-600 mt-1">
                                        When enabled, this employee will NOT have salary deductions for absent days or receive overtime pay.
                                        Their attendance will still be tracked for reporting purposes only.
                                        <br>
                                        <strong>Use for:</strong> Contractors with flat rates, remote workers with flexible schedules, or executives with special compensation agreements.
                                    </p>
                                </span>
                            </label>

                            <div id="attendance_exemption_reason_container" class="mt-3 {{ old('attendance_deduction_exempt', $employee->attendance_deduction_exempt) ? '' : 'hidden' }}">
                                <label for="attendance_exemption_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Exemption Reason / Notes
                                </label>
                                <textarea name="attendance_exemption_reason" id="attendance_exemption_reason" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                          placeholder="e.g., Contract worker - flat monthly rate, Remote worker - flexible hours, Executive - special agreement">{{ old('attendance_exemption_reason', $employee->attendance_exemption_reason) }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle"></i> Optional: Document why this employee is exempt (for audit and reference purposes)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tin" class="block text-sm font-medium text-gray-700 mb-2">
                            Tax Identification Number (TIN)
                        </label>
                        <input type="text" name="tin" id="tin"
                               value="{{ old('tin', $employee->tin) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter TIN">
                        @error('tin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pension_pin" class="block text-sm font-medium text-gray-700 mb-2">
                            Pension PIN
                        </label>
                        <input type="text" name="pension_pin" id="pension_pin"
                               value="{{ old('pension_pin', $employee->pension_pin) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter Pension PIN">
                        @error('pension_pin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Salary Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl mb-6">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="section-toggle w-full flex items-center justify-between" data-target="salary-section" aria-expanded="false">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2 text-emerald-500"></i>
                        Salary Information
                    </h3>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="salary-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group md:col-span-2">
                        <label for="basic_salary" class="block text-sm font-medium text-gray-700 mb-2">
                            Basic Salary <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                            <input type="number" name="basic_salary" id="basic_salary" required min="0" step="0.01"
                                   value="{{ old('basic_salary', $employee->currentSalary ? $employee->currentSalary->basic_salary : 0) }}"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                   placeholder="0.00">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Current: <span id="basic_salary_formatted" class="font-medium text-gray-900">₦0</span></p>
                        @error('basic_salary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Salary Components (Allowances & Deductions)
                        </label>
                        <div class="space-y-3">
                            @foreach($salaryComponents as $component)
                                @php
                                    $existingComponent = $employee->currentSalary
                                        ? $employee->currentSalary->salaryComponents->firstWhere('salary_component_id', $component->id)
                                        : null;
                                @endphp
                                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="components[{{ $loop->index }}][id]" value="{{ $component->id }}"
                                                   {{ $existingComponent ? 'checked' : '' }}
                                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $component->name }}</span>
                                            <span class="ml-2 text-xs px-2 py-1 rounded-full
                                                {{ $component->type === 'allowance' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($component->type) }}
                                            </span>
                                        </label>
                                    </div>

                                    @if($component->calculation_type === 'fixed')
                                        <div class="w-48">
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">₦</span>
                                                <input type="number" name="components[{{ $loop->index }}][amount]" step="0.01" min="0"
                                                       value="{{ $existingComponent ? $existingComponent->amount : '' }}"
                                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm"
                                                       placeholder="0.00">
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-32">
                                            <div class="relative">
                                                <input type="number" name="components[{{ $loop->index }}][percentage]" step="0.01" min="0" max="100"
                                                       value="{{ $existingComponent ? $existingComponent->percentage : '' }}"
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                                       placeholder="0.00">
                                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-sm">%</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Address Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl mb-6">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="section-toggle w-full flex items-center justify-between" data-target="address-section" aria-expanded="false">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>
                        Address Information
                    </h3>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="address-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Street Address
                        </label>
                        <textarea name="address" id="address" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                  placeholder="Enter street address">{{ old('address', $employee->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            City
                        </label>
                        <input type="text" name="city" id="city"
                               value="{{ old('city', $employee->city) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter city">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                            State/Province
                        </label>
                        <input type="text" name="state" id="state"
                               value="{{ old('state', $employee->state) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter state">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Postal Code
                        </label>
                        <input type="text" name="postal_code" id="postal_code"
                               value="{{ old('postal_code', $employee->postal_code) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter postal code">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                            Country
                        </label>
                        <input type="text" name="country" id="country"
                               value="{{ old('country', $employee->country) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter country">
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Bank Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl mb-6">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="section-toggle w-full flex items-center justify-between" data-target="bank-section" aria-expanded="false">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-university mr-2 text-indigo-500"></i>
                        Bank Information
                    </h3>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="bank-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Bank Name
                        </label>
                        <input type="text" name="bank_name" id="bank_name"
                               value="{{ old('bank_name', $employee->bank_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter bank name">
                        @error('bank_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Number
                        </label>
                        <input type="text" name="account_number" id="account_number"
                               value="{{ old('account_number', $employee->account_number) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter account number">
                        @error('account_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group md:col-span-2">
                        <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Name
                        </label>
                        <input type="text" name="account_name" id="account_name"
                               value="{{ old('account_name', $employee->account_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Enter account name">
                        @error('account_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pension Information -->
                    <div class="md:col-span-2 mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-piggy-bank mr-2 text-purple-500"></i>
                            Pension Information
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="pfa_provider" class="block text-sm font-medium text-gray-700 mb-2">
                                    PFA Provider
                                </label>
                                <div class="flex gap-2">
                                    <select name="pfa_provider" id="pfa_provider"
                                        class="flex-1 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                                        <option value="">Select PFA Provider</option>
                                        @foreach(\App\Models\Pfa::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get() as $pfa)
                                            <option value="{{ $pfa->name }}" {{ old('pfa_provider', $employee->pfa_provider) === $pfa->name ? 'selected' : '' }}>{{ $pfa->name }}</option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('tenant.payroll.pfas.index', ['tenant' => $tenant->slug]) }}" target="_blank" class="px-3 py-3 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                </div>
                                @error('pfa_provider')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="rsa_pin" class="block text-sm font-medium text-gray-700 mb-2">
                                    RSA PIN
                                </label>
                                <input type="text" name="rsa_pin" id="rsa_pin"
                                       value="{{ old('rsa_pin', $employee->rsa_pin) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       placeholder="Enter RSA PIN">
                                <p class="mt-1 text-xs text-gray-500">Retirement Savings Account PIN</p>
                                @error('rsa_pin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group md:col-span-2">
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" name="pension_exempt" id="pension_exempt"
                                               value="1"
                                               {{ old('pension_exempt', $employee->pension_exempt) ? 'checked' : '' }}
                                               class="mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        <span class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">
                                                <i class="fas fa-user-shield text-purple-600 mr-1"></i>
                                                Exempt from Pension Contributions
                                            </span>
                                            <p class="text-xs text-gray-600 mt-1">
                                                When enabled, this employee will NOT have pension deductions (8% employee + 10% employer).
                                                <br>
                                                <strong>Use for:</strong> Contract workers, interns, or employees with special pension arrangements.
                                            </p>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6">
            <a href="{{ route('tenant.payroll.employees.show', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
               class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </a>
            <div class="flex items-center space-x-4">
                <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Update Employee
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Quick Create Department Modal -->
<div id="departmentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Create Department</h3>
        </div>
        <form id="departmentForm">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="dept_name" class="block text-sm font-medium text-gray-700 mb-2">Department Name *</label>
                    <input type="text" id="dept_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="dept_code" class="block text-sm font-medium text-gray-700 mb-2">Department Code *</label>
                    <input type="text" id="dept_code" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="dept_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="dept_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
                <button type="button" onclick="closeDepartmentModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Quick Create Position Modal -->
<div id="positionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Create Position</h3>
        </div>
        <form id="positionForm">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="pos_name" class="block text-sm font-medium text-gray-700 mb-2">Position Name *</label>
                    <input type="text" id="pos_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="pos_code" class="block text-sm font-medium text-gray-700 mb-2">Position Code *</label>
                    <input type="text" id="pos_code" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="pos_level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                    <input type="number" id="pos_level" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label for="pos_department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select id="pos_department_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="pos_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="pos_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
                <button type="button" onclick="closePositionModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Create</button>
            </div>
        </form>
    </div>
</div>

<style>
.section-toggle:hover .transform {
    transform: rotate(180deg);
}

.section-toggle[aria-expanded="true"] .transform {
    transform: rotate(180deg);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group input.error,
.form-group select.error,
.form-group textarea.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Section toggle functionality
    const sectionToggles = document.querySelectorAll('.section-toggle');

    sectionToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);
            const arrow = this.querySelector('svg');

            if (targetSection.classList.contains('hidden')) {
                targetSection.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
                this.setAttribute('aria-expanded', 'true');
            } else {
                targetSection.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
                this.setAttribute('aria-expanded', 'false');
            }
        });
    });

    // Progress tracking
    function updateProgress() {
        const requiredFields = document.querySelectorAll('input[required], select[required]');
        const filledFields = Array.from(requiredFields).filter(field => field.value.trim() !== '');
        const progress = (filledFields.length / requiredFields.length) * 100;

        document.getElementById('progress-bar').style.width = progress + '%';
        document.getElementById('progress-indicator').textContent = Math.round(progress) + '% Complete';

        if (progress === 100) {
            document.getElementById('progress-bar').classList.add('bg-green-600');
            document.getElementById('progress-bar').classList.remove('bg-blue-600');
        } else {
            document.getElementById('progress-bar').classList.add('bg-blue-600');
            document.getElementById('progress-bar').classList.remove('bg-green-600');
        }
    }

    // Add event listeners to all form inputs for progress tracking
    const allInputs = document.querySelectorAll('input, select, textarea');
    allInputs.forEach(input => {
        input.addEventListener('input', updateProgress);
        input.addEventListener('change', updateProgress);
    });

    // Form validation
    const form = document.getElementById('employeeForm');
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = document.querySelectorAll('#employeeForm input[required], #employeeForm select[required]');
        const sectionsWithErrors = new Set();
        const errorMessages = [];

        requiredFields.forEach(field => {
            // Skip fields in hidden modals
            const inModal = field.closest('#departmentModal, #positionModal');
            if (inModal) {
                return;
            }

            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');

                // Find which section this field belongs to
                const section = field.closest('[id$="-section"]');
                if (section) {
                    sectionsWithErrors.add(section.id);
                }

                // Get field label
                const label = field.previousElementSibling || field.closest('.form-group')?.querySelector('label');
                const fieldName = label ? label.textContent.replace('*', '').trim() : field.name;
                errorMessages.push(fieldName);
            } else {
                field.classList.remove('error');
            }
        });

        // Email validation
        const emailField = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailField.value && !emailRegex.test(emailField.value)) {
            isValid = false;
            emailField.classList.add('error');
            alert('Please enter a valid email address.');
            e.preventDefault();
            return;
        }

        if (!isValid) {
            // Auto-expand sections with errors
            sectionsWithErrors.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                if (section && section.classList.contains('hidden')) {
                    section.classList.remove('hidden');
                    const toggle = document.querySelector(`[data-target="${sectionId}"]`);
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', 'true');
                        const arrow = toggle.querySelector('svg');
                        if (arrow) arrow.style.transform = 'rotate(180deg)';
                    }
                }
            });

            alert(`Please fill in all required fields. ${errorMessages.length} field(s) need attention:\n- ${errorMessages.join('\n- ')}`);
            e.preventDefault();
        }
    });

    // Position filtering by department
    const departmentSelect = document.getElementById('department_id');
    const positionSelect = document.getElementById('position_id');
    const allPositionOptions = Array.from(positionSelect.options);

    function filterPositions() {
        const selectedDepartmentId = departmentSelect.value;

        // Clear current options except the first one
        positionSelect.innerHTML = '<option value="">Select Position</option>';

        // Filter and add relevant positions
        allPositionOptions.slice(1).forEach(option => {
            if (!selectedDepartmentId || option.dataset.department === selectedDepartmentId) {
                positionSelect.appendChild(option.cloneNode(true));
            }
        });
    }

    // Filter positions when department changes
    if (departmentSelect && positionSelect) {
        departmentSelect.addEventListener('change', filterPositions);

        // Initial filter on page load
        if (departmentSelect.value) {
            filterPositions();
        }
    }

    // Initialize progress on page load
    updateProgress();

    // Format salary input with thousand separators
    const salaryInput = document.getElementById('basic_salary');
    if (salaryInput) {
        salaryInput.addEventListener('input', function() {
            const value = this.value;
            const formatted = new Intl.NumberFormat('en-NG').format(value || 0);
            document.getElementById('basic_salary_formatted').textContent = '₦' + formatted;
        });

        // Initialize formatted display on page load
        const formatted = new Intl.NumberFormat('en-NG').format(salaryInput.value || 0);
        document.getElementById('basic_salary_formatted').textContent = '₦' + formatted;
    }
});

// Quick create functions
function openDepartmentModal() {
    document.getElementById('departmentModal').classList.remove('hidden');
}

function closeDepartmentModal() {
    document.getElementById('departmentModal').classList.add('hidden');
    document.getElementById('departmentForm').reset();
}

function openPositionModal() {
    const selectedDept = document.getElementById('department_id').value;
    if (selectedDept) {
        document.getElementById('pos_department_id').value = selectedDept;
    }
    document.getElementById('positionModal').classList.remove('hidden');
}

function closePositionModal() {
    document.getElementById('positionModal').classList.add('hidden');
    document.getElementById('positionForm').reset();
}

// Handle department creation
document.getElementById('departmentForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const name = document.getElementById('dept_name').value;
    const code = document.getElementById('dept_code').value;
    const description = document.getElementById('dept_description').value;

    if (!name || !code) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';

        const response = await fetch('{{ route("tenant.payroll.departments.store", $tenant) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, code, description })
        });

        if (response.ok) {
            const data = await response.json();
            const departmentSelect = document.getElementById('department_id');
            const option = new Option(data.department.name, data.department.id, true, true);
            departmentSelect.add(option);
            closeDepartmentModal();
            showNotification('Department created successfully!', 'success');
        } else {
            const error = await response.json();
            alert('Error: ' + (error.message || 'Failed to create department'));
        }

        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    } catch (error) {
        console.error('Error creating department:', error);
        alert('Error: ' + error.message);

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create';
    }
});

// Handle position creation
document.getElementById('positionForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const name = document.getElementById('pos_name').value;
    const code = document.getElementById('pos_code').value;
    const level = document.getElementById('pos_level').value;
    const deptId = document.getElementById('pos_department_id').value;
    const description = document.getElementById('pos_description').value;

    if (!name || !code) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';

        const response = await fetch('{{ route("tenant.payroll.positions.store", $tenant) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, code, level, department_id: deptId, description })
        });

        if (response.ok) {
            const data = await response.json();
            const positionSelect = document.getElementById('position_id');
            const option = new Option(`${data.position.name} (${data.position.code})`, data.position.id, true, true);
            option.setAttribute('data-department', data.position.department_id || '');
            positionSelect.add(option);
            closePositionModal();
            showNotification('Position created successfully!', 'success');
        } else {
            const error = await response.json();
            alert('Error: ' + (error.message || 'Failed to create position'));
        }

        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    } catch (error) {
        console.error('Error creating position:', error);
        alert('Error: ' + error.message);

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create';
    }
});

// Helper function to show notifications
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Toggle attendance exemption reason field
function toggleAttendanceExemptionReason() {
    const checkbox = document.getElementById('attendance_deduction_exempt');
    const container = document.getElementById('attendance_exemption_reason_container');

    if (checkbox.checked) {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
        document.getElementById('attendance_exemption_reason').value = '';
    }
}

// Avatar upload and preview functionality
const avatarInput = document.getElementById('avatar');
const avatarImage = document.getElementById('avatar-image');
const removeAvatarPreviewBtn = document.getElementById('remove-avatar-preview');

if (avatarInput) {
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            // Validate file size (2MB)
            if (file.size > 2048 * 1024) {
                alert('File size must be less than 2MB');
                avatarInput.value = '';
                return;
            }

            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('Only JPG, JPEG and PNG files are allowed');
                avatarInput.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarImage.src = e.target.result;
                avatarImage.classList.remove('hidden');
                if (removeAvatarPreviewBtn) {
                    removeAvatarPreviewBtn.classList.remove('hidden');
                }
            };
            reader.readAsDataURL(file);
        }
    });
}

if (removeAvatarPreviewBtn) {
    removeAvatarPreviewBtn.addEventListener('click', function() {
        avatarInput.value = '';
        const currentAvatar = '{{ $employee->avatar ? asset($employee->avatar) : "" }}';
        if (currentAvatar) {
            avatarImage.src = currentAvatar;
        } else {
            avatarImage.src = '';
            avatarImage.classList.add('hidden');
        }
        removeAvatarPreviewBtn.classList.add('hidden');
    });
}
</script>
@endsection
