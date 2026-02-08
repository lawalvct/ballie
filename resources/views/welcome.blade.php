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

    /* Extra mobile responsive improvements */
    @media (max-width: 640px) {
        .section-spacing {
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
        }
        .hero-section {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
        .carousel-slide h2 {
            font-size: 1.75rem !important;
            line-height: 1.2;
        }
        .carousel-slide p {
            font-size: 1rem !important;
        }
    }

    .text-brand-purple { color: var(--color-purple); }
    .text-brand-green { color: var(--color-green); }
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
                    <span class="text-brand-gold">13+ Modules</span> — One Platform
                </h2>
                <p class="text-xl md:text-2xl text-gray-200 mb-10 max-w-3xl mx-auto">
                    Stop paying for 10 different tools. Everything your business needs, in one affordable subscription.
                </p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg backdrop-blur-sm">
                        <h3 class="text-sm font-semibold text-brand-gold mb-1">🤖 BallieAI</h3>
                        <p class="text-gray-300 text-xs">AI business assistant</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg backdrop-blur-sm">
                        <h3 class="text-sm font-semibold text-brand-gold mb-1">📊 Accounting</h3>
                        <p class="text-gray-300 text-xs">Double-entry & reports</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg backdrop-blur-sm">
                        <h3 class="text-sm font-semibold text-brand-gold mb-1">📦 Inventory</h3>
                        <p class="text-gray-300 text-xs">Stock & warehouse</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg backdrop-blur-sm">
                        <h3 class="text-sm font-semibold text-brand-gold mb-1">👥 CRM</h3>
                        <p class="text-gray-300 text-xs">Sales pipeline</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg backdrop-blur-sm">
                        <h3 class="text-sm font-semibold text-brand-gold mb-1">🛒 POS</h3>
                        <p class="text-gray-300 text-xs">Point of sale</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg backdrop-blur-sm">
                        <h3 class="text-sm font-semibold text-brand-gold mb-1">🌐 E-Commerce</h3>
                        <p class="text-gray-300 text-xs">Online store</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg backdrop-blur-sm">
                        <h3 class="text-sm font-semibold text-brand-gold mb-1">💰 Payroll</h3>
                        <p class="text-gray-300 text-xs">Salary & HR</p>
                    </div>
                    <div class="bg-white bg-opacity-10 p-4 rounded-lg backdrop-blur-sm">
                        <h3 class="text-sm font-semibold text-brand-gold mb-1">🏛️ Tax & More</h3>
                        <p class="text-gray-300 text-xs">VAT, PAYE, audit</p>
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
                    <div class="space-y-2.5 text-left">
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">All 13+ modules included</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">BallieAI assistant</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Double-entry accounting & tax</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">POS, inventory & e-commerce</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Payroll, CRM & HR</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Reports, audit & compliance</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">Mobile app + 24/7 support</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-brand-green mr-3">✓</span>
                            <span class="text-gray-700">No hidden fees — ever</span>
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

<!-- Features Section - 13+ Modules -->
<section class="section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-blue-100 text-brand-blue rounded-full px-4 py-1.5 text-sm font-medium mb-4">
                ⚡ 13+ Modules — One Platform
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Everything You Need to Run Your Business
            </h2>
            <p class="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto">
                Stop paying for 10 different tools. Accounting, inventory, CRM, POS, payroll, e-commerce, AI assistant, and more — all in one affordable platform.
            </p>
        </div>

        <!-- Feature Module Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 md:gap-6">
            <!-- BallieAI -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-purple-200 transition-all group">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">🤖 BallieAI Assistant</h3>
                <p class="text-gray-600 text-sm mb-3">Chat with your business data. Create invoices by prompt, interpret reports in plain language, and get smart suggestions.</p>
                <a href="{{ route('features') }}#ballie-ai" class="text-purple-600 hover:text-purple-800 text-sm font-medium">Explore AI features →</a>
            </div>

            <!-- Accounting -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-yellow-200 transition-all group">
                <div class="w-12 h-12 bg-brand-gold rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">📊 Double-Entry Accounting</h3>
                <p class="text-gray-600 text-sm mb-3">Chart of accounts, vouchers, journal entries, bank reconciliation, financial statements, and multi-currency support.</p>
                <a href="{{ route('features') }}#accounting" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore accounting →</a>
            </div>

            <!-- Inventory -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-teal-200 transition-all group">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">📦 Inventory Management</h3>
                <p class="text-gray-600 text-sm mb-3">Real-time stock tracking, barcode scanning, COGS calculation, supplier management, and multi-warehouse support.</p>
                <a href="{{ route('features') }}#inventory" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore inventory →</a>
            </div>

            <!-- CRM -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-pink-200 transition-all group">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform" style="background-color: #e05577;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">👥 CRM & Sales Pipeline</h3>
                <p class="text-gray-600 text-sm mb-3">Customer profiles, purchase history, lead tracking, sales pipeline, bulk import, and smart follow-up reminders.</p>
                <a href="{{ route('features') }}#crm" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore CRM →</a>
            </div>

            <!-- POS -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-cyan-200 transition-all group">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform" style="background-color: #22a3b3;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">🛒 Point of Sale</h3>
                <p class="text-gray-600 text-sm mb-3">Fast checkout, cash register management, split payments, barcode scanning, and branded receipt printing.</p>
                <a href="{{ route('features') }}#pos" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore POS →</a>
            </div>

            <!-- E-commerce -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-orange-200 transition-all group">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform" style="background-color: #e8913a;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">🌐 E-Commerce Store</h3>
                <p class="text-gray-600 text-sm mb-3">Built-in online store with auto-synced inventory, order management, and Paystack/Flutterwave payment integration.</p>
                <a href="{{ route('features') }}#ecommerce" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore e-commerce →</a>
            </div>

            <!-- Payroll -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-emerald-200 transition-all group">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">💰 Payroll & HR</h3>
                <p class="text-gray-600 text-sm mb-3">Automated PAYE, pension, NHF deductions, payslips, bulk payments, and an employee self-service portal.</p>
                <a href="{{ route('features') }}#payroll" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore payroll →</a>
            </div>

            <!-- Tax & Statutory -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-amber-200 transition-all group">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform" style="background-color: #d4953d;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">🏛️ Tax & Compliance</h3>
                <p class="text-gray-600 text-sm mb-3">Automated VAT, WHT, CIT calculations with FIRS-ready reports, filing reminders, and compliance dashboard.</p>
                <a href="{{ route('features') }}#statutory" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore tax features →</a>
            </div>

            <!-- Reports -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-violet-200 transition-all group">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform" style="background-color: #614c80;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">📈 Reports & Analytics</h3>
                <p class="text-gray-600 text-sm mb-3">Real-time dashboards, P&L, balance sheet, cash flow, aged reports, and PDF/Excel export — explained by AI.</p>
                <a href="{{ route('features') }}#reports" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore reports →</a>
            </div>

            <!-- Audit Log -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-indigo-200 transition-all group">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform" style="background-color: #5b5ea6;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">🔍 Audit Trail</h3>
                <p class="text-gray-600 text-sm mb-3">Tamper-proof record of every action — who did what, when. Complete accountability for your business.</p>
                <a href="{{ route('features') }}#more" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore audit →</a>
            </div>

            <!-- Admin & Security -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-blue-200 transition-all group">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">🔐 Admin & Security</h3>
                <p class="text-gray-600 text-sm mb-3">Role-based access, custom permissions, team management, SSL encryption, and automated backups.</p>
                <a href="{{ route('features') }}#more" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore admin →</a>
            </div>

            <!-- Smart Search & More -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-200 transition-all group">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform" style="background-color: var(--color-deep-purple);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">⚡ Smart Search & More</h3>
                <p class="text-gray-600 text-sm mb-3">Global search, multi-business support, smart notifications, mobile API, and seamless integrations.</p>
                <a href="{{ route('features') }}#more" class="text-brand-blue hover:text-brand-purple text-sm font-medium">Explore all features →</a>
            </div>
        </div>

        <!-- See All Features CTA -->
        <div class="text-center mt-10">
            <a href="{{ route('features') }}" class="inline-flex items-center bg-brand-blue text-white px-8 py-4 rounded-lg hover:opacity-90 transition-opacity font-semibold text-lg shadow-lg">
                See All 13+ Features in Detail
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
            <p class="text-gray-500 text-sm mt-3">All features included in every plan — no lock-outs</p>
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

<!-- Mobile App Download Section -->
<section class="section-spacing bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <!-- Left Content -->
            <div>
                <div class="inline-flex items-center bg-green-100 text-green-700 rounded-full px-4 py-1.5 text-sm font-medium mb-4">
                    📱 Available on Mobile
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Your Business in Your <span class="text-brand-green">Pocket</span>
                </h2>
                <p class="text-lg text-gray-600 mb-6">
                    Run your entire business from your phone. Create invoices, check stock, approve payroll, chat with BallieAI, and monitor sales — all from the palm of your hand.
                </p>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Lightning Fast</h4>
                            <p class="text-sm text-gray-600">Optimized for speed even on slow networks</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Push Notifications</h4>
                            <p class="text-sm text-gray-600">Stay updated on sales, approvals & low stock alerts</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Secure Access</h4>
                            <p class="text-sm text-gray-600">Biometric login & encrypted data on all devices</p>
                        </div>
                    </div>
                </div>

                <!-- App Store Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#" class="inline-flex items-center bg-black text-white px-6 py-3.5 rounded-xl hover:bg-gray-800 transition-colors group">
                        <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                        </svg>
                        <div class="text-left">
                            <div class="text-xs opacity-80">Download on the</div>
                            <div class="text-lg font-semibold -mt-1">App Store</div>
                        </div>
                    </a>

                    <a href="#" class="inline-flex items-center bg-black text-white px-6 py-3.5 rounded-xl hover:bg-gray-800 transition-colors group">
                        <svg class="w-8 h-8 mr-3" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3.609 1.814L13.792 12 3.61 22.186a.996.996 0 01-.61-.92V2.734a1 1 0 01.609-.92zm10.89 10.893l2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.198l2.807 1.626a1 1 0 010 1.73l-2.808 1.626L15.206 12l2.492-2.491zM5.864 2.658L16.8 8.99l-2.302 2.302-8.634-8.634z"/>
                        </svg>
                        <div class="text-left">
                            <div class="text-xs opacity-80">Get it on</div>
                            <div class="text-lg font-semibold -mt-1">Google Play</div>
                        </div>
                    </a>
                </div>
                <p class="text-xs text-gray-400 mt-3">Coming soon — Join the waitlist to get early access</p>
            </div>

            <!-- Right Side - Phone Mockup -->
            <div class="relative flex justify-center lg:justify-end">
                <div class="relative w-64 sm:w-72">
                    <!-- Phone Frame -->
                    <div class="bg-gray-900 rounded-[2.5rem] p-3 shadow-2xl">
                        <div class="bg-white rounded-[2rem] overflow-hidden">
                            <!-- Status Bar -->
                            <div class="bg-brand-blue px-4 py-2 flex items-center justify-between">
                                <span class="text-white text-xs font-medium">9:41</span>
                                <div class="flex items-center space-x-1">
                                    <div class="w-3.5 h-2.5 border border-white rounded-sm"><div class="w-2 h-1.5 bg-white rounded-sm m-px"></div></div>
                                </div>
                            </div>
                            <!-- App Content -->
                            <div class="bg-gray-50 p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <div class="text-xs text-gray-500">Welcome back</div>
                                        <div class="text-sm font-bold text-gray-900">Adebayo's Store</div>
                                    </div>
                                    <div class="w-8 h-8 bg-brand-gold rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-900">AD</span>
                                    </div>
                                </div>

                                <!-- Dashboard Cards -->
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <div class="text-xs text-gray-500">Today's Sales</div>
                                        <div class="text-sm font-bold text-brand-green">₦284,500</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <div class="text-xs text-gray-500">Orders</div>
                                        <div class="text-sm font-bold text-brand-blue">23 new</div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <div class="text-xs text-gray-500">Low Stock</div>
                                        <div class="text-sm font-bold text-orange-500">5 items</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <div class="text-xs text-gray-500">Receivables</div>
                                        <div class="text-sm font-bold text-brand-purple">₦1.2M</div>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="bg-white rounded-lg p-3 shadow-sm">
                                    <div class="text-xs font-semibold text-gray-700 mb-2">Quick Actions</div>
                                    <div class="grid grid-cols-4 gap-1">
                                        <div class="text-center">
                                            <div class="w-8 h-8 bg-brand-gold rounded-lg flex items-center justify-center mx-auto mb-1">
                                                <span class="text-xs">📄</span>
                                            </div>
                                            <span class="text-[9px] text-gray-600">Invoice</span>
                                        </div>
                                        <div class="text-center">
                                            <div class="w-8 h-8 bg-brand-teal rounded-lg flex items-center justify-center mx-auto mb-1">
                                                <span class="text-xs">📦</span>
                                            </div>
                                            <span class="text-[9px] text-gray-600">Stock</span>
                                        </div>
                                        <div class="text-center">
                                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mx-auto mb-1">
                                                <span class="text-xs">🤖</span>
                                            </div>
                                            <span class="text-[9px] text-gray-600">BallieAI</span>
                                        </div>
                                        <div class="text-center">
                                            <div class="w-8 h-8 bg-brand-green rounded-lg flex items-center justify-center mx-auto mb-1">
                                                <span class="text-xs">🛒</span>
                                            </div>
                                            <span class="text-[9px] text-gray-600">POS</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Bottom Nav -->
                            <div class="bg-white border-t border-gray-200 px-4 py-2 flex justify-around">
                                <div class="text-center">
                                    <div class="w-5 h-5 mx-auto mb-0.5 flex items-center justify-center"><span class="text-xs">🏠</span></div>
                                    <span class="text-[8px] text-brand-blue font-semibold">Home</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-5 h-5 mx-auto mb-0.5 flex items-center justify-center"><span class="text-xs">📊</span></div>
                                    <span class="text-[8px] text-gray-400">Reports</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-5 h-5 mx-auto mb-0.5 flex items-center justify-center"><span class="text-xs">🛒</span></div>
                                    <span class="text-[8px] text-gray-400">POS</span>
                                </div>
                                <div class="text-center">
                                    <div class="w-5 h-5 mx-auto mb-0.5 flex items-center justify-center"><span class="text-xs">⚙️</span></div>
                                    <span class="text-[8px] text-gray-400">More</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Phone Notch -->
                    <div class="absolute top-3 left-1/2 -translate-x-1/2 w-24 h-5 bg-gray-900 rounded-b-xl"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section-spacing bg-gray-50">
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

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
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
