@extends('layouts.app')

@section('title', 'Ballie -  Business Management Software')
@section('description', 'Comprehensive business management software built specifically for  businesses. Manage accounting, inventory, sales, and more in one platform.')

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

    .carousel-container {
        position: relative;
        overflow: hidden;
    }

    .carousel-slide {
        display: none;
        animation: fadeIn 0.5s ease-in-out;
    }

    .carousel-slide.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .carousel-indicators {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .carousel-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .carousel-indicator.active {
        background-color: var(--color-gold);
    }

    .bg-brand-blue { background-color: var(--color-blue); }
    .bg-brand-gold { background-color: var(--color-gold); }
    .bg-brand-purple { background-color: var(--color-dark-purple); }
    .bg-brand-teal { background-color: var(--color-teal); }
    .bg-brand-green { background-color: var(--color-green); }
    .text-brand-gold { color: var(--color-gold); }
    .text-brand-blue { color: var(--color-blue); }
    .border-brand-gold { border-color: var(--color-gold); }
    .hover\:bg-brand-gold:hover { background-color: var(--color-gold); }
    .hover\:text-brand-blue:hover { color: var(--color-blue); }

    /* Ensure proper spacing */
    .section-spacing {
        margin: 0;
        padding-top: 4rem;
        padding-bottom: 4rem;
    }

    .hero-section {
        margin: 0;
        padding-top: 5rem;
        padding-bottom: 5rem;
    }
</style>

<!-- Hero Section with Carousel -->
<section class="gradient-bg text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>

    <!-- Floating background elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-brand-gold opacity-20 rounded-full floating-animation"></div>
    <div class="absolute top-32 right-20 w-16 h-16 bg-brand-teal opacity-30 rounded-full floating-animation" style="animation-delay: -2s;"></div>
    <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-brand-lavender opacity-25 rounded-full floating-animation" style="animation-delay: -4s;"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Carousel Container -->
        <div class="carousel-container">
            <!-- Slide 1 - Main Hero -->
            <div class="carousel-slide active text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Manage Your Business
                    <span class="text-brand-gold">Like a Pro</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-200 mb-8 max-w-3xl mx-auto">
                    Complete business management software designed specifically for  entrepreneurs.
                    <strong class="text-brand-gold">Availability & Affordability</strong> at its finest.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-colors">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-colors">
                            Start Free Trial
                        </a>
                        <a href="{{ route('login') }}" class="border-2 border-brand-gold text-brand-gold px-8 py-4 rounded-lg hover:bg-brand-gold hover:text-gray-900 font-semibold text-lg transition-colors">
                            Sign In
                        </a>
                    @endauth
                </div>
                <div class="mt-6 text-gray-300 text-sm">
                    30-day free trial • No credit card required • Setup in minutes
                </div>
            </div>

            <!-- Slide 2 - Availability Focus -->
            <div class="carousel-slide text-center">
                <div class="flex items-center justify-center mb-8">
                    <div class="w-20 h-20 bg-brand-teal rounded-full flex items-center justify-center mr-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h2 class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">24/7 Availability</h2>
                        <p class="text-xl text-gray-200">Access your business data anytime, anywhere</p>
                    </div>
                </div>
                <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Cloud-based solution ensures your business management tools are always available.
                    Work from office, home, or on-the-go with seamless synchronization across all devices.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Cloud Storage</h3>
                        <p class="text-gray-300">Secure cloud infrastructure</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Mobile Ready</h3>
                        <p class="text-gray-300">Works on all devices</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Real-time Sync</h3>
                        <p class="text-gray-300">Instant data updates</p>
                    </div>
                </div>
            </div>

            <!-- Slide 3 - Affordability Focus -->
            <div class="carousel-slide text-center">
                <div class="flex items-center justify-center mb-8">
                    <div class="w-20 h-20 bg-brand-green rounded-full flex items-center justify-center mr-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h2 class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">Maximum Affordability</h2>
                        <p class="text-xl text-gray-200">Enterprise features at small business prices</p>
                    </div>
                </div>
                <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Get powerful business management tools without breaking the bank.
                    Designed specifically for Nigerian businesses with pricing that makes sense.
                </p>
                <div class="bg-white bg-opacity-10 p-8 rounded-xl backdrop-blur-sm max-w-2xl mx-auto">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-brand-gold mb-2">₦5,000</div>
                        <div class="text-lg text-gray-300 mb-4">per month</div>
                        <ul class="text-left space-y-2 text-gray-300">
                            <li class="flex items-center"><span class="text-brand-gold mr-2">✓</span> Unlimited invoices</li>
                            <li class="flex items-center"><span class="text-brand-gold mr-2">✓</span> Inventory management</li>
                            <li class="flex items-center"><span class="text-brand-gold mr-2">✓</span> Customer management</li>
                            <li class="flex items-center"><span class="text-brand-gold mr-2">✓</span> Financial reports</li>
                            <li class="flex items-center"><span class="text-brand-gold mr-2">✓</span> 24/7 support</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Slide 4 - AI Assistant Focus -->
            <div class="carousel-slide text-center">
                <div class="flex items-center justify-center mb-8">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full flex items-center justify-center mr-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <h2 class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">AI-Powered Assistant</h2>
                        <p class="text-xl text-gray-200">Your intelligent business companion</p>
                    </div>
                </div>
                <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
                    Experience the future of business management with AI that learns your patterns, suggests improvements, and automates routine tasks.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Smart Suggestions</h3>
                        <p class="text-gray-300">AI recommends products and actions</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Data Validation</h3>
                        <p class="text-gray-300">Automatic error detection</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Q&A Assistant</h3>
                        <p class="text-gray-300">Get answers instantly</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Smart Templates</h3>
                        <p class="text-gray-300">Personalized workflows</p>
                    </div>
                </div>
            </div>

            <!-- Slide 5 - Features Overview -->
            <div class="carousel-slide text-center">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    <span class="text-brand-gold">All-in-One</span> Business Solution
                </h2>
                <p class="text-xl md:text-2xl text-gray-200 mb-12 max-w-3xl mx-auto">
                    Everything you need to manage your business efficiently and profitably.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <div class="w-12 h-12 bg-brand-gold rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">AI-Smart Invoicing</h3>
                        <p class="text-gray-300 text-sm">AI-powered invoice creation</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Inventory Control</h3>
                        <p class="text-gray-300 text-sm">Track stock levels & suppliers</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Customer Management</h3>
                        <p class="text-gray-300 text-sm">Build stronger relationships</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-6 rounded-lg backdrop-blur-sm">
                        <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-4 mx-auto">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-brand-gold mb-2">Financial Reports</h3>
                        <p class="text-gray-300 text-sm">Detailed business insights</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            <div class="carousel-indicator active" onclick="currentSlide(1)"></div>
            <div class="carousel-indicator" onclick="currentSlide(2)"></div>
            <div class="carousel-indicator" onclick="currentSlide(3)"></div>
            <div class="carousel-indicator" onclick="currentSlide(4)"></div>
            <div class="carousel-indicator" onclick="currentSlide(5)"></div>
        </div>

        <!-- Carousel Navigation Arrows -->
        <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full p-3 transition-all duration-300" onclick="plusSlides(-1)">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full p-3 transition-all duration-300" onclick="plusSlides(1)">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</section>

<!-- Brand Promise Section -->
<section class="section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Our Promise: <span class="text-brand-blue">Availability & Affordability</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                We believe every Nigerian business deserves access to world-class management tools without the premium price tag.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Always Available</h3>
                        <p class="text-gray-600">99.9% uptime guarantee with 24/7 cloud access from any device, anywhere in Nigeria.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Truly Affordable</h3>
                        <p class="text-gray-600">Transparent pricing starting from ₦5,000/month with no hidden fees or setup costs.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75A9.75 9.75 0 0012 2.25z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Nigerian-First Design</h3>
                        <p class="text-gray-600">Built specifically for Nigerian businesses with local currency, tax compliance, and business practices in mind.</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-8 rounded-2xl">
                <div class="text-center">
                    <div class="text-4xl font-bold text-brand-blue mb-2">₦5,000</div>
                    <div class="text-lg text-gray-600 mb-6">per month</div>
                    <div class="space-y-3 text-left">
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Unlimited invoices & quotes</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Complete inventory management</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Customer relationship management</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Financial reports & analytics</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">AI-powered business assistant</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">24/7 customer support</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Mobile app access</span>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('pricing') }}" class="bg-brand-blue text-white px-6 py-3 rounded-lg hover:opacity-90 transition-opacity font-medium">
                            View All Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Everything You Need to Run Your Business
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                From invoicing to inventory management, we've got all the tools Nigerian businesses need to succeed.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6" style="background-color: var(--color-gold);">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">AI-Powered Smart Invoicing</h3>
                <p class="text-gray-600 mb-4">Create professional invoices with AI assistance that suggests products, validates data, and automates routine tasks.</p>
                <ul class="text-sm text-gray-500 space-y-1 mb-4">
                    <li>• AI-powered product suggestions</li>
                    <li>• Smart data validation</li>
                    <li>• Automated invoice generation</li>
                    <li>• Q&A assistant for help</li>
                </ul>
                <a href="{{ route('features') }}" class="text-brand-blue hover:text-brand-purple font-medium">Learn more →</a>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6" style="background-color: var(--color-teal);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Inventory Control</h3>
                <p class="text-gray-600 mb-4">Track stock levels, manage suppliers, and get alerts when items are running low.</p>
                <ul class="text-sm text-gray-500 space-y-1 mb-4">
                    <li>• Real-time stock tracking</li>
                    <li>• Low stock alerts</li>
                    <li>• Supplier management</li>
                    <li>• Product categorization</li>
                </ul>
                <a href="{{ route('features') }}" class="text-brand-blue hover:text-brand-purple font-medium">Learn more →</a>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6" style="background-color: var(--color-purple);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Customer Management</h3>
                <p class="text-gray-600 mb-4">Keep detailed customer records, track purchase history, and build stronger relationships.</p>
                <ul class="text-sm text-gray-500 space-y-1 mb-4">
                    <li>• Customer profiles</li>
                    <li>• Purchase history</li>
                    <li>• Communication logs</li>
                    <li>• Payment tracking</li>
                </ul>
                <a href="{{ route('features') }}" class="text-brand-blue hover:text-brand-purple font-medium">Learn more →</a>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6" style="background-color: var(--color-green);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Financial Reports</h3>
                <p class="text-gray-600 mb-4">Get detailed insights into your business performance with comprehensive financial reporting.</p>
                <ul class="text-sm text-gray-500 space-y-1 mb-4">
                    <li>• Profit & loss statements</li>
                    <li>• Cash flow reports</li>
                    <li>• Sales analytics</li>
                    <li>• Tax compliance reports</li>
                </ul>
                <a href="{{ route('features') }}" class="text-brand-blue hover:text-brand-purple font-medium">Learn more →</a>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6" style="background-color: var(--color-light-blue);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Mobile Access</h3>
                <p class="text-gray-600 mb-4">Manage your business on-the-go with our mobile-responsive platform and dedicated apps.</p>
                <ul class="text-sm text-gray-500 space-y-1 mb-4">
                    <li>• Mobile-responsive design</li>
                    <li>• iOS & Android apps</li>
                    <li>• Offline capabilities</li>
                    <li>• Real-time notifications</li>
                </ul>
                <a href="{{ route('features') }}" class="text-brand-blue hover:text-brand-purple font-medium">Learn more →</a>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-6" style="background-color: var(--color-deep-purple);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Secure & Compliant</h3>
                <p class="text-gray-600 mb-4">Bank-level security with Nigerian tax compliance and data protection standards.</p>
                <ul class="text-sm text-gray-500 space-y-1 mb-4">
                    <li>• SSL encryption</li>
                    <li>• Regular backups</li>
                    <li>• Nigerian tax compliance</li>
                    <li>• GDPR compliant</li>
                </ul>
                <a href="{{ route('features') }}" class="text-brand-blue hover:text-brand-purple font-medium">Learn more →</a>
            </div>
        </div>
    </div>
