@extends('layouts.tenant')

@section('title', 'Add Employee')
@section('page-title', 'Add New Employee')
@section('page-description', 'Create a new employee record in your database.')

@section('content')
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

    <form action="{{ route('tenant.payroll.employees.store', ['tenant' => $tenant->slug]) }}" method="POST" id="employeeForm">
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
                        value="{{ old('last_name') }}" placeholder="Doe">
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
                        <select name="department_id" id="department_id" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('department_id') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="department_id-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                            Position <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="position" id="position" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('position') ? 'border-red-300' : 'border-gray-300' }}"
                            value="{{ old('position') }}" placeholder="Software Engineer">
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="position-error"></div>
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
                        <label for="base_salary" class="block text-sm font-medium text-gray-700 mb-1">
                            Base Salary <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₦</span>
                            </div>
                            <input type="number" name="base_salary" id="base_salary" step="0.01" min="0" required
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 shadow-sm sm:text-sm rounded-md {{ $errors->has('base_salary') ? 'border-red-300' : 'border-gray-300' }}"
                                value="{{ old('base_salary') }}" placeholder="0.00">
                        </div>
                        @error('base_salary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="base_salary-error"></div>
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
                            value="{{ old('account_name') }}" placeholder="John Doe">
                        @error('account_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
        const requiredFields = document.querySelectorAll('input[required], select[required]');

        requiredFields.forEach(field => {
            const errorDiv = document.getElementById(field.id + '-error');
            if (field.value.trim() === '') {
                field.classList.add('error');
                if (errorDiv) {
                    errorDiv.textContent = 'This field is required';
                    errorDiv.classList.remove('hidden');
                }
                isValid = false;
            } else {
                field.classList.remove('error');
                if (errorDiv) {
                    errorDiv.classList.add('hidden');
                }
            }
        });

        // Email validation
        const emailField = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailField.value && !emailRegex.test(emailField.value)) {
            emailField.classList.add('error');
            const errorDiv = document.getElementById('email-error');
            if (errorDiv) {
                errorDiv.textContent = 'Please enter a valid email address';
                errorDiv.classList.remove('hidden');
            }
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = document.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
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

    // Initialize progress on page load
    updateProgress();
});
</script>
@endsection
