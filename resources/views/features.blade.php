@extends('layouts.app')

@section('title', 'Features - Complete Business Management Suite | @brand')
@section('description', 'Discover all the powerful features of our platform: accounting, inventory, CRM, POS, e-commerce, payroll, tax compliance, AI assistant, and more — designed for African businesses.')

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
        --color-orange: #e8913a;
        --color-rose: #e05577;
        --color-cyan: #22a3b3;
        --color-indigo: #5b5ea6;
        --color-amber: #d4953d;
    }

    .bg-brand-blue { background-color: var(--color-blue); }
    .bg-brand-gold { background-color: var(--color-gold); }
    .bg-brand-purple { background-color: var(--color-dark-purple); }
    .bg-brand-teal { background-color: var(--color-teal); }
    .bg-brand-green { background-color: var(--color-green); }
    .bg-brand-light-blue { background-color: var(--color-light-blue); }
    .bg-brand-deep-purple { background-color: var(--color-deep-purple); }
    .bg-brand-lavender { background-color: var(--color-lavender); }
    .bg-brand-violet { background-color: var(--color-violet); }
    .bg-brand-orange { background-color: var(--color-orange); }
    .bg-brand-rose { background-color: var(--color-rose); }
    .bg-brand-cyan { background-color: var(--color-cyan); }
    .bg-brand-indigo { background-color: var(--color-indigo); }
    .bg-brand-amber { background-color: var(--color-amber); }

    .text-brand-gold { color: var(--color-gold); }
    .text-brand-blue { color: var(--color-blue); }
    .text-brand-purple { color: var(--color-dark-purple); }
    .text-brand-teal { color: var(--color-teal); }
    .text-brand-green { color: var(--color-green); }
    .text-brand-violet { color: var(--color-violet); }
    .text-brand-orange { color: var(--color-orange); }

    .border-brand-gold { border-color: var(--color-gold); }
    .border-brand-blue { border-color: var(--color-blue); }

    .hover\:bg-brand-gold:hover { background-color: var(--color-gold); }
    .hover\:text-brand-blue:hover { color: var(--color-blue); }

    .gradient-bg {
        background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 50%, var(--color-deep-purple) 100%);
    }

    .gradient-bg-2 {
        background: linear-gradient(135deg, var(--color-dark-purple) 0%, var(--color-violet) 50%, var(--color-deep-purple) 100%);
    }

    .gradient-bg-3 {
        background: linear-gradient(135deg, var(--color-teal) 0%, var(--color-green) 100%);
    }

    .section-spacing {
        padding: 5rem 0;
    }

    .feature-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        position: relative;
        overflow: hidden;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--color-gold), var(--color-blue));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .feature-card:hover::before {
        transform: scaleX(1);
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .hover\:shadow-brand:hover {
        box-shadow: 0 20px 25px -5px rgba(43, 99, 153, 0.1), 0 10px 10px -5px rgba(43, 99, 153, 0.04);
    }

    .feature-nav-link {
        transition: all 0.3s ease;
        border-bottom: 2px solid transparent;
    }

    .feature-nav-link:hover,
    .feature-nav-link.active {
        border-bottom-color: var(--color-gold);
        color: var(--color-gold);
    }

    .feature-section {
        scroll-margin-top: 80px;
    }

    .pulse-dot {
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .ai-glow {
        box-shadow: 0 0 30px rgba(139, 92, 246, 0.3);
    }

    .ai-chat-bubble {
        animation: slideUp 0.5s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .section-spacing {
            padding: 3rem 0;
        }

        .feature-card {
            padding: 1.5rem !important;
        }

        .feature-nav-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .feature-nav-scroll::-webkit-scrollbar {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .section-spacing {
            padding: 2rem 0;
        }
    }
</style>

<!-- Hero Section -->
<section class="gradient-bg text-white py-16 md:py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>

    <!-- Floating background elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-brand-gold opacity-20 rounded-full floating-animation"></div>
    <div class="absolute top-32 right-20 w-16 h-16 bg-brand-teal opacity-30 rounded-full floating-animation" style="animation-delay: -2s;"></div>
    <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-brand-lavender opacity-25 rounded-full floating-animation" style="animation-delay: -4s;"></div>
    <div class="absolute bottom-10 right-1/3 w-8 h-8 bg-brand-green opacity-20 rounded-full floating-animation" style="animation-delay: -1s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="inline-flex items-center bg-white bg-opacity-10 backdrop-blur-sm rounded-full px-4 py-2 mb-6">
                <span class="pulse-dot w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                <span class="text-sm font-medium">13+ Powerful Modules — One Platform</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-6xl font-bold mb-6">
                Every Tool Your Business Needs
                <span class="text-brand-gold block mt-2">In One Affordable Platform</span>
            </h1>
            <p class="text-lg md:text-2xl text-gray-200 max-w-4xl mx-auto mb-8">
                From double-entry accounting to AI-powered insights, from POS to e-commerce — run your entire business with a single, intelligent platform built for <strong class="text-brand-gold">African businesses</strong>.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                <a href="{{ route('register') }}" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-all transform hover:scale-105 shadow-lg">
                    Start Free Trial — No Card Required
                </a>
                <a href="{{ route('pricing') }}" class="border-2 border-brand-gold text-brand-gold px-8 py-4 rounded-lg hover:bg-brand-gold hover:text-gray-900 font-semibold text-lg transition-all">
                    View Pricing
                </a>
            </div>
            <p class="text-gray-300 text-sm">✓ 30-day free trial &nbsp; ✓ Setup in minutes &nbsp; ✓ Cancel anytime</p>
        </div>
    </div>
</section>

<!-- Quick Feature Navigation -->
<section class="bg-white border-b border-gray-200 sticky top-0 z-20 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="feature-nav-scroll">
            <div class="flex items-center space-x-1 py-3 min-w-max">
                <a href="#ballie-ai" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">🤖 BallieAI</a>
                <a href="#accounting" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">📊 Accounting</a>
                <a href="#inventory" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">📦 Inventory</a>
                <a href="#crm" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">👥 CRM</a>
                <a href="#pos" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">🛒 POS</a>
                <a href="#ecommerce" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">🌐 E-commerce</a>
                <a href="#payroll" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">💰 Payroll</a>
                <a href="#statutory" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">🏛️ Tax</a>
                <a href="#reports" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">📈 Reports</a>
                <a href="#more" class="feature-nav-link px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 whitespace-nowrap">⚡ More</a>
            </div>
        </div>
    </div>
</section>

<!-- Feature Overview Grid -->
<section class="section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">One Platform. Every Feature. Zero Compromise.</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Stop juggling multiple tools. Everything you need to run, grow, and scale your business is right here.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
            <a href="#ballie-ai" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-purple-300 transition-all group">
                <div class="w-14 h-14 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">BallieAI</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">AI Assistant</p>
            </a>

            <a href="#accounting" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-300 transition-all group">
                <div class="w-14 h-14 bg-brand-gold rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">Accounting</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Double-Entry</p>
            </a>

            <a href="#inventory" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-teal-300 transition-all group">
                <div class="w-14 h-14 bg-brand-teal rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">Inventory</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Stock Control</p>
            </a>

            <a href="#crm" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-pink-300 transition-all group">
                <div class="w-14 h-14 bg-brand-rose rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">CRM</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Customers</p>
            </a>

            <a href="#pos" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-cyan-300 transition-all group">
                <div class="w-14 h-14 bg-brand-cyan rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">POS</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Point of Sale</p>
            </a>

            <a href="#ecommerce" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-orange-300 transition-all group">
                <div class="w-14 h-14 bg-brand-orange rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">E-commerce</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Online Store</p>
            </a>

            <a href="#payroll" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-emerald-300 transition-all group">
                <div class="w-14 h-14 bg-brand-green rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">Payroll</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Salaries & Tax</p>
            </a>

            <a href="#statutory" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-amber-300 transition-all group">
                <div class="w-14 h-14 bg-brand-amber rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">Tax & Statutory</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Compliance</p>
            </a>

            <a href="#reports" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-violet-300 transition-all group">
                <div class="w-14 h-14 bg-brand-violet rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">Reports</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Analytics</p>
            </a>

            <a href="#more" class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-lg hover:border-blue-300 transition-all group">
                <div class="w-14 h-14 bg-brand-blue rounded-xl mx-auto mb-3 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                </div>
                <h3 class="text-sm md:text-base font-semibold text-gray-900">And More</h3>
                <p class="text-xs text-gray-500 mt-1 hidden sm:block">Admin & Search</p>
            </a>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 1. BALLIE AI ASSISTANT -->
<!-- ============================================ -->
<section id="ballie-ai" class="feature-section section-spacing bg-gradient-to-br from-purple-50 via-indigo-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6 ai-glow">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
            </div>
            <div class="inline-flex items-center bg-purple-100 text-purple-700 rounded-full px-4 py-1 text-sm font-medium mb-4">
                ✨ Powered by AI
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Meet <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-600">BallieAI</span> — Your Business Co-Pilot</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Not just a chatbot — BallieAI understands your business data, interprets your reports in plain language, creates invoices from conversation, and helps you make smarter decisions every day.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start mb-12">
            <!-- AI Chat Demo -->
            <div class="bg-white rounded-2xl shadow-xl border border-purple-100 overflow-hidden order-2 lg:order-1">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex items-center space-x-3">
                    <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                    <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                    <span class="text-white text-sm font-medium ml-2">BallieAI Chat</span>
                </div>
                <div class="p-6 space-y-4">
                    <div class="ai-chat-bubble flex justify-end">
                        <div class="bg-purple-100 text-purple-900 rounded-2xl rounded-tr-md px-4 py-3 max-w-xs text-sm">
                            "Create an invoice for Chidi Okafor — 50 bags of rice at ₦35,000 each"
                        </div>
                    </div>
                    <div class="ai-chat-bubble flex justify-start">
                        <div class="bg-gray-100 text-gray-800 rounded-2xl rounded-tl-md px-4 py-3 max-w-sm text-sm">
                            ✅ Done! Invoice #INV-00247 created for <strong>Chidi Okafor</strong>:<br>
                            • 50 × Bags of Rice = ₦1,750,000<br>
                            • VAT (7.5%): ₦131,250<br>
                            • <strong>Total: ₦1,881,250</strong><br><br>
                            <span class="text-purple-600">Want me to send it via WhatsApp or email?</span>
                        </div>
                    </div>
                    <div class="ai-chat-bubble flex justify-end">
                        <div class="bg-purple-100 text-purple-900 rounded-2xl rounded-tr-md px-4 py-3 max-w-xs text-sm">
                            "How did my business do last month?"
                        </div>
                    </div>
                    <div class="ai-chat-bubble flex justify-start">
                        <div class="bg-gray-100 text-gray-800 rounded-2xl rounded-tl-md px-4 py-3 max-w-sm text-sm">
                            📊 <strong>January Summary:</strong><br>
                            Revenue: ₦12.5M (+18% ↑)<br>
                            Expenses: ₦8.2M<br>
                            Net Profit: ₦4.3M<br><br>
                            💡 <em>Your top product was "Premium Rice" with 340 units sold. Consider increasing stock — it ran out twice this month.</em>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Features List -->
            <div class="space-y-6 order-1 lg:order-2">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Smart Chat Assistant</h3>
                        <p class="text-gray-600">Ask accounting or business questions in plain English (or Pidgin!). BallieAI understands your context and gives you answers that make sense — no jargon.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Report Interpretation</h3>
                        <p class="text-gray-600">Don't understand a financial report? BallieAI breaks it down in your simple language so you or your admin can take action immediately.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Invoice by Prompt</h3>
                        <p class="text-gray-600">Just tell BallieAI what to invoice and for whom. It creates professional invoices, applies the right VAT, and sends them — all from a single prompt.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Voucher Assistance</h3>
                        <p class="text-gray-600">Need to create payment or receipt vouchers? BallieAI helps you pick the right accounts, ensures double-entry compliance, and guides you step by step.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-rose-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Predictive Insights</h3>
                        <p class="text-gray-600">BallieAI proactively alerts you about low stock, overdue invoices, cash flow dips, and growth opportunities before you even ask.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 2. ACCOUNTING -->
<!-- ============================================ -->
<section id="accounting" class="feature-section section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-brand-gold rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Professional Double-Entry Accounting</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Full-featured accounting designed for Nigerian compliance. From chart of accounts to bank reconciliation — your books stay perfect, automatically.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Chart of Accounts</h3>
                <p class="text-gray-600 text-sm mb-3">Pre-configured Nigerian COA with account groups, hierarchies, and full customization for any industry.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Standard Nigerian COA</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Custom account groups</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Parent-child hierarchies</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Vouchers & Journal Entries</h3>
                <p class="text-gray-600 text-sm mb-3">Payment vouchers, receipt vouchers, and journal entries with approval workflows and full audit trail.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Payment & receipt vouchers</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Approval workflows</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Contra & journal entries</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Financial Statements</h3>
                <p class="text-gray-600 text-sm mb-3">Auto-generated P&L, Balance Sheet, Cash Flow, and Trial Balance — always up-to-date.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Profit & Loss statement</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Balance Sheet</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Cash Flow & Trial Balance</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-light-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Bank Reconciliation</h3>
                <p class="text-gray-600 text-sm mb-3">Match bank transactions automatically, detect discrepancies, and keep your books balanced.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto-matching engine</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Multiple bank accounts</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Discrepancy alerts</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Multi-Currency Support</h3>
                <p class="text-gray-600 text-sm mb-3">Handle USD, GBP, EUR and more alongside NGN with real-time exchange rate tracking.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Real-time exchange rates</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto gain/loss calculation</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Multi-currency invoicing</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-lavender rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Ledger & Daybook</h3>
                <p class="text-gray-600 text-sm mb-3">Complete general ledger, account-level ledger, and daybook entries with date-based balance tracking.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> General & sub-ledger</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Date-based balances</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Daybook entries</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 3. INVENTORY -->
<!-- ============================================ -->
<section id="inventory" class="feature-section section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-brand-teal rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Complete Inventory Management</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Know exactly what you have, where it is, and when to reorder. Real-time stock tracking that syncs across your POS, e-commerce, and accounting.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Real-Time Stock Tracking</h3>
                <p class="text-gray-600 text-sm mb-3">Always know your exact stock levels with barcode support, batch tracking, and low-stock alerts.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Live stock levels</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Barcode scanning</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Low-stock alerts</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Product & Category Management</h3>
                <p class="text-gray-600 text-sm mb-3">Organize products by categories, set pricing tiers, manage variants, and bulk import from CSV.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Product categories & tags</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Pricing & variants</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> CSV bulk import</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Warehouse & Multi-Location</h3>
                <p class="text-gray-600 text-sm mb-3">Track inventory across multiple warehouses with inter-branch transfer management.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Multiple locations</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Stock transfers</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Location-level reports</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Supplier Management</h3>
                <p class="text-gray-600 text-sm mb-3">Maintain a supplier database with performance tracking, purchase history, and payment terms.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Supplier profiles</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Purchase orders</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Performance tracking</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-light-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">COGS & Costing</h3>
                <p class="text-gray-600 text-sm mb-3">Automated Cost of Goods Sold calculations with FIFO, LIFO, and weighted average support.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto COGS calculation</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Multiple costing methods</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Margin analysis</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-lavender rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Inventory Reports</h3>
                <p class="text-gray-600 text-sm mb-3">Valuation reports, stock movement history, aging analysis, and reorder suggestions.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Valuation reports</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Movement history</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Aging & reorder reports</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 4. CRM -->
<!-- ============================================ -->
<section id="crm" class="feature-section section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-brand-rose rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Customer Relationship Management</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Know your customers better than they know themselves. Track every interaction, manage sales pipelines, and build loyalty that lasts.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Customer & Vendor Profiles</h3>
                <p class="text-gray-600 text-sm mb-3">Rich profiles with purchase history, outstanding balances, contact details, and custom fields.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Complete customer cards</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Balance tracking</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Bulk import from CSV</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Sales Pipeline & Leads</h3>
                <p class="text-gray-600 text-sm mb-3">Visual pipeline to track deals from lead to close, with automated follow-ups and conversion metrics.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Visual deal pipeline</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Lead scoring</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Conversion analytics</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Communication & Follow-Up</h3>
                <p class="text-gray-600 text-sm mb-3">Log calls, send invoices, track interactions, and never miss a follow-up with smart reminders.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Interaction logging</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Smart reminders</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> CRM reports</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 5. POS -->
<!-- ============================================ -->
<section id="pos" class="feature-section section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-brand-cyan rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Point of Sale System</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Lightning-fast checkout, multiple payment methods, cash register management, and professional receipts — your sales floor, supercharged.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast Checkout</h3>
                <p class="text-gray-600 text-sm mb-3">Intuitive touchscreen interface with barcode scanning, product search, and keyboard shortcuts.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Barcode & search</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Cash, card, transfer</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Split payments</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Cash Register Management</h3>
                <p class="text-gray-600 text-sm mb-3">Open/close shifts, track cash movements, and reconcile at end of day with full audit trail.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Shift management</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Cash-in / cash-out</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> End-of-day reconciliation</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Receipts & Invoices</h3>
                <p class="text-gray-600 text-sm mb-3">Generate professional receipts and invoices with your branding. Print or send digitally via SMS/email.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Branded receipts</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Thermal & A4 printing</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Digital delivery</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 6. E-COMMERCE -->
<!-- ============================================ -->
<section id="ecommerce" class="feature-section section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-brand-orange rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Built-In E-Commerce Store</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Launch your online store without a separate platform. Your inventory, orders, and payments all sync automatically with your main system.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-orange rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Online Storefront</h3>
                <p class="text-gray-600 text-sm mb-3">Beautiful, mobile-responsive storefront that showcases your products and accepts orders 24/7.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Mobile-friendly design</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Product catalog</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Custom branding</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Order Management</h3>
                <p class="text-gray-600 text-sm mb-3">Track orders from placement to delivery. Manage fulfillment, returns, and customer notifications.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Order tracking</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Fulfillment workflow</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Inventory auto-sync</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Online Payments</h3>
                <p class="text-gray-600 text-sm mb-3">Accept payments via Paystack, Flutterwave, bank transfer, and more — all from your store.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Payment gateway integration</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Bank transfers</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Payment confirmation</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 7. PAYROLL -->
<!-- ============================================ -->
<section id="payroll" class="feature-section section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-brand-green rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Payroll Management</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Pay your team accurately, on time, every time. Automated PAYE calculation, pension deductions, payslip generation, and full Nigerian compliance built in.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">PAYE & Tax Calculation</h3>
                <p class="text-gray-600 text-sm mb-3">Automated PAYE with current Nigerian tax tables, allowances, and FIRS-compliant reporting.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Current PAYE rates</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Tax allowances & relief</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Annual tax returns</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Pension & NHF</h3>
                <p class="text-gray-600 text-sm mb-3">Auto-calculate pension contributions, NHF, NSITF, and ITF deductions with PFA integration.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Pension fund integration</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> NHF & NSITF deductions</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Statutory compliance</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Payslips & Bulk Payment</h3>
                <p class="text-gray-600 text-sm mb-3">Generate detailed payslips, process bulk bank payments, and manage overtime, advances, and bonuses.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Professional payslips</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Bulk payment uploads</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Overtime & advances</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Employee Self-Service Portal</h3>
                <p class="text-gray-600 text-sm mb-3">Employees get their own secure portal to mark attendance, view payslips, and manage their profile.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Mark attendance & view history</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Download payslips & tax certificates</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> View loans & update profile</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 8. STATUTORY (TAX) -->
<!-- ============================================ -->
<section id="statutory" class="feature-section section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-brand-amber rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Statutory & Tax Compliance</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Stay compliant without the stress. Automated VAT, WHT, and Company Income Tax calculations with FIRS-ready reports and filing support.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-amber rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">VAT Management</h3>
                <p class="text-gray-600 text-sm mb-3">Automated 7.5% VAT calculations on invoices and purchases with VAT return generation.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto VAT on invoices</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Input/output VAT tracking</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> VAT returns</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">WHT & CIT</h3>
                <p class="text-gray-600 text-sm mb-3">Withholding Tax tracking and Company Income Tax computations with filing-ready reports.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> WHT deduction tracking</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> CIT computation</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> FIRS-ready reports</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Filing Reminders</h3>
                <p class="text-gray-600 text-sm mb-3">Never miss a deadline with automated reminders for VAT, PAYE, pension, and tax filing dates.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Deadline alerts</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Filing calendar</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Compliance dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 9. REPORTS & ANALYTICS -->
<!-- ============================================ -->
<section id="reports" class="feature-section section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="w-20 h-20 bg-brand-violet rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Reports & Analytics</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                From real-time dashboards to detailed financial reports — get the clarity you need to make confident business decisions. BallieAI can even explain reports in plain language.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Real-Time Dashboards</h3>
                <p class="text-gray-600 text-sm mb-3">Interactive dashboards with live KPIs, revenue charts, expense tracking, and cash flow visibility.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Live KPI tracking</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Revenue & expense charts</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> At-a-glance overview</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Financial Reports</h3>
                <p class="text-gray-600 text-sm mb-3">Trial Balance, Aged Receivables/Payables, P&L Comparison, and more — exportable to PDF & Excel.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Trial balance & aged reports</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Period-over-period comparison</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> PDF & Excel export</li>
                </ul>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 md:p-8 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Sales, Inventory & CRM Reports</h3>
                <p class="text-gray-600 text-sm mb-3">Top products, sales by period, customer reports, inventory valuation, and stock movement analytics.</p>
                <ul class="text-sm text-gray-500 space-y-1.5">
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Sales analytics</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Inventory valuation</li>
                    <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Customer insights</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ============================================ -->
<!-- 10. MORE FEATURES: Audit, Admin, Search, etc -->
<!-- ============================================ -->
<section id="more" class="feature-section section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">And So Much More...</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Every detail is covered. From audit trails to admin controls, from smart search to security — everything a growing business needs.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Audit Log -->
            <div class="feature-card bg-white rounded-xl p-6 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-indigo rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Audit Log</h3>
                <p class="text-gray-600 text-sm">Complete trail of every action — who did what, when, and where. Tamper-proof records for accountability.</p>
                <ul class="text-xs text-gray-500 space-y-1 mt-3">
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> User activity tracking</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Change history</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Filterable audit reports</li>
                </ul>
            </div>

            <!-- Admin Management -->
            <div class="feature-card bg-white rounded-xl p-6 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Admin Management</h3>
                <p class="text-gray-600 text-sm">Manage users, roles, permissions, and teams. Control exactly who can see and do what.</p>
                <ul class="text-xs text-gray-500 space-y-1 mt-3">
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Role-based access</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Custom permissions</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Team management</li>
                </ul>
            </div>

            <!-- Smart Search -->
            <div class="feature-card bg-white rounded-xl p-6 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Smart Search</h3>
                <p class="text-gray-600 text-sm">Find anything instantly — invoices, customers, products, vouchers, or transactions with one search bar.</p>
                <ul class="text-xs text-gray-500 space-y-1 mt-3">
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Global search across modules</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Instant results</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Keyboard shortcuts</li>
                </ul>
            </div>

            <!-- Multi-Tenant / Multi-Branch -->
            <div class="feature-card bg-white rounded-xl p-6 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Multi-Business Support</h3>
                <p class="text-gray-600 text-sm">Run multiple businesses from one account. Each with its own data, users, settings, and reports.</p>
                <ul class="text-xs text-gray-500 space-y-1 mt-3">
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Isolated Company data</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Switch between businesses</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Consolidated overview</li>
                </ul>
            </div>

            <!-- Mobile Responsive -->
            <div class="feature-card bg-white rounded-xl p-6 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Mobile Access</h3>
                <p class="text-gray-600 text-sm">Access your entire business from your phone. Responsive design that works on any device, anywhere.</p>
                <ul class="text-xs text-gray-500 space-y-1 mt-3">
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Works on any phone</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Mobile API support</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Push notifications</li>
                </ul>
            </div>

            <!-- Data Security -->
            <div class="feature-card bg-white rounded-xl p-6 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-deep-purple rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Data Security</h3>
                <p class="text-gray-600 text-sm">Enterprise-grade encryption, SSL, regular backups, and role-based access to keep your data safe.</p>
                <ul class="text-xs text-gray-500 space-y-1 mt-3">
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> SSL encryption</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Automated backups</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> IP & login security</li>
                </ul>
            </div>

            <!-- Support Center -->
            <div class="feature-card bg-white rounded-xl p-6 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-rose rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Support Center</h3>
                <p class="text-gray-600 text-sm">Built-in ticket system, help documentation, and priority support to solve issues quickly.</p>
                <ul class="text-xs text-gray-500 space-y-1 mt-3">
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Ticket system</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Knowledge base</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Priority support</li>
                </ul>
            </div>

            <!-- Notifications & Alerts -->
            <div class="feature-card bg-white rounded-xl p-6 hover:shadow-brand">
                <div class="w-12 h-12 bg-brand-light-blue rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 mb-2">Smart Notifications</h3>
                <p class="text-gray-600 text-sm">Get alerted about low stock, overdue invoices, pending approvals, and important business events.</p>
                <ul class="text-xs text-gray-500 space-y-1 mt-3">
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> In-app alerts</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Email notifications</li>
                    <li class="flex items-center"><span class="text-brand-green mr-1.5">✓</span> Custom triggers</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us — Social Proof -->
<section class="gradient-bg-3 text-white section-spacing">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-4xl font-bold mb-4">Why Businesses Choose Us</h2>
            <p class="text-lg text-gray-100 max-w-3xl mx-auto">
                Other tools give you pieces. We give you the whole puzzle — solved.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 text-center">
                <div class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">13+</div>
                <div class="text-lg font-medium mb-2">Business Modules</div>
                <p class="text-gray-200 text-sm">Accounting, Inventory, CRM, POS, E-commerce, Payroll, Tax, Reports, Audit, Admin, AI, and more — all included.</p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 text-center">
                <div class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">1</div>
                <div class="text-lg font-medium mb-2">Platform, Zero Headaches</div>
                <p class="text-gray-200 text-sm">No more switching between apps. Everything syncs in real-time — your inventory updates your accounting, your POS feeds your reports.</p>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-8 text-center">
                <div class="text-4xl md:text-5xl font-bold text-brand-gold mb-2">24/7</div>
                <div class="text-lg font-medium mb-2">AI + Cloud Access</div>
                <p class="text-gray-200 text-sm">BallieAI never sleeps. Your business data is always available, always secure, and always accessible from any device.</p>
            </div>
        </div>
    </div>
</section>

<!-- Availability & Affordability Promise Section -->
<section class="gradient-bg-2 text-white section-spacing">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-4xl font-bold mb-4">
                Our Promise: <span class="text-brand-gold">Availability & Affordability</span>
            </h2>
            <p class="text-lg text-gray-200 max-w-3xl mx-auto">
                Every feature on this page is included in your plan. No "enterprise-only" lock-outs. No surprise charges.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-center">
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="w-14 h-14 bg-brand-gold rounded-full flex items-center justify-center mr-5 flex-shrink-0">
                        <svg class="w-7 h-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">99.9% Uptime Guarantee</h3>
                        <p class="text-gray-200">Cloud-hosted on enterprise infrastructure. Your business data is always available — whether you're at the shop, at home, or on the road.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-14 h-14 bg-brand-gold rounded-full flex items-center justify-center mr-5 flex-shrink-0">
                        <svg class="w-7 h-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Enterprise Power, SME Pricing</h3>
                        <p class="text-gray-200">You get the same features that global corporations pay millions for — at a price that fits Nigerian small and medium businesses.</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="w-14 h-14 bg-brand-gold rounded-full flex items-center justify-center mr-5 flex-shrink-0">
                        <svg class="w-7 h-7 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white mb-2">Built for Africa, Ready for the World</h3>
                        <p class="text-gray-200">Nigerian tax tables, Naira formatting, local payment gateways, and African business workflows — baked in from day one.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-2xl">
                <div class="text-center">
                    <div class="text-sm text-gray-300 uppercase tracking-wider mb-2">Starting from</div>
                    <div class="text-5xl font-bold text-brand-gold mb-1">₦5,000</div>
                    <div class="text-lg text-gray-200 mb-6">per month</div>
                    <div class="text-left space-y-2.5">
                        <div class="flex items-center text-gray-200 text-sm">
                            <span class="text-brand-gold mr-3 text-lg">✓</span> All 13+ modules included
                        </div>
                        <div class="flex items-center text-gray-200 text-sm">
                            <span class="text-brand-gold mr-3 text-lg">✓</span> BallieAI assistant
                        </div>
                        <div class="flex items-center text-gray-200 text-sm">
                            <span class="text-brand-gold mr-3 text-lg">✓</span> Unlimited invoices & vouchers
                        </div>
                        <div class="flex items-center text-gray-200 text-sm">
                            <span class="text-brand-gold mr-3 text-lg">✓</span> POS & E-commerce included
                        </div>
                        <div class="flex items-center text-gray-200 text-sm">
                            <span class="text-brand-gold mr-3 text-lg">✓</span> Full payroll & tax compliance
                        </div>
                        <div class="flex items-center text-gray-200 text-sm">
                            <span class="text-brand-gold mr-3 text-lg">✓</span> Audit log & admin controls
                        </div>
                        <div class="flex items-center text-gray-200 text-sm">
                            <span class="text-brand-gold mr-3 text-lg">✓</span> Priority support
                        </div>
                        <div class="flex items-center text-gray-200 text-sm">
                            <span class="text-brand-gold mr-3 text-lg">✓</span> No hidden fees — ever
                        </div>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('register') }}" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-colors inline-block w-full sm:w-auto">
                            Start Free Trial
                        </a>
                        <p class="text-xs text-gray-400 mt-3">30 days free • No credit card required</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Integration Section -->
<section class="section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl md:text-4xl font-bold text-gray-900 mb-4">Seamless Integrations</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Connect with the tools and services your business already uses.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
            <div class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 bg-brand-blue rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <p class="text-xs md:text-sm font-semibold text-gray-900">Nigerian Banks</p>
            </div>

            <div class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 bg-brand-teal rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <p class="text-xs md:text-sm font-semibold text-gray-900">Paystack & Flutterwave</p>
            </div>

            <div class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 bg-brand-green rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <p class="text-xs md:text-sm font-semibold text-gray-900">SMS & WhatsApp</p>
            </div>

            <div class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 bg-brand-purple rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-xs md:text-sm font-semibold text-gray-900">Email Services</p>
            </div>

            <div class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 bg-brand-light-blue rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div>
                <p class="text-xs md:text-sm font-semibold text-gray-900">REST API</p>
            </div>

            <div class="bg-white rounded-xl p-4 md:p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-14 h-14 bg-brand-lavender rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <p class="text-xs md:text-sm font-semibold text-gray-900">Pension Funds</p>
            </div>
        </div>
    </div>
</section>

@include('cta')

<script>
// Smooth scrolling for feature navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// Active navigation highlight on scroll
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.feature-section');
    const navLinks = document.querySelectorAll('.feature-nav-link');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + id) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, { rootMargin: '-50% 0px', threshold: 0 });

    sections.forEach(section => observer.observe(section));

    // Feature card stagger animation
    const cards = document.querySelectorAll('.feature-card');
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 50);
                cardObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        cardObserver.observe(card);
    });
});
</script>
@endsection
