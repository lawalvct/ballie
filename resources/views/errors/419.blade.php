@extends('layouts.app')

@section('title', 'Page Expired - Ballie')
@section('description', 'Your session has expired. Please refresh and try again.')

@section('content')
<script>
    // Auto-redirect after 2 seconds
    setTimeout(function() {
        @auth
            @if(isset($tenant) && $tenant)
                window.location.href = "{{ route('tenant.dashboard', $tenant->slug) }}";
            @else
                window.location.href = "{{ route('home') }}";
            @endif
        @else
            window.location.href = "{{ route('home') }}";
        @endauth
    }, 2000);
</script>
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
</script>

<div class="gradient-bg min-h-screen flex flex-col items-center justify-center px-4 py-16">
    <div class="max-w-3xl w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8 md:p-12">
            <div class="text-center mb-8">
                <!-- 419 Icon -->
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Page Expired</h1>
                <p class="text-lg text-gray-600 mb-8">
                    Your session has expired due to inactivity or an invalid CSRF token. This is often caused by:
                </p>

                <!-- Reasons -->
                <div class="bg-gray-50 p-6 rounded-xl mb-8">
                    <ul class="space-y-3 text-left max-w-md mx-auto">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-blue mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">Your browser session has timed out</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-blue mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">You've been inactive for too long</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-blue mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">The form was open for too long before submitting</span>
                        </li>
                    </ul>
                </div>

                <!-- Auto-redirect notice -->
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <div class="flex items-center justify-center space-x-2">
                        <svg class="animate-spin h-5 w-5 text-brand-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-brand-blue font-medium">Redirecting you in 2 seconds...</p>
                    </div>
                </div>

                <!-- Main Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        @if(isset($tenant) && $tenant)
                            <a href="{{ route('tenant.dashboard', $tenant->slug) }}" class="px-8 py-3 bg-brand-blue text-white rounded-lg hover:bg-opacity-90 transition-all font-medium">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('home') }}" class="px-8 py-3 bg-brand-blue text-white rounded-lg hover:bg-opacity-90 transition-all font-medium">
                                Return to Home
                            </a>
                        @endif
                    @else
                        <a href="{{ route('home') }}" class="px-8 py-3 bg-brand-blue text-white rounded-lg hover:bg-opacity-90 transition-all font-medium">
                            Return to Home
                        </a>
                    @endauth

                    <button onclick="window.location.reload()" class="px-8 py-3 border-2 border-brand-gold text-brand-gold rounded-lg hover:bg-brand-gold hover:text-white transition-all font-medium">
                        Refresh Page
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <div class="text-center text-gray-600 text-sm">
                <p>If you continue to see this error, please <a href="{{ route('contact') }}" class="text-brand-blue hover:underline">contact our support team</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
