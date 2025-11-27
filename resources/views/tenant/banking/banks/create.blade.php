@extends('layouts.tenant')

@section('title', 'Add Bank Account')
@section('page-title', 'Add Bank Account')
@section('page-description', 'Set up a new bank account for your business')

@php
    $breadcrumbs = [];
@endphp

@section('content')
<div class="space-y-6">

    <!-- Form -->
    <form action="{{ route('tenant.banking.banks.store', $tenant) }}"
          method="POST"
          id="bankForm"
          x-data="bankForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form - Takes 2 columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Enter the basic bank account details</p>
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
                                       value="{{ old('bank_name') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('bank_name') border-red-300 @enderror"
                                       placeholder="e.g., First Bank of Nigeria"
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
                                       value="{{ old('account_name') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('account_name') border-red-300 @enderror"
                                       placeholder="Account holder name"
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
                                       value="{{ old('account_number') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('account_number') border-red-300 @enderror"
                                       placeholder="1234567890"
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
                                    <option value="savings" {{ old('account_type') === 'savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="current" {{ old('account_type') === 'current' ? 'selected' : '' }}>Current/Checking</option>
                                    <option value="fixed_deposit" {{ old('account_type') === 'fixed_deposit' ? 'selected' : '' }}>Fixed Deposit</option>
                                    <option value="credit_card" {{ old('account_type') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="loan" {{ old('account_type') === 'loan' ? 'selected' : '' }}>Loan Account</option>
                                    <option value="investment" {{ old('account_type') === 'investment' ? 'selected' : '' }}>Investment</option>
                                    <option value="other" {{ old('account_type') === 'other' ? 'selected' : '' }}>Other</option>
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
                                    <option value="NGN" {{ old('currency', 'NGN') === 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira</option>
                                    <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                </select>
                                @error('currency')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Opening Balance -->
                            <div>
                                <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                    Opening Balance
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">₦</span>
                                    </div>
                                    <input type="number"
                                           name="opening_balance"
                                           id="opening_balance"
                                           value="{{ old('opening_balance', 0) }}"
                                           class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('opening_balance') border-red-300 @enderror"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00">
                                    @error('opening_balance')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
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
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
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
                                      placeholder="Enter any additional notes about this account">{{ old('description') }}</textarea>
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
                        <p class="mt-1 text-sm text-gray-500">Information about the bank branch</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Branch Name -->
                            <div>
                                <label for="branch_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Branch Name
                                </label>
                                <input type="text"
                                       name="branch_name"
                                       id="branch_name"
                                       value="{{ old('branch_name') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('branch_name') border-red-300 @enderror"
                                       placeholder="e.g., Lagos Island Branch">
                                @error('branch_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Branch Code -->
                            <div>
                                <label for="branch_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Branch Code
                                </label>
                                <input type="text"
                                       name="branch_code"
                                       id="branch_code"
                                       value="{{ old('branch_code') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('branch_code') border-red-300 @enderror"
                                       placeholder="Branch code">
                                @error('branch_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Branch Address -->
                        <div>
                            <label for="branch_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Branch Address
                            </label>
                            <textarea name="branch_address"
                                      id="branch_address"
                                      rows="2"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('branch_address') border-red-300 @enderror"
                                      placeholder="Branch address">{{ old('branch_address') }}</textarea>
                            @error('branch_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Branch City -->
                            <div>
                                <label for="branch_city" class="block text-sm font-medium text-gray-700 mb-2">
                                    City
                                </label>
                                <input type="text"
                                       name="branch_city"
                                       id="branch_city"
                                       value="{{ old('branch_city') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('branch_city') border-red-300 @enderror"
                                       placeholder="City">
                                @error('branch_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Branch State -->
                            <div>
                                <label for="branch_state" class="block text-sm font-medium text-gray-700 mb-2">
                                    State
                                </label>
                                <input type="text"
                                       name="branch_state"
                                       id="branch_state"
                                       value="{{ old('branch_state') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('branch_state') border-red-300 @enderror"
                                       placeholder="State">
                                @error('branch_state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Branch Phone -->
                            <div>
                                <label for="branch_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone
                                </label>
                                <input type="text"
                                       name="branch_phone"
                                       id="branch_phone"
                                       value="{{ old('branch_phone') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 @error('branch_phone') border-red-300 @enderror"
                                       placeholder="Phone">
                                @error('branch_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- International Codes (Collapsible) -->
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
                    <div x-show="showInternational"
                         x-transition
                         class="p-6 border-t border-gray-200"
                         style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="swift_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    SWIFT/BIC Code
                                </label>
                                <input type="text"
                                       name="swift_code"
                                       id="swift_code"
                                       value="{{ old('swift_code') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                       placeholder="e.g., FBNINGLA">
                            </div>

                            <div>
                                <label for="iban" class="block text-sm font-medium text-gray-700 mb-2">
                                    IBAN
                                </label>
                                <input type="text"
                                       name="iban"
                                       id="iban"
                                       value="{{ old('iban') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                       placeholder="International Bank Account Number">
                            </div>

                            <div>
                                <label for="routing_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Routing Number
                                </label>
                                <input type="text"
                                       name="routing_number"
                                       id="routing_number"
                                       value="{{ old('routing_number') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                       placeholder="Routing number (US)">
                            </div>

                            <div>
                                <label for="sort_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Sort Code
                                </label>
                                <input type="text"
                                       name="sort_code"
                                       id="sort_code"
                                       value="{{ old('sort_code') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                       placeholder="Sort code (UK)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Takes 1 column -->
            <div class="space-y-6">
                <!-- Account Flags -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Account Settings</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Primary Account -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       name="is_primary"
                                       id="is_primary"
                                       value="1"
                                       {{ old('is_primary') ? 'checked' : '' }}
                                       class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_primary" class="font-medium text-gray-700">Primary Account</label>
                                <p class="text-gray-500">Set as default bank account for transactions</p>
                            </div>
                        </div>

                        <!-- Payroll Account -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       name="is_payroll_account"
                                       id="is_payroll_account"
                                       value="1"
                                       {{ old('is_payroll_account') ? 'checked' : '' }}
                                       class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_payroll_account" class="font-medium text-gray-700">Payroll Account</label>
                                <p class="text-gray-500">Use this account for employee payments</p>
                            </div>
                        </div>

                        <!-- Enable Reconciliation -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox"
                                       name="enable_reconciliation"
                                       id="enable_reconciliation"
                                       value="1"
                                       {{ old('enable_reconciliation', true) ? 'checked' : '' }}
                                       class="focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="enable_reconciliation" class="font-medium text-gray-700">Enable Reconciliation</label>
                                <p class="text-gray-500">Track and reconcile bank statements</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Limits (Optional) -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Account Limits</h3>
                        <p class="mt-1 text-xs text-gray-500">Optional limits and balances</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Minimum Balance -->
                        <div>
                            <label for="minimum_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                Minimum Balance
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₦</span>
                                </div>
                                <input type="number"
                                       name="minimum_balance"
                                       id="minimum_balance"
                                       value="{{ old('minimum_balance', 0) }}"
                                       class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                        </div>

                        <!-- Overdraft Limit -->
                        <div>
                            <label for="overdraft_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                Overdraft Limit
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₦</span>
                                </div>
                                <input type="number"
                                       name="overdraft_limit"
                                       id="overdraft_limit"
                                       value="{{ old('overdraft_limit', 0) }}"
                                       class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6 space-y-3">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Create Bank Account
                        </button>

                        <a href="{{ route('tenant.banking.banks.index', $tenant) }}"
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Help Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Bank Account Tips</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Account numbers must be unique</li>
                                    <li>Opening balance creates initial ledger entry</li>
                                    <li>Only one primary account allowed</li>
                                    <li>International codes optional for local banks</li>
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
        showInternational: false,

        init() {
            // Initialize any Alpine.js specific setup here
        }
    }
}
</script>
@endpush
