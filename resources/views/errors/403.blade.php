@extends('layouts.app')

@section('title', 'Access Denied - Ballie')
@section('description', 'You do not have permission to access this page.')

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
                <!-- 403 Icon -->
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Access Denied</h1>
                <p class="text-lg text-gray-600 mb-8">
                    You don't have permission to access this page. This might be because:
                </p>

                <!-- Reasons -->
                <div class="bg-gray-50 p-6 rounded-xl mb-8">
                    <ul class="space-y-3 text-left max-w-md mx-auto">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-blue mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">You need to log in to access this page</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-blue mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">Your account doesn't have the necessary permissions</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-brand-blue mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-700">You're trying to access a restricted area</span>
                        </li>
                    </ul>
                </div>

                <!-- Main Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('home') }}" class="px-8 py-3 bg-brand-blue text-white rounded-lg hover:bg-opacity-90 transition-all font-medium">
                        Return to Home
                    </a>

                    @guest
                        <a href="{{ route('login') }}" class="px-8 py-3 border-2 border-brand-gold text-brand-gold rounded-lg hover:bg-brand-gold hover:text-white transition-all font-medium">
                            Sign In
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-8 py-3 border-2 border-brand-gold text-brand-gold rounded-lg hover:bg-brand-gold hover:text-white transition-all font-medium">
                            Go to Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <div class="text-center text-gray-600 text-sm">
                <p>If you believe you should have access to this page, please <a href="{{ route('contact') }}" class="text-brand-blue hover:underline">contact our support team</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
