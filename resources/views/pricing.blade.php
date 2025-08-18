@extends('layouts.app')

@section('title', 'Pricing Plans - Affordable Business Management Solutions | Ballie')
@section('description', 'Choose the perfect Ballie plan for your business. Transparent pricing with no hidden fees. Start with our free trial.')

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

    .bg-brand-blue { background-color: var(--color-blue); }
    .bg-brand-gold { background-color: var(--color-gold); }
    .bg-brand-purple { background-color: var(--color-purple); }
    .bg-brand-dark-purple { background-color: var(--color-dark-purple); }
    .bg-brand-teal { background-color: var(--color-teal); }
    .bg-brand-green { background-color: var(--color-green); }
    .bg-brand-light-blue { background-color: var(--color-light-blue); }
    .bg-brand-deep-purple { background-color: var(--color-deep-purple); }
    .bg-brand-lavender { background-color: var(--color-lavender); }
    .bg-brand-violet { background-color: var(--color-violet); }

    .text-brand-gold { color: var(--color-gold); }
    .text-brand-blue { color: var(--color-blue); }
    .text-brand-purple { color: var(--color-purple); }
    .text-brand-teal { color: var(--color-teal); }
    .text-brand-green { color: var(--color-green); }
    .text-brand-light-blue { color: var(--color-light-blue); }
    .text-brand-violet { color: var(--color-violet); }

    .border-brand-gold { border-color: var(--color-gold); }
    .border-brand-blue { border-color: var(--color-blue); }
    .border-brand-purple { border-color: var(--color-purple); }

    .hover\:bg-brand-gold:hover { background-color: var(--color-gold); }
    .hover\:bg-brand-blue:hover { background-color: var(--color-blue); }
    .hover\:text-brand-blue:hover { color: var(--color-blue); }
    .hover\:text-brand-purple:hover { color: var(--color-purple); }

    .gradient-bg {
        background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 50%, var(--color-deep-purple) 100%);
    }

    .gradient-bg-2 {
        background: linear-gradient(135deg, var(--color-dark-purple) 0%, var(--color-violet) 50%, var(--color-deep-purple) 100%);
    }

    .pricing-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .pricing-card.popular {
        transform: scale(1.05);
        z-index: 10;
    }

    .pricing-card.popular:hover {
        transform: scale(1.05) translateY(-8px);
    }

    .billing-toggle {
        background: var(--color-blue);
        transition: all 0.3s ease;
    }

    .billing-toggle.yearly {
        background: var(--color-gold);
    }

    .toggle-dot {
        transition: all 0.3s ease;
    }

    .savings-badge {
        background: linear-gradient(45deg, var(--color-gold), #f59e0b);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .feature-check {
        color: var(--color-green);
    }

    .feature-cross {
        color: #ef4444;
    }

    .price-highlight {
        background: linear-gradient(135deg, var(--color-gold), #f59e0b);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<!-- Hero Section -->
<section class="gradient-bg text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>

    <!-- Floating background elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-brand-gold opacity-20 rounded-full floating-animation"></div>
    <div class="absolute top-32 right-20 w-16 h-16 bg-brand-teal opacity-30 rounded-full floating-animation" style="animation-delay: -2s;"></div>
    <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-brand-lavender opacity-25 rounded-full floating-animation" style="animation-delay: -4s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6 slide-in-left">
            Simple, <span class="text-brand-gold">Transparent Pricing</span>
        </h1>
        <p class="text-xl text-gray-200 max-w-3xl mx-auto mb-8 slide-in-right">
            Choose the plan that fits your business size and needs. All plans include our core features with no hidden fees.
            <strong class="text-brand-gold">Maximum affordability, always available.</strong>
        </p>

        <!-- Billing Toggle -->
        <div class="flex items-center justify-center mb-12 slide-in-left">
            <span class="text-gray-200 mr-4 font-medium">Monthly</span>
            <button id="billing-toggle" class="billing-toggle relative inline-flex h-8 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2">
                <span id="toggle-dot" class="toggle-dot inline-block h-6 w-6 transform rounded-full bg-white transition-transform translate-x-1"></span>
            </button>
            <span class="text-gray-200 ml-4 font-medium">Yearly</span>
            <span class="savings-badge ml-3 text-sm text-gray-900 px-3 py-1 rounded-full font-bold pulse-animation">Save 15%</span>
        </div>
    </div>
</section>


<!-- Pricing Cards -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">

            <!-- Starter Plan -->
            <div class="pricing-card bg-white border-2 border-gray-200 rounded-2xl p-8 relative">
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-teal rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                    <p class="text-gray-600 mb-6">Perfect for small businesses and startups</p>

                    <div class="mb-8">
                        <div class="pricing-display">
                            <span class="monthly-price">
                                <span class="text-4xl font-bold price-highlight">₦7,500</span>
                                <span class="text-gray-600">/month</span>
                            </span>
                            <span class="yearly-price hidden">
                                <span class="text-4xl font-bold price-highlight">₦5,000</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-brand-green mt-1 font-semibold">Billed yearly (₦76,500) - Save ₦13,500!</div>
                            </span>
                        </div>
                        <div class="6-month-price hidden">
                            <span class="text-4xl font-bold price-highlight">₦7,500</span>
                            <span class="text-gray-600">/month</span>
                            <div class="text-sm text-gray-600 mt-1">Billed 6-monthly (₦45,000)</div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="w-full bg-brand-teal text-white py-3 px-6 rounded-lg hover:opacity-90 font-semibold transition-all mb-8 block text-center">
                        Start Free Trial
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Up to 5 users</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Basic accounting + AI assistance</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Inventory management</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Basic CRM</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Standard reports</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Email support</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Mobile app access</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Basic AI Q&A assistant</span>
                    </div>
                </div>
            </div>

            <!-- Professional Plan -->
            <div class="pricing-card popular bg-white border-2 border-brand-gold rounded-2xl p-8 relative shadow-xl ">
                <div class="absolute -top-1 left-1/2 transform -translate-x-1/2">
                    <span class="bg-brand-gold text-gray-900 px-6 py-2 rounded-full text-sm font-bold shadow-lg">Most Popular</span>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-gold rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                    <p class="text-gray-600 mb-6">Ideal for growing businesses</p>

                    <div class="mb-8">
                        <div class="pricing-display">
                            <span class="monthly-price">
                                <span class="text-4xl font-bold price-highlight">₦10,000</span>
                                <span class="text-gray-600">/month</span>
                            </span>
                            <span class="yearly-price hidden">
                                <span class="text-4xl font-bold price-highlight">₦8,500</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-brand-green mt-1 font-semibold">Billed yearly (₦102,000) - Save ₦18,000!</div>
                            </span>
                        </div>
                        <div class="6-month-price hidden">
                            <span class="text-4xl font-bold price-highlight">₦10,000</span>
                            <span class="text-gray-600">/month</span>
                            <div class="text-sm text-gray-600 mt-1">Billed 6-monthly (₦60,000)</div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="w-full bg-brand-gold text-gray-900 py-3 px-6 rounded-lg hover:bg-yellow-400 font-semibold transition-all mb-8 block text-center">
                        Start Free Trial
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Up to 15 users</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Advanced accounting + Full AI suite</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Full inventory management</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Advanced CRM & sales pipeline</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">POS system</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Basic payroll management</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Advanced reports & analytics</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Priority support</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">API access</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Full AI assistant suite</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Smart automation & templates</span>
                    </div>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="pricing-card bg-white border-2 border-gray-200 rounded-2xl p-8 relative">
                <div class="text-center">
                    <div class="w-16 h-16 bg-brand-blue rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                    <p class="text-gray-600 mb-6">For large businesses and corporations</p>

                    <div class="mb-8">
                        <div class="pricing-display">
                            <span class="monthly-price">
                                <span class="text-4xl font-bold price-highlight">₦15,000</span>
                                <span class="text-gray-600">/month</span>
                            </span>
                            <span class="yearly-price hidden">
                                <span class="text-4xl font-bold price-highlight">₦12,750</span>
                                <span class="text-gray-600">/month</span>
                                <div class="text-sm text-brand-green mt-1 font-semibold">Billed yearly (₦153,000) - Save ₦27,000!</div>
                            </span>
                        </div>
                        <div class="6-month-price hidden">
                            <span class="text-4xl font-bold price-highlight">₦15,000</span>
                            <span class="text-gray-600">/month</span>
                            <div class="text-sm text-gray-600 mt-1">Billed 6-monthly (₦90,000)</div>
                        </div>
                    </div>

                    <a href="{{ route('register') }}" class="w-full bg-brand-blue text-white py-3 px-6 rounded-lg hover:opacity-90 font-semibold transition-all mb-8 block text-center">
                        Start Free Trial
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Unlimited users</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Full accounting + Advanced AI features</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Multi-location inventory</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Enterprise CRM & automation</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Multi-location POS</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Full payroll & HR management</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Custom reports & dashboards</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">24/7 dedicated support</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Advanced API & integrations</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Custom training & onboarding</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Enterprise AI & predictive analytics</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 feature-check mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-700">Custom AI workflows & automation</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Availability & Affordability Promise -->
<section class="gradient-bg-2 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Our Promise: <span class="text-brand-gold">Availability & Affordability</span>
            </h2>
            <p class="text-xl text-gray-200 max-w-3xl mx-auto">
                Every plan is designed to give you maximum value while keeping costs low for Nigerian businesses.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div class="flex items-start">
                    <div class="w-16 h-16 bg-brand-gold rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-3">Always Available</h3>
                        <p class="text-gray-200 text-lg">99.9% uptime guarantee across all plans. Your business data is accessible 24/7 from anywhere in Nigeria.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-16 h-16 bg-brand-gold rounded-full flex items-center justify-center mr-6 flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white mb-3">Maximum Affordability</h3>
                        <p class="text-gray-200 text-lg">Transparent pricing with no hidden fees. Save up to 15% with yearly billing and get enterprise features at small business prices.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-2xl">
                <h3 class="text-2xl font-bold text-white mb-6 text-center">What You Get With Every Plan</h3>
                <div class="space-y-4">
                    <div class="flex items-center text-gray-200">
                        <span class="text-brand-gold mr-3 text-xl">✓</span>
                        <span>30-day free trial</span>
                    </div>
                    <div class="flex items-center text-gray-200">
                        <span class="text-brand-gold mr-3 text-xl">✓</span>
                        <span>No setup fees</span>
                    </div>
                    <div class="flex items-center text-gray-200">
                        <span class="text-brand-gold mr-3 text-xl">✓</span>
                        <span>Cancel anytime</span>
                    </div>
                    <div class="flex items-center text-gray-200">
                        <span class="text-brand-gold mr-3 text-xl">✓</span>
                        <span>Nigerian tax compliance</span>
                    </div>
                    <div class="flex items-center text-gray-200">
                        <span class="text-brand-gold mr-3 text-xl">✓</span>
                        <span>Mobile app included</span>
                    </div>
                    <div class="flex items-center text-gray-200">
                        <span class="text-brand-gold mr-3 text-xl">✓</span>
                        <span>Regular updates</span>
                    </div>
                    <div class="flex items-center text-gray-200">
                        <span class="text-brand-gold mr-3 text-xl">✓</span>
                        <span>Data backup & security</span>
                    </div>
                    <div class="flex items-center text-gray-200">
                        <span class="text-brand-gold mr-3 text-xl">✓</span>
                        <span>AI-powered business insights</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Feature Comparison Table -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Compare Plans</h2>
            <p class="text-lg text-gray-600">See exactly what's included in each plan</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white rounded-xl shadow-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left p-6 font-semibold text-gray-900">Features</th>
                        <th class="text-center p-6 font-semibold text-gray-900">Starter</th>
                        <th class="text-center p-6 font-semibold text-gray-900 bg-brand-gold bg-opacity-10">Professional</th>
                        <th class="text-center p-6 font-semibold text-gray-900">Enterprise</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="p-6 font-medium text-gray-900">Number of Users</td>
                        <td class="p-6 text-center text-gray-600">Up to 5</td>
                        <td class="p-6 text-center text-gray-600 bg-brand-gold bg-opacity-5">Up to 15</td>
                        <td class="p-6 text-center text-gray-600">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="p-6 font-medium text-gray-900">Accounting Features</td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center bg-brand-gold bg-opacity-5">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-6 font-medium text-gray-900">Inventory Management</td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center bg-brand-gold bg-opacity-5">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-6 font-medium text-gray-900">CRM & Sales Pipeline</td>
                        <td class="p-6 text-center">
                            <span class="text-gray-400 text-sm">Basic</span>
                        </td>
                        <td class="p-6 text-center bg-brand-gold bg-opacity-5">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-6 font-medium text-gray-900">POS System</td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-cross mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center bg-brand-gold bg-opacity-5">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-6 font-medium text-gray-900">Payroll Management</td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-cross mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center bg-brand-gold bg-opacity-5">
                            <span class="text-gray-600 text-sm">Basic</span>
                        </td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-6 font-medium text-gray-900">API Access</td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-cross mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center bg-brand-gold bg-opacity-5">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                        <td class="p-6 text-center">
                            <svg class="w-5 h-5 feature-check mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-6 font-medium text-gray-900">AI Assistant Features</td>
                        <td class="p-6 text-center text-gray-600">Basic Q&A</td>
                        <td class="p-6 text-center text-gray-600 bg-brand-gold bg-opacity-5">Full AI Suite</td>
                        <td class="p-6 text-center text-gray-600">Enterprise AI + Custom</td>
                    </tr>
                    <tr>
                        <td class="p-6 font-medium text-gray-900">Support Level</td>
                        <td class="p-6 text-center text-gray-600">Email</td>
                        <td class="p-6 text-center text-gray-600 bg-brand-gold bg-opacity-5">Priority</td>
                        <td class="p-6 text-center text-gray-600">24/7 Dedicated</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-lg text-gray-600">Everything you need to know about our pricing and plans</p>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Can I change plans anytime?</h3>
                <p class="text-gray-600">Yes, you can upgrade or downgrade your plan at any time. Changes take effect immediately, and we'll prorate any billing adjustments.</p>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Is there a setup fee?</h3>
                <p class="text-gray-600">No, there are no setup fees for any of our plans. You only pay the monthly or yearly subscription fee.</p>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">What payment methods do you accept?</h3>
                <p class="text-gray-600">We accept all major Nigerian banks, debit cards, and popular payment gateways including Paystack, Flutterwave, and bank transfers.</p>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Is my data secure?</h3>
                <p class="text-gray-600">Absolutely. We use bank-level encryption, regular backups, and comply with international data protection standards. Your business data is safe with us.</p>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Can I cancel anytime?</h3>
                <p class="text-gray-600">Yes, you can cancel your subscription at any time. There are no cancellation fees, and you'll continue to have access until the end of your billing period.</p>
            </div>

            <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Do you offer discounts for NGOs or educational institutions?</h3>
                <p class="text-gray-600">Yes, we offer special pricing for registered NGOs and educational institutions. Contact our sales team for more information about available discounts.</p>
            </div>
        </div>
    </div>
</section>

@include('cta')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const billingToggle = document.getElementById('billing-toggle');
    const toggleDot = document.getElementById('toggle-dot');
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const yearlyPrices = document.querySelectorAll('.yearly-price');
    let isYearly = false;

    billingToggle.addEventListener('click', function() {
        isYearly = !isYearly;

        if (isYearly) {
            // Switch to yearly
            billingToggle.classList.add('yearly');
            toggleDot.style.transform = 'translateX(1.5rem)';

            monthlyPrices.forEach(price => price.classList.add('hidden'));
            yearlyPrices.forEach(price => price.classList.remove('hidden'));
        } else {
            // Switch to monthly
            billingToggle.classList.remove('yearly');
            toggleDot.style.transform = 'translateX(0.25rem)';

            monthlyPrices.forEach(price => price.classList.remove('hidden'));
            yearlyPrices.forEach(price => price.classList.add('hidden'));
        }
    });

    // Add hover effects to pricing cards
    const pricingCards = document.querySelectorAll('.pricing-card');
    pricingCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (!this.classList.contains('popular')) {
                this.style.transform = 'translateY(-8px)';
                this.style.boxShadow = '0 25px 50px -12px rgba(0, 0, 0, 0.25)';
            }
        });

        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('popular')) {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            }
        });
    });

    // Smooth scroll for anchor links
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

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe pricing cards for animation
    pricingCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Add stagger animation delay
    pricingCards.forEach((card, index) => {
        card.style.transitionDelay = `${index * 0.1}s`;
    });
});

