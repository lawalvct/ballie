@extends('layouts.super-admin')

@section('title', 'Support Center')
@section('page-title', 'Support Center')

@section('content')
<div class="max-w-full space-y-8">

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center space-x-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm text-green-800">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center space-x-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span class="text-sm text-red-800">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">New Tickets</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['new_tickets'] }}</p>
                        <p class="text-xs text-red-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Needs attention
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-red-500 to-red-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
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
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Open Tickets</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['open_tickets'] }}</p>
                        <p class="text-xs text-blue-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            In progress
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-1"></div>
        </div>

        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Resolved Today</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['resolved_today'] }}</p>
                        <p class="text-xs text-green-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Completed
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-green-500 to-green-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Avg Response</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['avg_response_time'] }}h</p>
                        <p class="text-xs text-yellow-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Response time
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Satisfaction</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['satisfaction_rating'] ?? 0, 1) }}/5</p>
                        <p class="text-xs text-purple-600 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Rating
                        </p>
                    </div>
                    <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-1"></div>
        </div>
    </div>

    <!-- Enhanced Filters and Search -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <form method="GET" action="{{ route('super-admin.support.index') }}" x-data="{ showAdvanced: {{ request()->hasAny(['tenant', 'assigned', 'date_from', 'date_to']) ? 'true' : 'false' }} }">
            <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-slate-50 via-white to-indigo-50/60 border-b border-gray-100 space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-[0.18em] mb-2">Search Tickets</label>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by ticket #, subject, tenant, requester..."
                                   class="w-full pl-11 pr-4 py-3 text-sm border border-gray-200 rounded-xl bg-white shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="lg:w-auto flex items-center gap-2">
                        <span class="inline-flex items-center px-3 py-2 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold whitespace-nowrap">
                            {{ $tickets->total() }} tickets
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting_customer" {{ request('status') === 'waiting_customer' ? 'selected' : '' }}>Waiting</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Priority</label>
                        <select name="priority" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">All Priority</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 xl:col-span-2 flex items-end">
                        <button type="button" @click="showAdvanced = !showAdvanced" class="inline-flex items-center px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2 transition-transform" :class="showAdvanced ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                            Advanced Filters
                        </button>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-indigo-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['search', 'status', 'priority', 'category', 'tenant', 'assigned', 'date_from', 'date_to']))
                            <a href="{{ route('super-admin.support.index') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reset Filters
                            </a>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('super-admin.support.reports') }}" class="inline-flex items-center px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Reports
                        </a>
                        <a href="{{ route('super-admin.support.kb.index') }}" class="inline-flex items-center px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Knowledge Base
                        </a>
                        <a href="{{ route('super-admin.support.categories.index') }}" class="inline-flex items-center px-3 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Categories
                        </a>
                        <a href="{{ route('super-admin.support.templates.index') }}" class="inline-flex items-center px-3 py-2.5 bg-indigo-600 border border-transparent rounded-xl text-sm font-medium text-white hover:bg-indigo-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Templates
                        </a>
                    </div>
                </div>

                @if(request()->hasAny(['search', 'status', 'priority', 'category', 'tenant', 'assigned', 'date_from', 'date_to']))
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">Active Filters</span>
                        @if(request('search'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">Search: {{ request('search') }}</span>
                        @endif
                        @if(request('status'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">Status: {{ ucwords(str_replace('_', ' ', request('status'))) }}</span>
                        @endif
                        @if(request('priority'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-medium">Priority: {{ ucfirst(request('priority')) }}</span>
                        @endif
                        @if(request('category'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-medium">Category selected</span>
                        @endif
                        @if(request('tenant'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium">Tenant selected</span>
                        @endif
                        @if(request('assigned'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">Assigned: {{ request('assigned') === 'me' ? 'Me' : 'Unassigned' }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <div x-show="showAdvanced" x-transition class="px-4 sm:px-6 py-5 bg-slate-50/70 border-t border-gray-100">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-[0.18em]">Advanced Filters</p>
                        <p class="text-xs text-gray-500 mt-1">Refine the queue by ownership and time window.</p>
                    </div>
                    <button type="button" @click="showAdvanced = false" class="text-xs font-medium text-gray-500 hover:text-gray-700">Hide</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Tenant</label>
                        <select name="tenant" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                            <option value="">All Tenants</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ request('tenant') == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Assigned To</label>
                        <select name="assigned" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                            <option value="">All Tickets</option>
                            <option value="me" {{ request('assigned') === 'me' ? 'selected' : '' }}>Assigned to Me</option>
                            <option value="unassigned" {{ request('assigned') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                        </select>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                    </div>
                    <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Enhanced Tickets Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Bulk Actions Bar -->
        <div id="bulk-actions-bar" class="hidden bg-indigo-50 border-b border-indigo-200 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-medium text-indigo-700">
                        <span id="selected-count">0</span> tickets selected
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors" onclick="bulkAction('status')">
                        Change Status
                    </button>
                    <button type="button" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors" onclick="bulkAction('assign')">
                        Assign
                    </button>
                    <button type="button" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors" onclick="bulkAction('delete')">
                        Delete
                    </button>
                    <button type="button" class="px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors" onclick="clearSelection()">
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
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" title="Select all">
                        </th>
                        <th scope="col" class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider" style="width: 30%;">
                            Ticket
                        </th>
                        <th scope="col" class="px-2 sm:px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                            Status
                        </th>
                        <th scope="col" class="px-2 sm:px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                            Priority
                        </th>
                        <th scope="col" class="hidden md:table-cell px-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">
                            Category
                        </th>
                        <th scope="col" class="hidden lg:table-cell px-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">
                            Replies
                        </th>
                        <th scope="col" class="hidden lg:table-cell px-2 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-20">
                            Created
                        </th>
                        <th scope="col" class="relative px-2 py-3 w-16">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($tickets as $ticket)
                    <tr class="group hover:bg-gray-50 transition-colors duration-150" data-ticket-id="{{ $ticket->id }}">
                        <td class="px-3 py-3">
                            <input type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}" class="ticket-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="px-3 sm:px-4 py-3 overflow-hidden">
                            <div class="flex items-center min-w-0">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('super-admin.support.tickets.show', $ticket) }}" class="text-xs sm:text-sm font-bold text-indigo-600 hover:text-indigo-900 truncate">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </div>
                                    <div class="text-xs sm:text-sm text-gray-900 truncate mt-0.5">{{ $ticket->subject }}</div>
                                    <div class="text-xs text-gray-500 truncate">
                                        {{ $ticket->tenant->name }} &middot; {{ $ticket->user->name }}
                                    </div>
                                    <!-- Mobile-only: Show priority & category -->
                                    <div class="md:hidden mt-1 flex items-center space-x-1">
                                        @php
                                            $priorityColors = [
                                                'low' => 'bg-gray-100 text-gray-800',
                                                'medium' => 'bg-blue-100 text-blue-800',
                                                'high' => 'bg-orange-100 text-orange-800',
                                                'urgent' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $ticket->category->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-2 sm:px-3 py-3">
                            @php
                                $statusConfig = [
                                    'new' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'dot' => 'bg-red-400'],
                                    'open' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'dot' => 'bg-blue-400'],
                                    'in_progress' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'dot' => 'bg-yellow-400'],
                                    'waiting_customer' => ['class' => 'bg-purple-100 text-purple-800 border-purple-200', 'dot' => 'bg-purple-400'],
                                    'resolved' => ['class' => 'bg-green-100 text-green-800 border-green-200', 'dot' => 'bg-green-400'],
                                    'closed' => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'dot' => 'bg-gray-400'],
                                ];
                                $config = $statusConfig[$ticket->status] ?? $statusConfig['closed'];
                            @endphp
                            <div class="flex items-center min-w-0">
                                <div class="w-1.5 h-1.5 {{ $config['dot'] }} rounded-full mr-1 {{ in_array($ticket->status, ['new', 'open']) ? 'animate-pulse' : '' }}"></div>
                                <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-semibold border {{ $config['class'] }} truncate">
                                    <span class="sm:hidden">{{ substr(ucwords(str_replace('_', ' ', $ticket->status)), 0, 3) }}</span>
                                    <span class="hidden sm:inline truncate">{{ ucwords(str_replace('_', ' ', $ticket->status)) }}</span>
                                </span>
                            </div>
                        </td>
                        <td class="px-2 sm:px-3 py-3">
                            @php
                                $priorityConfig = [
                                    'low' => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'dot' => 'bg-gray-400'],
                                    'medium' => ['class' => 'bg-blue-100 text-blue-800 border-blue-200', 'dot' => 'bg-blue-400'],
                                    'high' => ['class' => 'bg-orange-100 text-orange-800 border-orange-200', 'dot' => 'bg-orange-400'],
                                    'urgent' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'dot' => 'bg-red-400'],
                                ];
                                $pConfig = $priorityConfig[$ticket->priority] ?? $priorityConfig['low'];
                            @endphp
                            <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded text-xs font-semibold border {{ $pConfig['class'] }} truncate">
                                <span class="sm:hidden">{{ substr(ucfirst($ticket->priority), 0, 1) }}</span>
                                <span class="hidden sm:inline truncate">{{ ucfirst($ticket->priority) }}</span>
                            </span>
                        </td>
                        <td class="hidden md:table-cell px-2 py-3">
                            <span class="text-xs text-gray-700 truncate block">{{ $ticket->category->name }}</span>
                        </td>
                        <td class="hidden lg:table-cell px-2 py-3">
                            <div class="flex items-center min-w-0">
                                <svg class="w-3 h-3 text-gray-400 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                                <span class="text-xs font-medium text-gray-900">{{ $ticket->replies->count() }}</span>
                            </div>
                        </td>
                        <td class="hidden lg:table-cell px-2 py-3">
                            <div class="text-xs text-gray-900 truncate">{{ $ticket->created_at->format('M j, Y') }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $ticket->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-2 py-3 text-right">
                            <div class="flex items-center justify-end space-x-1 opacity-60 group-hover:opacity-100 transition-opacity duration-200">
                                <a href="{{ route('super-admin.support.tickets.show', $ticket) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200 hover:scale-105"
                                   title="View Ticket">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">No tickets found</h3>
                                <p class="text-gray-500 mb-6 max-w-sm">Try adjusting your search criteria or filters to find what you're looking for.</p>
                                <a href="{{ route('super-admin.support.index') }}"
                                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Clear All Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            {{ $tickets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk selection functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const selectedCountSpan = document.getElementById('selected-count');

    function getTicketCheckboxes() {
        return document.querySelectorAll('.ticket-checkbox');
    }

    function updateBulkActionsBar() {
        const checkboxes = getTicketCheckboxes();
        const checkedBoxes = document.querySelectorAll('.ticket-checkbox:checked');
        const count = checkedBoxes.length;

        selectedCountSpan.textContent = count;

        if (count > 0) {
            bulkActionsBar.classList.remove('hidden');
        } else {
            bulkActionsBar.classList.add('hidden');
        }

        if (count === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (count === checkboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            getTicketCheckboxes().forEach(function(checkbox) {
                checkbox.checked = isChecked;
                const row = checkbox.closest('tr');
                if (isChecked) {
                    row.classList.add('bg-indigo-50');
                } else {
                    row.classList.remove('bg-indigo-50');
                }
            });
            updateBulkActionsBar();
        });
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('ticket-checkbox')) {
            const row = e.target.closest('tr');
            if (e.target.checked) {
                row.classList.add('bg-indigo-50');
            } else {
                row.classList.remove('bg-indigo-50');
            }
            updateBulkActionsBar();
        }
    });

    // Bulk actions
    window.bulkAction = function(action) {
        const checkedBoxes = document.querySelectorAll('.ticket-checkbox:checked');
        const ticketIds = Array.from(checkedBoxes).map(function(cb) { return cb.value; });

        if (ticketIds.length === 0) {
            alert('Please select at least one ticket.');
            return;
        }

        var confirmMessage = '';
        switch(action) {
            case 'status':
                confirmMessage = 'Change status for ' + ticketIds.length + ' selected ticket(s)?';
                break;
            case 'assign':
                confirmMessage = 'Assign ' + ticketIds.length + ' selected ticket(s)?';
                break;
            case 'delete':
                confirmMessage = 'Are you sure you want to delete ' + ticketIds.length + ' selected ticket(s)? This cannot be undone.';
                break;
        }

        if (confirm(confirmMessage)) {
            alert('Bulk ' + action + ' for ' + ticketIds.length + ' ticket(s) - Coming soon!');
        }
    };

    window.clearSelection = function() {
        getTicketCheckboxes().forEach(function(checkbox) {
            checkbox.checked = false;
            checkbox.closest('tr').classList.remove('bg-indigo-50');
        });
        updateBulkActionsBar();
    };
});
</script>
@endsection
