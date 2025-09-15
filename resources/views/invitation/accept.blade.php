<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Accept Invitation - {{ config('app.name', 'Ballie') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-white">
                    You're invited to join
                </h2>
                <p class="mt-2 text-xl text-indigo-100">
                    {{ $invitation->company_name }}
                </p>
            </div>

            <!-- Main Card -->
            <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
                <!-- Company Info Header -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-8 py-6 border-b border-gray-200">
                    <div class="text-center">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $invitation->company_name }}</h3>
                        <p class="mt-1 text-gray-600">{{ $invitation->company_email }}</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Business Type: {{ ucfirst(str_replace('_', ' ', $invitation->business_type)) }}
                        </p>
                    </div>
                </div>

                <!-- Plan Information -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-8 py-6 border-b border-gray-200">
                    <div class="text-center">
                        <h4 class="text-lg font-semibold text-gray-900">{{ $plan->name }} Plan</h4>
                        <div class="mt-2 text-3xl font-bold text-green-600">
                            ₦{{ number_format(($invitation->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price) / 100) }}
                        </div>
                        <p class="text-sm text-gray-600">
                            per {{ $invitation->billing_cycle === 'yearly' ? 'year' : 'month' }}
                            @if($invitation->billing_cycle === 'yearly')
                                <span class="text-green-600 font-medium">
                                    (Save ₦{{ number_format((($plan->monthly_price * 12) - $plan->yearly_price) / 100) }} annually!)
                                </span>
                            @endif
                        </p>
                        <div class="mt-3 inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            🎉 30-day free trial included!
                        </div>
                    </div>
                </div>

                <!-- Welcome Message -->
                @if($invitation->message)
                <div class="bg-amber-50 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-1l-4 4z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-amber-800">Personal Message</h4>
                            <p class="mt-1 text-sm text-amber-700">"{{ $invitation->message }}"</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('invitation.accept', $token) }}" class="px-8 py-8 space-y-6">
                    @csrf

                    <!-- Welcome Text -->
                    <div class="text-center">
                        <h3 class="text-lg font-semibold text-gray-900">Welcome, {{ $invitation->owner_name }}!</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Complete your account setup to get started with your free trial.
                        </p>
                    </div>

                    <!-- Password Fields -->
                    <div class="space-y-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Create Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                                   placeholder="Enter a secure password"
                                   required>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                   placeholder="Confirm your password"
                                   required>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   id="terms"
                                   name="terms"
                                   class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 @error('terms') border-red-500 @enderror"
                                   required>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="text-gray-700">
                                I agree to the
                                <a href="#" class="text-indigo-600 hover:text-indigo-500 underline">Terms of Service</a>
                                and
                                <a href="#" class="text-indigo-600 hover:text-indigo-500 underline">Privacy Policy</a>
                                <span class="text-red-500">*</span>
                            </label>
                            @error('terms')
                                <p class="mt-1 text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if($errors->has('error'))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-600">{{ $errors->first('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Accept Invitation & Create Account
                    </button>
                </form>

                <!-- Features Preview -->
                <div class="bg-gray-50 px-8 py-6">
                    <h4 class="text-sm font-semibold text-gray-900 mb-4">What you'll get with Ballie:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Complete accounting system
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Inventory management
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Customer & vendor management
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Financial reports & insights
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Multi-user collaboration
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            24/7 customer support
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-sm text-indigo-100">
                Need help? Contact us at
                <a href="mailto:{{ config('mail.from.address') }}" class="text-white underline hover:text-indigo-200">
                    {{ config('mail.from.address') }}
                </a>
            </p>
        </div>
    </div>

    <script>
        // Password strength validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const confirmPassword = document.getElementById('password_confirmation');

            // Basic password validation
            if (password.length < 8) {
                this.setCustomValidity('Password must be at least 8 characters long');
            } else {
                this.setCustomValidity('');
            }

            // Check password match
            if (confirmPassword.value && password !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });

        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Form submission handling
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            const originalHtml = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creating your account...
            `;

            // Reset button if form validation fails
            setTimeout(() => {
                if (submitButton.disabled) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalHtml;
                }
            }, 5000);
        });
    </script>
</body>
</html>
