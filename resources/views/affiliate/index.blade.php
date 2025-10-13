@extends('layouts.app')

@section('title', 'Affiliate Program - Ballie')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl sm:text-5xl font-bold mb-6">
                    Earn with Ballie's Affiliate Program
                </h1>
                <p class="text-xl text-blue-100 mb-8">
                    Join our affiliate program and earn {{ config('affiliate.default_commission_rate') }}% recurring commission for every business you refer to Ballie.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('affiliate.register') }}" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Join Now - It's Free
                    </a>
                    @auth
                        @if(Auth::user()->affiliate)
                            <a href="{{ route('affiliate.dashboard') }}" class="inline-flex items-center px-8 py-4 bg-transparent border-2 border-white text-white font-semibold rounded-xl hover:bg-white hover:text-blue-600 transition-all duration-200">
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

    <!-- Benefits Section -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Join Our Affiliate Program?</h2>
                <p class="text-xl text-gray-600">Earn passive income while helping Nigerian businesses succeed</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-100">
                    <div class="bg-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Recurring Commissions</h3>
                    <p class="text-gray-600 mb-4">Earn {{ config('affiliate.default_commission_rate') }}% commission not just on the first payment, but on every renewal for the lifetime of the customer.</p>
                    <div class="bg-white rounded-lg p-4 border border-blue-200">
                        <p class="text-sm text-gray-500 mb-1">Example:</p>
                        <p class="text-lg font-semibold text-blue-600">₦10,000/month × 12 months = ₦120,000/year per referral!</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-8 border border-green-100">
                    <div class="bg-green-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Easy to Promote</h3>
                    <p class="text-gray-600 mb-4">Get your unique referral link and start sharing immediately. No technical knowledge required.</p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Marketing materials provided
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Real-time tracking dashboard
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            30-day cookie tracking
                        </li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 border border-purple-100">
                    <div class="bg-purple-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Reliable Payouts</h3>
                    <p class="text-gray-600 mb-4">Get paid on time, every time with multiple payout options.</p>
                    <div class="space-y-3">
                        <div class="bg-white rounded-lg p-3 border border-purple-200 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Bank Transfer</span>
                        </div>
                        <div class="bg-white rounded-lg p-3 border border-purple-200 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">Paystack / Nomba</span>
                        </div>
                        <p class="text-xs text-gray-500 pt-2">Minimum payout: ₦{{ number_format(config('affiliate.minimum_payout'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Start earning in 3 simple steps</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="relative">
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="bg-blue-600 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl mb-6">
                            1
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Sign Up</h3>
                        <p class="text-gray-600">Create your free affiliate account in minutes. No approval waiting time with auto-approval enabled.</p>
                    </div>
                    <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2">
                        <svg class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>

                <div class="relative">
                    <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="bg-green-600 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl mb-6">
                            2
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Share Your Link</h3>
                        <p class="text-gray-600">Get your unique referral link and share it with your network via social media, email, or your website.</p>
                    </div>
                    <div class="hidden md:block absolute top-1/2 -right-4 transform -translate-y-1/2">
                        <svg class="w-8 h-8 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="bg-purple-600 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl mb-6">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Earn Commissions</h3>
                    <p class="text-gray-600">Earn {{ config('affiliate.default_commission_rate') }}% recurring commission every time your referrals make a payment. Track everything in your dashboard.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="py-16 bg-gradient-to-r from-blue-600 to-indigo-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-6">
                Ready to Start Earning?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Join hundreds of affiliates who are already earning with Ballie
            </p>
            <a href="{{ route('affiliate.register') }}" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-all duration-200 shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
                Get Started Now - It's Free
            </a>
        </div>
    </div>
</div>
@endsection
