@extends('layouts.app')

@section('title', 'Features - Complete Business Management Suite | Ballie')
@section('description', 'Discover all the powerful features of Ballie: accounting, inventory, CRM, POS, payroll, and reporting tools designed for African businesses.')

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
    .bg-brand-purple { background-color: var(--color-dark-purple); }
    .bg-brand-teal { background-color: var(--color-teal); }
    .bg-brand-green { background-color: var(--color-green); }
    .bg-brand-light-blue { background-color: var(--color-light-blue); }
    .bg-brand-deep-purple { background-color: var(--color-deep-purple); }
    .bg-brand-lavender { background-color: var(--color-lavender); }
    .bg-brand-violet { background-color: var(--color-violet); }

    .text-brand-gold { color: var(--color-gold); }
    .text-brand-blue { color: var(--color-blue); }
    .text-brand-purple { color: var(--color-dark-purple); }
    .text-brand-teal { color: var(--color-teal); }
    .text-brand-green { color: var(--color-green); }

    .border-brand-gold { border-color: var(--color-gold); }
    .border-brand-blue { border-color: var(--color-blue); }

    .hover\:bg-brand-gold:hover { background-color: var(--color-gold); }
    .hover\:text-brand-blue:hover { color: var(--color-blue); }
    .hover\:shadow-brand { box-shadow: 0 10px 25px rgba(43, 99, 153, 0.15); }

    .feature-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .section-spacing {
        padding: 5rem 0;
    }

    .gradient-bg-1 {
        background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 100%);
    }

    .gradient-bg-2 {
        background: linear-gradient(135deg, var(--color-teal) 0%, var(--color-green) 100%);
    }

    .gradient-bg-3 {
        background: linear-gradient(135deg, var(--color-purple) 0%, var(--color-lavender) 100%);
    }

    .icon-bounce {
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 53%, 80%, 100% {
            transform: translate3d(0,0,0);
        }
        40%, 43% {
            transform: translate3d(0,-10px,0);
        }
        70% {
            transform: translate3d(0,-5px,0);
        }
        90% {
            transform: translate3d(0,-2px,0);
        }
    }
</style>

<!-- Hero Section -->
<section class="gradient-bg text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>

    <!-- Floating background elements -->
    <div class="absolute top-10 left-10 w-20 h-20 bg-brand-gold opacity-20 rounded-full floating-animation"></div>
    <div class="absolute top-32 right-20 w-16 h-16 bg-brand-teal opacity-30 rounded-full floating-animation" style="animation-delay: -2s;"></div>
    <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-brand-lavender opacity-25 rounded-full floating-animation" style="animation-delay: -4s;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="icon-bounce w-20 h-20 bg-brand-gold rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
            </div>
            <h1 class="text-4xl md:text-6xl font-bold mb-6 slide-in-left">
                Everything You Need to Run Your Business
                <span class="text-brand-gold block mt-2">Like a Pro</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 max-w-4xl mx-auto mb-8 slide-in-right">
                From accounting to payroll, inventory to customer management - Ballie provides all the tools your African business needs to succeed with <strong class="text-brand-gold">maximum availability and affordability</strong>.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center slide-in-left">
                <a href="{{ route('register') }}" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-all transform hover:scale-105">
                    Start Free Trial
                </a>
                <a href="{{ route('pricing') }}" class="border-2 border-brand-gold text-brand-gold px-8 py-4 rounded-lg hover:bg-brand-gold hover:text-gray-900 font-semibold text-lg transition-all">
                    View Pricing
                </a>
            </div>
            <div class="mt-6 text-gray-300 text-sm slide-in-right">
                30-day free trial • No credit card required • Setup in minutes
            </div>
        </div>
    </div>
</section>


