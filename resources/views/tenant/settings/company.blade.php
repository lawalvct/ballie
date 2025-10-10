@extends('layouts.tenant')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Company Settings</h1>
                        <p class="text-lg text-gray-600">Manage your company information and preferences</p>
                    </div>
                </div>
                <a href="{{ route('tenant.dashboard', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl mb-6 flex items-center">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Error Messages -->
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="font-semibold mb-2">There were some errors with your submission:</h3>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Tabs -->
        <div class="mb-6" x-data="{ activeTab: 'company' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    <button @click="activeTab = 'company'"
                            :class="activeTab === 'company' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Company Information
                    </button>
                    <button @click="activeTab = 'business'"
                            :class="activeTab === 'business' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Business Details
                    </button>
                    <button @click="activeTab = 'branding'"
                            :class="activeTab === 'branding' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Branding & Logo
                    </button>
                    <button @click="activeTab = 'preferences'"
                            :class="activeTab === 'preferences' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Preferences
                    </button>
                </nav>
            </div>

            <!-- Company Information Tab -->
            <div x-show="activeTab === 'company'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mt-6">
                    <form method="POST" action="{{ route('tenant.settings.company.update-info', ['tenant' => $tenant->slug]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Company Information</h3>
                            <p class="text-sm text-gray-600">Basic information about your company</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $tenant->name) }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Company Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $tenant->email) }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <input type="tel"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', $tenant->phone) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                       placeholder="+234 800 000 0000">
                                @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                    Website
                                </label>
                                <input type="url"
                                       id="website"
                                       name="website"
                                       value="{{ old('website', $tenant->website) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                       placeholder="https://example.com">
                                @error('website')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Address
                            </label>
                            <textarea id="address"
                                      name="address"
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                      placeholder="Enter company address">{{ old('address', $tenant->address) }}</textarea>
                            @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                    City
                                </label>
                                <input type="text"
                                       id="city"
                                       name="city"
                                       value="{{ old('city', $tenant->city) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                    State/Province
                                </label>
                                <input type="text"
                                       id="state"
                                       name="state"
                                       value="{{ old('state', $tenant->state) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                    Country
                                </label>
                                <input type="text"
                                       id="country"
                                       name="country"
                                       value="{{ old('country', $tenant->country) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="submit"
                                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Business Details Tab -->
            <div x-show="activeTab === 'business'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mt-6">
                    <form method="POST" action="{{ route('tenant.settings.company.update-business', ['tenant' => $tenant->slug]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Business Details</h3>
                            <p class="text-sm text-gray-600">Legal and regulatory information about your business</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Type
                                </label>
                                <select id="business_type"
                                        name="business_type"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                    <option value="">Select Business Type</option>
                                    <option value="retail" {{ old('business_type', $tenant->business_type) == 'retail' ? 'selected' : '' }}>Retail & E-commerce</option>
                                    <option value="service" {{ old('business_type', $tenant->business_type) == 'service' ? 'selected' : '' }}>Service Business</option>
                                    <option value="restaurant" {{ old('business_type', $tenant->business_type) == 'restaurant' ? 'selected' : '' }}>Restaurant & Food</option>
                                    <option value="manufacturing" {{ old('business_type', $tenant->business_type) == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                    <option value="wholesale" {{ old('business_type', $tenant->business_type) == 'wholesale' ? 'selected' : '' }}>Wholesale & Distribution</option>
                                    <option value="other" {{ old('business_type', $tenant->business_type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('business_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="business_registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Registration Number
                                </label>
                                <input type="text"
                                       id="business_registration_number"
                                       name="business_registration_number"
                                       value="{{ old('business_registration_number', $tenant->business_registration_number) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                       placeholder="RC123456">
                                @error('business_registration_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tax_identification_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tax Identification Number (TIN)
                                </label>
                                <input type="text"
                                       id="tax_identification_number"
                                       name="tax_identification_number"
                                       value="{{ old('tax_identification_number', $tenant->tax_identification_number) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                       placeholder="12345678-0001">
                                @error('tax_identification_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="fiscal_year_start" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fiscal Year Start Date
                                </label>
                                <input type="date"
                                       id="fiscal_year_start"
                                       name="fiscal_year_start"
                                       value="{{ old('fiscal_year_start', $tenant->fiscal_year_start) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                @error('fiscal_year_start')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Start date of your financial year</p>
                            </div>

                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">
                                    Default Payment Terms (Days)
                                </label>
                                <input type="number"
                                       id="payment_terms"
                                       name="payment_terms"
                                       value="{{ old('payment_terms', $tenant->payment_terms ?? 30) }}"
                                       min="0"
                                       max="365"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                @error('payment_terms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Number of days for payment due date</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="submit"
                                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Branding & Logo Tab -->
            <div x-show="activeTab === 'branding'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mt-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Company Logo</h3>
                        <p class="text-sm text-gray-600">Upload your company logo to personalize your account</p>
                    </div>

                    <div class="flex items-center space-x-6 mb-8">
                        <div class="relative">
                            @if($tenant->logo)
                            <img id="logo-preview"
                                 src="{{ asset('storage/' . $tenant->logo) }}"
                                 alt="Company Logo"
                                 class="w-32 h-32 rounded-xl object-cover border-4 border-gray-200">
                            <form method="POST" action="{{ route('tenant.settings.company.remove-logo', ['tenant' => $tenant->slug]) }}" class="absolute -bottom-2 -right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                            @else
                            <div class="w-32 h-32 rounded-xl bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center border-4 border-gray-200">
                                <svg class="w-16 h-16 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <form method="POST" action="{{ route('tenant.settings.company.update-logo', ['tenant' => $tenant->slug]) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload New Logo
                                </label>
                                <input type="file"
                                       id="logo"
                                       name="logo"
                                       accept="image/*"
                                       required
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-purple-50 file:text-purple-700
                                              hover:file:bg-purple-100 cursor-pointer mb-3">
                                <p class="text-xs text-gray-500 mb-4">JPG, PNG, GIF, or SVG (MAX. 2MB). Recommended size: 400x400px</p>
                                <button type="submit"
                                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors text-sm">
                                    Upload Logo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences Tab -->
            <div x-show="activeTab === 'preferences'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mt-6">
                    <form method="POST" action="{{ route('tenant.settings.company.update-preferences', ['tenant' => $tenant->slug]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Regional Preferences</h3>
                            <p class="text-sm text-gray-600">Configure regional settings for your company</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                    Currency
                                </label>
                                <select id="currency"
                                        name="currency"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                    <option value="NGN" {{ old('currency', $tenant->settings['currency'] ?? 'NGN') == 'NGN' ? 'selected' : '' }}>Nigerian Naira (NGN)</option>
                                    <option value="USD" {{ old('currency', $tenant->settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                    <option value="EUR" {{ old('currency', $tenant->settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                    <option value="GBP" {{ old('currency', $tenant->settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                                </select>
                                @error('currency')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-2">
                                    Currency Symbol
                                </label>
                                <input type="text"
                                       id="currency_symbol"
                                       name="currency_symbol"
                                       value="{{ old('currency_symbol', $tenant->settings['currency_symbol'] ?? '₦') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                       placeholder="₦">
                                @error('currency_symbol')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date Format
                                </label>
                                <select id="date_format"
                                        name="date_format"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                    <option value="d/m/Y" {{ old('date_format', $tenant->settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                    <option value="m/d/Y" {{ old('date_format', $tenant->settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                    <option value="Y-m-d" {{ old('date_format', $tenant->settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                </select>
                                @error('date_format')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">
                                    Time Format
                                </label>
                                <select id="time_format"
                                        name="time_format"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                    <option value="12" {{ old('time_format', $tenant->settings['time_format'] ?? '12') == '12' ? 'selected' : '' }}>12-hour (AM/PM)</option>
                                    <option value="24" {{ old('time_format', $tenant->settings['time_format'] ?? '') == '24' ? 'selected' : '' }}>24-hour</option>
                                </select>
                                @error('time_format')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Timezone
                                </label>
                                <select id="timezone"
                                        name="timezone"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                    <option value="Africa/Lagos" {{ old('timezone', $tenant->settings['timezone'] ?? 'Africa/Lagos') == 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos (WAT)</option>
                                    <option value="UTC" {{ old('timezone', $tenant->settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ old('timezone', $tenant->settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                                    <option value="Europe/London" {{ old('timezone', $tenant->settings['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                </select>
                                @error('timezone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                                    Language
                                </label>
                                <select id="language"
                                        name="language"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                    <option value="en" {{ old('language', $tenant->settings['language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="fr" {{ old('language', $tenant->settings['language'] ?? '') == 'fr' ? 'selected' : '' }}>French</option>
                                </select>
                                @error('language')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="submit"
                                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Live preview for logo upload
    document.addEventListener('DOMContentLoaded', function() {
        const logoInput = document.getElementById('logo');

        if (logoInput) {
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();

                    reader.onload = function(event) {
                        // Find or create preview container
                        let previewContainer = document.querySelector('.relative');
                        if (!previewContainer) return;

                        // Update existing image or create new one
                        let existingImg = document.getElementById('logo-preview');
                        if (existingImg) {
                            existingImg.src = event.target.result;
                        } else {
                            // Replace placeholder with image
                            const placeholder = previewContainer.querySelector('.bg-gradient-to-br');
                            if (placeholder) {
                                const newImg = document.createElement('img');
                                newImg.id = 'logo-preview';
                                newImg.src = event.target.result;
                                newImg.alt = 'Company Logo Preview';
                                newImg.className = 'w-32 h-32 rounded-xl object-cover border-4 border-gray-200';
                                placeholder.replaceWith(newImg);
                            }
                        }
                    };

                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush

@endsection
