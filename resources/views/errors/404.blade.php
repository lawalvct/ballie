@extends('layouts.app')

@section('title', 'Page Not Found - Ballie')
@section('description', 'The page you are looking for could not be found.')

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
                <!-- 404 Icon -->
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-5xl font-bold text-gray-400">404</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Page Not Found</h1>
                <p class="text-lg text-gray-600 mb-8">
                    The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
                </p>

                <!-- Suggestions -->
                <div class="bg-gray-50 p-6 rounded-xl mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Here are some helpful links:</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('home') }}" class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-brand-blue transition-colors">
                            <div class="w-10 h-10 rounded-full bg-brand-blue flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Home Page</div>
                                <div class="text-sm text-gray-500">Return to our main page</div>
                            </div>
                        </a>

                     

                        <a href="{{ route('features') }}" class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-brand-blue transition-colors">
                            <div class="w-10 h-10 rounded-full bg-brand-teal flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Features</div>
                                <div class="text-sm text-gray-500">Explore our features</div>
                            </div>
                        </a>

                        <a href="{{ route('pricing') }}" class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-brand-blue transition-colors">
                            <div class="w-10 h-10 rounded-full bg-brand-green flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Pricing</div>
                                <div class="text-sm text-gray-500">View our plans</div>
                            </div>
                        </a>

                        <a href="{{ route('contact') }}" class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-brand-blue transition-colors">
                            <div class="w-10 h-10 rounded-full bg-brand-purple flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Contact Us</div>
                                <div class="text-sm text-gray-500">Get in touch</div>
                            </div>
                        </a>
                    </div>
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
                <p>If you believe this is an error, please <a href="{{ route('contact') }}" class="text-brand-blue hover:underline">contact our support team</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
