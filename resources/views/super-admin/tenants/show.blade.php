@extends('layouts.super-admin')

@section('title', 'Company Details - ' . $tenant->name)
@section('page-title', 'Company Details')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12 mr-4">
                        @if($tenant->logo)
                            <img class="h-12 w-12 rounded-lg border border-gray-200" src="{{ $tenant->logo }}" alt="{{ $tenant->name }}">
                        @else
                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-md">
                                <span class="text-lg font-bold text-white">{{ substr($tenant->name, 0, 2) }}</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h1>
                        <p class="text-sm text-gray-600">{{ $tenant->email }} • Created {{ $tenant->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    @php
                        $owner = $tenantUsers->first(fn($u) => $u->membership_role === 'owner' || strtolower($u->membership_role_label) === 'owner');
                    @endphp
                    @if($owner)
                    <button onclick="impersonateOwner()"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg text-sm font-medium hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Login as Owner
                    </button>
                    @endif
                    <a href="{{ route('super-admin.tenants.edit', $tenant) }}"
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg text-sm font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Company
                    </a>
                    <a href="{{ route('super-admin.tenants.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Companies
                    </a>
                </div>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="px-6 py-4">
            @php
                $statusConfig = [
                    'active' => ['class' => 'bg-green-100 border-green-200 text-green-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'trial' => ['class' => 'bg-yellow-100 border-yellow-200 text-yellow-800', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    'suspended' => ['class' => 'bg-red-100 border-red-200 text-red-800', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    'cancelled' => ['class' => 'bg-gray-100 border-gray-200 text-gray-800', 'icon' => 'M6 18L18 6M6 6l12 12'],
                ];
                $config = $statusConfig[$tenant->subscription_status] ?? $statusConfig['cancelled'];
            @endphp
            <div class="border-2 rounded-lg p-4 {{ $config['class'] }}">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold">Status: {{ ucfirst($tenant->subscription_status) }}</h3>
                        @if($tenant->subscription_status === 'trial' && $tenant->trial_ends_at)
                            <p class="text-sm">Trial ends {{ $tenant->trial_ends_at->diffForHumans() }} ({{ $tenant->trial_ends_at->format('M j, Y') }})</p>
                        @elseif($tenant->subscription_status === 'active' && $tenant->subscription_ends_at)
                            <p class="text-sm">Subscription ends {{ $tenant->subscription_ends_at->diffForHumans() }} ({{ $tenant->subscription_ends_at->format('M j, Y') }})</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Users Count -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Users</p>
                    <p class="text-3xl font-bold">{{ $totalUsersCount }}</p>
                </div>
                <div class="bg-blue-400/30 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Active Users</p>
                    <p class="text-3xl font-bold">{{ $activeUsersCount }}</p>
                </div>
                <div class="bg-green-400/30 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Days Active -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Days Active</p>
                    <p class="text-3xl font-bold">{{ $tenant->created_at->diffInDays(now()) }}</p>
                </div>
                <div class="bg-purple-400/30 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Customers</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['customers_count'] ?? 0) }}</p>
                </div>
                <div class="bg-orange-400/30 p-3 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Products -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['products_count'] ?? 0) }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Vouchers -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Vouchers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['vouchers_count'] ?? 0) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Ledger Accounts -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Ledger Accounts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['ledger_accounts_count'] ?? 0) }}</p>
                </div>
                <div class="bg-emerald-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Company Information -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Company Information</h2>
                        <span class="text-xs text-gray-500">ID: #{{ $tenant->id }}</span>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Company Name</label>
                            <p class="text-sm text-gray-900 font-semibold">{{ $tenant->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Company Slug</label>
                            <div class="flex items-center space-x-2">
                                <p class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $tenant->slug }}</p>
                                <button onclick="copyToClipboard('{{ $tenant->slug }}')"
                                        class="text-gray-400 hover:text-gray-600 transition-colors"
                                        title="Copy slug">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-sm text-gray-900">
                                <a href="mailto:{{ $tenant->email }}" class="text-blue-600 hover:text-blue-800 hover:underline">{{ $tenant->email }}</a>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                            <p class="text-sm text-gray-900">{{ $tenant->phone ?: '—' }}</p>
                        </div>
                        @if($tenant->website)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Website</label>
                            <p class="text-sm">
                                <a href="{{ $tenant->website }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center">
                                    {{ $tenant->website }}
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Business Details -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Business Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Business Type</label>
                                <p class="text-sm text-gray-900">{{ $tenant->businessType->name ?? '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Business Structure</label>
                                <p class="text-sm text-gray-900">{{ $tenant->business_structure ? ucfirst(str_replace('_', ' ', $tenant->business_structure)) : '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Registration Number</label>
                                <p class="text-sm text-gray-900">{{ $tenant->business_registration_number ?: '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Tax Identification Number (TIN)</label>
                                <p class="text-sm text-gray-900">{{ $tenant->tax_identification_number ?: '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Accounting & Operations -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Accounting & Operations
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Fiscal Year Start</label>
                                <p class="text-sm text-gray-900">{{ $tenant->fiscal_year_start ? \Carbon\Carbon::parse($tenant->fiscal_year_start)->format('F j') : '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Payment Terms</label>
                                <p class="text-sm text-gray-900">{{ $tenant->payment_terms ? $tenant->payment_terms . ' days' : '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Employee Number Format</label>
                                <p class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded inline-block">{{ $tenant->employee_number_format ?? 'EMP-{YYYY}-{####}' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Enabled Modules</label>
                                @if($tenant->enabled_modules && count($tenant->enabled_modules) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($tenant->enabled_modules as $module)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ ucfirst(str_replace('_', ' ', $module)) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500 italic">Using category defaults</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Address
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Street Address</label>
                                <p class="text-sm text-gray-900">{{ $tenant->address ?: '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">City</label>
                                <p class="text-sm text-gray-900">{{ $tenant->city ?: '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">State</label>
                                <p class="text-sm text-gray-900">{{ $tenant->state ?: '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Country</label>
                                <p class="text-sm text-gray-900">{{ $tenant->country ?: '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Referral Info (if applicable) -->
                    @if($tenant->referral_code || $tenant->referred_by_affiliate_id)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Referral Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($tenant->referral_code)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Referral Code</label>
                                <p class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded inline-block">{{ $tenant->referral_code }}</p>
                            </div>
                            @endif
                            @if($tenant->referral_registered_at)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Referral Date</label>
                                <p class="text-sm text-gray-900">{{ $tenant->referral_registered_at->format('M j, Y g:i A') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                                <p class="text-sm text-gray-900">{{ $tenant->created_at->format('M j, Y g:i A') }}</p>
                                <p class="text-xs text-gray-500">{{ $tenant->created_at->diffForHumans() }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                                <p class="text-sm text-gray-900">{{ $tenant->updated_at->format('M j, Y g:i A') }}</p>
                                <p class="text-xs text-gray-500">{{ $tenant->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-lg font-semibold text-gray-900">Users</h2>
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $totalUsersCount }} total
                            </span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $activeUsersCount }} active
                            </span>
                        </div>
                        <span class="text-xs text-gray-500 italic">Users are managed by company owner</span>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Search and Filter Bar -->
                    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0 sm:space-x-4">
                        <div class="flex-1 max-w-lg">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       id="userSearch"
                                       placeholder="Search users by name or email..."
                                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm"
                                       onkeyup="filterUsers()">
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <select id="statusFilter" onchange="filterUsers()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="active">Active Only</option>
                                <option value="inactive">Inactive Only</option>
                            </select>
                            <select id="roleFilter" onchange="filterUsers()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Roles</option>
                                @foreach($availableUserRoles as $roleKey => $roleLabel)
                                    <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($tenantUsers->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($tenantUsers as $user)
                            <div class="group flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200"
                                 data-user-card="true"
                                 data-user-name="{{ $user->name }}"
                                 data-user-email="{{ $user->email }}"
                                 data-user-status="{{ $user->membership_is_active ? 'active' : 'inactive' }}"
                                 data-user-role="{{ $user->membership_role }}">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center shadow-md">
                                            <span class="text-sm font-bold text-white">{{ substr($user->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $user->name }}</h4>
                                            @if($user->membership_role === 'owner' || strtolower($user->membership_role_label) === 'owner')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
                                                    </svg>
                                                    Owner
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                                    {{ $user->membership_role_label }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 flex items-center">
                                            <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                            {{ $user->email }}
                                        </p>
                                        <p class="text-xs text-gray-500 flex items-center">
                                            <svg class="w-3 h-3 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            @if($user->last_login_at)
                                                Last login {{ $user->last_login_at->diffForHumans() }}
                                            @else
                                                Never logged in
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    @if($user->membership_is_active)
                                        <div class="flex items-center space-x-1">
                                            <span class="flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                            </span>
                                            <span class="text-xs font-medium text-green-600">Active</span>
                                        </div>
                                    @else
                                        <div class="flex items-center space-x-1">
                                            <span class="h-2 w-2 rounded-full bg-red-400"></span>
                                            <span class="text-xs font-medium text-red-600">Inactive</span>
                                        </div>
                                    @endif
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <button onclick="editUser({{ $user->id }})"
                                                class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                            <p class="mt-1 text-sm text-gray-500">This company doesn't have any users yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @php
                            // Build a chronological activity feed
                            $activities = collect();

                            // Company creation
                            $activities->push((object)[
                                'type' => 'created',
                                'title' => 'Company created',
                                'detail' => $tenant->created_at->format('M j, Y \a\t g:i A'),
                                'date' => $tenant->created_at,
                            ]);

                            // Trial started
                            if ($tenant->trial_ends_at) {
                                $activities->push((object)[
                                    'type' => 'trial',
                                    'title' => 'Trial period started',
                                    'detail' => 'Expires ' . $tenant->trial_ends_at->format('M j, Y'),
                                    'date' => $tenant->created_at,
                                ]);
                            }

                            // User registrations
                            foreach ($tenantUsers as $user) {
                                $activities->push((object)[
                                    'type' => 'user',
                                    'title' => $user->name . ' joined',
                                    'detail' => $user->created_at->format('M j, Y \a\t g:i A'),
                                    'date' => $user->created_at,
                                ]);
                            }

                            // Payments
                            foreach ($payments as $payment) {
                                $activities->push((object)[
                                    'type' => 'payment',
                                    'title' => 'Payment of ₦' . number_format($payment->amount / 100),
                                    'detail' => ucfirst($payment->status) . ' — ' . ($payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('M j, Y') : $payment->created_at->format('M j, Y')),
                                    'date' => $payment->created_at,
                                ]);
                            }

                            // Subscriptions
                            foreach ($tenant->subscriptions as $sub) {
                                $activities->push((object)[
                                    'type' => 'subscription',
                                    'title' => ucfirst($sub->status) . ' subscription',
                                    'detail' => '₦' . number_format($sub->amount / 100) . '/' . ($sub->billing_cycle === 'yearly' ? 'year' : 'month'),
                                    'date' => $sub->created_at,
                                ]);
                            }

                            $activities = $activities->sortByDesc('date')->take(10);

                            $iconMap = [
                                'created' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                                'trial' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'user' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                'payment' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                                'subscription' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ];
                        @endphp

                        @foreach($activities as $activity)
                            @php $icon = $iconMap[$activity->type] ?? $iconMap['created']; @endphp
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full {{ $icon['bg'] }} flex items-center justify-center">
                                        <svg class="h-4 w-4 {{ $icon['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon['icon'] }}"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-gray-900">{{ $activity->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity->detail }}</p>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap ml-2">{{ $activity->date->diffForHumans() }}</span>
                            </div>
                        @endforeach

                        @if($activities->isEmpty())
                            <p class="text-sm text-gray-500 text-center py-4">No activity recorded</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Subscription History Timeline -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-teal-50">
                    <h2 class="text-lg font-semibold text-gray-900">Subscription & Payment History</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Current Subscription -->
                        <div class="relative flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center ring-4 ring-white">
                                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            @if($tenant->plan)
                                                {{ $tenant->plan->name }} Plan Active
                                            @else
                                                No Active Plan
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">Current subscription</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-green-600">
                                            @if($tenant->plan)
                                                ₦{{ number_format($tenant->getPlanPrice() / 100) }}/{{ $tenant->billing_cycle === 'yearly' ? 'year' : 'month' }}
                                            @else
                                                Free
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">{{ now()->format('M j, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Records -->
                        @foreach($payments as $payment)
                        <div class="relative flex items-start">
                            <div class="absolute top-5 left-5 w-px bg-gray-200 h-full"></div>
                            <div class="flex-shrink-0">
                                @php
                                    $paymentColors = [
                                        'success' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
                                        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
                                        'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
                                    ];
                                    $pColor = $paymentColors[$payment->status] ?? $paymentColors['pending'];
                                @endphp
                                <div class="h-10 w-10 rounded-full {{ $pColor['bg'] }} flex items-center justify-center ring-4 ring-white">
                                    <svg class="h-5 w-5 {{ $pColor['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            Payment — {{ ucfirst($payment->status) }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                            @if($payment->payment_reference)
                                                · Ref: {{ $payment->payment_reference }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold {{ $payment->status === 'success' ? 'text-green-600' : ($payment->status === 'failed' ? 'text-red-600' : 'text-yellow-600') }}">
                                            ₦{{ number_format($payment->amount / 100) }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $payment->created_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <!-- Trial Started -->
                        @if($tenant->trial_ends_at)
                        <div class="relative flex items-start">
                            <div class="absolute top-5 left-5 w-px bg-gray-200 h-full"></div>
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center ring-4 ring-white">
                                    <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Trial Period Started</p>
                                        <p class="text-xs text-gray-500">{{ $tenant->trial_ends_at->diffInDays($tenant->created_at) }} days trial period</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Free</p>
                                        <p class="text-xs text-gray-500">{{ $tenant->created_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Account Created -->
                        <div class="relative flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center ring-4 ring-white">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Account Created</p>
                                        <p class="text-xs text-gray-500">Company registered in system</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Setup</p>
                                        <p class="text-xs text-gray-500">{{ $tenant->created_at->format('M j, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Summary -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-lg font-bold text-gray-900">
                                    @if($tenant->plan)
                                        ₦{{ number_format(($tenant->getPlanPrice() / 100) * ($tenant->billing_cycle === 'yearly' ? 1 : 12)) }}
                                    @else
                                        ₦0
                                    @endif
                                </p>
                                <p class="text-xs text-gray-600">Annual Value</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-lg font-bold text-gray-900">
                                    ₦{{ number_format($payments->where('status', 'success')->sum('amount') / 100) }}
                                </p>
                                <p class="text-xs text-gray-600">Total Paid</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-lg font-bold text-gray-900">{{ $tenant->created_at->diffInDays(now()) }}</p>
                                <p class="text-xs text-gray-600">Days Active</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-8">

            <!-- Subscription Details -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                    <h2 class="text-lg font-semibold text-gray-900">Subscription</h2>
                </div>
                <div class="p-6 space-y-4">
                    @if($tenant->plan)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Current Plan</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $tenant->plan->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Billing Cycle</label>
                            <p class="text-sm text-gray-900">{{ ucfirst($tenant->billing_cycle) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Price</label>
                            <p class="text-lg font-semibold text-gray-900">
                                ₦{{ number_format($tenant->getPlanPrice() / 100) }}
                                <span class="text-sm font-normal text-gray-600">
                                    /{{ $tenant->billing_cycle === 'yearly' ? 'year' : 'month' }}
                                </span>
                            </p>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">No plan assigned</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Health -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50">
                    <h2 class="text-lg font-semibold text-gray-900">Account Health</h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Email Verification -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-500">Email Verified</label>
                            <span class="text-sm font-bold text-gray-900">{{ $verifiedUsersCount }}/{{ $totalUsersCount }}</span>
                        </div>
                        @php $verifyPercent = $totalUsersCount > 0 ? round(($verifiedUsersCount / $totalUsersCount) * 100) : 0; @endphp
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-500 {{ $verifyPercent === 100 ? 'bg-green-500' : ($verifyPercent >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                 style="width: {{ $verifyPercent }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $verifyPercent }}% of users have verified their email</p>
                    </div>

                    <!-- Recent Logins -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-500">Recent Logins</label>
                            <span class="text-sm font-bold text-gray-900">{{ $recentLogins->count() }}</span>
                        </div>
                        @if($recentLogins->isNotEmpty())
                            <div class="space-y-1">
                                @foreach($recentLogins->take(3) as $loginUser)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600 truncate max-w-[140px]">{{ $loginUser->name }}</span>
                                        <span class="text-gray-500">{{ $loginUser->last_login_at->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">No login records yet</p>
                        @endif
                    </div>

                    <!-- Active Sessions -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-500">Active Sessions (24h)</label>
                            <span class="text-sm font-bold text-gray-900">{{ $activeSessions->count() }}</span>
                        </div>
                        @if($activeSessions->isNotEmpty())
                            <div class="space-y-1">
                                @foreach($activeSessions->take(5) as $session)
                                    @php
                                        $ua = $session->user_agent ?? '';
                                        $browser = str_contains($ua, 'Chrome') ? 'Chrome' : (str_contains($ua, 'Firefox') ? 'Firefox' : (str_contains($ua, 'Safari') ? 'Safari' : 'Other'));
                                        $os = str_contains($ua, 'Windows') ? 'Windows' : (str_contains($ua, 'Mac') ? 'macOS' : (str_contains($ua, 'Linux') ? 'Linux' : (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') ? 'iOS' : (str_contains($ua, 'Android') ? 'Android' : 'Other'))));
                                        $lastActive = \Carbon\Carbon::createFromTimestamp($session->last_activity);
                                    @endphp
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600">{{ $browser }} &middot; {{ $os }}</span>
                                        <span class="text-gray-500">{{ $lastActive->diffForHumans() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500">No active sessions</p>
                        @endif
                    </div>

                    <!-- Onboarding -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-500">Onboarding</label>
                            @if($tenant->hasCompletedOnboarding())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $tenant->getOnboardingProgress() }}%
                                </span>
                            @endif
                        </div>
                        @if(!$tenant->hasCompletedOnboarding())
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full bg-blue-500 transition-all duration-500"
                                     style="width: {{ $tenant->getOnboardingProgress() }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    @if($tenant->subscription_status === 'active')
                        <form action="{{ route('super-admin.tenants.suspend', $tenant) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to suspend this company?')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Suspend Company
                            </button>
                        </form>
                    @elseif($tenant->subscription_status === 'suspended')
                        <form action="{{ route('super-admin.tenants.activate', $tenant) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to activate this company?')"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Activate Company
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('super-admin.tenants.edit', $tenant) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Company
                    </a>
                </div>
            </div>

            <!-- System Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">System Information</h2>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created By</label>
                        <p class="text-sm text-gray-900">
                            @if($tenant->superAdmin)
                                {{ $tenant->superAdmin->name }}
                            @else
                                System
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Created At</label>
                        <p class="text-sm text-gray-900">{{ $tenant->created_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                        <p class="text-sm text-gray-900">{{ $tenant->updated_at->format('M j, Y \a\t g:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                        <div class="flex items-center">
                            @if($tenant->is_active)
                                <span class="flex h-2 w-2 mr-2">
                                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                <span class="text-sm text-green-600">Active</span>
                            @else
                                <span class="h-2 w-2 mr-2 rounded-full bg-red-500"></span>
                                <span class="text-sm text-red-600">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Utility Functions
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    }).catch(() => {
        showToast('Failed to copy to clipboard', 'error');
    });
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white text-sm font-medium transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.remove('translate-x-full'), 100);
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}

// User Management Functions
function filterUsers() {
    const searchTerm = document.getElementById('userSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const roleFilter = document.getElementById('roleFilter').value;

    const userCards = document.querySelectorAll('[data-user-card]');

    userCards.forEach(card => {
        const userName = card.dataset.userName.toLowerCase();
        const userEmail = card.dataset.userEmail.toLowerCase();
        const userStatus = card.dataset.userStatus;
        const userRole = card.dataset.userRole;

        const matchesSearch = userName.includes(searchTerm) || userEmail.includes(searchTerm);
        const matchesStatus = !statusFilter || userStatus === statusFilter;
        const matchesRole = !roleFilter || userRole === roleFilter;

        card.style.display = (matchesSearch && matchesStatus && matchesRole) ? 'block' : 'none';
    });

    const visibleUsers = document.querySelectorAll('[data-user-card]:not([style*="display: none"])').length;
    const countBadge = document.querySelector('.user-count-badge');
    if (countBadge) {
        countBadge.textContent = `${visibleUsers} shown`;
    }
}

function impersonateOwner() {
    if (confirm('Are you sure you want to login as the company owner? This will redirect you to their dashboard.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ $owner ? route("super-admin.impersonate", [$tenant, $owner]) : "#" }}';
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize user card filtering
document.addEventListener('DOMContentLoaded', function() {
    const userCards = document.querySelectorAll('.group');
    userCards.forEach(card => {
        if (card.querySelector('.text-sm.font-semibold')) {
            const userName = card.querySelector('.text-sm.font-semibold').textContent;
            const userEmail = card.querySelector('.text-sm.text-gray-600')?.textContent || '';
            const isActive = card.querySelector('.text-green-600') !== null;
            const isOwner = card.querySelector('.text-purple-800') !== null;

            card.setAttribute('data-user-card', 'true');
            card.setAttribute('data-user-name', userName);
            card.setAttribute('data-user-email', userEmail);
            card.setAttribute('data-user-status', isActive ? 'active' : 'inactive');
            card.setAttribute('data-user-role', isOwner ? 'owner' : 'user');
        }
    });

    // Debounced search
    const searchInput = document.getElementById('userSearch');
    if (searchInput) {
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(filterUsers, 300);
        });
    }
});

// Keyboard shortcut: Escape to close modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
        modals.forEach(modal => modal.classList.add('hidden'));
        document.body.classList.remove('overflow-hidden');
    }
});
</script>

@push('styles')
<style>
@media (max-width: 768px) {
    .lg\:col-span-2 {
        grid-column: span 1 !important;
    }
    .grid.grid-cols-1.lg\:grid-cols-3 {
        grid-template-columns: 1fr !important;
    }
}

::-webkit-scrollbar {
    width: 6px;
}
::-webkit-scrollbar-track {
    background: #f1f5f9;
}
::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush
@endpush
