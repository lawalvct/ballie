@extends('layouts.app')

@section('title', 'Verify Email - Ballie')
@section('description', 'Verify your email address to get started with Ballie.')

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

    .verification-card {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    .code-input {
        letter-spacing: 0.5em;
        font-family: 'Courier New', monospace;
        transition: all 0.3s ease;
    }

    .code-input:focus {
        transform: scale(1.02);
        box-shadow: 0 0 0 4px rgba(209, 176, 94, 0.1);
    }

    .resend-btn {
        transition: all 0.3s ease;
    }

    .resend-btn:hover {
        transform: translateY(-2px);
    }

    .logout-btn {
        transition: all 0.3s ease;
    }

    .logout-btn:hover {
        transform: translateX(4px);
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .pulse-animation {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>

<div class="min-h-screen gradient-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Verify Your Email</h1>
            <p class="text-gray-200">We've sent a code to <strong class="font-semibold" style="color: var(--color-gold);">{{ Auth::user()->email }}</strong></p>
        </div>

        <!-- Verification Form -->
        <div class="verification-card rounded-2xl shadow-2xl p-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <!-- Instructions -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-gray-700 leading-relaxed">
                    Enter the <strong>4-digit verification code</strong> sent to your email. The code will expire in 60 minutes.
                </p>
            </div>

            <form method="POST" action="{{ route('verification.verify') }}" class="space-y-6">
                @csrf

                <!-- Verification Code Input -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Verification Code
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="code"
                            id="code"
                            maxlength="4"
                            pattern="[0-9]{4}"
                            class="code-input w-full pl-12 pr-4 py-4 text-center text-3xl font-bold rounded-lg border-2 border-gray-300 focus:ring-2 focus:ring-offset-2 transition-all duration-300 @error('code') border-red-300 @enderror"
                            style="border-color: var(--color-blue); color: var(--color-deep-purple);"
                            placeholder="○ ○ ○ ○"
                            value="{{ old('code') }}"
                            required
                            autofocus
                            autocomplete="off"
                        >
                    </div>
                    <p class="mt-2 text-xs text-gray-500 text-center">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Code auto-submits when complete
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="verifyBtn" class="w-full py-4 px-4 rounded-lg font-semibold text-white transition-all duration-300 hover:opacity-90 transform hover:scale-105 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg" style="background-color: var(--color-gold);">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Verify Email Address</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Need help?</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <!-- Resend Code Button -->
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" id="resendBtn" class="resend-btn w-full py-3 px-4 rounded-lg font-medium transition-all duration-300 hover:shadow-md flex items-center justify-center" style="background-color: rgba(43, 99, 153, 0.1); color: var(--color-blue);">
                        <svg id="resendIcon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <svg id="resendSpinner" class="hidden w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="resendText">Resend Verification Code</span>
                        <span id="resendCountdown" class="hidden"></span>
                    </button>
                </form>

                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn w-full py-3 px-4 rounded-lg font-medium text-gray-700 hover:bg-gray-100 transition-all duration-300 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>

            <!-- Expiry Notice -->
            <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 mr-2 pulse-animation" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-xs text-yellow-800">
                        <strong>Code expires in 60 minutes.</strong> Request a new code if expired.
                    </p>
                </div>
            </div>
        </div>

        <!-- Help Text -->
        <div class="text-center">
            <p class="text-white text-sm opacity-75">
                Didn't receive the email? Check your spam folder or
                <button onclick="document.getElementById('resendBtn').click()" class="font-semibold hover:underline" style="color: var(--color-gold);">
                    resend code
                </button>
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
        const codeInput = document.getElementById('code');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');
        const resendText = document.getElementById('resendText');
        const resendCountdown = document.getElementById('resendCountdown');
        const resendIcon = document.getElementById('resendIcon');
        const resendSpinner = document.getElementById('resendSpinner');

        // Auto-format and submit code input
        codeInput.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');

            // Update placeholder dots
            const filled = this.value.length;
            const empty = 4 - filled;
            const dots = '●'.repeat(filled) + '○'.repeat(empty);

            // Auto-submit when 4 digits are entered
            if (this.value.length === 4) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = `
                    <svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Verifying...</span>
                `;
                setTimeout(() => {
                    this.form.submit();
                }, 300);
            }
        });

        // Select all text when focused
        codeInput.addEventListener('focus', function() {
            this.select();
        });

        // Add visual feedback for typing
        codeInput.addEventListener('keyup', function() {
            if (this.value.length > 0) {
                this.style.borderColor = 'var(--color-gold)';
            } else {
                this.style.borderColor = 'var(--color-blue)';
            }
        });

        // Handle resend button rate limiting
        const rateLimitKey = 'email_verification_resend_limit';
        const rateLimitData = localStorage.getItem(rateLimitKey);

        if (rateLimitData) {
            const { expiresAt } = JSON.parse(rateLimitData);
            const now = Date.now();

            if (now < expiresAt) {
                startCountdown(Math.ceil((expiresAt - now) / 1000));
            } else {
                localStorage.removeItem(rateLimitKey);
            }
        }

        // Handle resend form submission
        resendBtn.closest('form').addEventListener('submit', function(e) {
            // Show loading state
            resendBtn.disabled = true;
            resendIcon.classList.add('hidden');
            resendSpinner.classList.remove('hidden');
            resendText.textContent = 'Sending...';

            // Store rate limit in localStorage (60 seconds)
            const expiresAt = Date.now() + (60 * 1000);
            localStorage.setItem(rateLimitKey, JSON.stringify({ expiresAt }));
        });

        function startCountdown(seconds) {
            resendBtn.disabled = true;
            resendText.classList.add('hidden');
            resendCountdown.classList.remove('hidden');
            resendIcon.classList.add('hidden');

            updateCountdown(seconds);

            const interval = setInterval(() => {
                seconds--;

                if (seconds <= 0) {
                    clearInterval(interval);
                    resetResendButton();
                    localStorage.removeItem(rateLimitKey);
                } else {
                    updateCountdown(seconds);
                }
            }, 1000);
        }

        function updateCountdown(seconds) {
            resendCountdown.textContent = `Wait ${seconds}s before resending`;
        }

        function resetResendButton() {
            resendBtn.disabled = false;
            resendText.classList.remove('hidden');
            resendCountdown.classList.add('hidden');
            resendIcon.classList.remove('hidden');
            resendSpinner.classList.add('hidden');
            resendText.textContent = 'Resend Verification Code';
        }

        // Add keyboard shortcut (Ctrl/Cmd + R to resend)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                if (!resendBtn.disabled) {
                    resendBtn.click();
                }
            }
        });
    });
</script>
@endsection