// Price calculation functions
function calculateYearlyPrice(monthlyPrice) {
    return Math.round(monthlyPrice * 12 * 0.85); // 15% discount
}

function calculateMonthlySavings(monthlyPrice) {
    const yearlyTotal = monthlyPrice * 12;
    const discountedYearly = calculateYearlyPrice(monthlyPrice);
    return yearlyTotal - discountedYearly;
}

// Format currency for Nigerian Naira
function formatNaira(amount) {
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// Add loading states for buttons
document.querySelectorAll('a[href*="register"]').forEach(button => {
    button.addEventListener('click', function(e) {
        const originalText = this.textContent;
        this.textContent = 'Loading...';
        this.style.opacity = '0.7';
        this.style.pointerEvents = 'none';

        // Reset after 3 seconds if page doesn't navigate
        setTimeout(() => {
            this.textContent = originalText;
            this.style.opacity = '1';
            this.style.pointerEvents = 'auto';
        }, 3000);
    });
});

// Add tooltip functionality for feature comparisons
const featureRows = document.querySelectorAll('tbody tr');
featureRows.forEach(row => {
    const featureName = row.querySelector('td:first-child').textContent.trim();

    // Add tooltips based on feature name
    const tooltips = {
        'Number of Users': 'Maximum number of team members who can access the system',
        'Accounting Features': 'Complete double-entry bookkeeping with Nigerian tax compliance',
        'Inventory Management': 'Track stock levels, manage suppliers, and automate reordering',
        'CRM & Sales Pipeline': 'Manage customer relationships and track sales opportunities',
        'POS System': 'Point of sale system for retail and restaurant businesses',
        'Payroll Management': 'Calculate salaries, taxes, and statutory deductions automatically',
        'API Access': 'Integrate with third-party applications and services',
        'Support Level': 'Level of customer support included with your plan'
    };

    if (tooltips[featureName]) {
        row.setAttribute('title', tooltips[featureName]);
        row.style.cursor = 'help';
    }
});

// Add plan recommendation logic
function getRecommendedPlan(businessSize, features) {
    if (businessSize <= 5 && !features.includes('pos') && !features.includes('payroll')) {
        return 'starter';
    } else if (businessSize <= 15 || features.includes('pos') || features.includes('advanced-crm')) {
        return 'professional';
    } else {
        return 'enterprise';
    }
}

// Highlight recommended plan based on user interaction
function highlightRecommendedPlan(planType) {
    const cards = document.querySelectorAll('.pricing-card');
    cards.forEach(card => {
        card.classList.remove('recommended');
    });

    const recommendedCard = document.querySelector(`.pricing-card[data-plan="${planType}"]`);
    if (recommendedCard) {
        recommendedCard.classList.add('recommended');
        recommendedCard.style.border = '2px solid var(--color-gold)';
    }
}

// Add data attributes to pricing cards for easier targeting
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.pricing-card');
    const planTypes = ['starter', 'professional', 'enterprise'];

    cards.forEach((card, index) => {
        if (planTypes[index]) {
            card.setAttribute('data-plan', planTypes[index]);
        }
    });
});

