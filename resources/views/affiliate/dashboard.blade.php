@extends('layouts.app')

@section('title', 'Affiliate Dashboard - Ballie')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->first_name }}!</h1>
                    <p class="text-blue-100">Here's your affiliate performance overview</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl text-sm font-medium">
                        Status:
                        @if($affiliate->status === 'active')
                            <span class="ml-2 px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold">ACTIVE</span>
                        @elseif($affiliate->status === 'pending')
                            <span class="ml-2 px-3 py-1 bg-yellow-500 text-white rounded-full text-xs font-bold">PENDING APPROVAL</span>
                        @else
                            <span class="ml-2 px-3 py-1 bg-red-500 text-white rounded-full text-xs font-bold">{{ strtoupper($affiliate->status) }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Earned -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">TOTAL EARNED</span>
                </div>
                <div class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_earned'], 2) }}</div>
                <p class="text-sm text-gray-500 mt-2">Lifetime earnings</p>
            </div>

            <!-- Total Paid -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">PAID OUT</span>
                </div>
                <div class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['total_paid'], 2) }}</div>
                <p class="text-sm text-gray-500 mt-2">Successfully withdrawn</p>
            </div>

            <!-- Pending Balance -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-yellow-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">PENDING</span>
                </div>
                <div class="text-3xl font-bold text-gray-900">₦{{ number_format($stats['pending_commissions'], 2) }}</div>
                <p class="text-sm text-gray-500 mt-2">Available for withdrawal</p>
            </div>

            <!-- Total Referrals -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">REFERRALS</span>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total_referrals'] }}</div>
                <p class="text-sm text-gray-500 mt-2">
                    <span class="text-green-600 font-medium">{{ $stats['confirmed_referrals'] }}</span> confirmed,
                    <span class="text-yellow-600 font-medium">{{ $stats['pending_referrals'] }}</span> pending
                </p>
            </div>
        </div>

        <!-- Referral Link Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl p-8 mb-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-6 md:mb-0 md:mr-8">
                    <h2 class="text-2xl font-bold mb-2">Your Referral Link</h2>
                    <p class="text-blue-100">Share this link to start earning commissions</p>
                </div>
                <div class="flex-1 max-w-2xl">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center">
                            <input type="text" id="referral-link" readonly
                                value="{{ $affiliate->getReferralLink('register') }}"
                                class="flex-1 bg-transparent text-white placeholder-blue-200 border-none focus:ring-0 text-sm">
                            <button onclick="copyReferralLink()"
                                class="ml-4 px-4 py-2 bg-white text-blue-600 rounded-lg font-medium hover:bg-blue-50 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copy
                            </button>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-sm text-blue-100">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Your affiliate code: <strong class="ml-1">{{ $affiliate->affiliate_code }}</strong>
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
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('affiliate.referrals') }}"
                       class="block p-4 border-2 border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-lg group-hover:bg-blue-200 transition-colors">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">View Referrals</div>
                                    <div class="text-sm text-gray-500">{{ $stats['total_referrals'] }} total referrals</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('affiliate.commissions') }}"
                       class="block p-4 border-2 border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg group-hover:bg-green-200 transition-colors">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Commission History</div>
                                    <div class="text-sm text-gray-500">View all earnings</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('affiliate.payouts') }}"
                       class="block p-4 border-2 border-gray-200 rounded-xl hover:border-yellow-500 hover:bg-yellow-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-yellow-100 p-2 rounded-lg group-hover:bg-yellow-200 transition-colors">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Request Payout</div>
                                    <div class="text-sm text-gray-500">₦{{ number_format($stats['pending_commissions'], 2) }} available</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>

                    <a href="{{ route('affiliate.settings') }}"
                       class="block p-4 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-lg group-hover:bg-purple-200 transition-colors">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Account Settings</div>
                                    <div class="text-sm text-gray-500">Update your information</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
