@extends('layouts.app')

@section('title', 'Become an Affiliate - Ballie')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Join Ballie's Affiliate Program</h1>
            <p class="text-xl text-gray-600">Start earning {{ config('affiliate.default_commission_rate') }}% recurring commission today</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <h2 class="text-2xl font-bold text-white">Create Your Affiliate Account</h2>
                <p class="text-blue-100 mt-2">Fill in the details below to get started</p>
            </div>

            <form method="POST" action="{{ route('affiliate.store') }}" class="p-8 space-y-6">
                @csrf

                @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-green-800 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-red-800 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="text-red-800 font-medium">Please fix the following errors:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>

                <!-- Account Credentials -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Account Credentials
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <p class="mt-1 text-sm text-gray-500">You'll use this email to log into your affiliate dashboard</p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                <input type="password" name="password" id="password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Business Information
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Company/Business Name (optional)</label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="+234 800 000 0000">
                        </div>
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio / About You</label>
                            <textarea name="bio" id="bio" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                placeholder="Tell us about yourself and how you plan to promote Ballie...">{{ old('bio') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Maximum 1000 characters</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Payment Information
                    </h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                        <p class="text-sm text-blue-800">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            You can update these details later from your dashboard
                        </p>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Preferred Payment Method *</label>
                            <select name="payment_method" id="payment_method" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">Select payment method</option>
                                @foreach(config('affiliate.payout_methods') as $key => $label)
                                    <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="bank-transfer-fields" class="space-y-4 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name</label>
                                <input type="text" name="payment_details[bank_name]" value="{{ old('payment_details.bank_name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account Number</label>
                                <input type="text" name="payment_details[account_number]" value="{{ old('payment_details.account_number') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Account Name</label>
                                <input type="text" name="payment_details[account_name]" value="{{ old('payment_details.account_name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div id="nomba-fields" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">nomba Email</label>
                            <input type="email" name="payment_details[nomba_email]" value="{{ old('payment_details.nomba_email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>

                        <div id="paystack-fields" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Paystack Account Email</label>
                            <input type="email" name="payment_details[paystack_email]" value="{{ old('payment_details.paystack_email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <label class="flex items-start">
                        <input type="checkbox" name="agree_terms" required
                            class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm text-gray-700">
                            I agree to the <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 hover:underline">Terms of Service</a>
                            and <a href="{{ route('privacy') }}" target="_blank" class="text-blue-600 hover:underline">Privacy Policy</a>.
                            I understand that I will earn {{ config('affiliate.default_commission_rate') }}% recurring commission on all payments made by my referrals.
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-4 px-8 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Create Affiliate Account
                    </button>
                    <a href="{{ route('affiliate.index') }}"
                        class="flex-1 text-center border-2 border-gray-300 text-gray-700 font-semibold py-4 px-8 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        Cancel
                    </a>
                </div>

                <div class="text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Sign in here</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const bankFields = document.getElementById('bank-transfer-fields');
    const nombaFields = document.getElementById('nomba-fields');
    const paystackFields = document.getElementById('paystack-fields');

    paymentMethod.addEventListener('change', function() {
        // Hide all fields first
        bankFields.classList.add('hidden');
        nombaFields.classList.add('hidden');
        paystackFields.classList.add('hidden');

        // Show relevant fields
        if (this.value === 'bank_transfer') {
            bankFields.classList.remove('hidden');
        } else if (this.value === 'nomba') {
            nombaFields.classList.remove('hidden');
        } else if (this.value === 'paystack') {
            paystackFields.classList.remove('hidden');
        }
    });

    // Trigger on page load if there's an old value
    if (paymentMethod.value) {
        paymentMethod.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
