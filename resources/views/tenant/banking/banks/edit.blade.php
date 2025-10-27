@extends('layouts.tenant')

@section('title', 'Edit Bank Account - ' . $bank->bank_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Bank Account</h1>
            <p class="mt-2 text-gray-600">Update bank account details and settings</p>

            <!-- Breadcrumb -->
            <nav class="flex mt-3" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('tenant.dashboard', $tenant) }}"
                           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L8 5.414V17a1 1 0 102 0V5.414l6.293 6.293a1 1 0 001.414-1.414l-9-9z"></path>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('tenant.banking.banks.index', $tenant) }}"
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-emerald-600 md:ml-2">
                                Bank Accounts
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('tenant.banking.banks.show', [$tenant, $bank->id]) }}"
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-emerald-600 md:ml-2">
                                {{ $bank->bank_name }}
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3">
            <a href="{{ route('tenant.banking.banks.show', [$tenant, $bank->id]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Account
            </a>
            <a href="{{ route('tenant.banking.banks.index', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('tenant.banking.banks.update', [$tenant, $bank->id]) }}"
          method="POST"
          id="bankForm"
          x-data="bankForm()">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form - Takes 2 columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Update the bank account details</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Bank Name -->
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="bank_name"
                                       id="bank_name"
                                       value="{{ old('bank_name', $bank->bank_name) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('bank_name') border-red-300 @enderror"
                                       required>
                                @error('bank_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Name -->
                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Name/Holder <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="account_name"
                                       id="account_name"
                                       value="{{ old('account_name', $bank->account_name) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('account_name') border-red-300 @enderror"
                                       required>
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Account Number -->
                            <div>
                                <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="account_number"
                                       id="account_number"
                                       value="{{ old('account_number', $bank->account_number) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('account_number') border-red-300 @enderror"
                                       required>
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Must be unique</p>
                            </div>

                            <!-- Account Type -->
                            <div>
                                <label for="account_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Type <span class="text-red-500">*</span>
                                </label>
                                <select name="account_type"
                                        id="account_type"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('account_type') border-red-300 @enderror"
                                        required>
                                    <option value="">Select Type</option>
                                    <option value="savings" {{ old('account_type', $bank->account_type) === 'savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="current" {{ old('account_type', $bank->account_type) === 'current' ? 'selected' : '' }}>Current/Checking</option>
                                    <option value="fixed_deposit" {{ old('account_type', $bank->account_type) === 'fixed_deposit' ? 'selected' : '' }}>Fixed Deposit</option>
                                    <option value="credit_card" {{ old('account_type', $bank->account_type) === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="loan" {{ old('account_type', $bank->account_type) === 'loan' ? 'selected' : '' }}>Loan Account</option>
                                    <option value="investment" {{ old('account_type', $bank->account_type) === 'investment' ? 'selected' : '' }}>Investment</option>
                                    <option value="other" {{ old('account_type', $bank->account_type) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('account_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Currency -->
                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                                    Currency <span class="text-red-500">*</span>
                                </label>
                                <select name="currency"
                                        id="currency"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('currency') border-red-300 @enderror"
                                        required>
                                    <option value="NGN" {{ old('currency', $bank->currency) === 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira</option>
                                    <option value="USD" {{ old('currency', $bank->currency) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('currency', $bank->currency) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ old('currency', $bank->currency) === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                </select>
                                @error('currency')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Opening Balance (Read Only) -->
                            <div>
                                <label for="opening_balance_display" class="block text-sm font-medium text-gray-700 mb-2">
                                    Opening Balance (Read-only)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₦</span>
                                    </div>
                                    <input type="text"
                                           id="opening_balance_display"
                                           value="{{ number_format($bank->opening_balance, 2) }}"
                                           class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-500"
                                           readonly>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Opening balance cannot be changed</p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Account Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    id="status"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('status') border-red-300 @enderror"
                                    required>
                                <option value="active" {{ old('status', $bank->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $bank->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="closed" {{ old('status', $bank->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="suspended" {{ old('status', $bank->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description/Notes
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-red-300 @enderror"
                                      placeholder="Enter any additional notes">{{ old('description', $bank->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Branch Details -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Branch Details</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="branch_name" class="block text-sm font-medium text-gray-700 mb-2">Branch Name</label>
                                <input type="text" name="branch_name" id="branch_name"
                                       value="{{ old('branch_name', $bank->branch_name) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="branch_code" class="block text-sm font-medium text-gray-700 mb-2">Branch Code</label>
                                <input type="text" name="branch_code" id="branch_code"
                                       value="{{ old('branch_code', $bank->branch_code) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <div>
                            <label for="branch_address" class="block text-sm font-medium text-gray-700 mb-2">Branch Address</label>
                            <textarea name="branch_address" id="branch_address" rows="2"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">{{ old('branch_address', $bank->branch_address) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="branch_city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" name="branch_city" id="branch_city"
                                       value="{{ old('branch_city', $bank->branch_city) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="branch_state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                                <input type="text" name="branch_state" id="branch_state"
                                       value="{{ old('branch_state', $bank->branch_state) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="branch_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="text" name="branch_phone" id="branch_phone"
                                       value="{{ old('branch_phone', $bank->branch_phone) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- International Codes -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <button type="button"
                            @click="showInternational = !showInternational"
                            class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center">
                            <h3 class="text-lg font-medium text-gray-900">International Codes & IDs</h3>
                            <span class="ml-2 text-xs text-gray-500">(Optional)</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform"
                             :class="{ 'rotate-180': showInternational }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="showInternational" x-transition class="p-6 border-t border-gray-200" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="swift_code" class="block text-sm font-medium text-gray-700 mb-2">SWIFT/BIC Code</label>
                                <input type="text" name="swift_code" id="swift_code"
                                       value="{{ old('swift_code', $bank->swift_code) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="iban" class="block text-sm font-medium text-gray-700 mb-2">IBAN</label>
                                <input type="text" name="iban" id="iban"
                                       value="{{ old('iban', $bank->iban) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="routing_number" class="block text-sm font-medium text-gray-700 mb-2">Routing Number</label>
                                <input type="text" name="routing_number" id="routing_number"
                                       value="{{ old('routing_number', $bank->routing_number) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="sort_code" class="block text-sm font-medium text-gray-700 mb-2">Sort Code</label>
                                <input type="text" name="sort_code" id="sort_code"
                                       value="{{ old('sort_code', $bank->sort_code) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Current Info -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Current Account</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="h-12 w-12 rounded-full {{ $bank->status === 'active' ? 'bg-emerald-100' : 'bg-gray-100' }} flex items-center justify-center">
                                <svg class="w-6 h-6 {{ $bank->status === 'active' ? 'text-emerald-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $bank->bank_name }}</h4>
                                <p class="text-sm text-gray-500">{{ $bank->masked_account_number }}</p>
                            </div>
                        </div>

                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Current Balance</dt>
                                <dd class="text-sm font-medium text-gray-900">₦{{ number_format($bank->getCurrentBalance(), 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Status</dt>
                                <dd><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $bank->status_color }}">{{ ucfirst($bank->status) }}</span></dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Created</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $bank->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Account Settings</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_primary" id="is_primary" value="1"
                                       {{ old('is_primary', $bank->is_primary) ? 'checked' : '' }}
                                       class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_primary" class="font-medium text-gray-700">Primary Account</label>
                                <p class="text-gray-500">Default bank for transactions</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_payroll_account" id="is_payroll_account" value="1"
                                       {{ old('is_payroll_account', $bank->is_payroll_account) ? 'checked' : '' }}
                                       class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_payroll_account" class="font-medium text-gray-700">Payroll Account</label>
                                <p class="text-gray-500">For employee payments</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="enable_reconciliation" id="enable_reconciliation" value="1"
                                       {{ old('enable_reconciliation', $bank->enable_reconciliation) ? 'checked' : '' }}
                                       class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="enable_reconciliation" class="font-medium text-gray-700">Enable Reconciliation</label>
                                <p class="text-gray-500">Track bank statements</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Limits -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Account Limits</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label for="minimum_balance" class="block text-sm font-medium text-gray-700 mb-2">Minimum Balance</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₦</span>
                                </div>
                                <input type="number" name="minimum_balance" id="minimum_balance"
                                       value="{{ old('minimum_balance', $bank->minimum_balance) }}"
                                       class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                       step="0.01" min="0">
                            </div>
                        </div>

                        <div>
                            <label for="overdraft_limit" class="block text-sm font-medium text-gray-700 mb-2">Overdraft Limit</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₦</span>
                                </div>
                                <input type="number" name="overdraft_limit" id="overdraft_limit"
                                       value="{{ old('overdraft_limit', $bank->overdraft_limit) }}"
                                       class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                       step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6 space-y-3">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Bank Account
                        </button>

                        <a href="{{ route('tenant.banking.banks.show', [$tenant, $bank->id]) }}"
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Help -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Editing Tips</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Account number must remain unique</li>
                                    <li>Opening balance cannot be modified</li>
                                    <li>Only one primary account allowed</li>
                                    <li>Changes sync to ledger account</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function bankForm() {
    return {
        showInternational: {{ $bank->swift_code || $bank->iban || $bank->routing_number ? 'true' : 'false' }},

        init() {
            // Initialize form
        }
    }
}
</script>
@endpush
