@extends('layouts.app')

@section('title', 'Verify Your Affiliate Email')

@section('content')
    <style>
        :root {
            --color-blue: #2b6399;
            --color-gold: #d1b05e;
            --color-dark-purple: #3c2c64;
            --color-deep-purple: #4a3570;
            --color-purple: #8b5cf6;
            --color-purple-light: #a78bfa;
            --color-indigo: #6366f1;
            --color-green: #10b981;
            --color-red: #ef4444;
            --color-yellow: #f59e0b;
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 50%, var(--color-deep-purple) 100%);
            min-height: 100vh;
        }

        .verification-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .code-input {
            letter-spacing: 0.5em;
            font-family: 'Courier New', monospace;
        }

        .icon-badge {
            background: rgba(255, 255, 255, 0.2);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .pulse { animation: pulse 2s infinite; }
    </style>

    <div class="gradient-bg flex items-center justify-center p-4">
        <div class="verification-card rounded-2xl shadow-2xl max-w-md w-full p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="icon-badge w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Verify Your Affiliate Email</h1>
                <p class="text-gray-600">
                    We've sent a verification code to
                    <span class="font-semibold" style="color: var(--color-gold);">{{ Auth::user()->email }}</span>
                </p>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="text-red-800 font-medium">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <!-- Instructions -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-blue-800 text-sm">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Enter the 4-digit verification code sent to your email to activate your affiliate account.
                </p>
            </div>

            <!-- Verification Form -->
            <form method="POST" action="{{ route('affiliate.verification.verify') }}" id="verification-form" class="space-y-6">
                @csrf

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="15 7a2 2 0 012 2m-2-2l-2-2m2 2l-2 2m2-2H9m6 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v2a2 2 0 002 2h6m2-2a2 2 0 002 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2v-2a2 2 0 012-2h6z"></path>
                        </svg>
                    </div>
                    <input type="text"
                           name="code"
                           id="code-input"
                           class="code-input w-full pl-10 pr-4 py-4 text-3xl text-center border-2 border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="○ ○ ○ ○"
                           maxlength="4"
                           autocomplete="off"
                           required>
                </div>

                <!-- Verify Button -->
                <button type="submit"
                        id="verify-btn"
                        class="w-full py-4 px-6 text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center"
                        style="background: var(--color-gold); box-shadow: 0 4px 6px rgba(209, 176, 94, 0.3);"
                        onmouseover="this.style.opacity='0.9'; this.style.transform='scale(1.05)'"
                        onmouseout="this.style.opacity='1'; this.style.transform='scale(1)'">
                    <svg class="w-5 h-5 mr-2" id="verify-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span id="verify-text">Verify Email & Activate Account</span>
                    <svg class="w-5 h-5 ml-2 animate-spin hidden" id="verify-spinner" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 text-center">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Need help?</span>
                    </div>
                </div>
            </div>

            <!-- Resend Code -->
            <form method="POST" action="{{ route('affiliate.verification.resend') }}" class="space-y-4">
                @csrf
                <button type="submit"
                        id="resend-btn"
                        class="w-full py-3 px-6 text-blue-700 font-medium rounded-xl border-2 border-transparent transition-all duration-200 flex items-center justify-center"
                        style="background: rgba(43, 99, 153, 0.1);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(43, 99, 153, 0.2)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <svg class="w-4 h-4 mr-2" id="resend-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="resend-text">Send New Code</span>
                    <svg class="w-4 h-4 ml-2 animate-spin hidden" id="resend-spinner" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <!-- Logout Link -->
            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm font-medium transition-all duration-200 hover:bg-gray-100 px-3 py-1 rounded flex items-center justify-center mx-auto"
                            onmouseover="this.style.transform='translateX(4px)'"
                            onmouseout="this.style.transform='translateX(0)'">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Sign out
                    </button>
                </form>
            </div>

            <!-- Code Expiry Notice -->
            <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800 text-xs text-center flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1 pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <strong>Verification codes expire after 60 minutes</strong>
                </p>
            </div>

            <!-- Help Text -->
            {{-- <div class="mt-4 text-center">
                <p class="text-white text-sm">
                    Didn't receive the code?
                    <button onclick="document.getElementById('resend-btn').click()" class="font-semibold underline hover:no-underline" style="color: var(--color-gold);">
                        Click here to resend
                    </button>
                </p>
                <p class="text-white text-xs mt-2 opacity-90">
                    Press <kbd class="px-1 py-0.5 bg-gray-200 rounded text-gray-800 text-xs">Ctrl+R</kbd> (or <kbd class="px-1 py-0.5 bg-gray-200 rounded text-gray-800 text-xs">⌘+R</kbd>) to quickly resend
                </p>
            </div> --}}

            <!-- Trust Indicators -->
            <div class="mt-6 flex justify-center space-x-6 text-xs" style="color: var(--color-blue);">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    SSL Secured
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Privacy Protected
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('code-input');
            const verifyForm = document.getElementById('verification-form');
            const verifyBtn = document.getElementById('verify-btn');
            const verifyIcon = document.getElementById('verify-icon');
            const verifySpinner = document.getElementById('verify-spinner');
            const verifyText = document.getElementById('verify-text');
            const resendBtn = document.getElementById('resend-btn');
            const resendIcon = document.getElementById('resend-icon');
            const resendSpinner = document.getElementById('resend-spinner');
            const resendText = document.getElementById('resend-text');

            // Auto-format input (numbers only)
            codeInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value;

                // Update visual dots
                const dots = ['○', '○', '○', '○'];
                for (let i = 0; i < value.length && i < 4; i++) {
                    dots[i] = '●';
                }

                // Change border color
                if (value.length > 0) {
                    e.target.style.borderColor = 'var(--color-gold)';
                } else {
                    e.target.style.borderColor = '#93c5fd';
                }

                // Auto-submit when 4 digits entered
                if (value.length === 4) {
                    setTimeout(() => {
                        verifyBtn.disabled = true;
                        verifyIcon.classList.add('hidden');
                        verifySpinner.classList.remove('hidden');
                        verifyText.textContent = 'Verifying...';
                        verifyForm.submit();
                    }, 300);
                }
            });

            // Select all on focus
            codeInput.addEventListener('focus', function() {
                this.select();
            });

            // Rate limiting for resend
            const lastResendKey = 'affiliate_verification_resend_limit';
            function checkResendLimit() {
                const lastResend = localStorage.getItem(lastResendKey);
                if (lastResend) {
                    const timeDiff = Date.now() - parseInt(lastResend);
                    const remainingTime = 60000 - timeDiff; // 60 seconds

                    if (remainingTime > 0) {
                        resendBtn.disabled = true;
                        resendText.textContent = `Wait ${Math.ceil(remainingTime / 1000)}s before resending`;

                        const countdown = setInterval(() => {
                            const remaining = 60000 - (Date.now() - parseInt(lastResend));
                            if (remaining <= 0) {
                                clearInterval(countdown);
                                resetResendButton();
                            } else {
                                resendText.textContent = `Wait ${Math.ceil(remaining / 1000)}s before resending`;
                            }
                        }, 1000);
                    }
                }
            }

            function resetResendButton() {
                resendBtn.disabled = false;
                resendText.textContent = 'Send New Code';
                localStorage.removeItem(lastResendKey);
            }

            // Handle resend form submission
            resendBtn.parentElement.addEventListener('submit', function(e) {
                if (resendBtn.disabled) {
                    e.preventDefault();
                    return;
                }

                resendBtn.disabled = true;
                resendIcon.classList.add('hidden');
                resendSpinner.classList.remove('hidden');
                resendText.textContent = 'Sending...';

                // Store timestamp
                localStorage.setItem(lastResendKey, Date.now().toString());
            });

            // Keyboard shortcut for resend (Ctrl+R or Cmd+R)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                    e.preventDefault();
                    if (!resendBtn.disabled) {
                        resendBtn.click();
                    }
                }
            });

            // Check rate limit on load
            checkResendLimit();
        });
    </script>
@endsection