@extends('layouts.super-admin')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div id="welcomeHeader" class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 rounded-2xl shadow-xl overflow-hidden transition-all duration-1000 ease-in-out" style="display: none; opacity: 0;">
        <div class="px-8 py-10 text-white relative">
            <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.5,81.4,29,73.1,41.9C64.8,54.8,54.8,66.1,42.4,74.5C30,82.9,15,88.4,-0.1,88.5C-15.3,88.6,-30.6,83.2,-43.5,75.1C-56.4,67,-66.9,56.2,-74.1,43.6C-81.3,31,-85.2,15.5,-83.7,0.7C-82.2,-14.1,-75.3,-28.2,-66.8,-40.5C-58.3,-52.8,-48.2,-63.3,-36.4,-71.2C-24.6,-79.1,-12.3,-84.4,1.4,-86.8C15.1,-89.2,30.3,-88.7,44.7,-76.4Z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Welcome back, {{ auth('super_admin')->user()->name }}!</h1>
                <p class="text-lg text-blue-100 mb-4">Here's what's happening with your system today</p>
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center">
                        <div class="w-2.5 h-2.5 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                        <span>System Online</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ now()->format('M j, Y g:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        {{-- Total Companies --}}
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Companies</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_tenants']) }}</p>
                        <p class="text-xs mt-1 {{ $stats['tenant_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($stats['tenant_growth'] >= 0)
                                <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            @else
                                <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                            @endif
                            {{ $stats['tenant_growth'] >= 0 ? '+' : '' }}{{ $stats['tenant_growth'] }}% this month
                        </p>
                    </div>
                    <div class="p-2.5 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-1.5"></div>
        </div>

        {{-- Active Companies --}}
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Active</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['active_tenants']) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            {{ number_format(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100, 1) }}% active rate
                        </p>
                    </div>
                    <div class="p-2.5 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 h-1.5"></div>
        </div>

        {{-- Trial Companies --}}
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">On Trial</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['trial_tenants']) }}</p>
                        <p class="text-xs text-yellow-600 mt-1">
                            {{ number_format(($stats['trial_tenants'] / max($stats['total_tenants'], 1)) * 100, 1) }}% of total
                        </p>
                    </div>
                    <div class="p-2.5 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-1.5"></div>
        </div>

        {{-- Monthly Revenue --}}
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Revenue (MTD)</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₦{{ number_format($stats['monthly_revenue'], 0) }}</p>
                        <p class="text-xs mt-1 {{ $stats['revenue_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            @if($stats['revenue_growth'] >= 0)
                                <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            @else
                                <svg class="w-3.5 h-3.5 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                            @endif
                            {{ $stats['revenue_growth'] >= 0 ? '+' : '' }}{{ $stats['revenue_growth'] }}% vs last month
                        </p>
                    </div>
                    <div class="p-2.5 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-1.5"></div>
        </div>

        {{-- Total Users --}}
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-xs text-indigo-600 mt-1">
                            Across all companies
                        </p>
                    </div>
                    <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-1.5"></div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Tenant Growth Chart (line) --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Company Registrations</h3>
                    </div>
                    <span class="text-xs text-gray-500">Last 12 months</span>
                </div>
            </div>
            <div class="p-5">
                <canvas id="tenantGrowthChart" height="120"></canvas>
            </div>
        </div>

        {{-- Subscription Status Doughnut --}}
        <div class="bg-white rounded-2xl shadow-lg">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-gradient-to-br from-green-500 to-green-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Subscription Status</h3>
                </div>
            </div>
            <div class="p-5 flex items-center justify-center">
                <div class="w-full max-w-[220px]">
                    <canvas id="statusDoughnutChart"></canvas>
                </div>
            </div>
            <div class="px-5 pb-5">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    @foreach($statusDistribution as $status => $count)
                    <div class="flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full mr-2
                            @if($status === 'active') bg-green-500
                            @elseif($status === 'trial') bg-yellow-500
                            @elseif($status === 'suspended') bg-red-500
                            @elseif($status === 'expired') bg-gray-500
                            @else bg-orange-500 @endif"></span>
                        <span class="text-gray-600">{{ ucfirst($status) }}: <strong class="text-gray-900">{{ $count }}</strong></span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Chart + Plan Distribution --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Revenue Trend (bar chart) --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Revenue Trend</h3>
                    </div>
                    <span class="text-xs text-gray-500">Last 6 months</span>
                </div>
            </div>
            <div class="p-5">
                <canvas id="revenueChart" height="120"></canvas>
            </div>
        </div>

        {{-- Plan Distribution --}}
        <div class="bg-white rounded-2xl shadow-lg">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Plan Distribution</h3>
                </div>
            </div>
            <div class="p-5">
                @if($subscriptionStats->count() > 0)
                    <div class="space-y-4">
                        @php $planTotal = $subscriptionStats->sum('count'); @endphp
                        @foreach($subscriptionStats as $plan)
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-sm font-semibold text-gray-900">{{ $plan->plan_name }}</span>
                                <span class="text-sm font-bold text-gray-700">{{ $plan->count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full
                                    @if($plan->plan_slug === 'starter') bg-blue-500
                                    @elseif($plan->plan_slug === 'professional') bg-green-500
                                    @elseif($plan->plan_slug === 'enterprise') bg-purple-500
                                    @else bg-indigo-500 @endif"
                                    style="width: {{ $planTotal > 0 ? round(($plan->count / $planTotal) * 100) : 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $planTotal > 0 ? round(($plan->count / $planTotal) * 100, 1) : 0 }}% of paid tenants</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <p class="text-sm text-gray-500">No plan data yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Tenants + Support & Expiring --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Recent Companies --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg">
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Recent Companies</h3>
                    </div>
                    <a href="{{ route('super-admin.tenants.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                        View All &rarr;
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentTenants as $tenant)
                <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition-colors duration-150">
                    <div class="flex items-center min-w-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($tenant->name, 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $tenant->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $tenant->email }} &middot; {{ optional($tenant->plan)->name ?? 'No plan' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                            @if($tenant->subscription_status === 'active') bg-green-100 text-green-800
                            @elseif($tenant->subscription_status === 'trial') bg-yellow-100 text-yellow-800
                            @elseif($tenant->subscription_status === 'suspended') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($tenant->subscription_status ?? 'N/A') }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $tenant->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-10">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <p class="text-sm text-gray-500 font-medium">No companies yet</p>
                    <a href="{{ route('super-admin.tenants.create') }}" class="text-sm text-indigo-600 hover:underline mt-1 inline-block">Create your first &rarr;</a>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Right Column: Support + Expiring --}}
        <div class="space-y-6">
            {{-- Support Tickets --}}
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="p-5 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-2 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900">Support</h3>
                        </div>
                        <a href="{{ route('super-admin.support.index') }}" class="text-xs font-medium text-orange-600 hover:text-orange-800">View All &rarr;</a>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-red-50 rounded-xl p-3">
                            <p class="text-xl font-bold text-red-700">{{ $ticketStats['open'] }}</p>
                            <p class="text-xs text-red-600 mt-0.5">Open</p>
                        </div>
                        <div class="bg-yellow-50 rounded-xl p-3">
                            <p class="text-xl font-bold text-yellow-700">{{ $ticketStats['unresolved'] }}</p>
                            <p class="text-xs text-yellow-600 mt-0.5">Unresolved</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-3">
                            <p class="text-xl font-bold text-green-700">{{ $ticketStats['resolved_this_month'] }}</p>
                            <p class="text-xs text-green-600 mt-0.5">Resolved (MTD)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expiring Soon --}}
            <div class="bg-white rounded-2xl shadow-lg">
                <div class="p-5 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="p-2 bg-gradient-to-br from-red-500 to-red-600 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Expiring Soon</h3>
                    </div>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($expiringSoon as $tenant)
                    <div class="px-5 py-3 hover:bg-red-50/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $tenant->name }}</p>
                            <span class="text-xs font-semibold text-red-600 flex-shrink-0 ml-2">
                                {{ $tenant->subscription_ends_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Expires {{ $tenant->subscription_ends_at->format('M j, Y') }}</p>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <svg class="w-8 h-8 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm text-gray-500">No upcoming expirations</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-lg">
        <div class="p-5 border-b border-gray-100">
            <div class="flex items-center">
                <div class="p-2 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Quick Actions</h3>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="{{ route('super-admin.tenants.create') }}" class="group flex items-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all duration-200">
                    <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg mr-3 group-hover:shadow-md transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700">Create Tenant</p>
                        <p class="text-xs text-gray-500">Add new company</p>
                    </div>
                </a>
                <a href="{{ route('super-admin.tenants.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-xl hover:from-green-100 hover:to-emerald-100 transition-all duration-200">
                    <div class="p-2 bg-gradient-to-br from-green-500 to-green-600 rounded-lg mr-3 group-hover:shadow-md transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 group-hover:text-green-700">Companies</p>
                        <p class="text-xs text-gray-500">Manage all</p>
                    </div>
                </a>
                <a href="{{ route('super-admin.support.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-orange-50 to-yellow-50 border border-orange-100 rounded-xl hover:from-orange-100 hover:to-yellow-100 transition-all duration-200">
                    <div class="p-2 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg mr-3 group-hover:shadow-md transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700">Support</p>
                        <p class="text-xs text-gray-500">Manage tickets</p>
                    </div>
                </a>
                <a href="{{ route('super-admin.affiliates.index') }}" class="group flex items-center p-4 bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-xl hover:from-purple-100 hover:to-violet-100 transition-all duration-200">
                    <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg mr-3 group-hover:shadow-md transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 group-hover:text-purple-700">Affiliates</p>
                        <p class="text-xs text-gray-500">Manage partners</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Welcome header auto-dismiss
    const welcomeHeader = document.getElementById('welcomeHeader');
    const storageKey = 'super_admin_welcome_dismissed_{{ auth("super_admin")->id() }}';
    const sessionKey = 'super_admin_session_{{ auth("super_admin")->id() }}';
    const currentSession = sessionStorage.getItem(sessionKey);
    if (!currentSession) {
        localStorage.removeItem(storageKey);
        sessionStorage.setItem(sessionKey, 'active');
    }
    if (!localStorage.getItem(storageKey)) {
        welcomeHeader.style.display = 'block';
        setTimeout(() => { welcomeHeader.style.opacity = '1'; }, 100);
        setTimeout(() => {
            welcomeHeader.style.opacity = '0';
            setTimeout(() => {
                welcomeHeader.style.display = 'none';
                localStorage.setItem(storageKey, 'true');
            }, 1000);
        }, 10000);
    }

    // Chart defaults
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.plugins.legend.display = false;

    // Tenant Growth Chart (Line)
    const tenantCtx = document.getElementById('tenantGrowthChart');
    if (tenantCtx) {
        new Chart(tenantCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($tenantTrend->pluck('label')) !!},
                datasets: [{
                    label: 'New Companies',
                    data: {!! json_encode($tenantTrend->pluck('count')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { weight: 'bold' },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(items) {
                                const months = {!! json_encode($tenantTrend->pluck('month')) !!};
                                return months[items[0].dataIndex];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#9ca3af' },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Revenue Chart (Bar)
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($revenueTrend->pluck('label')) !!},
                datasets: [{
                    label: 'Revenue (₦)',
                    data: {!! json_encode($revenueTrend->pluck('amount')) !!},
                    backgroundColor: [
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(139, 92, 246, 0.6)',
                        'rgba(139, 92, 246, 0.7)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(139, 92, 246, 0.9)'
                    ],
                    borderColor: '#8b5cf6',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(items) {
                                const months = {!! json_encode($revenueTrend->pluck('month')) !!};
                                return months[items[0].dataIndex];
                            },
                            label: function(item) {
                                return '₦' + Number(item.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#9ca3af',
                            callback: function(value) {
                                if (value >= 1000000) return '₦' + (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return '₦' + (value / 1000).toFixed(0) + 'K';
                                return '₦' + value;
                            }
                        },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // Status Doughnut Chart
    const doughnutCtx = document.getElementById('statusDoughnutChart');
    if (doughnutCtx) {
        const statusColors = {
            active: '#22c55e',
            trial: '#eab308',
            suspended: '#ef4444',
            expired: '#6b7280',
            cancelled: '#f97316'
        };
        const statusData = {!! json_encode($statusDistribution) !!};
        const labels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1));
        const values = Object.values(statusData);
        const colors = Object.keys(statusData).map(s => statusColors[s] || '#6366f1');

        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(item) {
                                const total = item.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((item.raw / total) * 100).toFixed(1) : 0;
                                return item.label + ': ' + item.raw + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush


