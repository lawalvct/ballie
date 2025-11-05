@extends('layouts.tenant')

@section('title', 'Audit Trail')

@section('page-title', 'Audit Trail')

@section('page-description', 'Comprehensive tracking of all user activities and system changes across your organization.')

@section('content')

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Real-time Status & Quick Actions --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse mr-2"></div>
                    <span class="text-sm text-gray-600">Audit logging active</span>
                </div>
                <div class="text-sm text-gray-500">
                    Retention: <span class="font-medium text-gray-700">90 days</span>
                </div>
            </div>
            <div class="mt-4 md:mt-0 flex flex-wrap items-center gap-3">
                <button onclick="refreshData()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh
                </button>
                <button onclick="window.print()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                </button>
                <a href="{{ route('tenant.audit.export', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Report
                </a>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
            {{-- Total Records --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Records</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_records'] ?? 0) }}</dd>
                                <dd class="text-xs text-gray-400 mt-1">All time</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Created Today --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Created Today</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['created_today'] ?? 0) }}</dd>
                                <dd class="text-xs text-green-600 mt-1">{{ now()->format('M j, Y') }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Updated Today --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Updated Today</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['updated_today'] ?? 0) }}</dd>
                                <dd class="text-xs text-yellow-600 mt-1">Modifications</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Posted Today --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Posted Today</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['posted_today'] ?? 0) }}</dd>
                                <dd class="text-xs text-purple-600 mt-1">Completed</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Users --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                                <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_users'] ?? 0) }}</dd>
                                <dd class="text-xs text-indigo-600 mt-1">Last 24 hours</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Advanced Filters --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Filters</h3>
                    <button type="button" onclick="toggleFilters()" class="text-sm text-indigo-600 hover:text-indigo-500">
                        <span id="filter-toggle-text">Show Advanced</span>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('tenant.audit.index', ['tenant' => $tenant->slug]) }}" id="filter-form">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                            <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Users</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}" {{ ($userFilter ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                            <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Actions</option>
                                <option value="created" {{ ($actionFilter ?? '') == 'created' ? 'selected' : '' }}>Created</option>
                                <option value="updated" {{ ($actionFilter ?? '') == 'updated' ? 'selected' : '' }}>Updated</option>
                                <option value="deleted" {{ ($actionFilter ?? '') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                <option value="posted" {{ ($actionFilter ?? '') == 'posted' ? 'selected' : '' }}>Posted</option>
                                <option value="restored" {{ ($actionFilter ?? '') == 'restored' ? 'selected' : '' }}>Restored</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Model Type</label>
                            <select name="model" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Types</option>
                                <option value="customer" {{ ($modelFilter ?? '') == 'customer' ? 'selected' : '' }}>Customers</option>
                                <option value="vendor" {{ ($modelFilter ?? '') == 'vendor' ? 'selected' : '' }}>Vendors</option>
                                <option value="product" {{ ($modelFilter ?? '') == 'product' ? 'selected' : '' }}>Products</option>
                                <option value="voucher" {{ ($modelFilter ?? '') == 'voucher' ? 'selected' : '' }}>Vouchers</option>
                                <option value="invoice" {{ ($modelFilter ?? '') == 'invoice' ? 'selected' : '' }}>Invoices</option>
                                <option value="payment" {{ ($modelFilter ?? '') == 'payment' ? 'selected' : '' }}>Payments</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                            <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                            <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search activities..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                    </div>

                    <div id="advanced-filters" class="hidden grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">IP Address</label>
                            <input type="text" name="ip_address" value="{{ request('ip_address') }}" placeholder="Filter by IP" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">User Agent</label>
                            <select name="user_agent" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Devices</option>
                                <option value="mobile">Mobile</option>
                                <option value="desktop">Desktop</option>
                                <option value="tablet">Tablet</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Range</label>
                            <select name="time_range" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Time</option>
                                <option value="1h">Last Hour</option>
                                <option value="24h">Last 24 Hours</option>
                                <option value="7d">Last 7 Days</option>
                                <option value="30d">Last 30 Days</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col xl:flex-row xl:justify-between xl:items-center">
                        <div class="text-sm text-gray-500 mb-2 sm:mb-0">
                            @if(method_exists($activities, 'total'))
                                Showing {{ ($activities->currentPage() - 1) * $activities->perPage() + 1 }} to {{ min($activities->currentPage() * $activities->perPage(), $activities->total()) }} of {{ $activities->total() }} results
                            @else
                                Showing {{ $activities->count() }} results
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('tenant.audit.index', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Clear Filters
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Activities Timeline --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Audit Timeline</h2>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-500">View:</span>
                        <button onclick="toggleView('timeline')" id="timeline-btn" class="px-3 py-1 text-sm rounded-md bg-indigo-100 text-indigo-700">Timeline</button>
                        <button onclick="toggleView('table')" id="table-btn" class="px-3 py-1 text-sm rounded-md text-gray-500 hover:bg-gray-100">Table</button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if(($activities ?? collect())->count() > 0)
                    <div id="timeline-view" class="space-y-6">
                        @foreach($activities ?? [] as $activity)
                            <div class="relative flex flex-col items-start space-x-0 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group">
                                {{-- Timeline Line --}}
                                @if(!$loop->last)
                                    <div class="absolute left-8 top-16 w-0.5 h-full bg-gray-200"></div>
                                @endif

                                {{-- Action Icon --}}
                                <div class="flex-shrink-0">
                                    @php
                                        $actionConfig = match($activity['action'] ?? 'default') {
                                            'created' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                                            'updated' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-600', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                            'deleted' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'icon' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'],
                                            'posted' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                            'restored' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z']
                                        };
                                    @endphp
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $actionConfig['bg'] }} {{ $actionConfig['text'] }} ring-4 ring-white shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $actionConfig['icon'] }}" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Activity Content --}}
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <h4 class="text-sm font-semibold text-gray-900">{{ $activity['details'] ?? 'Activity performed' }}</h4>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $actionConfig['bg'] }} {{ $actionConfig['text'] }}">
                                                    {{ ucfirst($activity['action'] ?? 'unknown') }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col items-start space-y-2 text-sm text-gray-600">
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <span class="font-medium">{{ $activity['user']->name ?? 'System' }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    <span>{{ $activity['model'] ?? 'Unknown' }}</span>
                                                </div>
                                                @if(isset($activity['ip_address']))
                                                    <div class="flex items-center space-x-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                                        </svg>
                                                        <span class="text-xs">{{ $activity['ip_address'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if(isset($activity['changes']) && !empty($activity['changes']))
                                                <div class="mt-2 p-2 bg-white rounded border border-gray-200">
                                                    <details class="text-xs">
                                                        <summary class="cursor-pointer text-gray-600 hover:text-gray-800">View Changes</summary>
                                                        <div class="mt-2 space-y-1">
                                                            @foreach($activity['changes'] as $field => $change)
                                                                <div class="flex flex-col justify-between">
                                                                    <span class="font-medium">{{ $field }}:</span>
                                                                    <span class="text-red-600">{{ $change['old'] ?? 'null' }}</span>
                                                                    <span>→</span>
                                                                    <span class="text-green-600">{{ $change['new'] ?? 'null' }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </details>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col items-start space-y-2 ml-0 mt-2">
                                            <div class="text-right">
                                                <div class="text-sm text-gray-900">{{ $activity['timestamp']->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $activity['timestamp']->format('H:i:s') }}</div>
                                                <div class="text-xs text-gray-400">{{ $activity['timestamp']->diffForHumans() }}</div>
                                            </div>
                                            @if(isset($activity['model']) && isset($activity['id']))
                                                <a href="{{ route('tenant.audit.show', ['tenant' => $tenant->slug, 'model' => strtolower($activity['model']), 'id' => $activity['id']]) }}"
                                                   class="p-2 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-md transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Table View (Hidden by default) --}}
                    <div id="table-view" class="hidden overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($activities ?? [] as $activity)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $actionConfig['bg'] ?? 'bg-gray-100' }} {{ $actionConfig['text'] ?? 'text-gray-600' }}">
                                                {{ ucfirst($activity['action'] ?? 'unknown') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">{{ $activity['details'] ?? 'Activity performed' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $activity['user']->name ?? 'System' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $activity['model'] ?? 'Unknown' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $activity['timestamp']->format('M d, Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if(isset($activity['model']) && isset($activity['id']))
                                                <a href="{{ route('tenant.audit.show', ['tenant' => $tenant->slug, 'model' => strtolower($activity['model']), 'id' => $activity['id']]) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if(isset($activities) && method_exists($activities, 'links'))
                        <div class="mt-6">
                            {{ $activities->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No audit records found</h3>
                        <p class="mt-1 text-sm text-gray-500">No activities match your current filter criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleFilters() {
        const advancedFilters = document.getElementById('advanced-filters');
        const toggleText = document.getElementById('filter-toggle-text');
        
        if (advancedFilters.classList.contains('hidden')) {
            advancedFilters.classList.remove('hidden');
            toggleText.textContent = 'Hide Advanced';
        } else {
            advancedFilters.classList.add('hidden');
            toggleText.textContent = 'Show Advanced';
        }
    }

    function toggleView(view) {
        const timelineView = document.getElementById('timeline-view');
        const tableView = document.getElementById('table-view');
        const timelineBtn = document.getElementById('timeline-btn');
        const tableBtn = document.getElementById('table-btn');

        if (view === 'timeline') {
            timelineView.classList.remove('hidden');
            tableView.classList.add('hidden');
            timelineBtn.classList.add('bg-indigo-100', 'text-indigo-700');
            timelineBtn.classList.remove('text-gray-500', 'hover:bg-gray-100');
            tableBtn.classList.remove('bg-indigo-100', 'text-indigo-700');
            tableBtn.classList.add('text-gray-500', 'hover:bg-gray-100');
        } else {
            timelineView.classList.add('hidden');
            tableView.classList.remove('hidden');
            tableBtn.classList.add('bg-indigo-100', 'text-indigo-700');
            tableBtn.classList.remove('text-gray-500', 'hover:bg-gray-100');
            timelineBtn.classList.remove('bg-indigo-100', 'text-indigo-700');
            timelineBtn.classList.add('text-gray-500', 'hover:bg-gray-100');
        }
    }

    function refreshData() {
        const button = event.target;
        const originalContent = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Refreshing...';
        
        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    // Auto-submit form on filter change
    document.querySelectorAll('#filter-form select, #filter-form input[type="date"]').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    });

    // Debounced search
    let searchTimeout;
    document.querySelector('input[name="search"]').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                document.getElementById('filter-form').submit();
            }
        }, 500);
    });
</script>
@endpush