<!-- Feature Categories -->
<section class="section-spacing bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- AI Assistant Section -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">AI-Powered Business Assistant</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Your intelligent business companion that learns, suggests, and automates to make your operations more efficient - <span class="text-purple-600 font-semibold">available 24/7 to help you succeed</span>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Smart Product Suggestions</h3>
                    <p class="text-gray-600 mb-4">AI analyzes your sales patterns and customer behavior to suggest the right products for each invoice.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Customer purchase history analysis</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Seasonal trend predictions</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Cross-selling recommendations</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Intelligent Data Validation</h3>
                    <p class="text-gray-600 mb-4">Automatically validates invoice data, checks stock levels, and prevents errors before they occur.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Real-time error detection</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Stock availability checks</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Price consistency validation</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">24/7 Q&A Assistant</h3>
                    <p class="text-gray-600 mb-4">Get instant answers to your business questions with context-aware AI assistance.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Business-specific answers</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Context-aware responses</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Learning from your data</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Smart Templates & Automation</h3>
                    <p class="text-gray-600 mb-4">AI creates personalized templates and automates repetitive tasks based on your business patterns.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto-generated workflows</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Personalized templates</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Task automation</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-rose-500 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Predictive Analytics</h3>
                    <p class="text-gray-600 mb-4">AI analyzes your data to predict trends, forecast sales, and provide actionable business insights.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Sales forecasting</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Trend analysis</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Performance predictions</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-gradient-to-r from-teal-500 to-green-500 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V4a1 1 0 011-1h3a1 1 0 001-1v1z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">AI-Powered Insights</h3>
                    <p class="text-gray-600 mb-4">Get intelligent recommendations to optimize your business operations and increase profitability.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Business optimization tips</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Cost reduction suggestions</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Revenue growth strategies</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Accounting Features -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-brand-gold rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">AI-Enhanced Double-Entry Accounting</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Professional accounting features with full Nigerian compliance, automated bookkeeping, and AI-powered insights - <span class="text-brand-blue font-semibold">available 24/7 at an affordable price</span>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Chart of Accounts</h3>
                    <p class="text-gray-600 mb-4">Pre-configured Nigerian chart of accounts with the flexibility to customize for your business needs.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Standard Nigerian COA</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Custom account creation</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Account hierarchies</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">AI-Assisted Journal Entries</h3>
                    <p class="text-gray-600 mb-4">Automated and manual journal entries with AI suggestions, full audit trail and approval workflows.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> AI-generated entries</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Smart entry suggestions</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Approval workflows</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Financial Statements</h3>
                    <p class="text-gray-600 mb-4">Generate professional financial statements including P&L, Balance Sheet, and Cash Flow.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Profit & Loss</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Balance Sheet</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Cash Flow Statement</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">VAT Management</h3>
                    <p class="text-gray-600 mb-4">Complete VAT handling with automated calculations and FIRS-ready reports.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> VAT calculations</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> FIRS reports</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> VAT returns</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-light-blue rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Bank Reconciliation</h3>
                    <p class="text-gray-600 mb-4">Streamlined bank reconciliation with automatic matching and discrepancy detection.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto-matching</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Multiple banks</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Discrepancy alerts</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-lavender rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Multi-Currency</h3>
                    <p class="text-gray-600 mb-4">Handle multiple currencies with real-time exchange rates and conversion tracking.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Real-time rates</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Currency conversion</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Exchange gain/loss</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Inventory Features -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-brand-teal rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">AI-Smart Inventory Management</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Complete inventory control with real-time tracking, AI-powered reordering predictions, and intelligent analytics - <span class="text-brand-teal font-semibold">always available when you need it</span>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Stock Tracking</h3>
                    <p class="text-gray-600 mb-4">Real-time inventory levels with barcode scanning and batch tracking capabilities.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Real-time levels</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Barcode scanning</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Batch tracking</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">AI-Powered Reordering</h3>
                    <p class="text-gray-600 mb-4">Smart reorder predictions based on sales trends and seasonal patterns to prevent stockouts.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> AI reorder predictions</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Smart suggestions</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Supplier integration</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Warehouse Management</h3>
                    <p class="text-gray-600 mb-4">Multi-location inventory with transfer tracking and location-based reporting.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Multiple locations</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Stock transfers</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Location reports</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Supplier Management</h3>
                    <p class="text-gray-600 mb-4">Comprehensive supplier database with performance tracking and payment terms.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Supplier profiles</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Performance metrics</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Payment terms</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-light-blue rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Cost Tracking</h3>
                    <p class="text-gray-600 mb-4">Advanced costing methods including FIFO, LIFO, and weighted average calculations.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> FIFO/LIFO costing</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Weighted average</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Cost analysis</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-lavender rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 6l2.586-2.586A2 2 0 0018 15.586V10.414a2 2 0 00-.586-1.414L16 7.586A2 2 0 0014.586 7H9.414a2 2 0 00-1.414.586L6.586 9A2 2 0 006 10.414v5.172a2 2 0 00.586 1.414L8 18.414A2 2 0 009.414 19h5.172a2 2 0 001.414-.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Inventory Reports</h3>
                    <p class="text-gray-600 mb-4">Detailed inventory reports including valuation, movement, and aging analysis.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Valuation reports</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Movement tracking</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Aging analysis</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- CRM Features -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-brand-purple rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Customer Relationship Management</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Build stronger customer relationships with comprehensive CRM tools and sales pipeline management - <span class="text-brand-purple font-semibold">affordable solutions for every business size</span>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Customer Profiles</h3>
                    <p class="text-gray-600 mb-4">Detailed customer information with contact history and purchase behavior tracking.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Complete profiles</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Contact history</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Purchase tracking</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Sales Pipeline</h3>
                    <p class="text-gray-600 mb-4">Visual sales pipeline with deal tracking and conversion analytics.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Visual pipeline</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Deal tracking</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Conversion metrics</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Lead Management</h3>
                    <p class="text-gray-600 mb-4">Capture and nurture leads with automated follow-ups and scoring systems.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Lead capture</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto follow-ups</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Lead scoring</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- POS Features -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-brand-green rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Point of Sale System</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Modern POS system with offline capability, mobile support, and seamless integration with your inventory - <span class="text-brand-green font-semibold">works even when internet is down</span>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Quick Checkout</h3>
                    <p class="text-gray-600 mb-4">Fast and intuitive checkout process with barcode scanning and multiple payment options.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Barcode scanning</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Multiple payments</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Quick shortcuts</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Offline Mode</h3>
                    <p class="text-gray-600 mb-4">Continue selling even without internet connection with automatic sync when online.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Offline capability</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto sync</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Data backup</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Receipt Management</h3>
                    <p class="text-gray-600 mb-4">Professional receipts with customizable templates and digital delivery options.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Custom templates</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Digital receipts</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Print options</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Payroll Features -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-brand-light-blue rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Payroll Management</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Comprehensive payroll system with Nigerian tax compliance, pension integration, and automated calculations - <span class="text-brand-light-blue font-semibold">fully compliant and always accurate</span>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">PAYE Calculation</h3>
                    <p class="text-gray-600 mb-4">Automated PAYE calculations with current Nigerian tax rates and allowances.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Current tax rates</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Tax allowances</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> FIRS compliance</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Pension Integration</h3>
                    <p class="text-gray-600 mb-4">Seamless integration with pension fund administrators for automated contributions.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> PFA integration</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Auto contributions</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Compliance reports</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Payslip Generation</h3>
                    <p class="text-gray-600 mb-4">Professional payslips with detailed breakdowns and digital delivery options.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Detailed breakdowns</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Digital delivery</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Custom templates</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-purple rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Employee Self-Service</h3>
                    <p class="text-gray-600 mb-4">Employee portal for accessing payslips, leave requests, and personal information updates.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Payslip access</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Leave requests</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Profile updates</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-lavender rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Statutory Deductions</h3>
                    <p class="text-gray-600 mb-4">Automated calculation of all statutory deductions including NHF, NSITF, and ITF.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> NHF deductions</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> NSITF contributions</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> ITF levy</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-violet rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Payroll Reports</h3>
                    <p class="text-gray-600 mb-4">Comprehensive payroll reports for management and regulatory compliance.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Management reports</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Tax reports</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Audit trails</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Reporting Features -->
        <div class="mb-20">
            <div class="text-center mb-12">
                <div class="w-20 h-20 bg-brand-violet rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Advanced Reporting & Analytics</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Powerful reporting tools with real-time dashboards and customizable analytics for data-driven decisions - <span class="text-brand-violet font-semibold">insights available whenever you need them</span>.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-blue rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Real-time Dashboards</h3>
                    <p class="text-gray-600 mb-4">Interactive dashboards with key performance indicators and business metrics.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> KPI tracking</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Interactive charts</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Custom widgets</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-teal rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Financial Reports</h3>
                    <p class="text-gray-600 mb-4">Comprehensive financial reporting including trial balance, aged receivables, and cash flow.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Trial balance</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Aged receivables</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Cash flow analysis</li>
                    </ul>
                </div>

                <div class="feature-card bg-white rounded-xl p-8 hover:shadow-brand">
                    <div class="w-12 h-12 bg-brand-green rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V4a1 1 0 011-1h3a1 1 0 001-1v1z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Custom Reports</h3>
                    <p class="text-gray-600 mb-4">Build custom reports with drag-and-drop report builder and advanced filtering options.</p>
                    <ul class="text-sm text-gray-500 space-y-2">
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Report builder</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Advanced filters</li>
                        <li class="flex items-center"><span class="text-brand-green mr-2">✓</span> Scheduled reports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Availability & Affordability Promise Section -->
