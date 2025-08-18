@extends('layouts.tenant')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')
@section('page-description', 'Update customer information and details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Customer</h2>
                    <p class="text-gray-600 mt-1">Update customer information and details</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('tenant.crm.customers.show', ['tenant' => $tenant->slug, 'customer' => $customer->id]) }}"
                       class="text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-6">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                    <span>Form Completion</span>
                    <span id="progress-indicator">0% Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <form id="customerForm" action="{{ route('tenant.crm.customers.update', ['tenant' => $tenant->slug, 'customer' => $customer->id]) }}" method="POST" class="p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Error Summary -->
            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-red-800 font-medium">Please fix the following errors:</h3>
                </div>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Customer Type Selection -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Customer Type</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="relative">
                        <input type="radio" name="customer_type" value="individual" id="individual"
                               {{ old('customer_type', $customer->customer_type) == 'individual' ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="p-4 border-2 border-gray-300 rounded-xl cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Individual</h4>
                                    <p class="text-sm text-gray-500">Personal customer</p>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" name="customer_type" value="business" id="business"
                               {{ old('customer_type', $customer->customer_type) == 'business' ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="p-4 border-2 border-gray-300 rounded-xl cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Business</h4>
                                    <p class="text-sm text-gray-500">Company or organization</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
                @error('customer_type')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Individual Customer Fields -->
            <div id="individualFields" class="space-y-6 {{ old('customer_type', $customer->customer_type) == 'individual' ? '' : 'hidden' }}">
                <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name"
                               value="{{ old('first_name', $customer->first_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('first_name') border-red-500 @enderror">
                        @error('first_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name"
                               value="{{ old('last_name', $customer->last_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('last_name') border-red-500 @enderror">
                        @error('last_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Business Customer Fields -->
            <div id="businessFields" class="space-y-6 {{ old('customer_type', $customer->customer_type) == 'business' ? '' : 'hidden' }}">
                <h3 class="text-lg font-semibold text-gray-900">Business Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="company_name" name="company_name"
                               value="{{ old('company_name', $customer->company_name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('company_name') border-red-500 @enderror">
                        @error('company_name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Tax ID / Registration Number
                        </label>
                        <input type="text" id="tax_id" name="tax_id"
                               value="{{ old('tax_id', $customer->tax_id) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('tax_id') border-red-500 @enderror">
                        @error('tax_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $customer->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone"
                               value="{{ old('phone', $customer->phone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('phone') border-red-500 @enderror">
                        @error('phone')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mobile" class="block text-sm font-medium text-gray-700 mb-2">
                            Mobile Number
                        </label>
                        <input type="tel" id="mobile" name="mobile"
                               value="{{ old('mobile', $customer->mobile) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('mobile') border-red-500 @enderror">
                        @error('mobile')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Address Information</h3>
                    <button type="button" id="toggleAddress" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <span id="addressToggleText">{{ $customer->address_line1 || $customer->city ? 'Hide' : 'Show' }} Address</span>
                    </button>
                </div>

                <div id="addressSection" class="space-y-6 {{ $customer->address_line1 || $customer->city ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="address_line1" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 1
                            </label>
                            <input type="text" id="address_line1" name="address_line1"
                                   value="{{ old('address_line1', $customer->address_line1) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('address_line1') border-red-500 @enderror">
                            @error('address_line1')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address_line2" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 2
                            </label>
                            <input type="text" id="address_line2" name="address_line2"
                                   value="{{ old('address_line2', $customer->address_line2) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('address_line2') border-red-500 @enderror">
                            @error('address_line2')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                    City
                                </label>
                                <input type="text" id="city" name="city"
                                       value="{{ old('city', $customer->city) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('city') border-red-500 @enderror">
                                @error('city')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                    State
                                </label>
                                <input type="text" id="state" name="state"
                                       value="{{ old('state', $customer->state) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('state') border-red-500 @enderror">
                                @error('state')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Postal Code
                                </label>
                                <input type="text" id="postal_code" name="postal_code"
                                       value="{{ old('postal_code', $customer->postal_code) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('postal_code') border-red-500 @enderror">
                                @error('postal_code')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                Country
                            </label>
                            <select id="country" name="country"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('country') border-red-500 @enderror">
                                <option value="">Select Country</option>
                                <option value="Nigeria" {{ old('country', $customer->country) == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                                <option value="Nigeria" {{ old('country', $customer->country) == 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                                <option value="Ghana" {{ old('country', $customer->country) == 'Ghana' ? 'selected' : '' }}>Ghana</option>
                                <option value="Kenya" {{ old('country', $customer->country) == 'Kenya' ? 'selected' : '' }}>Kenya</option>
                                <option value="South Africa" {{ old('country', $customer->country) == 'South Africa' ? 'selected' : '' }}>South Africa</option>
                                <option value="United Kingdom" {{ old('country', $customer->country) == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="United States" {{ old('country', $customer->country) == 'United States' ? 'selected' : '' }}>United States</option>
                                <option value="Canada" {{ old('country', $customer->country) == 'Canada' ? 'selected' : '' }}>Canada</option>
                                <option value="Australia" {{ old('country', $customer->country) == 'Australia' ? 'selected' : '' }}>Australia</option>
                                <option value="Other" {{ old('country', $customer->country) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('country')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Settings -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900">Financial Settings</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Currency
                        </label>
                        <select id="currency" name="currency"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('currency') border-red-500 @enderror">
                            <option value="">Select Currency</option>
                            <option value="NGN" {{ old('currency', $customer->currency) == 'NGN' ? 'selected' : '' }}>Nigerian Naira (NGN)</option>
                            <option value="USD" {{ old('currency', $customer->currency) == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                            <option value="EUR" {{ old('currency', $customer->currency) == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                            <option value="GBP" {{ old('currency', $customer->currency) == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                            <option value="CAD" {{ old('currency', $customer->currency) == 'CAD' ? 'selected' : '' }}>Canadian Dollar (CAD)</option>
                            <option value="AUD" {{ old('currency', $customer->currency) == 'AUD' ? 'selected' : '' }}>Australian Dollar (AUD)</option>
                            <option value="ZAR" {{ old('currency', $customer->currency) == 'ZAR' ? 'selected' : '' }}>South African Rand (ZAR)</option>
                            <option value="GHS" {{ old('currency', $customer->currency) == 'GHS' ? 'selected' : '' }}>Ghanaian Cedi (GHS)</option>
                            <option value="KES" {{ old('currency', $customer->currency) == 'KES' ? 'selected' : '' }}>Kenyan Shilling (KES)</option>
                        </select>
                        @error('currency')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Terms
                        </label>
                        <select id="payment_terms" name="payment_terms"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('payment_terms') border-red-500 @enderror">
                            <option value="">Select Payment Terms</option>
                            <option value="Net 15" {{ old('payment_terms', $customer->payment_terms) == 'Net 15' ? 'selected' : '' }}>Net 15 Days</option>
                            <option value="Net 30" {{ old('payment_terms', $customer->payment_terms) == 'Net 30' ? 'selected' : '' }}>Net 30 Days</option>
                            <option value="Net 45" {{ old('payment_terms', $customer->payment_terms) == 'Net 45' ? 'selected' : '' }}>Net 45 Days</option>
                            <option value="Net 60" {{ old('payment_terms', $customer->payment_terms) == 'Net 60' ? 'selected' : '' }}>Net 60 Days</option>
                            <option value="Due on Receipt" {{ old('payment_terms', $customer->payment_terms) == 'Due on Receipt' ? 'selected' : '' }}>Due on Receipt</option>
                            <option value="Cash on Delivery" {{ old('payment_terms', $customer->payment_terms) == 'Cash on Delivery' ? 'selected' : '' }}>Cash on Delivery</option>
                        </select>
                        @error('payment_terms')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Customer Status -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900">Customer Status</h3>
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="radio" name="status" value="active"
                               {{ old('status', $customer->status) == 'active' ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="status" value="inactive"
                               {{ old('status', $customer->status) == 'inactive' ? 'checked' : '' }}
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Inactive</span>
                    </label>
                </div>
                @error('status')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Notes -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900">Additional Information</h3>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200 @error('notes') border-red-500 @enderror"
                              placeholder="Any additional notes about this customer...">{{ old('notes', $customer->notes) }}</textarea>
                    @error('notes')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button type="button" id="saveAndNew"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Save & Add New
                        </button>
                        <button type="button" id="previewBtn"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </button>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('tenant.crm.customers.show', ['tenant' => $tenant->slug, 'customer' => $customer->id]) }}"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2 hidden" id="submitSpinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Update Customer
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Preview</h3>
                    <button id="closePreview" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="previewContent" class="p-6">
                <!-- Preview content will be populated here -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const individualRadio = document.getElementById('individual');
    const businessRadio = document.getElementById('business');
    const individualFields = document.getElementById('individualFields');
    const businessFields = document.getElementById('businessFields');
    const customerForm = document.getElementById('customerForm');
    const toggleAddressBtn = document.getElementById('toggleAddress');
    const addressSection = document.getElementById('addressSection');
    const addressToggleText = document.getElementById('addressToggleText');
    const previewBtn = document.getElementById('previewBtn');
    const previewModal = document.getElementById('previewModal');
    const closePreviewBtn = document.getElementById('closePreview');
    const submitBtn = document.getElementById('submitBtn');
    const submitSpinner = document.getElementById('submitSpinner');
    const saveAndNewBtn = document.getElementById('saveAndNew');
    const progressBar = document.getElementById('progress-bar');
    const progressIndicator = document.getElementById('progress-indicator');

    // Customer type toggle
    function toggleCustomerType() {
        if (individualRadio.checked) {
            individualFields.classList.remove('hidden');
            businessFields.classList.add('hidden');
        } else if (businessRadio.checked) {
            businessFields.classList.remove('hidden');
            individualFields.classList.add('hidden');
        }
        updateProgress();
    }

    individualRadio.addEventListener('change', toggleCustomerType);
    businessRadio.addEventListener('change', toggleCustomerType);

    // Address section toggle
    toggleAddressBtn.addEventListener('click', function() {
        addressSection.classList.toggle('hidden');
        if (addressSection.classList.contains('hidden')) {
            addressToggleText.textContent = 'Show Address';
        } else {
            addressToggleText.textContent = 'Hide Address';
        }
        updateProgress();
    });

    // Progress tracking
    function updateProgress() {
        const formFields = customerForm.querySelectorAll('input, select, textarea');
        const totalFields = formFields.length;
        let filledFields = 0;

        formFields.forEach(field => {
            if (field.type === 'radio') {
                if (field.checked) filledFields++;
            } else if (field.value.trim() !== '') {
                filledFields++;
            }
        });

        const progressPercentage = Math.round((filledFields / totalFields) * 100);
        progressBar.style.width = progressPercentage + '%';
        progressIndicator.textContent = progressPercentage + '% Complete';
    }

    // Add event listeners to all form fields for progress tracking
    const formFields = customerForm.querySelectorAll('input, select, textarea');
    formFields.forEach(field => {
        field.addEventListener('input', updateProgress);
        field.addEventListener('change', updateProgress);
    });

    // Initial progress calculation
    updateProgress();

    // Form validation
    function validateForm() {
        const customerType = document.querySelector('input[name="customer_type"]:checked');
        const email = document.getElementById('email').value.trim();

        if (!customerType) {
            alert('Please select a customer type.');
            return false;
        }

        if (!email) {
            alert('Please enter an email address.');
            return false;
        }

        if (customerType.value === 'individual') {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();

            if (!firstName || !lastName) {
                alert('Please enter both first and last name for individual customers.');
                return false;
            }
        } else if (customerType.value === 'business') {
            const companyName = document.getElementById('company_name').value.trim();

            if (!companyName) {
                alert('Please enter a company name for business customers.');
                return false;
            }
        }

        return true;
    }

    // Preview functionality
    previewBtn.addEventListener('click', function() {
        if (!validateForm()) return;

        const customerType = document.querySelector('input[name="customer_type"]:checked').value;
        const previewContent = document.getElementById('previewContent');

        let customerName = '';
        if (customerType === 'individual') {
            customerName = document.getElementById('first_name').value + ' ' + document.getElementById('last_name').value;
        } else {
            customerName = document.getElementById('company_name').value;
        }

        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const mobile = document.getElementById('mobile').value;
        const address = [
            document.getElementById('address_line1').value,
            document.getElementById('address_line2').value,
            document.getElementById('city').value,
            document.getElementById('state').value,
            document.getElementById('postal_code').value,
            document.getElementById('country').value
        ].filter(item => item.trim() !== '').join(', ');

        const currency = document.getElementById('currency').value;
        const paymentTerms = document.getElementById('payment_terms').value;
        const status = document.querySelector('input[name="status"]:checked')?.value || 'active';
        const notes = document.getElementById('notes').value;

        previewContent.innerHTML = `
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        ${customerName.charAt(0)}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">${customerName}</h3>
                        <p class="text-gray-600">${customerType === 'individual' ? 'Individual Customer' : 'Business Customer'}</p>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            ${status.charAt(0).toUpperCase() + status.slice(1)}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Contact Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Email:</strong> ${email}</p>
                            ${phone ? `<p><strong>Phone:</strong> ${phone}</p>` : ''}
                            ${mobile ? `<p><strong>Mobile:</strong> ${mobile}</p>` : ''}
                        </div>
                    </div>

                    ${address ? `
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Address</h4>
                        <p class="text-sm text-gray-700">${address}</p>
                    </div>
                    ` : ''}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    ${currency ? `
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Financial Settings</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Currency:</strong> ${currency}</p>
                            ${paymentTerms ? `<p><strong>Payment Terms:</strong> ${paymentTerms}</p>` : ''}
                        </div>
                    </div>
                    ` : ''}

                    ${notes ? `
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Notes</h4>
                        <p class="text-sm text-gray-700">${notes}</p>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;

        previewModal.classList.remove('hidden');
    });

    // Close preview modal
    closePreviewBtn.addEventListener('click', function() {
        previewModal.classList.add('hidden');
    });

    // Close modal when clicking outside
    previewModal.addEventListener('click', function(e) {
        if (e.target === previewModal) {
            previewModal.classList.add('hidden');
        }
    });

    // Save and New functionality
    saveAndNewBtn.addEventListener('click', function() {
        if (!validateForm()) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = customerForm.action;
        form.innerHTML = customerForm.innerHTML;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'save_and_new';
        input.value = '1';
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
    });

    // Form submission with loading state
    customerForm.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return;
        }

        submitBtn.disabled = true;
        submitSpinner.classList.remove('hidden');
        submitBtn.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Updating...';
    });

    // Auto-save functionality (optional)
    let autoSaveTimeout;
    function autoSave() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            // You can implement auto-save to localStorage here
            console.log('Auto-saving form data...');
        }, 2000);
    }

    formFields.forEach(field => {
        field.addEventListener('input', autoSave);
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S or Cmd+S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (validateForm()) {
                customerForm.submit();
            }
        }

        // Escape to close modal
        if (e.key === 'Escape' && !previewModal.classList.contains('hidden')) {
            previewModal.classList.add('hidden');
        }
    });

    // Add smooth animations
    const sections = document.querySelectorAll('.space-y-6');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        section.style.transitionDelay = (index * 0.1) + 's';

        setTimeout(() => {
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, 100);
    });
});
</script>

<style>
/* Custom styles for the edit form */
.form-section {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.9) 100%);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.input-focus:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

/* Animated progress bar */
#progress-bar {
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    transition: width 0.3s ease;
}

/* Custom radio button styling */
input[type="radio"]:checked + .peer-checked\:border-blue-500 {
    border-color: #3b82f6;
    background-color: rgba(59, 130, 246, 0.05);
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

/* Loading animation */
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid-cols-1.sm\:grid-cols-2 {
        grid-template-columns: 1fr;
    }

    .flex.items-center.justify-between {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
}
</style>
@endsection
