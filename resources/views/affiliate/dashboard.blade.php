@extends('layouts.affiliate')

@section('title', 'Affiliate Dashboard - Ballie')

@section('affiliate-content')
    <!-- Header -->
    <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 text-white py-12 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:20px_20px]"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold tracking-tight">Welcome back, {{ Auth::user()->first_name }}!</h1>
                            <p class="text-blue-100 mt-1 text-lg">Here's your affiliate performance overview</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 md:mt-0">
                    <div class="bg-white/20 backdrop-blur-md border border-white/30 rounded-2xl px-6 py-4 shadow-xl">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col">
                                <span class="text-xs text-blue-100 font-medium uppercase tracking-wider">Account Status</span>
                                @if($affiliate->status === 'active')
                                    <span class="mt-2 px-4 py-2 bg-green-500 text-white rounded-xl text-sm font-bold shadow-lg inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        ACTIVE
                                    </span>
                                @elseif($affiliate->status === 'pending')
                                    <span class="mt-2 px-4 py-2 bg-yellow-500 text-white rounded-xl text-sm font-bold shadow-lg inline-flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        PENDING APPROVAL
                                    </span>
                                @else
                                    <span class="mt-2 px-4 py-2 bg-red-500 text-white rounded-xl text-sm font-bold shadow-lg">{{ strtoupper($affiliate->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Earned -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-blue-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Earned</span>
                </div>
                <div class="text-3xl font-extrabold text-gray-900 mb-1">₦{{ number_format($stats['total_earned'], 2) }}</div>
                <p class="text-sm text-gray-500 flex items-center gap-1">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Lifetime earnings
                </p>
            </div>

            <!-- Total Paid -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-green-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Paid Out</span>
                </div>
                <div class="text-3xl font-extrabold text-gray-900 mb-1">₦{{ number_format($stats['total_paid'], 2) }}</div>
                <p class="text-sm text-gray-500 flex items-center gap-1">
                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Successfully withdrawn
                </p>
            </div>

            <!-- Pending Balance -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-yellow-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Pending</span>
                </div>
                <div class="text-3xl font-extrabold text-gray-900 mb-1">₦{{ number_format($stats['pending_commissions'], 2) }}</div>
                <p class="text-sm text-gray-500 flex items-center gap-1">
                    <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Available for withdrawal
                </p>
            </div>

            <!-- Total Referrals -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-purple-500 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Referrals</span>
                </div>
                <div class="text-3xl font-extrabold text-gray-900 mb-1">{{ $stats['total_referrals'] }}</div>
                <div class="flex items-center gap-3 text-sm">
                    <span class="inline-flex items-center gap-1 text-green-600 font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $stats['confirmed_referrals'] }}
                    </span>
                    <span class="inline-flex items-center gap-1 text-yellow-600 font-semibold">
                        <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $stats['pending_referrals'] }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Referral Link Section -->
        <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-3xl shadow-2xl p-8 mb-8 text-white relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl"></div>

            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="mb-6 md:mb-0 md:mr-8">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold">Your Referral Link</h2>
                        </div>
                        <p class="text-blue-100">Share this link to start earning commissions on every successful referral</p>
                    </div>
                </div>

                <div class="bg-white/15 backdrop-blur-md rounded-2xl p-5 border border-white/30 shadow-xl">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <div class="flex-1">
                            <label class="text-xs text-blue-100 font-medium mb-2 block">Your unique affiliate link</label>
                            <input type="text" id="referral-link" readonly
                                value="{{ $affiliate->getReferralLink('register') }}"
                                class="w-full bg-white/10 text-white placeholder-blue-200 border border-white/20 rounded-xl px-4 py-3 focus:ring-2 focus:ring-white/50 focus:outline-none text-sm font-mono">
                        </div>
                        <button onclick="copyReferralLink()"
                            class="px-6 py-3 bg-white text-blue-600 rounded-xl font-bold hover:bg-blue-50 transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Copy Link</span>
                        </button>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm text-blue-100 bg-white/10 rounded-lg px-4 py-2 border border-white/20">
                        <svg class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Your affiliate code: <strong class="ml-1 text-white font-bold">{{ $affiliate->affiliate_code }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Earnings Chart -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Last 6 Months Earnings
                </h3>
                <div class="space-y-3">
                    @foreach($monthlyEarnings as $earning)
                        <div class="flex items-center">
                            <div class="w-24 text-sm text-gray-600 font-medium">{{ $earning['month'] }}</div>
                            <div class="flex-1 mx-4">
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    @php
                                        $maxEarning = collect($monthlyEarnings)->max('amount');
                                        $percentage = $maxEarning > 0 ? ($earning['amount'] / $maxEarning) * 100 : 0;
                                    @endphp
                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all"
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <div class="w-28 text-right text-sm font-semibold text-gray-900">
                                ₦{{ number_format($earning['amount'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">This Month</span>
                        <span class="text-lg font-bold text-blue-600">₦{{ number_format($stats['this_month_earnings'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-2 rounded-xl mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('affiliate.referrals') }}"
                       class="block p-4 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-xl group-hover:bg-blue-200 group-hover:scale-110 transition-all duration-200">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="font-bold text-gray-900 group-hover:text-blue-700">View Referrals</div>
                                    <div class="text-sm text-gray-500">{{ $stats['total_referrals'] }} total referrals</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('affiliate.commissions') }}"
                       class="block p-4 border-2 border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-xl group-hover:bg-green-200 group-hover:scale-110 transition-all duration-200">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="font-bold text-gray-900 group-hover:text-green-700">Commission History</div>
                                    <div class="text-sm text-gray-500">View all earnings</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('affiliate.payouts') }}"
                       class="block p-4 border-2 border-gray-200 rounded-xl hover:border-yellow-500 hover:bg-yellow-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-yellow-100 p-3 rounded-xl group-hover:bg-yellow-200 group-hover:scale-110 transition-all duration-200">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="font-bold text-gray-900 group-hover:text-yellow-700">Request Payout</div>
                                    <div class="text-sm text-gray-500">₦{{ number_format($stats['pending_commissions'], 2) }} available</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('affiliate.settings') }}"
                       class="block p-4 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-3 rounded-xl group-hover:bg-purple-200 group-hover:scale-110 transition-all duration-200">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="font-bold text-gray-900 group-hover:text-purple-700">Account Settings</div>
                                    <div class="text-sm text-gray-500">Update your information</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Referrals -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Recent Referrals</h3>
                    <a href="{{ route('affiliate.referrals') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                </div>
                @if($recentReferrals->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentReferrals as $referral)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $referral->tenant->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $referral->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($referral->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($referral->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($referral->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p>No referrals yet</p>
                        <p class="text-sm mt-1">Share your link to start earning!</p>
                    </div>
                @endif
            </div>

            <!-- Recent Commissions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Recent Commissions</h3>
                    <a href="{{ route('affiliate.commissions') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                </div>
                @if($recentCommissions->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentCommissions as $commission)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $commission->tenant->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $commission->payment_date->format('M d, Y') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-green-600">₦{{ number_format($commission->commission_amount, 2) }}</div>
                                    <span class="text-xs text-gray-500">{{ $commission->commission_rate }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p>No commissions yet</p>
                        <p class="text-sm mt-1">Earn when your referrals subscribe!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .bg-grid-white\/\[0\.05\] {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(255 255 255 / 0.05)'%3e%3cpath d='M0 .5H31.5V32'/%3e%3c/svg%3e");
    }
</style>

<script>
function copyReferralLink() {
    const linkInput = document.getElementById('referral-link');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices

    navigator.clipboard.writeText(linkInput.value).then(() => {
        // Show success message
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Copied!
        `;
        button.classList.add('bg-green-500', 'text-white');
        button.classList.remove('bg-white', 'text-blue-600');

        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-500', 'text-white');
            button.classList.add('bg-white', 'text-blue-600');
        }, 2000);
    });
}
</script>
@endsection