<section class="gradient-bg-2 text-white section-spacing">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Our Promise: <span class="text-brand-gold">Availability & Affordability</span>
            </h2>
            <p class="text-xl text-gray-200 max-w-3xl mx-auto">
                Every feature you see here is designed with Nigerian businesses in mind - always accessible, always affordable.
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
                        <h3 class="text-2xl font-bold text-white mb-3">24/7 Availability</h3>
                        <p class="text-gray-200 text-lg">All these powerful features are available round the clock. Cloud-based infrastructure ensures 99.9% uptime so your business never stops.</p>
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
                        <p class="text-gray-200 text-lg">Enterprise-grade features at small business prices. Starting from just ₦5,000/month with no hidden costs or setup fees.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-sm p-8 rounded-2xl">
                <div class="text-center">
                    <div class="text-5xl font-bold text-brand-gold mb-4">₦5,000</div>
                    <div class="text-xl text-gray-200 mb-6">per month</div>
                    <div class="text-left space-y-3">
                        <div class="flex items-center text-gray-200">
                            <span class="text-brand-gold mr-3 text-xl">✓</span>
                            <span>All accounting features</span>
                        </div>
                        <div class="flex items-center text-gray-200">
                            <span class="text-brand-gold mr-3 text-xl">✓</span>
                            <span>Complete inventory management</span>
                        </div>
                        <div class="flex items-center text-gray-200">
                            <span class="text-brand-gold mr-3 text-xl">✓</span>
                            <span>Full CRM capabilities</span>
                        </div>
                        <div class="flex items-center text-gray-200">
                            <span class="text-brand-gold mr-3 text-xl">✓</span>
                            <span>POS system with offline mode</span>
                        </div>
                        <div class="flex items-center text-gray-200">
                            <span class="text-brand-gold mr-3 text-xl">✓</span>
                            <span>Nigerian-compliant payroll</span>
                        </div>
                        <div class="flex items-center text-gray-200">
                            <span class="text-brand-gold mr-3 text-xl">✓</span>
                            <span>Advanced reporting & analytics</span>
                        </div>
                        <div class="flex items-center text-gray-200">
                            <span class="text-brand-gold mr-3 text-xl">✓</span>
                            <span>24/7 customer support</span>
                        </div>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('pricing') }}" class="bg-brand-gold text-gray-900 px-8 py-4 rounded-lg hover:bg-yellow-400 font-semibold text-lg transition-colors inline-block">
                            View All Plans
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Integration Section -->
<section class="section-spacing bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Seamless Integrations</h2>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Connect Ballie with your favorite Nigerian services and international tools for a complete business ecosystem.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
            <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-brand-blue rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">Nigerian Banks</p>
                <p class="text-xs text-gray-500 mt-1">GTB, Zenith, UBA, etc.</p>
            </div>

            <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-brand-teal rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">Payment Gateways</p>
                <p class="text-xs text-gray-500 mt-1">Paystack, Flutterwave</p>
            </div>

            <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-brand-green rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">Pension Funds</p>
                <p class="text-xs text-gray-500 mt-1">All Nigerian PFAs</p>
            </div>

            <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-brand-purple rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">REST APIs</p>
                <p class="text-xs text-gray-500 mt-1">Custom integrations</p>
            </div>

            <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-brand-light-blue rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">SMS Services</p>
                <p class="text-xs text-gray-500 mt-1">Bulk SMS providers</p>
            </div>

            <div class="bg-white rounded-xl p-6 text-center shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-brand-lavender rounded-lg mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-900">Email Services</p>
                <p class="text-xs text-gray-500 mt-1">SMTP & cloud email</p>
            </div>
        </div>

        <div class="text-center mt-12">
            <p class="text-gray-600 mb-6">Need a custom integration? Our API makes it possible.</p>
            <a href="{{ route('contact') }}" class="bg-brand-blue text-white px-6 py-3 rounded-lg hover:opacity-90 transition-opacity font-medium">
                Contact Our Integration Team
            </a>
        </div>
    </div>
