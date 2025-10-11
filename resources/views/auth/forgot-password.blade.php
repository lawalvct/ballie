@extends('layouts.app')

@section('title', 'Forgot Password - Ballie')
@section('description', 'Reset your Ballie account password.')

@section('content')
<style>
    :root {
        --color-gold: #d1b05e;
        --color-blue: #2b6399;
        --color-dark-purple: #3c2c64;
        --color-teal: #69a2a4;
        --color-purple: #85729d;
        --color-light-blue: #7b87b8;
        --color-deep-purple: #4a3570;
        --color-lavender: #a48cb4;
        --color-violet: #614c80;
        --color-green: #249484;
    }

    .gradient-bg {
        background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 50%, var(--color-deep-purple) 100%);
    }

    .forgot-password-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    .back-btn {
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        transform: translateX(-4px);
    }
</style>

<div class="min-h-screen gradient-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Forgot Password?</h1>
            <p class="text-gray-200">No worries, we'll send you reset instructions</p>
        </div>

        <!-- Forgot Password Form -->
        <div class="forgot-password-card rounded-2xl shadow-2xl p-8">
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Instructions -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-gray-700 leading-relaxed">
                    Enter your email address and we'll send you a link to reset your password. The link will be valid for 60 minutes.
                </p>
            </div>

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300"
                               placeholder="Enter your email address">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="w-full py-3 px-4 rounded-lg font-semibold text-white transition-all duration-300 hover:opacity-90 transform hover:scale-105 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" style="background-color: var(--color-gold);">
                    <svg id="mailIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <svg id="spinnerIcon" class="hidden w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="btnText">Send Reset Link</span>
                    <span id="countdownText" class="hidden"></span>
                </button>

                <!-- Back to Login -->
                <div class="text-center pt-4">
                    <a href="{{ route('login') }}" class="back-btn inline-flex items-center text-sm font-medium hover:underline" style="color: var(--color-blue);">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Sign In
                    </a>
                </div>
            </form>
        </div>

        <!-- Help Text -->
        <div class="text-center">
            <p class="text-white text-sm opacity-75">
                Need help? Contact our
                <a href="mailto:support@ballie.com" class="font-semibold hover:underline" style="color: var(--color-gold);">
                    support team
                </a>
            </p>
        </div>

        <!-- Trust Indicators -->
        <div class="text-center">
            <div class="flex items-center justify-center space-x-6 text-white text-sm opacity-75">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    SSL Secured
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Privacy Protected
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const countdownText = document.getElementById('countdownText');
        const mailIcon = document.getElementById('mailIcon');
        const spinnerIcon = document.getElementById('spinnerIcon');

        // Check if there's a rate limit from localStorage
        const rateLimitKey = 'password_reset_limit';
        const rateLimitData = localStorage.getItem(rateLimitKey);

        if (rateLimitData) {
            const { email, expiresAt } = JSON.parse(rateLimitData);
            const now = Date.now();

            if (now < expiresAt) {
                startCountdown(Math.ceil((expiresAt - now) / 1000));
            } else {
                localStorage.removeItem(rateLimitKey);
            }
        }

        // Handle form submission
        form.addEventListener('submit', function(e) {
            // Show loading state
            submitBtn.disabled = true;
            mailIcon.classList.add('hidden');
            spinnerIcon.classList.remove('hidden');
            btnText.textContent = 'Sending...';

            // Store rate limit in localStorage (1 minute = 60 seconds)
            const emailInput = document.getElementById('email');
            const expiresAt = Date.now() + (60 * 1000); // 1 minute from now

            localStorage.setItem(rateLimitKey, JSON.stringify({
                email: emailInput.value,
                expiresAt: expiresAt
            }));
        });

        function startCountdown(seconds) {
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            countdownText.classList.remove('hidden');
            mailIcon.classList.add('hidden');

            updateCountdown(seconds);

            const interval = setInterval(() => {
                seconds--;

                if (seconds <= 0) {
                    clearInterval(interval);
                    resetButton();
                    localStorage.removeItem(rateLimitKey);
                } else {
                    updateCountdown(seconds);
                }
            }, 1000);
        }

        function updateCountdown(seconds) {
            countdownText.textContent = `Please wait ${seconds}s before requesting again`;
        }

        function resetButton() {
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            countdownText.classList.add('hidden');
            mailIcon.classList.remove('hidden');
            spinnerIcon.classList.add('hidden');
            btnText.textContent = 'Send Reset Link';
        }
    });
</script>
@endsection
