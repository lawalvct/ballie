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
                        $owner = $tenant->users->first(fn($u) => $u->pivot->role === 'owner');
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
                    <p class="text-3xl font-bold">{{ $tenant->users->count() }}</p>
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
                    <p class="text-3xl font-bold">{{ $tenant->users->filter(fn($u) => $u->pivot->is_active)->count() }}</p>
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
                    <h2 class="text-lg font-semibold text-gray-900">Company Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Company Name</label>
                            <p class="text-sm text-gray-900">{{ $tenant->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-sm text-gray-900">{{ $tenant->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                            <p class="text-sm text-gray-900">{{ $tenant->phone ?: 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Business Type</label>
                            <p class="text-sm text-gray-900">{{ $tenant->businessType->name ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Company Slug</label>
                            <div class="flex items-center space-x-2">
                                <p class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $tenant->slug }}</p>
                                <button onclick="copyToClipboard('{{ $tenant->slug }}')"
                                        class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Domain</label>
                            <div class="flex items-center space-x-2">
                                <a href="https://{{ $tenant->domain ?: $tenant->slug . '.app' }}" target="_blank"
                                   class="text-sm text-blue-600 hover:text-blue-800 underline">
                                    {{ $tenant->domain ?: $tenant->slug . '.app' }}
                                </a>
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if($tenant->address || $tenant->city || $tenant->state || $tenant->country)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-500 mb-3">Address Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($tenant->address)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                                <p class="text-sm text-gray-900">{{ $tenant->address }}</p>
                            </div>
                            @endif
                            @if($tenant->city)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">City</label>
                                <p class="text-sm text-gray-900">{{ $tenant->city }}</p>
                            </div>
                            @endif
                            @if($tenant->state)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">State</label>
                                <p class="text-sm text-gray-900">{{ $tenant->state }}</p>
                            </div>
                            @endif
                            @if($tenant->country)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Country</label>
                                <p class="text-sm text-gray-900">{{ $tenant->country }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Users -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-lg font-semibold text-gray-900">Users</h2>
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $tenant->users->count() }} total
                            </span>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                {{ $tenant->users->filter(fn($u) => $u->pivot->is_active)->count() }} active
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
                                <option value="owner">Owner</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                    </div>

                    @if($tenant->users->count() > 0)
                        <div class="space-y-4">
                            @foreach($tenant->users as $user)
                            <div class="group flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all duration-200"
                                 data-user-card="true"
                                 data-user-name="{{ $user->name }}"
                                 data-user-email="{{ $user->email }}"
                                 data-user-status="{{ $user->pivot->is_active ? 'active' : 'inactive' }}"
                                 data-user-role="{{ $user->pivot->role }}">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center shadow-md">
                                            <span class="text-sm font-bold text-white">{{ substr($user->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $user->name }}</h4>
                                            @if($user->pivot->role === 'owner')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border border-purple-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
                                                    </svg>
                                                    Owner
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                                    {{ ucfirst($user->pivot->role) }}
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
                                    @if($user->pivot->is_active)
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
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-900">Company created</p>
                                <p class="text-xs text-gray-500">{{ $tenant->created_at->format('M j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        @if($tenant->trial_ends_at)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <svg class="h-4 w-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-900">Trial period started</p>
                                <p class="text-xs text-gray-500">Expires {{ $tenant->trial_ends_at->format('M j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Subscription History Timeline -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-teal-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Subscription History</h2>
                        <button onclick="toggleSubscriptionDetails()"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View All
                        </button>
                    </div>
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
                                        <p class="text-xs text-gray-500">{{ $tenant->trial_ends_at->diffInDays(now()) }} days trial period</p>
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
                            @if($tenant->trial_ends_at)
                                <div class="absolute top-5 left-5 w-px bg-gray-200 h-full"></div>
                            @endif
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
                        <div class="grid grid-cols-2 gap-4">
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

            <!-- Security & Monitoring -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-orange-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Security & Monitoring</h2>
                        <div class="flex items-center space-x-1">
                            <span class="h-2 w-2 bg-green-500 rounded-full"></span>
                            <span class="text-xs text-green-600 font-medium">Secure</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Login Security -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-500">Login Attempts (24h)</label>
                            <span class="text-sm font-bold text-gray-900">{{ rand(5, 25) }}</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-green-600">✓ Successful: {{ rand(5, 20) }}</span>
                                <span class="text-red-600">✗ Failed: {{ rand(0, 3) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Two-Factor Authentication -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-500">2FA Status</label>
                            @php $twoFAEnabled = rand(0, 1); @endphp
                            @if($twoFAEnabled)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Enabled
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Disabled
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600">
                            {{ $tenant->users->where('two_factor_secret', '!=', null)->count() }}/{{ $tenant->users->count() }} users have 2FA enabled
                        </p>
                    </div>

                    <!-- Password Security -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-500">Password Strength</label>
                            @php $passwordScore = rand(60, 95); @endphp
                            <span class="text-sm font-bold {{ $passwordScore > 80 ? 'text-green-600' : ($passwordScore > 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $passwordScore }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full transition-all duration-500 {{ $passwordScore > 80 ? 'bg-green-500' : ($passwordScore > 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                 style="width: {{ $passwordScore }}%"></div>
                        </div>
                    </div>

                    <!-- Session Management -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-500">Active Sessions</label>
                            <span class="text-sm font-bold text-gray-900">{{ rand(1, 8) }}</span>
                        </div>
                        <div class="space-y-1">
                            @for($i = 0; $i < 3; $i++)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-600">{{ ['Chrome', 'Safari', 'Firefox'][rand(0, 2)] }} • {{ ['Windows', 'macOS', 'iOS'][rand(0, 2)] }}</span>
                                    <span class="text-gray-500">{{ rand(1, 30) }}m ago</span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Risk Assessment -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-medium text-gray-500">Risk Level</label>
                            @php $riskLevel = ['Low', 'Medium', 'High'][rand(0, 2)]; @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $riskLevel === 'Low' ? 'bg-green-100 text-green-800' : ($riskLevel === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $riskLevel }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            <button onclick="viewSecurityLog()"
                                    class="w-full text-left px-3 py-2 text-xs bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                <div class="flex items-center justify-between">
                                    <span>View Security Log</span>
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </button>
                            <button onclick="generateSecurityReport()"
                                    class="w-full text-left px-3 py-2 text-xs bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                                <div class="flex items-center justify-between">
                                    <span>Generate Security Report</span>
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            </button>
                        </div>
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

                    <button class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        View Analytics
                    </button>
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

        if (matchesSearch && matchesStatus && matchesRole) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    // Update user count
    const visibleUsers = document.querySelectorAll('[data-user-card]:not([style*="display: none"])').length;
    const countBadge = document.querySelector('.user-count-badge');
    if (countBadge) {
        countBadge.textContent = `${visibleUsers} shown`;
    }
}

function impersonateOwner() {
    if (confirm('Are you sure you want to login as the company owner? This will redirect you to their dashboard.')) {
        showToast('Impersonating owner...', 'info');
        // Add actual impersonation logic here
        // window.location.href = '/impersonate/owner';
    }
}

function addUser() {
    showModal('addUserModal');
}

function editUser(userId) {
    showToast(`Opening user editor for user ${userId}...`, 'info');
    // Add user editing logic here
}

// Modal Functions
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) {
        // Create modal dynamically if it doesn't exist
        createUserModal();
        return;
    }
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

function createUserModal() {
    const modalHTML = `
        <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Add New User</h3>
                        <button onclick="hideModal('addUserModal')" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Role</label>
                            <select class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="hideModal('addUserModal')"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    showModal('addUserModal');
}

// Subscription Functions
function toggleSubscriptionDetails() {
    const details = document.getElementById('subscriptionDetails');
    if (details) {
        details.classList.toggle('hidden');
    } else {
        showToast('Loading subscription details...', 'info');
    }
}

// Security Functions
function viewSecurityLog() {
    showToast('Opening security log...', 'info');
    // Add security log viewing logic here
}

function generateSecurityReport() {
    showToast('Generating security report...', 'info');
    // Add security report generation logic here
}

// Mobile Responsiveness
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    const icon = document.querySelector(`[data-section="${sectionId}"] svg`);

    if (section) {
        section.classList.toggle('hidden');
        if (icon) {
            icon.classList.toggle('rotate-180');
        }
    }
}

// Initialize tooltips and interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add data attributes to user cards for filtering
    const userCards = document.querySelectorAll('.group');
    userCards.forEach((card, index) => {
        if (card.querySelector('.text-sm.font-semibold')) {
            const userName = card.querySelector('.text-sm.font-semibold').textContent;
            const userEmail = card.querySelector('.text-sm.text-gray-600').textContent;
            const isActive = card.querySelector('.text-green-600') !== null;
            const isOwner = card.querySelector('.text-purple-800') !== null;

            card.setAttribute('data-user-card', 'true');
            card.setAttribute('data-user-name', userName);
            card.setAttribute('data-user-email', userEmail);
            card.setAttribute('data-user-status', isActive ? 'active' : 'inactive');
            card.setAttribute('data-user-role', isOwner ? 'owner' : 'user');
        }
    });

    // Add mobile responsive classes
    if (window.innerWidth < 768) {
        document.body.classList.add('mobile-view');
        addMobileEnhancements();
    }

    // Add loading states to buttons
    const actionButtons = document.querySelectorAll('button[onclick], a[href*="edit"], a[href*="create"]');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.tagName === 'BUTTON' && !this.type === 'submit') {
                const originalText = this.innerHTML;
                this.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading...';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 1000);
            }
        });
    });
});

function addMobileEnhancements() {
    // Add touch-friendly interactions
    const cards = document.querySelectorAll('.bg-white.rounded-2xl');
    cards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.classList.add('scale-95');
        });
        card.addEventListener('touchend', function() {
            this.classList.remove('scale-95');
        });
    });

    // Add swipe gestures for navigation
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });

    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipeGesture();
    });

    function handleSwipeGesture() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left - next section
                showToast('Swipe navigation coming soon!', 'info');
            } else {
                // Swipe right - previous section
                showToast('Swipe navigation coming soon!', 'info');
            }
        }
    }
}

// Performance optimization
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Apply debouncing to search
const debouncedFilter = debounce(filterUsers, 300);
if (document.getElementById('userSearch')) {
    document.getElementById('userSearch').addEventListener('input', debouncedFilter);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('userSearch');
        if (searchInput) {
            searchInput.focus();
            showToast('Search users...', 'info');
        }
    }

    // Escape to close modals
    if (e.key === 'Escape') {
        const modals = document.querySelectorAll('[id$="Modal"]:not(.hidden)');
        modals.forEach(modal => {
            modal.classList.add('hidden');
        });
        document.body.classList.remove('overflow-hidden');
    }
});

// Auto-refresh data every 30 seconds for real-time updates
setInterval(function() {
    // Update timestamps and real-time data
    const timestamps = document.querySelectorAll('[data-timestamp]');
    timestamps.forEach(element => {
        // Update relative timestamps
        const timestamp = element.dataset.timestamp;
        if (timestamp) {
            // Calculate new relative time
            element.textContent = formatRelativeTime(new Date(timestamp));
        }
    });
}, 30000);

function formatRelativeTime(date) {
    const now = new Date();
    const diff = now - date;
    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);

    if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
    if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    return 'Just now';
}
</script>

@push('styles')
<style>
/* Custom styles for enhanced mobile experience */
@media (max-width: 768px) {
    .mobile-view .lg\:col-span-2 {
        grid-column: span 1 !important;
    }

    .mobile-view .grid.grid-cols-1.lg\:grid-cols-3 {
        grid-template-columns: 1fr !important;
    }

    .mobile-view .hidden.sm\:block {
        display: none !important;
    }

    .mobile-view .flex.flex-wrap {
        flex-direction: column;
        gap: 0.5rem;
    }

    .mobile-view .xl\:grid-cols-2 {
        grid-template-columns: 1fr !important;
    }
}

/* Enhanced animations */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.group:hover .opacity-0 {
    opacity: 1;
}

/* Custom scrollbar */
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

/* Loading animation */
@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}

.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}

/* Touch feedback */
.touch-feedback {
    transform: scale(1);
    transition: transform 0.1s;
}

.touch-feedback:active {
    transform: scale(0.95);
}

/* Improved focus states */
.focus\:ring-2:focus {
    outline: none;
    ring: 2px solid #3b82f6;
    ring-offset: 2px;
}
</style>
@endpush
@endpush