</section>

@include('cta')

<script>
// Carousel functionality
let slideIndex = 1;
let slideInterval;

function showSlides(n) {
    let slides = document.getElementsByClassName("carousel-slide");
    let indicators = document.getElementsByClassName("carousel-indicator");

    if (n > slides.length) { slideIndex = 1 }
    if (n < 1) { slideIndex = slides.length }

    for (let i = 0; i < slides.length; i++) {
        slides[i].classList.remove("active");
    }

    for (let i = 0; i < indicators.length; i++) {
        indicators[i].classList.remove("active");
    }

    if (slides[slideIndex - 1]) {
        slides[slideIndex - 1].classList.add("active");
    }
    if (indicators[slideIndex - 1]) {
        indicators[slideIndex - 1].classList.add("active");
    }
}

function plusSlides(n) {
    clearInterval(slideInterval);
    showSlides(slideIndex += n);
    startAutoSlide();
}

function currentSlide(n) {
    clearInterval(slideInterval);
    showSlides(slideIndex = n);
    startAutoSlide();
}

function startAutoSlide() {
    slideInterval = setInterval(function() {
        slideIndex++;
        showSlides(slideIndex);
    }, 5000); // Change slide every 5 seconds
}

// Initialize carousel
document.addEventListener('DOMContentLoaded', function() {
    showSlides(slideIndex);
    startAutoSlide();
});

