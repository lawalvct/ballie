@extends('layouts.super-admin')

@section('title', 'Send Company Invitation')
@section('page-title', 'Send Company Invitation')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                @if(session('invitation_link'))
                <div class="mt-3 p-3 bg-yellow-100 rounded border">
                    <p class="text-xs font-medium text-yellow-800 mb-2">Manual Invitation Link:</p>
                    <div class="flex items-center space-x-2">
                        <input type="text" value="{{ session('invitation_link') }}" readonly 
                               class="flex-1 text-xs p-2 border rounded bg-white" id="invitation-link">
                        <button onclick="copyInvitationLink()" 
                                class="px-3 py-2 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                            Copy
                        </button>
                    </div>
                    <p class="text-xs text-yellow-700 mt-2">Send this link to: {{ session('recipient_email') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if($errors->any() && $errors->has('general'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were some problems with your submission:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @if($errors->has('general'))
                            @foreach($errors->get('general') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        @else
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Send Company Invitation
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">Send an invitation email to a prospective company owner</p>
                </div>
                <a href="{{ route('super-admin.tenants.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Companies
                </a>
            </div>
        </div>

        <!-- Info Banner -->
        <div class="p-6 bg-blue-50 border-l-4 border-blue-400">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">How it works</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>An invitation email will be sent to the owner's email address</li>
                            <li>They'll have 7 days to accept the invitation</li>
                            <li>Upon acceptance, their company and account will be created automatically</li>
                            <li>A 30-day trial period will begin for the selected plan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('super-admin.tenants.send-invitation') }}" method="POST" class="space-y-8">
        @csrf

        <!-- Company Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Company Information
                </h3>
                <p class="mt-1 text-sm text-gray-600">Details about the company to be created</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Name -->
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="company_name"
                               name="company_name"
                               value="{{ old('company_name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('company_name') border-red-500 @enderror"
                               placeholder="Enter company name"
                               required>
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Email -->
                    <div>
                        <label for="company_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               id="company_email"
                               name="company_email"
                               value="{{ old('company_email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('company_email') border-red-500 @enderror"
                               placeholder="company@example.com"
                               required>
                        @error('company_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="text"
                               id="phone"
                               name="phone"
                               value="{{ old('phone') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('phone') border-red-500 @enderror"
                               placeholder="+234 xxx xxx xxxx">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Type -->
                    <div>
                        <label for="business_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Business Type <span class="text-red-500">*</span>
                        </label>
                        <select id="business_type"
                                name="business_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('business_type') border-red-500 @enderror"
                                required>
                            <option value="">Select business type</option>
                            <option value="retail" {{ old('business_type') === 'retail' ? 'selected' : '' }}>Retail & E-commerce</option>
                            <option value="service" {{ old('business_type') === 'service' ? 'selected' : '' }}>Service Business</option>
                            <option value="restaurant" {{ old('business_type') === 'restaurant' ? 'selected' : '' }}>Restaurant & Food</option>
                            <option value="manufacturing" {{ old('business_type') === 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                            <option value="wholesale" {{ old('business_type') === 'wholesale' ? 'selected' : '' }}>Wholesale & Distribution</option>
                            <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('business_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Owner Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Owner Information
                </h3>
                <p class="mt-1 text-sm text-gray-600">Details about the person who will receive the invitation</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Owner Name -->
                    <div>
                        <label for="owner_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Owner Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="owner_name"
                               name="owner_name"
                               value="{{ old('owner_name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('owner_name') border-red-500 @enderror"
                               placeholder="Enter owner's full name"
                               required>
                        @error('owner_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Owner Email -->
                    <div>
                        <label for="owner_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Owner Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               id="owner_email"
                               name="owner_email"
                               value="{{ old('owner_email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('owner_email') border-red-500 @enderror"
                               placeholder="owner@company.com"
                               required>
                        <p class="mt-1 text-xs text-gray-500">The invitation will be sent to this email address</p>
                        @error('owner_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Plan -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    Subscription Plan
                </h3>
                <p class="mt-1 text-sm text-gray-600">Choose a plan for this company (30-day trial will be started upon acceptance)</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Plan Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-4">
                        Select Plan <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($plans as $plan)
                        <div class="relative">
                            <input type="radio"
                                   id="plan_{{ $plan->id }}"
                                   name="plan_id"
                                   value="{{ $plan->id }}"
                                   class="sr-only peer"
                                   {{ old('plan_id') == $plan->id ? 'checked' : '' }}
                                   required>
                            <label for="plan_{{ $plan->id }}"
                                   class="block p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all duration-200">
                                <div class="text-center">
                                    <h4 class="font-semibold text-gray-900">{{ $plan->name }}</h4>
                                    <div class="mt-2 space-y-1">
                                        <p class="text-2xl font-bold text-gray-900">
                                            ₦{{ number_format($plan->monthly_price / 100) }}
                                            <span class="text-sm font-normal text-gray-600">/month</span>
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            ₦{{ number_format($plan->yearly_price / 100) }}/year
                                        </p>
                                    </div>
                                    @if($plan->description)
                                    <p class="mt-2 text-xs text-gray-500">{{ $plan->description }}</p>
                                    @endif
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('plan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Billing Cycle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Billing Cycle <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-4">
                        <div class="flex items-center">
                            <input type="radio"
                                   id="monthly"
                                   name="billing_cycle"
                                   value="monthly"
                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                   {{ old('billing_cycle', 'monthly') === 'monthly' ? 'checked' : '' }}
                                   required>
                            <label for="monthly" class="ml-2 text-sm text-gray-700">Monthly</label>
                        </div>
                        <div class="flex items-center">
                            <input type="radio"
                                   id="yearly"
                                   name="billing_cycle"
                                   value="yearly"
                                   class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                   {{ old('billing_cycle') === 'yearly' ? 'checked' : '' }}>
                            <label for="yearly" class="ml-2 text-sm text-gray-700">Yearly (Save 2 months)</label>
                        </div>
                    </div>
                    @error('billing_cycle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Personal Message -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"></path>
                    </svg>
                    Personal Message
                </h3>
                <p class="mt-1 text-sm text-gray-600">Optional personal message to include in the invitation email</p>
            </div>

            <div class="p-6">
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                        Invitation Message
                    </label>
                    <textarea id="message"
                              name="message"
                              rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('message') border-red-500 @enderror"
                              placeholder="Add a personal message to the invitation email (optional)">{{ old('message') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">This message will be included in the invitation email</p>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('super-admin.tenants.index') }}"
               class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    id="submit-btn"
                    class="px-6 py-3 bg-amber-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span id="submit-text">Send Invitation</span>
            </button>
        </div>
    </form>

    <!-- Alternative Action -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-4">
                    Need to create a company immediately instead?
                </p>
                <a href="{{ route('super-admin.tenants.create') }}"
                   class="inline-flex items-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Company Directly
                </a>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill owner email when company email changes
    const companyEmail = document.getElementById('company_email');
    const ownerEmail = document.getElementById('owner_email');

    companyEmail.addEventListener('blur', function() {
        if (!ownerEmail.value && companyEmail.value) {
            const domain = companyEmail.value.split('@')[1];
            if (domain) {
                ownerEmail.placeholder = `owner@${domain}`;
            }
        }
    });

    // Form submission confirmation and loading state
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    
    form.addEventListener('submit', function(e) {
        const companyName = document.getElementById('company_name').value;
        const ownerEmail = document.getElementById('owner_email').value;

        if (!confirm(`Are you sure you want to send an invitation to "${ownerEmail}" for company "${companyName}"?`)) {
            e.preventDefault();
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Sending Invitation...';
        submitBtn.classList.add('opacity-75');
    });
});

function copyInvitationLink() {
    const linkInput = document.getElementById('invitation-link');
    linkInput.select();
    document.execCommand('copy');
    
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copied!';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-yellow-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-yellow-600');
    }, 2000);
});
</script>
@endsection
