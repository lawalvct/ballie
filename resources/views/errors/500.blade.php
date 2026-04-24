@extends('layouts.app')

@section('title', 'Server Error - Ballie')
@section('description', 'We encountered an unexpected error. Our team has been notified.')

@section('content')
@php
    // Decide where to redirect the user automatically. If they are inside a
    // tenant context, send them to the tenant login; otherwise use the
    // global login or home page.
    $__tenant = request()->route('tenant') ?? (function_exists('tenant') ? tenant() : null);
    $__tenantSlug = is_object($__tenant) ? ($__tenant->slug ?? null) : $__tenant;

    if (! empty($__tenantSlug) && \Illuminate\Support\Facades\Route::has('tenant.login')) {
        $redirectUrl   = route('tenant.login', ['tenant' => $__tenantSlug]);
        $redirectLabel = 'Go to Sign In';
    } elseif (auth()->check() && \Illuminate\Support\Facades\Route::has('home')) {
        $redirectUrl   = route('home');
        $redirectLabel = 'Return to Home';
    } elseif (\Illuminate\Support\Facades\Route::has('login')) {
        $redirectUrl   = route('login');
        $redirectLabel = 'Go to Sign In';
    } else {
        $redirectUrl   = url('/');
        $redirectLabel = 'Return to Home';
    }

    $homeUrl    = \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/');
    $contactUrl = \Illuminate\Support\Facades\Route::has('contact') ? route('contact') : url('/');
    $countdown  = 10; // seconds before auto-redirect
@endphp
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

    .countdown-ring {
        transform: rotate(-90deg);
    }

    .countdown-ring circle.progress {
        transition: stroke-dashoffset 1s linear;
    }
</style>

<div class="gradient-bg min-h-screen flex flex-col items-center justify-center px-4 py-16">
    <div class="max-w-3xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 md:p-12">
            <div class="text-center mb-8">
                <!-- 500 Icon -->
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl font-bold text-gray-400">500</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Server Error</h1>
                <p class="text-lg text-gray-600 mb-6">
                    We're experiencing some technical difficulties. Our team has been notified and is working to fix the issue.
                </p>

                <!-- Auto-redirect countdown -->
                <div id="auto-redirect" class="mb-8 inline-flex items-center gap-3 px-5 py-3 rounded-full bg-blue-50 border border-blue-200">
                    <svg class="countdown-ring" width="36" height="36" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15" fill="none" stroke="#dbeafe" stroke-width="3"></circle>
                        <circle class="progress" cx="18" cy="18" r="15" fill="none" stroke="#2b6399" stroke-width="3"
                                stroke-dasharray="94.25" stroke-dashoffset="0" stroke-linecap="round"></circle>
                    </svg>
                    <div class="text-left">
                        <p class="text-sm text-gray-700">
                            Redirecting to <span class="font-semibold">{{ $redirectLabel }}</span> in
                            <span id="countdown-seconds" class="font-bold text-brand-blue">{{ $countdown }}</span>s
                        </p>
                        <button type="button" id="cancel-redirect"
                                class="text-xs text-gray-500 hover:text-gray-700 underline mt-0.5">
                            Cancel auto-redirect
                        </button>
                    </div>
                </div>

                <!-- Suggestions -->
                <div class="bg-gray-50 p-6 rounded-xl mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">You can try:</h2>
                    <ul class="space-y-3 text-left max-w-md mx-auto">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-green mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Refreshing the page</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-green mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Clearing your browser cache</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-green mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Signing in again</span>
                        </li>
                    </ul>
                </div>

                <!-- Main Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ $redirectUrl }}" class="px-8 py-3 bg-brand-blue text-white rounded-lg hover:bg-opacity-90 transition-all font-medium">
                        {{ $redirectLabel }}
                    </a>

                    <a href="{{ $homeUrl }}" class="px-8 py-3 border-2 border-brand-gold text-brand-gold rounded-lg hover:bg-brand-gold hover:text-white transition-all font-medium">
                        Return to Home
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <div class="text-center text-gray-600 text-sm">
                <p>If the problem persists, please <a href="{{ $contactUrl }}" class="text-brand-blue hover:underline">contact our support team</a>.</p>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var redirectUrl = @json($redirectUrl);
    var total = {{ (int) $countdown }};
    var remaining = total;

    var secondsEl = document.getElementById('countdown-seconds');
    var cancelBtn = document.getElementById('cancel-redirect');
    var container = document.getElementById('auto-redirect');
    var progress  = document.querySelector('#auto-redirect .progress');

    if (!secondsEl || !cancelBtn || !progress) {
        return;
    }

    var circumference = 2 * Math.PI * 15; // r = 15
    progress.setAttribute('stroke-dasharray', circumference.toFixed(3));
    progress.setAttribute('stroke-dashoffset', '0');

    var intervalId = setInterval(function () {
        remaining -= 1;
        if (remaining <= 0) {
            clearInterval(intervalId);
            window.location.href = redirectUrl;
            return;
        }
        secondsEl.textContent = remaining;
        var offset = circumference * ((total - remaining) / total);
        progress.setAttribute('stroke-dashoffset', offset.toFixed(3));
    }, 1000);

    cancelBtn.addEventListener('click', function () {
        clearInterval(intervalId);
        if (container) {
            container.remove();
        }
    });
})();
</script>
@endsection
@extends('layouts.app')

@section('title', 'Server Error - Ballie')
@section('description', 'We encountered an unexpected error. Our team has been notified.')

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
</style>

<div class="gradient-bg min-h-screen flex flex-col items-center justify-center px-4 py-16">
    <div class="max-w-3xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 md:p-12">
            <div class="text-center mb-8">
                <!-- 500 Icon -->
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl font-bold text-gray-400">500</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Server Error</h1>
                <p class="text-lg text-gray-600 mb-8">
                    We're experiencing some technical difficulties. Our team has been notified and is working to fix the issue.
                </p>

                <!-- Suggestions -->
                <div class="bg-gray-50 p-6 rounded-xl mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">You can try:</h2>
                    <ul class="space-y-3 text-left max-w-md mx-auto">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-green mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Refreshing the page</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-green mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Clearing your browser cache</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-green mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700">Coming back later</span>
                        </li>
                    </ul>
                </div>

                <!-- Main Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('home') }}" class="px-8 py-3 bg-brand-blue text-white rounded-lg hover:bg-opacity-90 transition-all font-medium">
                        Return to Home
                    </a>

                    <a href="{{ route('contact') }}" class="px-8 py-3 border-2 border-brand-gold text-brand-gold rounded-lg hover:bg-brand-gold hover:text-white transition-all font-medium">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <div class="text-center text-gray-600 text-sm">
                <p>If the problem persists, please <a href="{{ route('contact') }}" class="text-brand-blue hover:underline">contact our support team</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