</section>

<!-- AI Assistant Section -->
<section class="section-spacing bg-gradient-to-br from-indigo-50 to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-full mb-6">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Meet Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600">AI Business Assistant</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Powered by advanced AI technology, Ballie doesn't just manage your business - it intelligently assists you every step of the way.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Smart Product Suggestions</h3>
                        <p class="text-gray-600">AI analyzes your inventory and customer patterns to suggest the right products for each invoice, saving you time and reducing errors.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Intelligent Data Validation</h3>
                        <p class="text-gray-600">Automatically validates invoice data, checks stock levels, and alerts you to potential issues before they become problems.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">24/7 Q&A Assistant</h3>
                        <p class="text-gray-600">Get instant answers to your business questions. Our AI assistant understands your business context and provides relevant guidance.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Smart Templates & Automation</h3>
                        <p class="text-gray-600">AI learns from your business patterns to create personalized templates and automate repetitive tasks, making your workflow more efficient.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6 rounded-lg text-white mb-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold">AI-Powered Features</h4>
                    </div>
                    <p class="text-purple-100 text-sm">Your intelligent business companion that works 24/7</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-purple-50 rounded-lg">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                        <span class="text-gray-700 text-sm">Suggests products based on customer history</span>
                    </div>
                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-gray-700 text-sm">Validates invoice data in real-time</span>
                    </div>
                    <div class="flex items-center p-3 bg-green-50 rounded-lg">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-gray-700 text-sm">Answers business questions instantly</span>
                    </div>
                    <div class="flex items-center p-3 bg-orange-50 rounded-lg">
                        <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                        <span class="text-gray-700 text-sm">Creates personalized workflow templates</span>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 italic">
                        "The AI assistant feels like having a business expert right beside me. It catches mistakes I might miss and suggests improvements I wouldn't have thought of."
                    </p>
                    <div class="flex items-center mt-3">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-2">
                            <span class="text-white text-xs font-semibold">AM</span>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-900">Adebayo Mustapha</div>
                            <div class="text-xs text-gray-500">Tech Entrepreneur</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Trusted by Nigerian Businesses
            </h2>
            <p class="text-xl text-gray-600">Join thousands of businesses already growing with Ballie</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-50 p-8 rounded-xl">
                <div class="flex items-center mb-4">
                    <div class="flex text-brand-gold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">"Finally, a business software that understands Nigerian businesses! The affordability is unmatched, and I love how I can access everything from my phone. Customer support is excellent too."</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-brand-teal rounded-full flex items-center justify-center mr-3">
                        <span class="text-white font-semibold">FK</span>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Fatima Kano</div>
                        <div class="text-sm text-gray-500">Restaurant Owner, Abuja</div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-8 rounded-xl">
                <div class="flex items-center mb-4">
                    <div class="flex text-brand-gold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">"Ballie has transformed how I manage my retail business. The inventory tracking is spot-on, and the financial reports help me make better decisions. Worth every naira!"</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-brand-green rounded-full flex items-center justify-center mr-3">
                        <span class="text-white font-semibold">OA</span>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Olumide Adebayo</div>
                        <div class="text-sm text-gray-500">Retail Store Owner, Lagos</div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-8 rounded-xl">
                <div class="flex items-center mb-4">
                    <div class="flex text-brand-gold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">"As a service provider, I needed something simple yet powerful. Ballie's customer management and invoicing features are exactly what I needed. The price is very reasonable too."</p>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-brand-purple rounded-full flex items-center justify-center mr-3">
                        <span class="text-white font-semibold">CU</span>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Chioma Ugwu</div>
                        <div class="text-sm text-gray-500">Digital Marketing Agency, Port Harcourt</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="section-spacing" style="background: linear-gradient(135deg, var(--color-teal) 0%, var(--color-blue) 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center text-white">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Trusted by Growing Businesses
            </h2>
            <p class="text-xl text-gray-200 mb-12 max-w-2xl mx-auto">
                Join thousands of Nigerian entrepreneurs who have transformed their business operations with Ballie.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">5,000+</div>
                    <div class="text-lg text-gray-200">Active Businesses</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">₦2.5B+</div>
                    <div class="text-lg text-gray-200">Invoices Processed</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">99.9%</div>
                    <div class="text-lg text-gray-200">Uptime Guarantee</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">24/7</div>
                    <div class="text-lg text-gray-200">Customer Support</div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('cta')

