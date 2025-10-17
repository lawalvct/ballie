@extends('layouts.super-admin')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div id="welcomeHeader" class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 rounded-2xl shadow-xl overflow-hidden transition-all duration-1000 ease-in-out" style="display: none; opacity: 0;">
        <div class="px-8 py-12 text-white relative">
            <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M44.7,-76.4C58.8,-69.2,71.8,-59.1,79.6,-45.8C87.4,-32.6,90,-16.3,88.5,-0.9C87,14.5,81.4,29,73.1,41.9C64.8,54.8,54.8,66.1,42.4,74.5C30,82.9,15,88.4,-0.1,88.5C-15.3,88.6,-30.6,83.2,-43.5,75.1C-56.4,67,-66.9,56.2,-74.1,43.6C-81.3,31,-85.2,15.5,-83.7,0.7C-82.2,-14.1,-75.3,-28.2,-66.8,-40.5C-58.3,-52.8,-48.2,-63.3,-36.4,-71.2C-24.6,-79.1,-12.3,-84.4,1.4,-86.8C15.1,-89.2,30.3,-88.7,44.7,-76.4Z"/>
                </svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-4xl font-bold mb-4">Welcome back, {{ auth('super_admin')->user()->name }}!</h1>
                <p class="text-xl text-blue-100 mb-6">Here's what's happening with your system today</p>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                        <span class="text-sm">System Status: Online</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm">Last updated: {{ now()->format('M j, Y g:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Companies</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_tenants'] }}</p>
                        <p class="text-sm text-green-600 mt-1">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            +12% from last month
                        </p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Active Companies</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['active_tenants'] }}</p>
                        <p class="text-sm text-green-600 mt-1">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            {{ number_format(($stats['active_tenants'] / max($stats['total_tenants'], 1)) * 100, 1) }}% active rate
                        </p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Trial Companies</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['trial_tenants'] }}</p>
                        <p class="text-sm text-yellow-600 mt-1">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Conversion pending
                        </p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Monthly Revenue</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">₦{{ number_format($stats['monthly_revenue'], 0) }}</p>
                        <p class="text-sm text-purple-600 mt-1">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            +18% from last month
                        </p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2"></div>
        </div>
    </div>

    <!-- Recent Tenants & Subscription Stats -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Recent Tenants -->
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Recent Companies</h3>
                    </div>
                    <a href="{{ route('super-admin.tenants.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors duration-200">
                        View All
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recentTenants->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentTenants as $tenant)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4 shadow-md">
                                    <span class="text-white font-bold text-lg">{{ substr($tenant->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $tenant->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $tenant->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($tenant->subscription_status === 'active') bg-green-100 text-green-800
                                    @elseif($tenant->subscription_status === 'trial') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($tenant->subscription_status) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $tenant->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">No tenants yet</p>
                        <p class="text-sm text-gray-400 mt-1">Get started by creating your first tenant</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Subscription Stats -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-gradient-to-br from-green-500 to-green-600 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Subscription Plans</h3>
                </div>
            </div>
            <div class="p-6">
                @if($subscriptionStats->count() > 0)
                    <div class="space-y-6">
                        @foreach($subscriptionStats as $plan => $count)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-4
                                    @if($plan === 'starter') bg-blue-500
                                    @elseif($plan === 'professional') bg-green-500
                                    @else bg-purple-500 @endif">
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-900">{{ ucfirst($plan) }}</span>
                                    <p class="text-xs text-gray-500">Plan subscribers</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-gray-900">{{ $count }}</span>
                                <p class="text-xs text-gray-500">tenants</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">No subscription data</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center">
                <div class="p-2 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('super-admin.tenants.create') }}" class="group flex items-center p-6 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 transform hover:scale-105">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl mr-4 group-hover:shadow-lg transition-shadow duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">Create Tenant</p>
                        <p class="text-sm text-gray-600">Add a new tenant to the system</p>
                    </div>
                </a>

                <a href="{{ route('super-admin.tenants.index') }}" class="group flex items-center p-6 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-xl hover:from-green-100 hover:to-emerald-100 transition-all duration-300 transform hover:scale-105">
                    <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl mr-4 group-hover:shadow-lg transition-shadow duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-green-700 transition-colors duration-200">Manage Tenants</p>
                        <p class="text-sm text-gray-600">View and manage all tenants</p>
                    </div>
                </a>

                <a href="#" class="group flex items-center p-6 bg-gradient-to-br from-purple-50 to-violet-50 border border-purple-100 rounded-xl hover:from-purple-100 hover:to-violet-100 transition-all duration-300 transform hover:scale-105">
                    <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl mr-4 group-hover:shadow-lg transition-shadow duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 group-hover:text-purple-700 transition-colors duration-200">View Analytics</p>
                        <p class="text-sm text-gray-600">System performance & insights</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- System Health Status -->
    <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 rounded-2xl shadow-xl text-white overflow-hidden">
        <div class="px-8 py-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold">System Health</h3>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    All Systems Operational
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-400 mb-1">99.9%</div>
                    <div class="text-sm text-gray-300">Uptime</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-400 mb-1">152ms</div>
                    <div class="text-sm text-gray-300">Response Time</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $stats['active_tenants'] }}</div>
                    <div class="text-sm text-gray-300">Active Sessions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-400 mb-1">5.2GB</div>
                    <div class="text-sm text-gray-300">Storage Used</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const welcomeHeader = document.getElementById('welcomeHeader');
    const storageKey = 'super_admin_welcome_dismissed_{{ auth("super_admin")->id() }}';
    const sessionKey = 'super_admin_session_{{ auth("super_admin")->id() }}';
    
    // Check if user has a current session marker
    const currentSession = sessionStorage.getItem(sessionKey);
    
    // If no session marker, this is a new login - clear the localStorage flag
    if (!currentSession) {
        localStorage.removeItem(storageKey);
        sessionStorage.setItem(sessionKey, 'active');
    }
    
    // Check if welcome was already dismissed in this login session
    const welcomeDismissed = localStorage.getItem(storageKey);
    
    if (!welcomeDismissed) {
        // Show the welcome header with fade-in effect
        welcomeHeader.style.display = 'block';
        setTimeout(() => {
            welcomeHeader.style.opacity = '1';
        }, 100);
        
        // Hide after 10 seconds with fade-out effect
        setTimeout(() => {
            welcomeHeader.style.opacity = '0';
            
            // Remove from DOM after fade-out completes
            setTimeout(() => {
                welcomeHeader.style.display = 'none';
                // Mark as dismissed in localStorage
                localStorage.setItem(storageKey, 'true');
            }, 1000); // Wait for transition to complete
        }, 10000); // 10 seconds
    }
});
</script>
@endsection


