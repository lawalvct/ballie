@extends('layouts.tenant')

@section('title', 'Quotations - ' . $tenant->name)
@section('page-title', 'Quotations')
@section('page-description', 'Manage your quotations and track sales opportunities')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col space-y-3 md:flex-row md:items-center md:justify-between md:space-y-0">
        <div class="grid grid-cols-2 md:flex md:flex-wrap gap-2">
            <!-- Create Quotation Button -->
            <a href="{{ route('tenant.accounting.quotations.create', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center justify-center px-2 py-2 md:px-4 border border-teal-200 rounded-lg shadow-sm text-xs md:text-sm font-medium text-white bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                <svg class="w-3 h-3 md:w-4 md:h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="ml-1 md:ml-0">New Quotation</span>
            </a>
        </div>

        <div class="flex items-center space-x-2 md:space-x-3">
            <a href="{{ route('tenant.accounting.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-3 py-2 md:px-4 border border-gray-300 rounded-lg shadow-sm text-xs md:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
              <svg class="w-4 h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              <span class="hidden md:inline">Back to home</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Quotation number, customer name, subject..."
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                                id="status"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted</option>
                        </select>
                    </div>

                    <!-- Customer -->
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <select name="customer_id"
                                id="customer_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->company_name ?: ($customer->first_name . ' ' . $customer->last_name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date"
                               name="date_from"
                               id="date_from"
                               value="{{ request('date_from') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date"
                               name="date_to"
                               id="date_to"
                               value="{{ request('date_to') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm">
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        Apply Filters
                    </button>
                    <a href="{{ route('tenant.accounting.quotations.index', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center px-6 py-2 bg-gray-300 border border-transparent rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Quotations</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $quotations->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $quotations->where('status', 'sent')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Accepted</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $quotations->where('status', 'accepted')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Value</p>
                    <p class="text-2xl font-semibold text-gray-900">₦{{ number_format($quotations->sum('total_amount'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quotations</h3>
        </div>

        @if($quotations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quotation #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Expiry Date
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($quotations as $quotation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $quotation->quotation_number }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        QT-{{ $quotation->id }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $quotation->quotation_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($quotation->customer)
                                        {{ $quotation->customer->company_name ?: ($quotation->customer->first_name . ' ' . $quotation->customer->last_name) }}
                                    @elseif($quotation->vendor)
                                        {{ $quotation->vendor->company_name ?: ($quotation->vendor->first_name . ' ' . $quotation->vendor->last_name) }}
                                    @else
                                        <span class="text-gray-400">No Customer</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ Str::limit($quotation->subject ?: 'No subject', 30) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($quotation->expiry_date)
                                        {{ $quotation->expiry_date->format('M d, Y') }}
                                        @if($quotation->isExpired())
                                            <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Expired
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                    ₦{{ number_format($quotation->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @include('tenant.accounting.quotations.partials.status-badge', ['quotation' => $quotation])
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <!-- View -->
                                        <a href="{{ route('tenant.accounting.quotations.show', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}"
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                           title="View Quotation">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>

                                        <!-- Edit (only for draft) -->
                                        @if($quotation->status === 'draft')
                                            <a href="{{ route('tenant.accounting.quotations.edit', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}"
                                               class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50"
                                               title="Edit Quotation">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- Print -->
                                        <a href="{{ route('tenant.accounting.quotations.print', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}"
                                           target="_blank"
                                           class="text-gray-600 hover:text-gray-900 p-1 rounded hover:bg-gray-50"
                                           title="Print Quotation">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </a>

                                        <!-- Download PDF -->
                                        <a href="{{ route('tenant.accounting.quotations.pdf', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}"
                                           class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50"
                                           title="Download PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>

                                        <!-- More Actions Dropdown -->
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                    class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-50"
                                                    title="More Actions">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
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
                                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    @if($quotation->status === 'draft')
                                                        <!-- Send Quotation -->
                                                        <form method="POST" action="{{ route('tenant.accounting.quotations.send', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}" class="block">
                                                            @csrf
                                                            <button type="submit"
                                                                    onclick="return confirm('Are you sure you want to send this quotation?')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                                </svg>
                                                                Send Quotation
                                                            </button>
                                                        </form>

                                                        <!-- Delete Quotation -->
                                                        <form method="POST" action="{{ route('tenant.accounting.quotations.destroy', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}" class="block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    onclick="return confirm('Are you sure you want to delete this quotation?')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                                Delete Quotation
                                                            </button>
                                                        </form>
                                                    @elseif($quotation->status === 'sent')
                                                        <!-- Mark as Accepted -->
                                                        <form method="POST" action="{{ route('tenant.accounting.quotations.accept', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}" class="block">
                                                            @csrf
                                                            <button type="submit"
                                                                    onclick="return confirm('Mark this quotation as accepted?')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50 flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                Mark as Accepted
                                                            </button>
                                                        </form>

                                                        <!-- Mark as Rejected -->
                                                        <form method="POST" action="{{ route('tenant.accounting.quotations.reject', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}" class="block">
                                                            @csrf
                                                            <button type="submit"
                                                                    onclick="return confirm('Mark this quotation as rejected?')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                                Mark as Rejected
                                                            </button>
                                                        </form>
                                                    @elseif($quotation->status === 'accepted')
                                                        <!-- Convert to Invoice -->
                                                        <form method="POST" action="{{ route('tenant.accounting.quotations.convert', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}" class="block">
                                                            @csrf
                                                            <button type="submit"
                                                                    onclick="return confirm('Convert this quotation to an invoice?')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-purple-700 hover:bg-purple-50 flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                                Convert to Invoice
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <!-- Duplicate Quotation -->
                                                    <form method="POST" action="{{ route('tenant.accounting.quotations.duplicate', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}" class="block">
                                                        @csrf
                                                        <button type="submit"
                                                                onclick="return confirm('Create a copy of this quotation?')"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg>
                                                            Duplicate
                                                        </button>
                                                    </form>

                                                    <!-- Email Quotation -->
                                                    @if(in_array($quotation->status, ['sent', 'accepted']))
                                                        <form method="POST" action="{{ route('tenant.accounting.quotations.email', ['tenant' => $tenant->slug, 'quotation' => $quotation->id]) }}" class="block">
                                                            @csrf
                                                            <button type="submit"
                                                                    onclick="return confirm('Send this quotation via email?')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 flex items-center">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                                </svg>
                                                                Email Quotation
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $quotations->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No quotations found</h3>
                <p class="text-gray-500 mb-6">
                    @if(request()->hasAny(['search', 'status', 'customer_id', 'date_from', 'date_to']))
                        No quotations match your current filters. Try adjusting your search criteria.
                    @else
                        You haven't created any quotations yet. Create your first quotation to get started.
                    @endif
                </p>
                <a href="{{ route('tenant.accounting.quotations.create', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Your First Quotation
                </a>
            </div>
        @endif
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