<script>
    let slideIndex = 1;
    let slideInterval;

    // Initialize carousel
    document.addEventListener('DOMContentLoaded', function() {
        showSlides(slideIndex);
        startAutoSlide();
    });

    // Next/previous controls
    function plusSlides(n) {
        showSlides(slideIndex += n);
        resetAutoSlide();
    }

    // Thumbnail image controls
    function currentSlide(n) {
        showSlides(slideIndex = n);
        resetAutoSlide();
    }

    function showSlides(n) {
        let slides = document.getElementsByClassName("carousel-slide");
        let dots = document.getElementsByClassName("carousel-indicator");

        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}

        for (let i = 0; i < slides.length; i++) {
            slides[i].classList.remove("active");
        }

        for (let i = 0; i < dots.length; i++) {
            dots[i].classList.remove("active");
        }

        if (slides[slideIndex-1]) {
            slides[slideIndex-1].classList.add("active");
        }
        if (dots[slideIndex-1]) {
            dots[slideIndex-1].classList.add("active");
        }
    }

    // Auto-slide functionality
    function startAutoSlide() {
        slideInterval = setInterval(function() {
            slideIndex++;
            if (slideIndex > 5) slideIndex = 1;
            showSlides(slideIndex);
        }, 5000); // Change slide every 5 seconds
    }

    function resetAutoSlide() {
        clearInterval(slideInterval);
        startAutoSlide();
    }

    // Pause auto-slide on hover
    document.querySelector('.carousel-container').addEventListener('mouseenter', function() {
        clearInterval(slideInterval);
    });

    document.querySelector('.carousel-container').addEventListener('mouseleave', function() {
        startAutoSlide();
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    document.querySelector('.carousel-container').addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });

    document.querySelector('.carousel-container').addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        if (touchEndX < touchStartX - 50) {
            // Swipe left - next slide
            plusSlides(1);
        }
        if (touchEndX > touchStartX + 50) {
            // Swipe right - previous slide
            plusSlides(-1);
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            plusSlides(-1);
        } else if (e.key === 'ArrowRight') {
            plusSlides(1);
        }
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation for CTA buttons
    document.querySelectorAll('a[href*="register"], a[href*="login"]').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!this.classList.contains('loading')) {
                this.classList.add('loading');
                const originalText = this.textContent;
                this.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-current inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading...';

                // Reset after 2 seconds if still on page
                setTimeout(() => {
                    if (this.classList.contains('loading')) {
                        this.classList.remove('loading');
                        this.textContent = originalText;
                    }
                }, 2000);
            }
        });
    });
</script>
@endsection
