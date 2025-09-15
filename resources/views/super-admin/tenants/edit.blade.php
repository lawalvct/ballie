@extends('layouts.super-admin')

@section('title', 'Edit Company - ' . $tenant->name)
@section('page-title', 'Edit Company')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 mr-4">
                        @if($tenant->logo)
                            <img class="h-12 w-12 rounded-lg border border-gray-200" src="{{ $tenant->logo }}" alt="{{ $tenant->name }}">
                        @else
                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-md">
                                <span class="text-lg font-bold text-white">{{ substr($tenant->name, 0, 2) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit {{ $tenant->name }}</h1>
                        <p class="text-sm text-gray-600">Update company information and settings</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('super-admin.tenants.show', $tenant) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View Details
                    </a>
                    <a href="{{ route('super-admin.tenants.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Companies
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <form action="{{ route('super-admin.tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Main Form - Left Side -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Basic Information -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                        <p class="text-sm text-gray-600">Essential company details and contact information</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Company Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $tenant->name) }}"
                                       required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-300 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email', $tenant->email) }}"
                                       required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-300 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', $tenant->phone) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('phone') border-red-300 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Website -->
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                <input type="url"
                                       name="website"
                                       id="website"
                                       value="{{ old('website', $tenant->website) }}"
                                       placeholder="https://example.com"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('website') border-red-300 @enderror">
                                @error('website')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Business Type -->
                            <div>
                                <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">Business Type</label>
                                <select name="business_type"
                                        id="business_type"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('business_type') border-red-300 @enderror">
                                    <option value="">Select Business Type</option>
                                    <option value="retail" {{ old('business_type', $tenant->business_type) === 'retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="wholesale" {{ old('business_type', $tenant->business_type) === 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                                    <option value="manufacturing" {{ old('business_type', $tenant->business_type) === 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                    <option value="service" {{ old('business_type', $tenant->business_type) === 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="restaurant" {{ old('business_type', $tenant->business_type) === 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                                    <option value="healthcare" {{ old('business_type', $tenant->business_type) === 'healthcare' ? 'selected' : '' }}>Healthcare</option>
                                    <option value="education" {{ old('business_type', $tenant->business_type) === 'education' ? 'selected' : '' }}>Education</option>
                                    <option value="nonprofit" {{ old('business_type', $tenant->business_type) === 'nonprofit' ? 'selected' : '' }}>Non-profit</option>
                                    <option value="other" {{ old('business_type', $tenant->business_type) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('business_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Company Slug -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    Company Slug <span class="text-red-500">*</span>
                                </label>
                                <div class="flex">
                                    <span class="inline-flex items-center px-3 py-2 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                        {{ config('app.url') }}/
                                    </span>
                                    <input type="text"
                                           name="slug"
                                           id="slug"
                                           value="{{ old('slug', $tenant->slug) }}"
                                           required
                                           pattern="[a-z0-9-]+"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-r-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('slug') border-red-300 @enderror">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Only lowercase letters, numbers, and hyphens allowed</p>
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                        <h2 class="text-lg font-semibold text-gray-900">Address Information</h2>
                        <p class="text-sm text-gray-600">Company location and registration details</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                                <textarea name="address"
                                          id="address"
                                          rows="3"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('address') border-red-300 @enderror">{{ old('address', $tenant->address) }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- City -->
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                    <input type="text"
                                           name="city"
                                           id="city"
                                           value="{{ old('city', $tenant->city) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('city') border-red-300 @enderror">
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- State -->
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                                    <input type="text"
                                           name="state"
                                           id="state"
                                           value="{{ old('state', $tenant->state) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('state') border-red-300 @enderror">
                                    @error('state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Country -->
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                    <select name="country"
                                            id="country"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('country') border-red-300 @enderror">
                                        <option value="">Select Country</option>
                                        <option value="Nigeria" {{ old('country', $tenant->country) === 'Nigeria' ? 'selected' : '' }}>Nigeria</option>
                                        <option value="Ghana" {{ old('country', $tenant->country) === 'Ghana' ? 'selected' : '' }}>Ghana</option>
                                        <option value="Kenya" {{ old('country', $tenant->country) === 'Kenya' ? 'selected' : '' }}>Kenya</option>
                                        <option value="South Africa" {{ old('country', $tenant->country) === 'South Africa' ? 'selected' : '' }}>South Africa</option>
                                        <option value="United States" {{ old('country', $tenant->country) === 'United States' ? 'selected' : '' }}>United States</option>
                                        <option value="United Kingdom" {{ old('country', $tenant->country) === 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                        <option value="Canada" {{ old('country', $tenant->country) === 'Canada' ? 'selected' : '' }}>Canada</option>
                                    </select>
                                    @error('country')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Business Registration Number -->
                            <div>
                                <label for="business_registration_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Business Registration Number
                                </label>
                                <input type="text"
                                       name="business_registration_number"
                                       id="business_registration_number"
                                       value="{{ old('business_registration_number', $tenant->business_registration_number) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('business_registration_number') border-red-300 @enderror">
                                @error('business_registration_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tax Identification Number -->
                            <div>
                                <label for="tax_identification_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tax Identification Number
                                </label>
                                <input type="text"
                                       name="tax_identification_number"
                                       id="tax_identification_number"
                                       value="{{ old('tax_identification_number', $tenant->tax_identification_number) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('tax_identification_number') border-red-300 @enderror">
                                @error('tax_identification_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Settings -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
                        <h2 class="text-lg font-semibold text-gray-900">Financial Settings</h2>
                        <p class="text-sm text-gray-600">Accounting and financial configuration</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payment Terms -->
                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">Default Payment Terms (Days)</label>
                                <select name="payment_terms"
                                        id="payment_terms"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('payment_terms') border-red-300 @enderror">
                                    <option value="">Select Payment Terms</option>
                                    <option value="0" {{ old('payment_terms', $tenant->payment_terms) === '0' ? 'selected' : '' }}>Due on Receipt</option>
                                    <option value="7" {{ old('payment_terms', $tenant->payment_terms) === '7' ? 'selected' : '' }}>Net 7</option>
                                    <option value="15" {{ old('payment_terms', $tenant->payment_terms) === '15' ? 'selected' : '' }}>Net 15</option>
                                    <option value="30" {{ old('payment_terms', $tenant->payment_terms) === '30' ? 'selected' : '' }}>Net 30</option>
                                    <option value="45" {{ old('payment_terms', $tenant->payment_terms) === '45' ? 'selected' : '' }}>Net 45</option>
                                    <option value="60" {{ old('payment_terms', $tenant->payment_terms) === '60' ? 'selected' : '' }}>Net 60</option>
                                </select>
                                @error('payment_terms')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fiscal Year Start -->
                            <div>
                                <label for="fiscal_year_start" class="block text-sm font-medium text-gray-700 mb-2">Fiscal Year Start</label>
                                <select name="fiscal_year_start"
                                        id="fiscal_year_start"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('fiscal_year_start') border-red-300 @enderror">
                                    <option value="">Select Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('fiscal_year_start', $tenant->fiscal_year_start) == $i ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                                @error('fiscal_year_start')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-8">

                <!-- Logo Upload -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                        <h2 class="text-lg font-semibold text-gray-900">Company Logo</h2>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="mb-4">
                                @if($tenant->logo)
                                    <img id="logo-preview" src="{{ $tenant->logo }}" alt="Company Logo" class="mx-auto h-24 w-24 rounded-lg border border-gray-200 object-cover">
                                @else
                                    <div id="logo-preview" class="mx-auto h-24 w-24 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-md">
                                        <span class="text-2xl font-bold text-white">{{ substr($tenant->name, 0, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="space-y-2">
                                <input type="file"
                                       name="logo"
                                       id="logo"
                                       accept="image/*"
                                       class="hidden"
                                       onchange="previewLogo(this)">
                                <label for="logo" class="cursor-pointer inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    Upload Logo
                                </label>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                @error('logo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Settings -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h2 class="text-lg font-semibold text-gray-900">Subscription</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Plan Selection -->
                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">Plan</label>
                            <select name="plan_id"
                                    id="plan_id"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('plan_id') border-red-300 @enderror">
                                <option value="">Select Plan</option>
                                @foreach(\App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get() as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->plan_id) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} - ₦{{ number_format($plan->monthly_price / 100) }}/month
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Billing Cycle -->
                        <div>
                            <label for="billing_cycle" class="block text-sm font-medium text-gray-700 mb-2">Billing Cycle</label>
                            <select name="billing_cycle"
                                    id="billing_cycle"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('billing_cycle') border-red-300 @enderror">
                                <option value="monthly" {{ old('billing_cycle', $tenant->billing_cycle) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ old('billing_cycle', $tenant->billing_cycle) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                            @error('billing_cycle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subscription Status -->
                        <div>
                            <label for="subscription_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="subscription_status"
                                    id="subscription_status"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('subscription_status') border-red-300 @enderror">
                                <option value="trial" {{ old('subscription_status', $tenant->subscription_status) === 'trial' ? 'selected' : '' }}>Trial</option>
                                <option value="active" {{ old('subscription_status', $tenant->subscription_status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('subscription_status', $tenant->subscription_status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="cancelled" {{ old('subscription_status', $tenant->subscription_status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('subscription_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Trial End Date -->
                        <div>
                            <label for="trial_ends_at" class="block text-sm font-medium text-gray-700 mb-2">Trial End Date</label>
                            <input type="date"
                                   name="trial_ends_at"
                                   id="trial_ends_at"
                                   value="{{ old('trial_ends_at', $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d') : '') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('trial_ends_at') border-red-300 @enderror">
                            @error('trial_ends_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50">
                        <h2 class="text-lg font-semibold text-gray-900">System Settings</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company Status</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Company is active</span>
                                </label>
                            </div>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Domain -->
                        <div>
                            <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">Custom Domain</label>
                            <input type="text"
                                   name="domain"
                                   id="domain"
                                   value="{{ old('domain', $tenant->domain) }}"
                                   placeholder="company.com"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('domain') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Optional custom domain</p>
                            @error('domain')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <div class="space-y-3">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent rounded-lg font-medium text-white hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Company
                            </button>

                            <a href="{{ route('super-admin.tenants.show', $tenant) }}"
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const preview = document.getElementById('logo-preview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" class="h-24 w-24 rounded-lg border border-gray-200 object-cover">`;
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-generate slug from company name
document.getElementById('name').addEventListener('input', function() {
    const slugField = document.getElementById('slug');
    if (!slugField.dataset.manualEdit) {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        slugField.value = slug;
    }
});

document.getElementById('slug').addEventListener('input', function() {
    this.dataset.manualEdit = 'true';
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = ['name', 'email', 'slug'];
    let isValid = true;

    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('border-red-300');
        } else {
            input.classList.remove('border-red-300');
        }
    });

    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>
@endsection