// Feature card hover effects
document.addEventListener('DOMContentLoaded', function() {
    const featureCards = document.querySelectorAll('.feature-card');

    featureCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
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
    .hover\:bg-brand-gold:hover { background-color: var(--color-gold); }
    .hover\:text-brand-blue:hover { color: var(--color-blue); }
    .hover\:text-brand-purple:hover { color: var(--color-purple); }

    .gradient-bg {
        background: linear-gradient(135deg, var(--color-blue) 0%, var(--color-dark-purple) 50%, var(--color-deep-purple) 100%);
    }

    .gradient-bg-2 {
        background: linear-gradient(135deg, var(--color-dark-purple) 0%, var(--color-violet) 50%, var(--color-deep-purple) 100%);
    }

    .section-spacing {
        padding: 5rem 0;
    }

    .feature-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }

    .feature-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .hover\:shadow-brand:hover {
        box-shadow: 0 20px 25px -5px rgba(43, 99, 153, 0.1), 0 10px 10px -5px rgba(43, 99, 153, 0.04);
    }

    /* Carousel styles */
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
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .carousel-indicators {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 2rem;
    }

    .carousel-indicator {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .carousel-indicator.active {
        background-color: var(--color-gold);
        transform: scale(1.2);
    }

    .carousel-indicator:hover {
        background-color: rgba(255, 255, 255, 0.7);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .section-spacing {
            padding: 3rem 0;
        }

        .feature-card {
            padding: 1.5rem;
        }
    }
</style>
@endsection
