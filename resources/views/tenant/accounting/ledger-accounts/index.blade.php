@extends('layouts.tenant')

@section('title', 'Ledger Accounts')

@section('page-title', 'Ledger Accounts')
@section('page-description')
    <span class="hidden md:inline">
     Manage your chart of accounts and account hierarchy.
    </span>
@endsection
@push('styles')
<style>
    /* Accounting Tree Styles */
    .accounting-tree-container {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .nature-header {
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(8px);
    }

    .group-header {
        transition: all 0.2s ease-in-out;
    }

    .group-header:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .account-row {
        transition: all 0.2s ease-in-out;
        border-left: 3px solid transparent;
    }

    .account-row:hover {
        border-left-color: #3b82f6;
        transform: translateX(4px);
    }

    .account-node[data-level="0"] .account-row {
        background: linear-gradient(to right, #fafafa, #ffffff);
        border-left-width: 4px;
    }

    .account-node[data-level="1"] .account-row {
        background-color: #fcfcfc;
        border-left-width: 3px;
    }

    .account-node[data-level="2"] .account-row {
        background-color: #fdfdfd;
        border-left-width: 2px;
    }

    /* Tree lines and connectors */
    .group-accounts {
        position: relative;
    }

    .group-accounts::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 0;
        bottom: 0;
        width: 1px;
        background: linear-gradient(to bottom, #e2e8f0, #f1f5f9);
    }

    .child-accounts {
        position: relative;
    }

    .child-accounts::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 0;
        bottom: 0;
        width: 1px;
        background: #e2e8f0;
    }

    /* Toggle animations */
    .group-toggle svg,
    .account-toggle svg {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .group-toggle.expanded svg,
    .account-toggle.expanded svg {
        transform: rotate(90deg);
    }

    /* Balance display styling */
    .balance-positive {
        color: #059669;
        font-weight: 600;
    }

    .balance-negative {
        color: #dc2626;
        font-weight: 600;
    }

    .balance-zero {
        color: #64748b;
        font-weight: 500;
    }

    /* Hover effects for actions */
    .group:hover .opacity-0 {
        opacity: 1;
    }

    /* Account type badges */
    .account-type-asset {
        background-color: #dcfce7;
        color: #166534;
    }

    .account-type-liability {
        background-color: #fecaca;
        color: #991b1b;
    }

    .account-type-equity {
        background-color: #fef3c7;
        color: #92400e;
    }

    .account-type-income {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .account-type-expense {
        background-color: #e9d5ff;
        color: #7c2d12;
    }

    /* Custom scrollbar */
    .accounting-tree-container::-webkit-scrollbar {
        width: 8px;
    }

    .accounting-tree-container::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .accounting-tree-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .accounting-tree-container::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .nature-header {
            padding: 1rem;
        }

        .group-header {
            padding: 0.75rem;
        }

        .account-row {
            padding: 0.75rem;
        }

        .ml-6 { margin-left: 1rem; }
        .ml-12 { margin-left: 1.5rem; }
        .ml-18 { margin-left: 2rem; }
        .ml-24 { margin-left: 2.5rem; }
    }

    /* Print styles */
    @media print {
        .nature-header {
            background: white !important;
            border: 1px solid #000 !important;
        }

        .account-row {
            background: white !important;
            border: 1px solid #ccc !important;
        }

        .opacity-0 {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <button type="button"
                    onclick="openImportModal()"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Import Accounts
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- View Toggle -->
            <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1">
                <a href="{{ route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug, 'view' => 'list']) }}"
                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $viewType === 'list' ? 'bg-primary-100 text-primary-700' : 'text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    List
                </a>
                <a href="{{ route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug, 'view' => 'tree']) }}"
                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ $viewType === 'tree' ? 'bg-primary-100 text-primary-700' : 'text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v0M8 5a2 2 0 012-2h2a2 2 0 012 2v0"></path>
                    </svg>
                    Tree
                </a>
            </div>

            <a href="{{ route('tenant.accounting.ledger-accounts.create', $tenant) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Account
            </a>

            <a href="{{ route('tenant.accounting.ledger-accounts.export', $tenant) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
            </a>

            <a href="{{ route('tenant.accounting.ledger-accounts.download-pdf', $tenant) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                PDF
            </a>

        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800">
                        {{ session('warning') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(session('import_errors'))
        <div class="rounded-md bg-red-50 p-4 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Import Errors:</h3>
                    <div class="mt-2 text-sm text-red-700 max-h-40 overflow-y-auto">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Accounts</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $totalAccounts ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Accounts</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $activeAccounts ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">With Balance</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $accountsWithBalance ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v0M8 5a2 2 0 012-2h2a2 2 0 012 2v0"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Parent Accounts</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $parentAccounts ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="{ filtersExpanded: false }">
        <div class="p-6">
            <!-- Header with Toggle Button -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Search & Filters</h3>
                <button @click="filtersExpanded = !filtersExpanded"
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <span x-text="filtersExpanded ? 'Hide Filters' : 'Show Filters'"></span>
                    <svg class="ml-2 h-4 w-4 transition-transform duration-200"
                         :class="{ 'rotate-180': filtersExpanded }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            <!-- Always Visible Search Bar -->
            <form method="GET" class="space-y-4">
                <input type="hidden" name="view" value="{{ $viewType }}">

                <div class="flex-1">
                    <label for="search" class="sr-only">Search accounts</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Search by name, code, or description...">
                    </div>
                </div>

                <!-- Collapsible Filters Section -->
                <div x-show="filtersExpanded"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    <!-- Account Type Filter -->
                    <div>
                        <label for="account_type" class="block text-sm font-medium text-gray-700 mb-1">Account Type</label>
                        <select name="account_type"
                                id="account_type"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="">All Types</option>
                            <option value="asset" {{ request('account_type') === 'asset' ? 'selected' : '' }}>Asset</option>
                            <option value="liability" {{ request('account_type') === 'liability' ? 'selected' : '' }}>Liability</option>
                            <option value="equity" {{ request('account_type') === 'equity' ? 'selected' : '' }}>Equity</option>
                            <option value="income" {{ request('account_type') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('account_type') === 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>

                    <!-- Account Group Filter -->
                    <div>
                        <label for="account_group_id" class="block text-sm font-medium text-gray-700 mb-1">Account Group</label>
                        <select name="account_group_id"
                                id="account_group_id"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="">All Groups</option>
                            @foreach($accountGroups as $group)
                                <option value="{{ $group->id }}" {{ request('account_group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_active"
                                id="is_active"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort"
                                id="sort"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                            <option value="code" {{ request('sort') === 'code' ? 'selected' : '' }}>Code</option>
                            <option value="account_type" {{ request('sort') === 'account_type' ? 'selected' : '' }}>Type</option>
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                            </svg>
                            Apply Filters
                        </button>

                        @if(request()->hasAny(['search', 'account_type', 'account_group_id', 'is_active', 'sort']))
                            <a href="{{ route('tenant.accounting.ledger-accounts.index', ['tenant' => $tenant->slug, 'view' => $viewType]) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear All
                            </a>
                        @endif
                    </div>

                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['search', 'account_type', 'account_group_id', 'is_active']))
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm text-gray-500">Active filters:</span>

                            @if(request('search'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-primary-600 hover:text-primary-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            @if(request('account_type'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Type: {{ ucfirst(request('account_type')) }}
                                    <a href="{{ request()->fullUrlWithQuery(['account_type' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            @if(request('account_group_id'))
                                @php
                                    $selectedGroup = $accountGroups->find(request('account_group_id'));
                                @endphp
                                @if($selectedGroup)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Group: {{ $selectedGroup->name }}
                                        <a href="{{ request()->fullUrlWithQuery(['account_group_id' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    </span>
                                @endif
                            @endif

                            @if(request('is_active'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Status: {{ request('is_active') === '1' ? 'Active' : 'Inactive' }}
                                    <a href="{{ request()->fullUrlWithQuery(['is_active' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </a>
                                    </span>
                                @endif
                            @endif
                        </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Accounts Display -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        @if($viewType === 'tree')
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Account Hierarchy</h3>
                <p class="mt-1 text-sm text-gray-500">View accounts in a hierarchical tree structure</p>
            </div>
            <div class="p-6">
                @include('tenant.accounting.ledger-accounts.partials.account-tree', ['accounts' => $accounts])
            </div>
        @else
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Account List</h3>
                <p class="mt-1 text-sm text-gray-500">View all accounts in a detailed list format</p>
            </div>
            @include('tenant.accounting.ledger-accounts.partials.account-list', ['accounts' => $accounts])
        @endif
    </div>
</div>

<!-- Import Modal -->
@include('tenant.accounting.ledger-accounts.partials.import-modal')

<script>
// Global function to open import modal
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

// Auto-submit form on filter change (optional)
document.addEventListener('DOMContentLoaded', function() {
    const autoSubmitSelects = document.querySelectorAll('select[name="account_type"], select[name="account_group_id"], select[name="is_active"]');

    autoSubmitSelects.forEach(function(select) {
        select.addEventListener('change', function() {
            // Optional: Auto-submit on change
            // this.form.submit();
        });
    });
});
</script>
@endsection
