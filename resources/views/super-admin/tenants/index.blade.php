@extends('layouts.super-admin')

@section('title', 'Company Management')
@section('page-title', 'Company Management')

@section('content')
<div class="max-w-full space-y-8">


    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Active Companies</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tenants->where('subscription_status', 'active')->count() }}</p>
                        <p class="text-xs text-green-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            {{ number_format(($tenants->where('subscription_status', 'active')->count() / max($tenants->count(), 1)) * 100, 1) }}% of total
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-green-500 to-green-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 h-1"></div>
        </div>

        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Trial Companies</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tenants->where('subscription_status', 'trial')->count() }}</p>
                        <p class="text-xs text-yellow-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Conversion pending
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-1"></div>
        </div>

        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Suspended</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tenants->where('subscription_status', 'suspended')->count() }}</p>
                        <p class="text-xs text-red-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h5l-5-5V9a6 6 0 10-12 0v3l-5 5h5a3 3 0 006 0z"></path>
                            </svg>
                            Requires attention
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-red-500 to-red-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 h-1"></div>
        </div>

        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Companies</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tenants->count() }}</p>
                        <p class="text-xs text-indigo-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            All registered
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-1"></div>
        </div>
    </div>

    <!-- Enhanced Filters and Search -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 bg-gray-50/50">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0 lg:space-x-4">
                <!-- Left side: Search and Filters -->
                <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-3 flex-1">
                    <!-- Search -->
                    <div class="relative flex-1 max-w-sm">
                        <input type="text"
                               id="search"
                               placeholder="Search companies..."
                               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center space-x-2">
                        <select id="status-filter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white min-w-0">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="trial">Trial</option>
                            <option value="suspended">Suspended</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <select id="plan-filter" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white min-w-0">
                            <option value="">All Plans</option>
                            @foreach($availablePlans as $plan)
                                <option value="{{ $plan->slug }}">{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Results Count -->
                    <span class="text-xs text-gray-600 font-medium whitespace-nowrap">
                        <span id="visible-count">{{ $tenants->count() }}</span> of {{ $tenants->count() }} companies
                    </span>
                </div>

                <!-- Right side: Actions -->
                <div class="flex items-center space-x-2">
                    <button onclick="exportSelected()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Export</span>
                    </button>
                    <a href="{{ route('super-admin.tenants.invite') }}" class="inline-flex items-center px-3 py-2 bg-amber-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-amber-700 transition-colors">
                        <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Send Invite</span>
                    </a>
                    <a href="{{ route('super-admin.tenants.create') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 sm:mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="hidden sm:inline">Add New</span>
                    </a>
                </div>
            </div>
        </div>
    </div>    <!-- Enhanced Companies Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Bulk Actions Bar -->
        <div id="bulk-actions-bar" class="hidden bg-indigo-50 border-b border-indigo-200 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-indigo-700">
                        <span id="selected-count">0</span> companies selected
                    </span>
                    <div class="hidden md:flex items-center space-x-4 text-xs text-indigo-600">
                        <span>Press Ctrl+A to select all visible</span>
                        <span>•</span>
                        <span>Click on rows to select</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button"
                            class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                            onclick="bulkAction('activate')"
                            title="Activate selected companies">
                        <i class="fas fa-check mr-1"></i>
                        Activate
                    </button>
                    <button type="button"
                            class="px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors"
                            onclick="bulkAction('suspend')"
                            title="Suspend selected companies">
                        <i class="fas fa-pause mr-1"></i>
                        Suspend
                    </button>
                    <button type="button"
                            class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                            onclick="exportSelected()"
                            title="Export selected companies to CSV">
                        <i class="fas fa-download mr-1"></i>
                        Export
                    </button>
                    <button type="button"
                            class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors"
                            onclick="bulkAction('delete')"
                            title="Delete selected companies (cannot be undone)">
                        <i class="fas fa-trash mr-1"></i>
                        Delete
                    </button>
                    <button type="button"
                            class="px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors"
                            onclick="clearSelection()"
                            title="Clear selection">
                        Clear
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto max-w-full">
            <table class="w-full divide-y divide-gray-200" style="table-layout: fixed;">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left w-8">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" title="Select all companies">
                        </th>
                        <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">
                            Company
                        </th>
                        <th scope="col" class="px-2 sm:px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                            Status
                        </th>
                        <th scope="col" class="hidden sm:table-cell px-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                            Plan
                        </th>
                        <th scope="col" class="hidden md:table-cell px-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">
                            Users
                        </th>
                        <th scope="col" class="hidden lg:table-cell px-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                            Revenue
                        </th>
                        <th scope="col" class="hidden lg:table-cell px-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                            Created
                        </th>
                        <th scope="col" class="hidden xl:table-cell px-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                            Last Active
                        </th>
                        <th scope="col" class="relative px-2 py-3 w-24">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($tenants as $tenant)
                    <tr class="group hover:bg-gray-50 transition-colors duration-150 relative" data-tenant-status="{{ strtolower($tenant->subscription_status) }}" data-tenant-plan="{{ $tenant->plan ? strtolower($tenant->plan->slug) : 'none' }}">
                        <td class="px-3 py-3">
                            <input type="checkbox" name="selected_tenants[]" value="{{ $tenant->id }}" class="tenant-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="px-3 sm:px-4 py-3 overflow-hidden">
                            <div class="flex items-center min-w-0">
                                <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                    @if($tenant->logo)
                                        <img class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg border border-gray-200" src="{{ $tenant->logo }}" alt="{{ $tenant->name }}">
                                    @else
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-md">
                                            <span class="text-xs sm:text-sm font-bold text-white">{{ substr($tenant->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                                    <div class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $tenant->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ $tenant->email }}</div>
                                    @if($tenant->domain)
                                        <div class="text-xs text-indigo-600 font-medium hidden sm:block truncate">{{ $tenant->domain }}</div>
                                    @else
                                        <div class="text-xs text-gray-400 hidden sm:block truncate">{{ $tenant->slug }}.app</div>
                                    @endif
                                    <!-- Mobile-only: Show plan info -->
                                    <div class="sm:hidden mt-0.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $tenant->plan ? ucfirst($tenant->plan->name) : 'No Plan' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-2 sm:px-3 py-3">
                            @php
                                $statusConfig = [
                                    'active' => ['class' => 'bg-green-100 text-green-800 border-green-200', 'dot' => 'bg-green-400', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    'trial' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'dot' => 'bg-yellow-400', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    'suspended' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'dot' => 'bg-red-400', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                                    'cancelled' => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'dot' => 'bg-gray-400', 'icon' => 'M6 18L18 6M6 6l12 12'],
                                ];
                                $config = $statusConfig[$tenant->subscription_status] ?? $statusConfig['cancelled'];
                            @endphp
                            <div class="flex items-center min-w-0">
                                <div class="w-1.5 h-1.5 {{ $config['dot'] }} rounded-full mr-1 {{ $tenant->subscription_status === 'active' ? 'animate-pulse' : '' }}"></div>
                                <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-semibold border {{ $config['class'] }} truncate">
                                    <svg class="w-2.5 h-2.5 mr-0.5 hidden sm:block flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
                                    </svg>
                                    <span class="sm:hidden">{{ substr(ucfirst($tenant->subscription_status), 0, 1) }}</span>
                                    <span class="hidden sm:inline truncate">{{ ucfirst($tenant->subscription_status) }}</span>
                                </span>
                            </div>
                            @if($tenant->trial_ends_at && $tenant->subscription_status === 'trial')
                                <div class="text-xs text-gray-500 mt-0.5 hidden sm:block truncate">
                                    Ends {{ $tenant->trial_ends_at->diffForHumans() }}
                                </div>
                            @endif
                        </td>
                        <td class="hidden sm:table-cell px-2 py-3">
                            @if($tenant->plan)
                                @php
                                    $planConfig = [
                                        'starter' => ['class' => 'bg-blue-100 text-blue-800'],
                                        'professional' => ['class' => 'bg-purple-100 text-purple-800'],
                                        'enterprise' => ['class' => 'bg-indigo-100 text-indigo-800'],
                                    ];
                                    $config = $planConfig[$tenant->plan->slug] ?? ['class' => 'bg-gray-100 text-gray-800'];
                                @endphp
                                <div class="min-w-0">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $config['class'] }} truncate">
                                        {{ $tenant->plan->name }}
                                    </span>
                                    <div class="text-xs text-gray-500 mt-0.5 truncate">
                                        {{ $tenant->billing_cycle === 'yearly' ? '₦' . number_format($tenant->plan->yearly_price / 100) . '/yr' : '₦' . number_format($tenant->plan->monthly_price / 100) . '/mo' }}
                                    </div>
                                </div>
                            @else
                                <div class="min-w-0">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 truncate">
                                        No Plan
                                    </span>
                                    <div class="text-xs text-gray-500 mt-0.5 truncate">₦0/mo</div>
                                </div>
                            @endif
                        </td>
                        <td class="hidden md:table-cell px-2 py-3">
                            <div class="flex items-center min-w-0">
                                <svg class="w-3 h-3 text-gray-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                </svg>
                                <span class="text-xs font-medium text-gray-900">{{ $tenant->users->count() }}</span>
                            </div>
                        </td>
                        <td class="hidden lg:table-cell px-2 py-3">
                            @if($tenant->plan)
                                <div class="text-xs font-semibold text-gray-900 truncate">₦{{ number_format($tenant->getPlanPrice() / 100) }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ $tenant->billing_cycle === 'yearly' ? 'per year' : 'per month' }}</div>
                            @else
                                <div class="text-xs font-semibold text-gray-900 truncate">₦0</div>
                                <div class="text-xs text-gray-500 truncate">No plan</div>
                            @endif
                        </td>
                        <td class="hidden lg:table-cell px-2 py-3">
                            <div class="text-xs text-gray-900 truncate">{{ $tenant->created_at->format('M j, Y') }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $tenant->created_at->format('g:i A') }}</div>
                        </td>
                        <td class="hidden xl:table-cell px-2 py-3">
                            <div class="text-xs text-gray-900 truncate">{{ $tenant->updated_at->diffForHumans() }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $tenant->updated_at->format('M j, g:i A') }}</div>
                        </td>
                        <td class="px-2 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1 opacity-60 group-hover:opacity-100 transition-opacity duration-200">
                                <!-- View Button -->
                                <a href="{{ route('super-admin.tenants.show', $tenant) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200 hover:scale-105"
                                   title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('super-admin.tenants.edit', $tenant) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200 hover:scale-105"
                                   title="Edit Company">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>

                                <!-- Status Action Button -->
                                @if($tenant->subscription_status === 'active')
                                    <form action="{{ route('super-admin.tenants.suspend', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 transition-all duration-200 hover:scale-105"
                                                onclick="return confirm('Are you sure you want to suspend this company?')"
                                                title="Suspend Company">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @elseif($tenant->subscription_status === 'suspended')
                                    <form action="{{ route('super-admin.tenants.activate', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-green-600 hover:bg-green-50 transition-all duration-200 hover:scale-105"
                                                onclick="return confirm('Are you sure you want to activate this company?')"
                                                title="Activate Company">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                <!-- More Actions Dropdown (Hidden on Mobile) -->
                                <div class="relative hidden sm:block" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200 hover:scale-105 relative z-10"
                                            title="More Actions">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-1 w-44 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                        @if($tenant->users->where('role', 'owner')->first())
                                            <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                Impersonate User
                                            </a>
                                        @endif
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                            View Analytics
                                        </a>
                                        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Export Data
                                        </a>
                                        <div class="border-t border-gray-200 my-0.5"></div>
                                        <a href="#" class="block px-3 py-1.5 text-xs text-red-600 hover:bg-red-50">
                                            <svg class="w-3 h-3 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Company
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">No companies found</h3>
                                <p class="text-gray-500 mb-6 max-w-sm">You haven't registered any companies yet. Get started by creating your first company.</p>
                                <a href="{{ route('super-admin.tenants.create') }}"
                                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create First Company
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tenants->hasPages())
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Enhanced JavaScript for Professional Functionality -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced search functionality
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');
    const planFilter = document.getElementById('plan-filter');
    const visibleCount = document.getElementById('visible-count');
    const tableRows = document.querySelectorAll('tbody tr[data-tenant-status]');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value.toLowerCase();
        const selectedPlan = planFilter.value.toLowerCase();
        let visibleRowsCount = 0;

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const rowStatus = row.getAttribute('data-tenant-status');
            const rowPlan = row.getAttribute('data-tenant-plan');

            const matchesSearch = !searchTerm || text.includes(searchTerm);
            const matchesStatus = !selectedStatus || rowStatus === selectedStatus;
            const matchesPlan = !selectedPlan || rowPlan === selectedPlan;

            if (matchesSearch && matchesStatus && matchesPlan) {
                row.style.display = '';
                visibleRowsCount++;
                // Add entrance animation
                row.style.animation = 'fadeInUp 0.3s ease-out';
            } else {
                row.style.display = 'none';
            }
        });

        // Update visible count
        visibleCount.textContent = visibleRowsCount;

        // Show/hide empty state
        const emptyRow = document.querySelector('tbody tr:not([data-tenant-status])');
        if (emptyRow && visibleRowsCount === 0 && tableRows.length > 0) {
            emptyRow.style.display = '';
            emptyRow.querySelector('td').innerHTML = `
                <div class="flex flex-col items-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No companies match your filters</h3>
                    <p class="text-gray-500 mb-4">Try adjusting your search criteria or filters</p>
                    <button onclick="clearFilters()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200">
                        Clear Filters
                    </button>
                </div>
            `;
        } else if (emptyRow && visibleRowsCount > 0) {
            emptyRow.style.display = 'none';
        }
    }

    // Clear filters function
    window.clearFilters = function() {
        searchInput.value = '';
        statusFilter.value = '';
        planFilter.value = '';
        filterTable();
    };

    // Debounced search
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterTable, 300);
    });

    // Immediate filter on dropdown changes
    statusFilter.addEventListener('change', filterTable);
    planFilter.addEventListener('change', filterTable);

    // Add loading states for action buttons
    document.querySelectorAll('form[action*="suspend"], form[action*="activate"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button');
            const originalHtml = button.innerHTML;

            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;

            // Reset button state if form submission fails
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }, 3000);
        });
    });

    // Enhanced table hover effects
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.002)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
            this.style.zIndex = '1';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
            this.style.zIndex = 'auto';
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }

        // Escape to clear search
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            clearFilters();
            searchInput.blur();
        }

        // Ctrl/Cmd + A to select all visible (when not focused on input)
        if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !searchInput.matches(':focus')) {
            e.preventDefault();
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            }
        }

        // Delete key to trigger bulk delete (when companies are selected)
        if (e.key === 'Delete' && !searchInput.matches(':focus')) {
            const selectedCount = document.querySelectorAll('.tenant-checkbox:checked').length;
            if (selectedCount > 0) {
                e.preventDefault();
                bulkAction('delete');
            }
        }
    });

    // Add keyboard shortcut hint
    searchInput.setAttribute('title', 'Press Ctrl+K to quickly search');

    // Bulk selection functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const tenantCheckboxes = document.querySelectorAll('.tenant-checkbox');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const selectedCountSpan = document.getElementById('selected-count');

    function updateBulkActionsBar() {
        const checkedBoxes = document.querySelectorAll('.tenant-checkbox:checked');
        const count = checkedBoxes.length;

        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkActionsBar.classList.remove('hidden');
            bulkActionsBar.style.animation = 'fadeInUp 0.3s ease both';
        } else {
            bulkActionsBar.classList.add('hidden');
        }

        // Update select all checkbox state
        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === tenantCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Bulk selection event listeners
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            tenantCheckboxes.forEach(checkbox => {
                if (checkbox.closest('tr').style.display !== 'none') {
                    checkbox.checked = isChecked;
                }
            });
            updateBulkActionsBar();
        });
    }

    tenantCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionsBar);

        // Add visual feedback for selection
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            if (this.checked) {
                row.classList.add('bg-indigo-50', 'border-indigo-200');
            } else {
                row.classList.remove('bg-indigo-50', 'border-indigo-200');
            }
        });
    });

    // Enhanced row interactions - click anywhere on row to select
    tableRows.forEach(row => {
        const checkbox = row.querySelector('.tenant-checkbox');

        if (checkbox) {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('a') && !e.target.closest('input')) {
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        }
    });

    // Global functions for bulk actions
    window.bulkAction = function(action) {
        const checkedBoxes = document.querySelectorAll('.tenant-checkbox:checked');
        const tenantIds = Array.from(checkedBoxes).map(cb => cb.value);

        if (tenantIds.length === 0) {
            alert('Please select at least one company.');
            return;
        }

        let confirmMessage = '';
        let actionUrl = '';

        switch(action) {
            case 'activate':
                confirmMessage = `Are you sure you want to activate ${tenantIds.length} selected companies?`;
                actionUrl = '/super-admin/tenants/bulk-activate';
                break;
            case 'suspend':
                confirmMessage = `Are you sure you want to suspend ${tenantIds.length} selected companies?`;
                actionUrl = '/super-admin/tenants/bulk-suspend';
                break;
            case 'delete':
                confirmMessage = `Are you sure you want to delete ${tenantIds.length} selected companies? This action cannot be undone.`;
                actionUrl = '/super-admin/tenants/bulk-delete';
                break;
        }

        if (confirm(confirmMessage)) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = actionUrl;

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add tenant IDs
            tenantIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'tenant_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    };

    window.exportSelected = function() {
        const checkedBoxes = document.querySelectorAll('.tenant-checkbox:checked');
        const tenantIds = Array.from(checkedBoxes).map(cb => cb.value);

        // Build export URL with current filters
        const urlParams = new URLSearchParams();

        // Add current filters
        if (searchInput && searchInput.value) {
            urlParams.append('search', searchInput.value);
        }
        if (statusFilter && statusFilter.value) {
            urlParams.append('status', statusFilter.value);
        }
        if (planFilter && planFilter.value) {
            urlParams.append('plan', planFilter.value);
        }

        // Add selected IDs if any
        if (tenantIds.length > 0) {
            urlParams.append('ids', tenantIds.join(','));
        }

        // Create export URL
        const exportUrl = '{{ route("super-admin.tenants.export") }}?' + urlParams.toString();

        // Open in new window to trigger download
        window.open(exportUrl, '_blank');
    };

    window.clearSelection = function() {
        tenantCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change'));
        });
        updateBulkActionsBar();
    };
});

// Animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .table-row {
        transition: all 0.2s ease-in-out;
    }

    .status-dot {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    tbody tr {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    tbody tr:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .tenant-checkbox {
        transform: scale(1.1);
        transition: all 0.2s ease;
    }

    .tenant-checkbox:hover {
        transform: scale(1.2);
    }

    #bulk-actions-bar {
        border-left: 4px solid #4f46e5;
        backdrop-filter: blur(10px);
    }

    tr.bg-indigo-50 {
        border-left: 3px solid #4f46e5;
    }

    /* Improved action buttons */
    .action-btn {
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: scale(1.05);
    }

    /* Enhanced dropdown menus */
    .dropdown-menu {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95);
    }

    /* Smooth scrolling for search results */
    .table-container {
        scroll-behavior: smooth;
    }

    /* Loading states */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Custom checkbox styling */
    input[type="checkbox"]:indeterminate {
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M5 8h6'/%3e%3c/svg%3e");
    }
`;
document.head.appendChild(style);
</script>
@endsection
