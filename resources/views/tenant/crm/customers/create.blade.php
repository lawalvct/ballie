@extends('layouts.tenant')

@section('title', 'Add Customer')
@section('page-title', 'Add New Customer')
@section('page-description', 'Create a new customer record in your database.')

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.crm.customers.index', ['tenant' => tenant()->slug]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Customers
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Creating new customer</span>
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

    <form action="{{ route('tenant.crm.customers.store', ['tenant' => tenant()->slug]) }}" method="POST" id="customerForm">
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

        <!-- Section 1: Customer Type Selection (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">1</span>
                Customer Type
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <input type="radio" id="individual" name="customer_type" value="individual" class="hidden peer" {{ old('customer_type', 'individual') === 'individual' ? 'checked' : '' }}>
                    <label for="individual" class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 {{ $errors->has('customer_type') ? 'border-red-300' : 'border-gray-300' }}">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Individual</p>
                            <p class="text-sm text-gray-500">Personal customer account</p>
                        </div>
                    </label>
                </div>

                <div class="relative">
                    <input type="radio" id="business" name="customer_type" value="business" class="hidden peer" {{ old('customer_type') === 'business' ? 'checked' : '' }}>
                    <label for="business" class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 {{ $errors->has('customer_type') ? 'border-red-300' : 'border-gray-300' }}">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Business</p>
                            <p class="text-sm text-gray-500">Company or organization</p>
                        </div>
                    </label>
                </div>
                @error('customer_type')
                    <div class="md:col-span-2 mt-1">
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    </div>
                @enderror
            </div>
        </div>

        <!-- Section 2: Customer Information (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">2</span>
                Customer Information
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Individual Fields -->
                <div id="individual-fields" class="transition-all duration-300 {{ old('customer_type') === 'business' ? 'hidden' : '' }}">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" id="first_name"
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
                            <input type="text" name="last_name" id="last_name"
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('last_name') ? 'border-red-300' : 'border-gray-300' }}"
                                value="{{ old('last_name') }}" placeholder="Doe">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="hidden text-sm text-red-600 mt-1 field-error" id="last_name-error"></div>
                        </div>
                    </div>
                </div>

                <!-- Business Fields -->
                <div id="business-fields" class="hidden transition-all duration-300 {{ old('customer_type') === 'business' ? 'block' : 'hidden' }}">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="company_name" id="company_name"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('company_name') ? 'border-red-300' : 'border-gray-300' }}"
                            value="{{ old('company_name') }}" placeholder="Acme Corporation">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="hidden text-sm text-red-600 mt-1 field-error" id="company_name-error"></div>
                    </div>

                    <div class="mt-4">
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Tax ID / VAT Number
                        </label>
                        <input type="text" name="tax_id" id="tax_id"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('tax_id') ? 'border-red-300' : 'border-gray-300' }}"
                            value="{{ old('tax_id') }}" placeholder="123456789">
                        @error('tax_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Common Fields -->
                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email"
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
                            <input type="tel" name="phone" id="phone"
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('phone') ? 'border-red-300' : 'border-gray-300' }}"
                                value="{{ old('phone') }}" placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="hidden text-sm text-red-600 mt-1 field-error" id="phone-error"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Address Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="w-full flex items-center justify-between text-left section-toggle" data-target="address-section">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 mr-2 text-sm font-semibold">3</span>
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
                        <label for="address_line_1" class="block text-sm font-medium text-gray-700 mb-1">
                            Address Line 1
                        </label>
                        <input type="text" name="address_line_1" id="address_line_1"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('address_line_1') }}" placeholder="123 Main Street">
                        @error('address_line_1')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address_line_2" class="block text-sm font-medium text-gray-700 mb-1">
                            Address Line 2
                        </label>
                        <input type="text" name="address_line_2" id="address_line_2"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('address_line_2') }}" placeholder="Apartment, suite, etc.">
                        @error('address_line_2')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                            City
                        </label>
                        <input type="text" name="city" id="city"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('city') }}" placeholder="New York">
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
                            value="{{ old('state') }}" placeholder="NY">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">
                            Postal Code
                        </label>
                        <input type="text" name="postal_code" id="postal_code"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('postal_code') }}" placeholder="10001">
                        @error('postal_code')
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
                            <option value="US" {{ old('country') === 'US' ? 'selected' : '' }}>United States</option>
                            <option value="CA" {{ old('country') === 'CA' ? 'selected' : '' }}>Canada</option>
                            <option value="GB" {{ old('country') === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="AU" {{ old('country') === 'AU' ? 'selected' : '' }}>Australia</option>
                            <option value="DE" {{ old('country') === 'DE' ? 'selected' : '' }}>Germany</option>
                            <option value="FR" {{ old('country') === 'FR' ? 'selected' : '' }}>France</option>
                            <option value="NG" {{ old('country') === 'NG' ? 'selected' : '' }}>Nigeria</option>
                            <!-- Add more countries as needed -->
                        </select>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 4: Additional Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="w-full flex items-center justify-between text-left section-toggle" data-target="additional-section">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 mr-2 text-sm font-semibold">4</span>
                        Additional Information
                        <span class="text-gray-400 ml-2 text-sm">(Optional)</span>
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="additional-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                            Website
                        </label>
                        <input type="url" name="website" id="website"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            value="{{ old('website') }}" placeholder="https://example.com">
                        @error('website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_status" class="block text-sm font-medium text-gray-700 mb-1">
                            Customer Status
                        </label>
                        <select name="customer_status" id="customer_status"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                            <option value="active" {{ old('customer_status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('customer_status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="pending" {{ old('customer_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        @error('customer_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                            placeholder="Any additional notes about this customer...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Financial Information (Collapsible) -->
        <div class="bg-white rounded-2xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="p-6 border-b border-gray-200">
                <button type="button" class="w-full flex items-center justify-between text-left section-toggle" data-target="financial-section">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-600 mr-2 text-sm font-semibold">5</span>
                        Financial Information
                        <span class="text-gray-400 ml-2 text-sm">(Optional)</span>
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div id="financial-section" class="hidden p-6 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Opening Balance Section -->
                    <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4">



                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="opening_balance_amount" class="block text-sm font-medium text-gray-700 mb-1">
                                    Opening Balance Amount
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₦</span>
                                    </div>
                                    <input type="number" name="opening_balance_amount" id="opening_balance_amount" step="0.01" min="0"
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 shadow-sm sm:text-sm rounded-md border-gray-300"
                                        value="{{ old('opening_balance_amount', '0.00') }}" placeholder="0.00">
                                </div>
                                @error('opening_balance_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Enter the balance amount (always positive)</p>
                            </div>

                            <div>
                                <label for="opening_balance_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Balance Type
                                </label>
                                <select name="opening_balance_type" id="opening_balance_type"
                                    class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                                    <option value="none" {{ old('opening_balance_type', 'none') === 'none' ? 'selected' : '' }}>None (No Opening Balance)</option>
                                    <option value="debit" {{ old('opening_balance_type') === 'debit' ? 'selected' : '' }}>Debit (Customer Owes You)</option>
                                    <option value="credit" {{ old('opening_balance_type') === 'credit' ? 'selected' : '' }}>Credit (You Owe Customer)</option>
                                </select>
                                @error('opening_balance_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="opening_balance_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Opening Balance Date
                            </label>
                            <input type="date" name="opening_balance_date" id="opening_balance_date"
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300"
                                value="{{ old('opening_balance_date', date('Y-m-d')) }}">
                            @error('opening_balance_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">The date when this opening balance should be recorded</p>
                        </div>
                    </div>

                    <div>
                        <label for="credit_limit" class="block text-sm font-medium text-gray-700 mb-1">
                            Credit Limit
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₦</span>
                            </div>
                            <input type="number" name="credit_limit" id="credit_limit" step="0.01" min="0"
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 shadow-sm sm:text-sm rounded-md border-gray-300"
                                value="{{ old('credit_limit') }}" placeholder="0.00">
                        </div>
                        @error('credit_limit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-1">
                            Payment Terms
                        </label>
                        <select name="payment_terms" id="payment_terms"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                            <option value="">Select Payment Terms</option>
                            <option value="net_15" {{ old('payment_terms') === 'net_15' ? 'selected' : '' }}>Net 15</option>
                            <option value="net_30" {{ old('payment_terms') === 'net_30' ? 'selected' : '' }}>Net 30</option>
                            <option value="net_45" {{ old('payment_terms') === 'net_45' ? 'selected' : '' }}>Net 45</option>
                            <option value="net_60" {{ old('payment_terms') === 'net_60' ? 'selected' : '' }}>Net 60</option>
                            <option value="due_on_receipt" {{ old('payment_terms') === 'due_on_receipt' ? 'selected' : '' }}>Due on Receipt</option>
                            <option value="cash_on_delivery" {{ old('payment_terms') === 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                        </select>
                        @error('payment_terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="preferred_payment_method" class="block text-sm font-medium text-gray-700 mb-1">
                            Preferred Payment Method
                        </label>
                        <select name="preferred_payment_method" id="preferred_payment_method"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                            <option value="">Select Payment Method</option>
                            <option value="cash" {{ old('preferred_payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="cheque" {{ old('preferred_payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>

                            <option value="bank_transfer" {{ old('preferred_payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>

                        </select>
                        @error('preferred_payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_exempt" class="block text-sm font-medium text-gray-700 mb-1">
                            Tax Status
                        </label>
                        <select name="tax_exempt" id="tax_exempt"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md border-gray-300">
                            <option value="0" {{ old('tax_exempt', '0') === '0' ? 'selected' : '' }}>Taxable</option>
                            <option value="1" {{ old('tax_exempt') === '1' ? 'selected' : '' }}>Tax Exempt</option>
                        </select>
                        @error('tax_exempt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('tenant.crm.customers.index', ['tenant' => tenant()->slug]) }}"
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
                    Create Customer
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

    // Customer type toggle functionality
    const customerTypeRadios = document.querySelectorAll('input[name="customer_type"]');
    const individualFields = document.getElementById('individual-fields');
    const businessFields = document.getElementById('business-fields');

    customerTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'individual') {
                individualFields.classList.remove('hidden');
                businessFields.classList.add('hidden');
                // Clear business fields
                document.getElementById('company_name').value = '';
                document.getElementById('tax_id').value = '';
            } else {
                individualFields.classList.add('hidden');
                businessFields.classList.remove('hidden');
                // Clear individual fields
                document.getElementById('first_name').value = '';
                document.getElementById('last_name').value = '';
            }
            updateProgress();
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
    const form = document.getElementById('customerForm');
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
        const form = document.getElementById('customerForm');
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'save_as_draft';
        draftInput.value = '1';
        form.appendChild(draftInput);
        form.submit();
    });

    // Initialize progress on page load
    updateProgress();

    // Opening balance handling
    const openingBalanceAmount = document.getElementById('opening_balance_amount');
    const openingBalanceType = document.getElementById('opening_balance_type');

    // Update balance type when amount changes
    openingBalanceAmount.addEventListener('input', function() {
        if (parseFloat(this.value) > 0 && openingBalanceType.value === 'none') {
            openingBalanceType.value = 'debit'; // Default to debit (customer owes)
        } else if (parseFloat(this.value) === 0) {
            openingBalanceType.value = 'none';
        }
    });

    // Reset amount if type is set to none
    openingBalanceType.addEventListener('change', function() {
        if (this.value === 'none') {
            openingBalanceAmount.value = '0.00';
        }
    });
});
</script>
@endsection
