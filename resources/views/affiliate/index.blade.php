@extends('layouts.app')

@section('title', 'Affiliate Program - Ballie')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 text-white py-24 overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-6">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    Now accepting new affiliates
                </div>
                <h1 class="text-5xl sm:text-6xl font-bold mb-6 bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                    Earn with Ballie's Affiliate Program
                </h1>
                <p class="text-xl text-blue-100 mb-10 leading-relaxed">
                    Join our affiliate program and earn <span class="font-bold text-yellow-300">{{ config('affiliate.default_commission_rate', 10) }}% recurring commission</span> on every subscription payment from businesses you refer. Promote an all-in-one business management platform - for life!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('affiliate.register') }}" class="group inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-2xl hover:bg-blue-50 transition-all duration-300 shadow-2xl hover:shadow-blue-500/25 transform hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Join Now - It's Free
                    </a>
                    @auth
                        @if(Auth::user()->affiliate)
                            <a href="{{ route('affiliate.dashboard') }}" class="inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white font-semibold rounded-2xl hover:bg-white hover:text-blue-600 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Go to Dashboard
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-16">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ config('affiliate.default_commission_rate', 10) }}%</div>
                    <div class="text-gray-600">Commission Rate</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">30</div>
                    <div class="text-gray-600">Day Cookie</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">₦0</div>
                    <div class="text-gray-600">Joining Fee</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">24/7</div>
                    <div class="text-gray-600">Support</div>
                </div>
            </div>

            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Join Our Affiliate Program?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Earn passive income while helping Nigerian businesses succeed with our all-in-one business management platform for Accounting, POS, Inventory, Payroll & more</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8">
                <div class="group bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl p-8 border border-blue-200 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 hover:-translate-y-2">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Lifetime Recurring Commissions</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">Earn {{ config('affiliate.default_commission_rate', 10) }}% commission on every subscription payment - monthly, quarterly, or yearly - from every business you refer. As long as they stay subscribed, you keep earning!</p>
                    <div class="bg-white rounded-2xl p-6 border border-blue-200 shadow-sm">
                        <p class="text-sm text-gray-500 mb-2">Example: 1 business paying ₦10,000/month</p>
                        <p class="text-2xl font-bold text-blue-600">₦12,000/year</p>
                        <p class="text-sm text-gray-500 mt-1">recurring commission - every year!</p>
                    </div>
                </div>

                <div class="group bg-gradient-to-br from-green-50 to-emerald-100 rounded-3xl p-8 border border-green-200 hover:shadow-2xl hover:shadow-green-500/10 transition-all duration-300 hover:-translate-y-2">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Easy to Promote</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">Promote a complete business solution that businesses actually need. Get your unique referral link and start sharing immediately.</p>
                    <div class="space-y-3">
                        <div class="flex items-center text-gray-700">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            All-in-one platform appeal
                        </div>
                        <div class="flex items-center text-gray-700">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            Real-time tracking dashboard
                        </div>
                        <div class="flex items-center text-gray-700">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                            30-day cookie tracking
                        </div>
                    </div>
                </div>

                <div class="group bg-gradient-to-br from-purple-50 to-pink-100 rounded-3xl p-8 border border-purple-200 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-300 hover:-translate-y-2">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Reliable Payouts</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">Get paid on time, every time with multiple secure payout options.</p>
                    <div class="space-y-3">
                        <div class="bg-white rounded-xl p-4 border border-purple-200 flex items-center shadow-sm">
                            <svg class="w-5 h-5 text-purple-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium text-gray-700">Bank Transfer</span>
                        </div>
                        <div class="bg-white rounded-xl p-4 border border-purple-200 flex items-center shadow-sm">
                            <svg class="w-5 h-5 text-purple-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                            <span class="font-medium text-gray-700">Monthly Payouts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-16 bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Start earning in just 3 simple steps</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Sign Up</h3>
                    <p class="text-gray-600">Create your free affiliate account and get approved instantly</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Share</h3>
                    <p class="text-gray-600">Share your unique referral link with businesses that need accounting, POS, inventory, payroll, or complete business management</p>
                </div>
                <div class="text-center">
                    <div class="bg-purple-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Earn Forever</h3>
                    <p class="text-gray-600">Get paid {{ config('affiliate.default_commission_rate', 10) }}% on every subscription renewal from your referrals</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-20 bg-gradient-to-r from-blue-600 to-purple-700">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-white mb-6">Ready to Build Passive Income?</h2>
            <p class="text-xl text-blue-100 mb-10">Help businesses streamline their operations with Ballie's Accounting, POS, Inventory, Payroll & Business Management tools while earning recurring commissions</p>
            <a href="{{ route('affiliate.register') }}" class="inline-flex items-center px-10 py-5 bg-white text-blue-600 font-bold text-lg rounded-2xl hover:bg-blue-50 transition-all duration-300 shadow-2xl hover:shadow-white/25 transform hover:-translate-y-1">
                Get Started Today
                <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