// Add keyboard navigation for accessibility
document.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
        const focusedElement = document.activeElement;
        if (focusedElement.classList.contains('pricing-card')) {
            focusedElement.style.outline = '2px solid var(--color-gold)';
            focusedElement.style.outlineOffset = '4px';
        }
    }
});

document.addEventListener('focusout', function(e) {
    if (e.target.classList.contains('pricing-card')) {
        e.target.style.outline = 'none';
    }
});

// Add analytics tracking for plan selection
function trackPlanSelection(planName, billingCycle) {
    // This would integrate with your analytics service
    console.log(`Plan selected: ${planName} - ${billingCycle}`);

    // Example: Google Analytics event
    if (typeof gtag !== 'undefined') {
        gtag('event', 'plan_selected', {
            'plan_name': planName,
            'billing_cycle': billingCycle,
            'value': getPlanPrice(planName, billingCycle)
        });
    }
}

function getPlanPrice(planName, billingCycle) {
    const prices = {
        'starter': { monthly: 7500, yearly: 76500 },
        'professional': { monthly: 10000, yearly: 102000 },
        'enterprise': { monthly: 15000, yearly: 153000 }
    };

    return prices[planName] ? prices[planName][billingCycle] : 0;
}

// Add plan selection tracking to CTA buttons
document.querySelectorAll('.pricing-card a[href*="register"]').forEach((button, index) => {
    const planNames = ['starter', 'professional', 'enterprise'];
    const planName = planNames[index];

    button.addEventListener('click', function() {
        const billingCycle = document.querySelector('.yearly-price.hidden') ? 'monthly' : 'yearly';
        trackPlanSelection(planName, billingCycle);
    });
});
</script>

