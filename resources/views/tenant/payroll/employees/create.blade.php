@extends('layouts.tenant')

@section('title', 'Add Employee')
@section('page-title', 'Add New Employee')
@section('page-description', 'Create a new employee record in your database.')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Employees
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Creating new employee</span>
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
        </div>
    </div>

    <!-- Display any validation errors at the top of the form -->
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
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
                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Display error message if available -->
    @if (session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('tenant.payroll.employees.store', ['tenant' => $tenant->slug]) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
        @csrf

        <!-- Progress Indicator -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Complete all required fields</h3>
                <span class="text-sm font-medium text-blue-600" id="progress-indicator">0% Complete</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
            </div>
        </div>

        <!-- Section 1: Personal Information (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">1</span>
                Personal Information
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <!-- Employee Photo Upload -->
            <div class="mb-6 flex items-start space-x-6">
                <div class="flex-shrink-0">
                    <div class="relative">
                        <div id="avatar-preview" class="w-32 h-32 rounded-full border-4 border-gray-200 overflow-hidden bg-gray-100 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <img id="avatar-image" src="" alt="Employee Photo" class="hidden w-full h-full object-cover">
                        </div>
                    </div>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Employee Photo (Optional)
                    </label>
                    <div class="flex items-center space-x-3">
                        <label for="avatar" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Choose Photo
                        </label>
                        <button type="button" id="remove-avatar" class="hidden inline-flex items-center px-4 py-2 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove
                        </button>
                    </div>
                    <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/jpg" class="hidden">
                    <p class="mt-2 text-xs text-gray-500">
                        Accepted formats: JPG, JPEG, PNG. Maximum size: 2MB.
                    </p>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="hidden text-sm text-red-600 mt-1 field-error" id="avatar-error"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('first_name') ? 'border-red-300' : 'border-gray-300' }}"
                        value="{{ old('first_name') }}" placeholder="John">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="hidden text-sm text-red-600 mt-1 field-error" id="first_name-error"></div>
                </div>

                <div class="form-group">
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('last_name') ? 'border-red-300' : 'border-gray-300' }}"
                        value="{{ old('last_name') }}" placeholder="Lawal">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="hidden text-sm text-red-600 mt-1 field-error" id="last_name-error"></div>
                </div>

                <div class="form-group">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }}"
                        value="{{ old('email') }}" placeholder="john@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="hidden text-sm text-red-600 mt-1 field-error" id="email-error"></div>
                </div>

                <div class="form-group">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone" id="phone" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('phone') ? 'border-red-300' : 'border-gray-300' }}"
                        value="{{ old('phone') }}" placeholder="+1 (555) 123-4567">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="hidden text-sm text-red-600 mt-1 field-error" id="phone-error"></div>
                </div>

                <div class="form-group">
                    <label for="employee_number" class="block text-sm font-medium text-gray-700 mb-1">
                        Employee ID
                    </label>
                    <input type="text" name="employee_number" id="employee_number"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('employee_number') ? 'border-red-300' : 'border-gray-300' }}"
                        value="{{ old('employee_number') }}" placeholder="EMP001">
                    @error('employee_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Hire Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="hire_date" id="hire_date" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('hire_date') ? 'border-red-300' : 'border-gray-300' }}"
                        value="{{ old('hire_date') }}">
                    @error('hire_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="hidden text-sm text-red-600 mt-1 field-error" id="hire_date-error"></div>
                </div>
            </div>
        </div>

        <!-- Section 2: Employment Details (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="w-full flex items-center justify-between text-left section-toggle" data-target="employment-section">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 mr-2 text-sm font-semibold">2</span>
                        Employment Details
                        <span class="text-red-500 ml-1">*</span>
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="employment-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <div class="flex space-x-2">
                            <select name="department_id" id="department_id" required
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('department_id') ? 'border-red-300' : 'border-gray-300' }}">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openDepartmentModal()"
                                class="mt-1 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="department_id-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Position
                        </label>
                        <div class="flex space-x-2">
                            <select name="position_id" id="position_id"
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('position_id') ? 'border-red-300' : 'border-gray-300' }}">
                                <option value="">Select Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}"
                                        data-department="{{ $position->department_id }}"
                                        {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }} ({{ $position->code }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openPositionModal()"
                                class="mt-1 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                        @error('position_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="position_id-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Job Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="job_title" id="job_title" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('job_title') ? 'border-red-300' : 'border-gray-300' }}"
                            value="{{ old('job_title') }}" placeholder="Software Engineer">
                        @error('job_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="job_title-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Employment Type <span class="text-red-500">*</span>
                        </label>
                        <select name="employment_type" id="employment_type" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('employment_type') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Select Type</option>
                            <option value="full_time" {{ old('employment_type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('employment_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="intern" {{ old('employment_type') === 'intern' ? 'selected' : '' }}>Intern</option>
                            <option value="casual" {{ old('employment_type') === 'casual' ? 'selected' : '' }}>Casual</option>
                        </select>
                        @error('employment_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="employment_type-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status
                        </label>
                        <select name="status" id="status"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Salary Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="w-full flex items-center justify-between text-left section-toggle" data-target="salary-section">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 mr-2 text-sm font-semibold">3</span>
                        Salary Information
                        <span class="text-red-500 ml-1">*</span>
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="salary-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="basic_salary" class="block text-sm font-medium text-gray-700 mb-1">
                            Base Salary <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">

                            <input type="number" name="basic_salary" id="basic_salary" step="0.01" min="0" required
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 shadow-sm sm:text-sm rounded-md {{ $errors->has('basic_salary') ? 'border-red-300' : 'border-gray-300' }}"
                                value="{{ old('basic_salary') }}" placeholder="0.00">
                            <div class="mt-1 text-sm text-gray-600" id="basic_salary_formatted">
                                @if(old('basic_salary'))
                                    ₦{{ number_format(old('basic_salary'), 2, '.', ',') }}
                                @else
                                    ₦0.00
                                @endif
                            </div>
                        </div>
                        @error('basic_salary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="basic_salary-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="pay_frequency" class="block text-sm font-medium text-gray-700 mb-1">
                            Pay Frequency <span class="text-red-500">*</span>
                        </label>
                        <select name="pay_frequency" id="pay_frequency" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('pay_frequency') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Select Frequency</option>
                            <option value="monthly" {{ old('pay_frequency', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly" {{ old('pay_frequency') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="bi_weekly" {{ old('pay_frequency') === 'bi_weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                            <option value="annual" {{ old('pay_frequency') === 'annual' ? 'selected' : '' }}>Annual</option>
                        </select>
                        @error('pay_frequency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="pay_frequency-error"></div>
                    </div>

                    <!-- Attendance Deduction Exemption -->
                    <div class="form-group md:col-span-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" name="attendance_deduction_exempt" id="attendance_deduction_exempt"
                                       value="1"
                                       {{ old('attendance_deduction_exempt') ? 'checked' : '' }}
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

                            <div id="attendance_exemption_reason_container" class="mt-3 {{ old('attendance_deduction_exempt') ? '' : 'hidden' }}">
                                <label for="attendance_exemption_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Exemption Reason / Notes
                                </label>
                                <textarea name="attendance_exemption_reason" id="attendance_exemption_reason" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                          placeholder="e.g., Contract worker - flat monthly rate, Remote worker - flexible hours, Executive - special agreement">{{ old('attendance_exemption_reason') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle"></i> Optional: Document why this employee is exempt (for audit and reference purposes)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">
                            Currency
                        </label>
                        <select name="currency" id="currency"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                            <option value="NGN" {{ old('currency', 'NGN') === 'NGN' ? 'selected' : '' }}>Nigerian Naira (₦)</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>US Dollar ($)</option>
                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>British Pound (£)</option>
                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Effective Date
                        </label>
                        <input type="date" name="effective_date" id="effective_date"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('effective_date', date('Y-m-d')) }}">
                        @error('effective_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Address Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="w-full flex items-center justify-between text-left section-toggle" data-target="address-section">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 mr-2 text-sm font-semibold">4</span>
                        Address Information
                        <span class="text-gray-400 ml-2 text-sm">(Optional)</span>
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="address-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                            Address
                        </label>
                        <textarea name="address" id="address" rows="3"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            placeholder="123 Main Street, City, State, Country">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                            City
                        </label>
                        <input type="text" name="city" id="city"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('city') }}" placeholder="Lagos">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                            State/Province
                        </label>
                        <input type="text" name="state" id="state"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('state') }}" placeholder="Lagos State">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                            Country
                        </label>
                        <select name="country" id="country"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                            <option value="">Select Country</option>
                            <option value="NG" {{ old('country', 'NG') === 'NG' ? 'selected' : '' }}>Nigeria</option>
                            <option value="US" {{ old('country') === 'US' ? 'selected' : '' }}>United States</option>
                            <option value="GB" {{ old('country') === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="CA" {{ old('country') === 'CA' ? 'selected' : '' }}>Canada</option>
                        </select>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                            Postal Code
                        </label>
                        <input type="text" name="postal_code" id="postal_code"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('postal_code') }}" placeholder="100001">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Bank Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="w-full flex items-center justify-between text-left section-toggle" data-target="bank-section">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 mr-2 text-sm font-semibold">5</span>
                        Bank Information
                        <span class="text-gray-400 ml-2 text-sm">(Optional)</span>
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="bank-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Bank Name
                        </label>
                        <input type="text" name="bank_name" id="bank_name"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('bank_name') }}" placeholder="First Bank">
                        @error('bank_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Account Number
                        </label>
                        <input type="text" name="account_number" id="account_number"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('account_number') }}" placeholder="1234567890">
                        @error('account_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Account Name
                        </label>
                        <input type="text" name="account_name" id="account_name"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('account_name') }}" placeholder="John Lawal">
                        @error('account_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pension Information -->
                    <div class="md:col-span-2 mt-6 pt-6 border-t border-purple-200">
                        <h4 class="text-md font-medium text-purple-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Pension Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="pfa_provider" class="block text-sm font-medium text-gray-700 mb-1">
                                    PFA Provider
                                </label>
                                <div class="flex gap-2">
                                    <select name="pfa_provider" id="pfa_provider"
                                        class="flex-1 mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                                        <option value="">Select PFA Provider</option>
                                        @foreach(\App\Models\Pfa::where('tenant_id', $tenant->id)->where('is_active', true)->orderBy('name')->get() as $pfa)
                                            <option value="{{ $pfa->name }}" {{ old('pfa_provider') === $pfa->name ? 'selected' : '' }}>{{ $pfa->name }}</option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('tenant.payroll.pfas.index', ['tenant' => $tenant->slug]) }}" target="_blank" class="mt-1 px-3 py-2 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 text-sm">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                </div>
                                @error('pfa_provider')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="rsa_pin" class="block text-sm font-medium text-gray-700 mb-1">
                                    RSA PIN
                                </label>
                                <input type="text" name="rsa_pin" id="rsa_pin"
                                    class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                                    value="{{ old('rsa_pin') }}" placeholder="PEN123456789012">
                                @error('rsa_pin')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" name="pension_exempt" id="pension_exempt"
                                               value="1"
                                               {{ old('pension_exempt') ? 'checked' : '' }}
                                               class="mt-1 w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                        <span class="ml-3">
                                            <span class="text-sm font-medium text-gray-900">
                                                <svg class="inline w-4 h-4 text-purple-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                </svg>
                                                Exempt from Pension Contributions
                                            </span>
                                            <p class="text-xs text-gray-600 mt-1">
                                                Check this if the employee is exempt from pension contributions (e.g., contractors, temporary staff, or employees under special agreements).
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
            <div class="flex items-center space-x-4">
                <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="button" id="save-draft-btn"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    Save as Draft
                </button>
            </div>
            <div class="flex items-center space-x-4">
                <button type="submit" name="action" value="save_and_new"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Save & Add New
                </button>
                <button type="submit" name="action" value="save"
                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Employee
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="dept_name" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                    <input type="text" id="dept_code" required maxlength="10" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="dept_description" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
                <button type="button" onclick="closeDepartmentModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">Create</button>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" id="pos_name" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                    <input type="text" id="pos_code" required maxlength="10" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="pos_department_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <select id="pos_level" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1">1 - Entry Level</option>
                        <option value="2">2 - Junior</option>
                        <option value="3" selected>3 - Mid-Level</option>
                        <option value="4">4 - Senior</option>
                        <option value="5">5 - Lead</option>
                        <option value="6">6 - Manager</option>
                        <option value="7">7 - Senior Manager</option>
                        <option value="8">8 - Director</option>
                        <option value="9">9 - Senior Director</option>
                        <option value="10">10 - Executive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="pos_description" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
                <button type="button" onclick="closePositionModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">Create</button>
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

.progress-step {
    transition: all 0.3s ease;
}

.progress-step.completed {
    background-color: #10b981;
    color: white;
}

.progress-step.active {
    background-color: #3b82f6;
    color: white;
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
                return; // Skip modal fields
            }

            const errorDiv = document.getElementById(field.id + '-error');
            const fieldLabel = field.closest('.form-group')?.querySelector('label')?.textContent.replace('*', '').trim() || field.name;

            // Check if field is empty
            const isEmpty = field.value.trim() === '';

            if (isEmpty) {
                field.classList.add('error');
                if (errorDiv) {
                    errorDiv.textContent = 'This field is required';
                    errorDiv.classList.remove('hidden');
                }

                // Find which section this field belongs to
                const section = field.closest('[id$="-section"]');
                if (section && section.classList.contains('hidden')) {
                    sectionsWithErrors.add(section.id);
                }

                errorMessages.push(fieldLabel);
                console.log('Missing field:', field.id, 'Label:', fieldLabel, 'Value:', field.value);
                isValid = false;
            } else {
                field.classList.remove('error');
                if (errorDiv) {
                    errorDiv.classList.add('hidden');
                }
            }
        });        // Email validation
        const emailField = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailField.value && !emailRegex.test(emailField.value)) {
            emailField.classList.add('error');
            const errorDiv = document.getElementById('email-error');
            if (errorDiv) {
                errorDiv.textContent = 'Please enter a valid email address';
                errorDiv.classList.remove('hidden');
            }
            errorMessages.push('Email Address (invalid format)');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();

            // Expand all sections with errors
            sectionsWithErrors.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                const toggle = document.querySelector(`[data-target="${sectionId}"]`);
                if (section && toggle) {
                    section.classList.remove('hidden');
                    const arrow = toggle.querySelector('svg');
                    if (arrow) {
                        arrow.style.transform = 'rotate(180deg)';
                    }
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });

            // Scroll to first error
            const firstError = document.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Show alert with specific field names
            const errorCount = errorMessages.length;
            const fieldList = errorMessages.join('\n• ');
            alert(`Please fill in all required fields (${errorCount} field(s) need attention):\n\n• ${fieldList}`);
        }
    });

    // Save as draft functionality
    document.getElementById('save-draft-btn').addEventListener('click', function() {
        const form = document.getElementById('employeeForm');
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'save_as_draft';
        draftInput.value = '1';
        form.appendChild(draftInput);
        form.submit();
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
            const positionDepartmentId = option.getAttribute('data-department');

            // Show position if:
            // 1. No department selected (show all)
            // 2. Position has no department (available for all)
            // 3. Position belongs to selected department
            if (!selectedDepartmentId || !positionDepartmentId || positionDepartmentId === selectedDepartmentId) {
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
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';

        // Send AJAX request
        const response = await fetch('{{ route("tenant.payroll.departments.store", $tenant) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                code: code,
                description: description
            })
        });

        if (response.ok) {
            const data = await response.json();

            // Add the new department to dropdown
            const select = document.getElementById('department_id');
            const option = new Option(name, data.id || data.department?.id);
            select.add(option);
            select.value = data.id || data.department?.id;

            // Trigger change event to update position filter
            select.dispatchEvent(new Event('change'));

            closeDepartmentModal();
            if (typeof updateProgress === 'function') updateProgress();

            // Show success message
            showNotification('Department created successfully!', 'success');
        } else {
            const errorData = await response.json();
            const errorMessage = errorData.message || 'Failed to create department';
            throw new Error(errorMessage);
        }

        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    } catch (error) {
        console.error('Error creating department:', error);
        alert('Error: ' + error.message);

        // Restore button state
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
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating...';

        // Send AJAX request
        const response = await fetch('{{ route("tenant.payroll.positions.store", $tenant) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                code: code,
                level: level || 3,
                department_id: deptId || null,
                description: description
            })
        });

        if (response.ok) {
            const data = await response.json();

            // Add the new position to dropdown
            const select = document.getElementById('position_id');
            const option = new Option(`${name} (${code})`, data.id || data.position?.id);
            if (deptId) {
                option.setAttribute('data-department', deptId);
            }
            select.add(option);
            select.value = data.id || data.position?.id;

            closePositionModal();
            if (typeof updateProgress === 'function') updateProgress();

            // Show success message
            showNotification('Position created successfully!', 'success');
        } else {
            const errorData = await response.json();
            const errorMessage = errorData.message || 'Failed to create position';
            throw new Error(errorMessage);
        }

        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    } catch (error) {
        console.error('Error creating position:', error);
        alert('Error: ' + error.message);

        // Restore button state
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

// Format salary input with thousand separators
document.getElementById('basic_salary').addEventListener('input', function() {
    const value = this.value;
    const formatted = new Intl.NumberFormat('en-NG').format(value || 0);
    document.getElementById('basic_salary_formatted').textContent = '₦' + formatted;
});

// Initialize formatted display on page load
document.addEventListener('DOMContentLoaded', function() {
    const salaryInput = document.getElementById('basic_salary');
    const formatted = new Intl.NumberFormat('en-NG').format(salaryInput.value || 0);
    document.getElementById('basic_salary_formatted').textContent = '₦' + formatted;
});

// Avatar upload and preview functionality
const avatarInput = document.getElementById('avatar');
const avatarPreview = document.getElementById('avatar-preview');
const avatarImage = document.getElementById('avatar-image');
const removeAvatarBtn = document.getElementById('remove-avatar');

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
            removeAvatarBtn.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

removeAvatarBtn.addEventListener('click', function() {
    avatarInput.value = '';
    avatarImage.src = '';
    avatarImage.classList.add('hidden');
    removeAvatarBtn.classList.add('hidden');
});
</script>
@endsection
