@extends('layouts.tenant')

@section('title', 'Create Physical Stock Voucher')

@push('styles')
<style>
    /* Custom difference indicator styles for JavaScript interaction */
    .difference-positive {
        @apply text-green-600 bg-green-50 border border-green-200 rounded px-2 py-1;
    }

    .difference-negative {
        @apply text-red-600 bg-red-50 border border-red-200 rounded px-2 py-1;
    }

    .difference-zero {
        @apply text-gray-600 bg-gray-50 border border-gray-200 rounded px-2 py-1;
    }

    /* Product search results positioning */
    .search-results {
        z-index: 50;
    }

    .search-result-item {
        @apply p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0;
    }
</style>

@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Physical Stock Voucher</h1>
            <p class="mt-2 text-gray-600">Record and manage physical stock adjustments</p>
        </div>
        <div class="mt-4 lg:mt-0">
            <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Physical Stock
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('tenant.inventory.physical-stock.store', ['tenant' => $tenant->slug]) }}" id="voucherForm">
        @csrf

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Display Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Voucher Details -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">Voucher Details</h3>
                <p class="mt-1 text-sm text-gray-600">Enter the basic information for the physical stock voucher.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="voucher_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Voucher Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="voucher_date"
                           id="voucher_date"
                           class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 {{ $errors->has('voucher_date') ? 'border-red-300' : 'border-gray-300' }}"
                           value="{{ old('voucher_date', now()->toDateString()) }}"
                           max="{{ now()->toDateString() }}"
                           required>
                    @error('voucher_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Select the date for stock counting</p>
                </div>

                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Reference Number
                    </label>
                    <input type="text"
                           name="reference_number"
                           id="reference_number"
                           class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 {{ $errors->has('reference_number') ? 'border-red-300' : 'border-gray-300' }}"
                           value="{{ old('reference_number') }}"
                           placeholder="e.g., PSC-2024-001">
                    @error('reference_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Optional external reference</p>
                </div>

                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                        Remarks
                    </label>
                    <input type="text"
                           name="remarks"
                           id="remarks"
                           class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 {{ $errors->has('remarks') ? 'border-red-300' : 'border-gray-300' }}"
                           value="{{ old('remarks') }}"
                           placeholder="e.g., Monthly stock count">
                    @error('remarks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Optional notes about this count</p>
                </div>
            </div>
        </div>

        <!-- Product Entries -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Product Entries</h3>
                    <p class="mt-1 text-sm text-gray-600">Add products to count and record physical quantities.</p>
                </div>
                <button type="button"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        id="addEntryBtn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Product
                </button>
            </div>

            <div id="entriesContainer">
                <!-- Entries will be added here dynamically -->
            </div>

            <div class="text-center py-12" id="noEntriesMessage">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Products Added Yet</h3>
                <p class="mt-2 text-gray-600">Start building your physical stock voucher by adding products to count.</p>
                <button type="button"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        id="addFirstProductBtn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Your First Product
                </button>
            </div>
        </div>

        <!-- Summary -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 hidden" id="summaryCard">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900">Summary</h3>
                <p class="mt-1 text-sm text-gray-600">Overview of stock adjustments and totals.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-blue-600" id="totalItems">0</div>
                    <div class="text-sm text-gray-600">Total Items</div>
                </div>

                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-green-600" id="totalExcess">₦0.00</div>
                    <div class="text-sm text-gray-600">Total Excess</div>
                </div>

                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-red-600" id="totalShortage">₦0.00</div>
                    <div class="text-sm text-gray-600">Total Shortage</div>
                </div>

                <div class="bg-indigo-50 rounded-lg p-4 text-center">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 bg-indigo-100 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="text-2xl font-bold text-indigo-600" id="netAdjustment">₦0.00</div>
                    <div class="text-sm text-gray-600">Net Adjustment</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mb-4 lg:mb-0">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>

                <div class="flex space-x-4">
                    <button type="submit"
                            name="action"
                            value="save_draft"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        Save as Draft
                    </button>

                    <button type="submit"
                            name="action"
                            value="submit"
                            class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Save & Submit for Approval
                    </button>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">
                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Draft vouchers can be edited later. Submitted vouchers require approval to process.
                </p>
            </div>
        </div>
    </form>
</div>

<!-- Entry Template -->
<template id="entryTemplate">
    <div class="entry-row bg-gray-50 border border-gray-200 rounded-lg p-6 mb-4" data-entry-index="">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700">Product Entry #<span class="entry-number"></span></span>
            </div>
            <button type="button" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded text-xs font-semibold text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 remove-entry">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Remove
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <!-- Product Selection -->
            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Product <span class="text-red-500">*</span>
                </label>
                <div class="product-search relative">
                    <input type="text"
                           class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 border-gray-300 product-search-input"
                           placeholder="Type product name or SKU..."
                           autocomplete="off">
                    <input type="hidden" name="entries[][product_id]" class="product-id-input" required>
                    <div class="search-results absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden"></div>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Type at least 2 characters to search
                </p>
            </div>

            <!-- Current Stock (Book Quantity) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Book Quantity</label>
                <input type="number"
                       class="block w-full rounded-lg shadow-sm bg-gray-50 border-gray-300 book-quantity"
                       step="0.0001"
                       readonly>
                <p class="mt-1 text-sm text-gray-500">System records</p>
            </div>

            <!-- Physical Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Physical Quantity <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       name="entries[][physical_quantity]"
                       class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 border-gray-300 physical-quantity"
                       step="0.0001"
                       min="0"
                       required>
                <p class="mt-1 text-sm text-gray-500">Counted stock</p>
            </div>

            <!-- Difference -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Difference</label>
                <div class="flex items-center justify-center h-10 bg-gray-50 border border-gray-300 rounded-lg">
                    <span class="difference-indicator difference-zero text-sm font-medium">0.00</span>
                </div>
                <p class="mt-1 text-sm text-gray-500">Physical - Book</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <!-- Batch Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number</label>
                <input type="text"
                       name="entries[][batch_number]"
                       class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 border-gray-300"
                       placeholder="e.g., LOT001">
                <p class="mt-1 text-sm text-gray-500">Optional batch info</p>
            </div>

            <!-- Expiry Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                <input type="date"
                       name="entries[][expiry_date]"
                       class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 border-gray-300"
                       min="{{ now()->addDay()->toDateString() }}">
                <p class="mt-1 text-sm text-gray-500">Future dates only</p>
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text"
                       name="entries[][location]"
                       class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 border-gray-300"
                       placeholder="e.g., Warehouse A">
                <p class="mt-1 text-sm text-gray-500">Storage location</p>
            </div>

            <!-- Remarks -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                <input type="text"
                       name="entries[][remarks]"
                       class="block w-full rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 border-gray-300"
                       placeholder="Optional notes">
                <p class="mt-1 text-sm text-gray-500">Additional notes</p>
            </div>
        </div>

        <!-- Product Info Display -->
        <div class="product-info mt-4 hidden">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <svg class="w-4 h-4 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong class="product-name text-blue-900"></strong>
                </div>
                <div class="flex flex-wrap gap-2 mb-3">
                    <span class="product-sku inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"></span>
                    <span class="product-category inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"></span>
                    <span class="product-unit inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">
                        <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Current Rate:
                        <span class="font-semibold">₦<span class="current-rate">0.00</span></span>
                    </span>
                    <span class="text-sm text-blue-600">
                        <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Product selected
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let entryIndex = 0;
    let searchTimeout;

    // Add new entry - both buttons
    const addEntryBtn = document.getElementById('addEntryBtn');
    const addFirstProductBtn = document.getElementById('addFirstProductBtn');

    if (addEntryBtn) {
        addEntryBtn.addEventListener('click', addNewEntry);
    }
    if (addFirstProductBtn) {
        addFirstProductBtn.addEventListener('click', addNewEntry);
    }

    // Remove entry - using event delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-entry') || e.target.closest('.remove-entry')) {
            const button = e.target.classList.contains('remove-entry') ? e.target : e.target.closest('.remove-entry');
            const entryRow = button.closest('.entry-row');
            if (entryRow) {
                entryRow.remove();
                updateEntryNumbers();
                updateArrayIndices();
                updateSummary();
                toggleNoEntriesMessage();
            }
        }
    });

    // Product search - using event delegation
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('product-search-input')) {
            const input = e.target;
            const results = input.parentNode.querySelector('.search-results');
            const query = input.value.trim();

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                results.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(function() {
                searchProducts(query, results, input);
            }, 300);
        }
    });

    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.product-search')) {
            const searchResults = document.querySelectorAll('.search-results');
            searchResults.forEach(result => {
                result.style.display = 'none';
            });
        }
    });

    // Physical quantity change - using event delegation
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('physical-quantity')) {
            const entry = e.target.closest('.entry-row');
            calculateDifference(entry);
            updateSummary();
        }
    });

    // Voucher date change
    const voucherDateInput = document.querySelector('input[name="voucher_date"]');
    if (voucherDateInput) {
        voucherDateInput.addEventListener('change', function() {
            const voucherDate = this.value;
            // Update all book quantities for the new date
            const entryRows = document.querySelectorAll('.entry-row');
            entryRows.forEach(function(entry) {
                const productIdInput = entry.querySelector('.product-id-input');
                if (productIdInput && productIdInput.value) {
                    updateBookQuantity(entry, productIdInput.value, voucherDate);
                }
            });
        });
    }

    function addNewEntry() {
        const template = document.getElementById('entryTemplate');
        if (!template) {
            console.error('Entry template not found');
            return;
        }

        const clone = template.content.cloneNode(true);

        entryIndex++;

        // Set entry index and number
        const entryRow = clone.querySelector('.entry-row');
        entryRow.setAttribute('data-entry-index', entryIndex);
        clone.querySelector('.entry-number').textContent = entryIndex;

        // Clear any values from template
        const inputs = clone.querySelectorAll('input');
        inputs.forEach(input => input.value = '');

        const productInfo = clone.querySelector('.product-info');
        if (productInfo) {
            productInfo.style.display = 'none';
        }

        const differenceIndicator = clone.querySelector('.difference-indicator');
        if (differenceIndicator) {
            differenceIndicator.textContent = '0.0000';
            differenceIndicator.className = 'difference-indicator difference-zero text-sm font-medium';
        }

        document.getElementById('entriesContainer').appendChild(clone);
        updateEntryNumbers();
        updateArrayIndices();
        toggleNoEntriesMessage();

        // Focus on the product search input of the new entry
        const newEntry = document.querySelector('#entriesContainer .entry-row:last-child');
        const searchInput = newEntry.querySelector('.product-search-input');
        if (searchInput) {
            searchInput.focus();
        }
    }

    function updateEntryNumbers() {
        const entryRows = document.querySelectorAll('.entry-row');
        entryRows.forEach(function(row, index) {
            const entryNumber = row.querySelector('.entry-number');
            if (entryNumber) {
                entryNumber.textContent = index + 1;
            }
        });
    }

    function updateArrayIndices() {
        const entryRows = document.querySelectorAll('.entry-row');
        entryRows.forEach(function(row, index) {
            // Update all input names to use sequential indices
            const inputs = row.querySelectorAll('input[name*="entries["]');
            inputs.forEach(function(input) {
                const name = input.getAttribute('name');
                if (name) {
                    // Replace the array index with the current sequential index
                    const newName = name.replace(/entries\[\d*\]/, `entries[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
    }    function toggleNoEntriesMessage() {
        const hasEntries = document.querySelectorAll('.entry-row').length > 0;
        const noEntriesMessage = document.getElementById('noEntriesMessage');
        const summaryCard = document.getElementById('summaryCard');

        if (hasEntries) {
            if (noEntriesMessage) noEntriesMessage.style.display = 'none';
            if (summaryCard) summaryCard.style.display = 'block';
        } else {
            if (noEntriesMessage) noEntriesMessage.style.display = 'block';
            if (summaryCard) summaryCard.style.display = 'none';
        }
    }

    function searchProducts(query, results, input) {
        const voucherDateInput = document.querySelector('input[name="voucher_date"]');
        const voucherDate = voucherDateInput ? voucherDateInput.value : '';

        fetch('{{ route("tenant.inventory.physical-stock.products-search", ["tenant" => $tenant->slug]) }}?' + new URLSearchParams({
            search: query,
            as_of_date: voucherDate
        }), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(products => {
            results.innerHTML = '';

            if (products.length === 0) {
                results.innerHTML = '<div class="search-result-item text-muted">No products found</div>';
            } else {
                products.forEach(function(product) {
                    const item = document.createElement('div');
                    item.className = 'search-result-item';
                    item.setAttribute('data-product-id', product.id);
                    item.innerHTML = `
                        <strong>${product.name}</strong>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 ml-2">${product.sku}</span>
                        <br>
                        <small class="text-gray-500">
                            ${product.category} | ${product.unit} |
                            Stock: ${product.current_stock} |
                            Rate: ₦${product.average_rate}
                        </small>
                    `;

                    item.addEventListener('click', function() {
                        selectProduct(input, product);
                    });

                    results.appendChild(item);
                });
            }

            results.style.display = 'block';
        })
        .catch(error => {
            results.innerHTML = '<div class="search-result-item text-red-600">Error loading products</div>';
            results.style.display = 'block';
            console.error('Error:', error);
        });
    }

    function selectProduct(input, product) {
        const entry = input.closest('.entry-row');
        const voucherDateInput = document.querySelector('input[name="voucher_date"]');
        const voucherDate = voucherDateInput ? voucherDateInput.value : '';

        // Set product details
        input.value = product.name;
        const productIdInput = entry.querySelector('.product-id-input');
        if (productIdInput) {
            productIdInput.value = product.id;
        }

        // Update product info display
        const productName = entry.querySelector('.product-name');
        const productSku = entry.querySelector('.product-sku');
        const productCategory = entry.querySelector('.product-category');
        const productUnit = entry.querySelector('.product-unit');
        const currentRate = entry.querySelector('.current-rate');
        const productInfo = entry.querySelector('.product-info');

        if (productName) productName.textContent = product.name;
        if (productSku) productSku.textContent = product.sku;
        if (productCategory) productCategory.textContent = product.category;
        if (productUnit) productUnit.textContent = product.unit;
        if (currentRate) currentRate.textContent = parseFloat(product.average_rate).toFixed(2);
        if (productInfo) productInfo.style.display = 'block';

        // Set book quantity
        const bookQuantity = entry.querySelector('.book-quantity');
        if (bookQuantity) {
            bookQuantity.value = parseFloat(product.current_stock).toFixed(4);
        }

        // Hide search results
        const searchResults = input.parentNode.querySelector('.search-results');
        if (searchResults) {
            searchResults.style.display = 'none';
        }

        // Calculate difference if physical quantity is entered
        calculateDifference(entry);
        updateSummary();
    }

    function updateBookQuantity(entry, productId, voucherDate) {
        fetch('{{ route("tenant.inventory.physical-stock.product-stock", ["tenant" => $tenant->slug]) }}?' + new URLSearchParams({
            product_id: productId,
            as_of_date: voucherDate
        }), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            const bookQuantity = entry.querySelector('.book-quantity');
            const currentRate = entry.querySelector('.current-rate');

            if (bookQuantity) {
                bookQuantity.value = parseFloat(data.stock_quantity).toFixed(4);
            }
            if (currentRate) {
                currentRate.textContent = parseFloat(data.average_rate).toFixed(2);
            }

            calculateDifference(entry);
            updateSummary();
        })
        .catch(error => {
            console.error('Error updating book quantity:', error);
        });
    }

    function calculateDifference(entry) {
        const bookQtyInput = entry.querySelector('.book-quantity');
        const physicalQtyInput = entry.querySelector('.physical-quantity');
        const indicator = entry.querySelector('.difference-indicator');

        if (!bookQtyInput || !physicalQtyInput || !indicator) return;

        const bookQty = parseFloat(bookQtyInput.value) || 0;
        const physicalQty = parseFloat(physicalQtyInput.value) || 0;
        const difference = physicalQty - bookQty;

        indicator.textContent = Math.abs(difference).toFixed(4);

        // Update styling based on difference
        indicator.className = 'difference-indicator text-sm font-medium';
        entry.classList.remove('has-difference');

        if (difference > 0) {
            indicator.classList.add('difference-positive');
            indicator.textContent = '+' + difference.toFixed(4);
            entry.classList.add('has-difference');
        } else if (difference < 0) {
            indicator.classList.add('difference-negative');
            indicator.textContent = difference.toFixed(4);
            entry.classList.add('has-difference');
        } else {
            indicator.classList.add('difference-zero');
        }
    }

    function updateSummary() {
        let totalItems = 0;
        let totalExcess = 0;
        let totalShortage = 0;

        const entryRows = document.querySelectorAll('.entry-row');
        entryRows.forEach(function(entry) {
            const bookQtyInput = entry.querySelector('.book-quantity');
            const physicalQtyInput = entry.querySelector('.physical-quantity');
            const currentRateSpan = entry.querySelector('.current-rate');
            const productIdInput = entry.querySelector('.product-id-input');

            if (productIdInput && productIdInput.value) {
                const bookQty = parseFloat(bookQtyInput ? bookQtyInput.value : 0) || 0;
                const physicalQty = parseFloat(physicalQtyInput ? physicalQtyInput.value : 0) || 0;
                const currentRate = parseFloat(currentRateSpan ? currentRateSpan.textContent : 0) || 0;
                const difference = physicalQty - bookQty;
                const differenceValue = Math.abs(difference) * currentRate;

                totalItems++;

                if (difference > 0) {
                    totalExcess += differenceValue;
                } else if (difference < 0) {
                    totalShortage += differenceValue;
                }
            }
        });

        const netAdjustment = totalExcess - totalShortage;

        const totalItemsEl = document.getElementById('totalItems');
        const totalExcessEl = document.getElementById('totalExcess');
        const totalShortageEl = document.getElementById('totalShortage');
        const netAdjustmentEl = document.getElementById('netAdjustment');

        if (totalItemsEl) totalItemsEl.textContent = totalItems;
        if (totalExcessEl) totalExcessEl.textContent = '₦' + totalExcess.toFixed(2);
        if (totalShortageEl) totalShortageEl.textContent = '₦' + totalShortage.toFixed(2);
        if (netAdjustmentEl) {
            netAdjustmentEl.textContent = '₦' + netAdjustment.toFixed(2);

            // Update net adjustment color
            netAdjustmentEl.classList.remove('text-green-600', 'text-red-600', 'text-blue-600');
            if (netAdjustment > 0) {
                netAdjustmentEl.classList.add('text-green-600');
            } else if (netAdjustment < 0) {
                netAdjustmentEl.classList.add('text-red-600');
            } else {
                netAdjustmentEl.classList.add('text-blue-600');
            }
        }
    }

    // Form validation
    const voucherForm = document.getElementById('voucherForm');
    if (voucherForm) {
        voucherForm.addEventListener('submit', function(e) {
            const hasEntries = document.querySelectorAll('.entry-row').length > 0;

            if (!hasEntries) {
                e.preventDefault();
                alert('Please add at least one product entry.');
                return false;
            }

            // Validate all entries have products selected
            let allValid = true;
            const entryRows = document.querySelectorAll('.entry-row');
            entryRows.forEach(function(entry) {
                const productIdInput = entry.querySelector('.product-id-input');
                const searchInput = entry.querySelector('.product-search-input');
                const physicalQtyInput = entry.querySelector('.physical-quantity');

                if (!productIdInput || !productIdInput.value) {
                    allValid = false;
                    if (searchInput) {
                        searchInput.classList.add('border-red-500');
                        searchInput.focus();
                    }
                } else {
                    if (searchInput) {
                        searchInput.classList.remove('border-red-500');
                    }
                }

                if (!physicalQtyInput || !physicalQtyInput.value) {
                    allValid = false;
                    if (physicalQtyInput) {
                        physicalQtyInput.classList.add('border-red-500');
                        if (allValid) physicalQtyInput.focus();
                    }
                } else {
                    if (physicalQtyInput) {
                        physicalQtyInput.classList.remove('border-red-500');
                    }
                }
            });

            if (!allValid) {
                e.preventDefault();
                alert('Please select products and enter physical quantities for all entries.');
                return false;
            }

            // Debug: Log form data before submission
            console.log('Form data being submitted:');
            const formData = new FormData(voucherForm);
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
        });
    }    // Initialize the page
    toggleNoEntriesMessage();
    updateSummary();
});
</script>
@endpush