<style>
/* Additional responsive styles */
@media (max-width: 768px) {
    .pricing-card.popular {
        transform: none;
        margin-top: 2rem;
    }

    .pricing-card.popular:hover {
        transform: translateY(-8px);
    }

    .pricing-display {
        margin-bottom: 1.5rem;
    }

    .feature-comparison-table {
        font-size: 0.875rem;
    }

    .gradient-bg h1 {
        font-size: 2.5rem;
    }

    .billing-toggle {
        transform: scale(0.9);
    }
}

@media (max-width: 640px) {
    .pricing-card {
        padding: 1.5rem;
    }

    .price-highlight {
        font-size: 2.5rem;
    }

    .savings-badge {
        display: block;
        margin-top: 0.5rem;
        margin-left: 0;
    }
}

/* Print styles */
@media print {
    .gradient-bg,
    .gradient-bg-2 {
        background: white !important;
        color: black !important;
    }

    .pricing-card {
        border: 2px solid #000 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }

    .bg-brand-gold {
        background: #f0f0f0 !important;
    }

    .text-white {
        color: black !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .pricing-card {
        border-width: 3px;
    }

    .feature-check {
        font-weight: bold;
    }

    .feature-cross {
        font-weight: bold;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .pricing-card,
    .toggle-dot,
    .savings-badge {
        transition: none !important;
        animation: none !important;
    }

    .pricing-card:hover {
        transform: none !important;
    }
}
</style>
@endsection
