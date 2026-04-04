@extends('layouts.tenant')

@section('title', 'Create Ledger Account')
@section('page-title', 'Create Ledger Account')
@section('page-description')
    <span class="hidden md:inline">
        Add a new account to your chart of accounts
    </span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- BallieAI Quick Create Banner -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center text-white">
                <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Create with BallieAI</h3>
                    <p class="text-purple-100 text-sm">Describe your account in plain English and let AI do the rest</p>
                </div>
            </div>
            <button type="button"
                    onclick="openAIAccountModal()"
                    class="inline-flex items-center px-5 py-2.5 bg-white text-purple-700 font-semibold rounded-lg hover:bg-purple-50 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Try BallieAI
            </button>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('tenant.accounting.ledger-accounts.store', $tenant) }}"
          method="POST"
          id="accountForm"
          x-data="accountForm()"
          @submit="validateForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form - 2 columns -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Account Details Card -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Account Details</h3>
                        <p class="mt-1 text-sm text-gray-500">Fill in account name and type &mdash; we'll handle the rest</p>
                    </div>
                    <div class="p-6 space-y-5">

                        <!-- Account Name (Primary field) -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Account Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-base @error('name') border-red-300 @enderror"
                                   placeholder="e.g. Cash in Hand, Office Rent, Sales Revenue"
                                   required
                                   autofocus>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Account Type -->
                            <div>
                                <label for="account_type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Account Type <span class="text-red-500">*</span>
                                </label>
                                <select name="account_type"
                                        id="account_type"
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('account_type') border-red-300 @enderror"
                                        required
                                        x-on:change="handleAccountTypeChange">
                                    <option value="">Select Type</option>
                                    <option value="asset" {{ old('account_type') === 'asset' ? 'selected' : '' }}>Asset</option>
                                    <option value="liability" {{ old('account_type') === 'liability' ? 'selected' : '' }}>Liability</option>
                                    <option value="equity" {{ old('account_type') === 'equity' ? 'selected' : '' }}>Equity</option>
                                    <option value="income" {{ old('account_type') === 'income' ? 'selected' : '' }}>Income</option>
                                    <option value="expense" {{ old('account_type') === 'expense' ? 'selected' : '' }}>Expense</option>
                                </select>
                                @error('account_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Group -->
                            <div>
                                <label for="account_group_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Account Group <span class="text-red-500">*</span>
                                </label>
                                <select name="account_group_id"
                                        id="account_group_id"
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('account_group_id') border-red-300 @enderror"
                                        required>
                                    <option value="">Select Group</option>
                                    @foreach($accountGroups as $group)
                                        <option value="{{ $group->id }}"
                                                data-nature="{{ $group->nature }}"
                                                {{ old('account_group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_group_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Account Code (Auto-generated) -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                    Account Code <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text"
                                           name="code"
                                           id="code"
                                           value="{{ old('code') }}"
                                           class="block w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('code') border-red-300 @enderror"
                                           placeholder="Auto-generated"
                                           required>
                                    <div id="codeSpinner" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                        <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <div id="codeCheck" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                                        <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Auto-assigned when you pick a type. You can edit it.</p>
                            </div>

                            <!-- Opening Balance -->
                            <div>
                                <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-1">
                                    Opening Balance
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">&#8358;</span>
                                    </div>
                                    <input type="number"
                                           name="opening_balance"
                                           id="opening_balance"
                                           value="{{ old('opening_balance', 0) }}"
                                           class="block w-full pl-8 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('opening_balance') border-red-300 @enderror"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00">
                                </div>
                                @error('opening_balance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="2"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-300 @enderror"
                                      placeholder="Brief description of this account">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden balance_type (auto-set from account_type) -->
                        <input type="hidden" name="balance_type" id="balance_type" value="{{ old('balance_type', 'dr') }}">
                    </div>
                </div>

                <!-- Advanced Options (Collapsed) -->
                <div x-data="{ showAdvanced: {{ old('parent_id') || old('address') || old('phone') || old('email') ? 'true' : 'false' }} }" class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <button type="button"
                            @click="showAdvanced = !showAdvanced"
                            class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Advanced Options</span>
                            <span class="ml-2 text-xs text-gray-400">(Parent account, contact info)</span>
                        </div>
                        <svg :class="showAdvanced ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="showAdvanced" x-transition class="px-6 pb-6 space-y-5 border-t border-gray-200 pt-5">
                        <!-- Parent Account -->
                        <div>
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Parent Account
                            </label>
                            <select name="parent_id"
                                    id="parent_id"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                <option value="">No Parent (Main Account)</option>
                                @foreach($parentAccounts as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->code }} - {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Make this a sub-account of an existing account</p>
                        </div>

                        <!-- Contact Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Phone number">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Email address">
                            </div>
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" id="address" rows="2"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                      placeholder="Address">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Quick Info Card (shown when type is selected) -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-5" x-show="selectedType" x-transition>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Account Summary
                    </h4>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Type</span>
                            <span class="font-medium text-gray-900 capitalize" x-text="selectedType || '—'"></span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                            <span class="text-gray-500">Balance Type</span>
                            <span class="font-medium" :class="balanceType === 'dr' ? 'text-blue-600' : 'text-green-600'" x-text="balanceType === 'dr' ? 'Debit (Dr)' : 'Credit (Cr)'"></span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-gray-500">Code</span>
                            <span class="font-mono font-medium text-gray-900" x-text="currentCode || '—'"></span>
                        </div>
                    </div>
                    <div class="mt-3 p-2.5 rounded-md text-xs" :class="{
                        'bg-green-50 text-green-700': selectedType === 'asset',
                        'bg-red-50 text-red-700': selectedType === 'liability',
                        'bg-yellow-50 text-yellow-700': selectedType === 'equity',
                        'bg-blue-50 text-blue-700': selectedType === 'income',
                        'bg-purple-50 text-purple-700': selectedType === 'expense'
                    }">
                        <span x-show="selectedType === 'asset'">Assets are resources owned by your business (Cash, Bank, Equipment)</span>
                        <span x-show="selectedType === 'liability'">Liabilities are debts owed by your business (Loans, Payables)</span>
                        <span x-show="selectedType === 'equity'">Equity represents the owner's stake (Capital, Retained Earnings)</span>
                        <span x-show="selectedType === 'income'">Income accounts track revenue (Sales, Service Income)</span>
                        <span x-show="selectedType === 'expense'">Expense accounts track costs (Rent, Salaries, Utilities)</span>
                    </div>
                </div>

                <!-- Account Code Guide (shown when no type selected) -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-5" x-show="!selectedType">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Account Code Convention</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                            <span class="font-medium text-green-800">Asset</span>
                            <span class="text-green-600">1XXX</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-red-50 rounded">
                            <span class="font-medium text-red-800">Liability</span>
                            <span class="text-red-600">2XXX</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                            <span class="font-medium text-yellow-800">Equity</span>
                            <span class="text-yellow-600">3XXX</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                            <span class="font-medium text-blue-800">Income</span>
                            <span class="text-blue-600">4XXX</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-purple-50 rounded">
                            <span class="font-medium text-purple-800">Expense</span>
                            <span class="text-purple-600">5XXX</span>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-500">Codes are auto-assigned when you select a type.</p>
                </div>

                <!-- Status -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-5">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="is_active"
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">Active Account</label>
                            <p class="text-gray-500 text-xs">Available for transactions when active</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="isSubmitting">
                        <template x-if="!isSubmitting">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </template>
                        <template x-if="isSubmitting">
                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isSubmitting ? 'Creating...' : 'Create Account'"></span>
                    </button>

                    <button type="button"
                            onclick="saveAndContinue()"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-lg text-green-700 bg-green-50 hover:bg-green-100">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Save & Create Another
                    </button>

                    <a href="{{ route('tenant.accounting.ledger-accounts.index', $tenant) }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- BallieAI Account Modal -->
<div id="aiAccountModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeAIAccountModal()"></div>

        <!-- Modal Panel -->
        <div class="relative inline-block w-full max-w-lg bg-white rounded-xl shadow-xl transform transition-all sm:my-8 overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-white">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold">Create Account with BallieAI</h3>
                    </div>
                    <button type="button" onclick="closeAIAccountModal()" class="text-purple-200 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Input Section -->
            <div id="aiInputSection" class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Describe the account you want to create</label>
                <textarea id="aiAccountDescription"
                          rows="3"
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 text-sm"
                          placeholder="e.g. Bank account for GTBank with opening balance of 500,000"></textarea>

                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" onclick="useAIExample('Office rent expense account')"
                            class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 text-xs rounded-full hover:bg-purple-100 border border-purple-200">
                        Office Rent
                    </button>
                    <button type="button" onclick="useAIExample('Bank account for GTBank with opening balance 500000')"
                            class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 text-xs rounded-full hover:bg-purple-100 border border-purple-200">
                        Bank Account
                    </button>
                    <button type="button" onclick="useAIExample('Sales revenue account')"
                            class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 text-xs rounded-full hover:bg-purple-100 border border-purple-200">
                        Sales Revenue
                    </button>
                    <button type="button" onclick="useAIExample('Salary and wages expense')"
                            class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 text-xs rounded-full hover:bg-purple-100 border border-purple-200">
                        Salary Expense
                    </button>
                </div>
            </div>

            <!-- Loading Section -->
            <div id="aiLoadingSection" class="hidden p-8 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-purple-100 rounded-full mb-4">
                    <svg class="animate-spin w-7 h-7 text-purple-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 font-medium">BallieAI is analyzing...</p>
                <p class="text-gray-400 text-sm mt-1">Setting up your account details</p>
            </div>

            <!-- Preview Section -->
            <div id="aiPreviewSection" class="hidden p-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-semibold text-green-800">Account Ready</span>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-600">Name:</span><span id="aiPreviewName" class="font-medium text-gray-900"></span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Type:</span><span id="aiPreviewType" class="font-medium text-gray-900 capitalize"></span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Code:</span><span id="aiPreviewCode" class="font-mono font-medium text-gray-900"></span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Group:</span><span id="aiPreviewGroup" class="font-medium text-gray-900"></span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Balance Type:</span><span id="aiPreviewBalance" class="font-medium text-gray-900"></span></div>
                        <div class="flex justify-between"><span class="text-gray-600">Opening Balance:</span><span id="aiPreviewOpening" class="font-medium text-gray-900"></span></div>
                    </div>
                    <div id="aiPreviewInterpretation" class="mt-3 pt-3 border-t border-green-200 text-xs text-green-700 hidden"></div>
                </div>
            </div>

            <!-- Error Section -->
            <div id="aiErrorSection" class="hidden p-6">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-semibold text-red-800">Something went wrong</span>
                    </div>
                    <p id="aiErrorMessage" class="text-sm text-red-700"></p>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                <button type="button" onclick="closeAIAccountModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>

                <button type="button" id="aiGenerateBtn" onclick="generateAIAccount()"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Generate
                    </span>
                </button>

                <button type="button" id="aiApplyBtn" onclick="applyAIAccount()"
                        class="hidden px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Apply to Form
                </button>

                <button type="button" id="aiSubmitBtn" onclick="submitAIAccount()"
                        class="hidden px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Create Account
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-4 right-4 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('success') }}
            <button @click="show = false" class="ml-4 text-green-500 hover:text-green-700">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
@endif

@if($errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition
         class="fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
        <div class="flex items-start">
            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h4 class="font-medium mb-1">Please fix the following:</h4>
                <ul class="text-sm list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="ml-4 text-red-500 hover:text-red-700 flex-shrink-0">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
@endif

<script>
// ── Alpine.js Form Component ──
function accountForm() {
    return {
        isSubmitting: false,
        selectedType: '{{ old("account_type", "") }}',
        balanceType: '{{ old("balance_type", "dr") }}',
        currentCode: '{{ old("code", "") }}',

        init() {
            if (this.selectedType) {
                this.filterAccountGroups(this.selectedType);
                this.fetchNextCode(this.selectedType);
            }
        },

        validateForm(event) {
            const requiredFields = ['code', 'name', 'account_type', 'account_group_id'];
            let isValid = true;
            let firstErrorField = null;

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field || !field.value.trim()) {
                    if (field) field.classList.add('border-red-300');
                    isValid = false;
                    if (!firstErrorField && field) firstErrorField = field;
                } else {
                    field.classList.remove('border-red-300');
                }
            });

            if (!isValid) {
                event.preventDefault();
                firstErrorField?.focus();
                showToast('Please fill in all required fields.', 'error');
                return false;
            }

            this.isSubmitting = true;
            return true;
        },

        async handleAccountTypeChange(event) {
            const accountType = event.target.value;
            this.selectedType = accountType;

            if (!accountType) return;

            // Auto-set balance type
            const drTypes = ['asset', 'expense'];
            this.balanceType = drTypes.includes(accountType) ? 'dr' : 'cr';
            document.getElementById('balance_type').value = this.balanceType;

            // Filter account groups
            this.filterAccountGroups(accountType);

            // Fetch next code from server
            await this.fetchNextCode(accountType);
        },

        filterAccountGroups(accountType) {
            const groupSelect = document.getElementById('account_group_id');
            const options = groupSelect.querySelectorAll('option');
            const natureMap = {
                'asset': 'assets',
                'liability': 'liabilities',
                'equity': 'equity',
                'income': 'income',
                'expense': 'expenses'
            };
            const targetNature = natureMap[accountType];
            let firstVisible = null;

            options.forEach(option => {
                if (option.value === '') return;
                const nature = option.dataset.nature;
                const shouldShow = nature === targetNature;
                option.style.display = shouldShow ? 'block' : 'none';
                if (shouldShow && !firstVisible) firstVisible = option;
            });

            // If current selection is hidden, auto-select first visible
            const current = groupSelect.querySelector(`option[value="${groupSelect.value}"]`);
            if (!groupSelect.value || (current && current.style.display === 'none')) {
                groupSelect.value = firstVisible ? firstVisible.value : '';
            }
        },

        async fetchNextCode(accountType) {
            const codeField = document.getElementById('code');
            const spinner = document.getElementById('codeSpinner');
            const check = document.getElementById('codeCheck');

            // Don't overwrite if user manually typed a code
            if (codeField.dataset.userEdited === 'true') return;

            spinner.classList.remove('hidden');
            check.classList.add('hidden');

            try {
                const url = `{{ route('tenant.accounting.ledger-accounts.next-code', $tenant) }}?account_type=${encodeURIComponent(accountType)}`;
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await response.json();
                if (data.code) {
                    codeField.value = data.code;
                    this.currentCode = data.code;
                    spinner.classList.add('hidden');
                    check.classList.remove('hidden');
                    setTimeout(() => check.classList.add('hidden'), 2000);
                }
            } catch (e) {
                console.error('Failed to fetch next code:', e);
                spinner.classList.add('hidden');
            }
        }
    }
}

// Track manual edits to code field
document.addEventListener('DOMContentLoaded', function() {
    const codeField = document.getElementById('code');
    if (codeField) {
        codeField.addEventListener('input', function() {
            this.dataset.userEdited = 'true';
        });
    }

    // Clear validation errors on input
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', () => field.classList.remove('border-red-300'));
        field.addEventListener('change', () => field.classList.remove('border-red-300'));
    });
});

// ── Save Actions ──
function saveAndContinue() {
    const form = document.getElementById('accountForm');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'save_and_continue';
    input.value = '1';
    form.appendChild(input);
    form.submit();
}

// ── Toast Notification ──
function showToast(message, type = 'info') {
    const colors = {
        success: 'bg-green-100 border-green-400 text-green-700',
        error: 'bg-red-100 border-red-400 text-red-700',
        info: 'bg-blue-100 border-blue-400 text-blue-700'
    };
    const el = document.createElement('div');
    el.className = `fixed top-4 right-4 z-[60] px-4 py-3 rounded-lg shadow-lg border transition-all duration-300 ${colors[type] || colors.info}`;
    el.innerHTML = `<div class="flex items-center"><span>${message}</span><button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current hover:opacity-70 flex-shrink-0"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg></button></div>`;
    document.body.appendChild(el);
    setTimeout(() => { if (el.parentElement) el.remove(); }, 5000);
}

// ── BallieAI Modal Functions ──
let parsedAIAccount = null;

function openAIAccountModal() {
    document.getElementById('aiAccountModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    resetAIAccountModal();
    setTimeout(() => document.getElementById('aiAccountDescription').focus(), 100);
}

function closeAIAccountModal() {
    document.getElementById('aiAccountModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function resetAIAccountModal() {
    parsedAIAccount = null;
    document.getElementById('aiAccountDescription').value = '';
    document.getElementById('aiInputSection').classList.remove('hidden');
    document.getElementById('aiLoadingSection').classList.add('hidden');
    document.getElementById('aiPreviewSection').classList.add('hidden');
    document.getElementById('aiErrorSection').classList.add('hidden');
    document.getElementById('aiGenerateBtn').classList.remove('hidden');
    document.getElementById('aiApplyBtn').classList.add('hidden');
    document.getElementById('aiSubmitBtn').classList.add('hidden');
}

function useAIExample(example) {
    document.getElementById('aiAccountDescription').value = example;
}

async function generateAIAccount() {
    const description = document.getElementById('aiAccountDescription').value.trim();
    if (!description) {
        showToast('Please describe the account you want to create.', 'error');
        return;
    }

    // Show loading
    document.getElementById('aiInputSection').classList.add('hidden');
    document.getElementById('aiLoadingSection').classList.remove('hidden');
    document.getElementById('aiErrorSection').classList.add('hidden');
    document.getElementById('aiPreviewSection').classList.add('hidden');
    document.getElementById('aiGenerateBtn').classList.add('hidden');

    try {
        const response = await fetch('/api/ai/parse-account', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                description: description,
                tenant_id: {{ $tenant->id }}
            })
        });

        const data = await response.json();

        if (data.success && data.parsed_account) {
            parsedAIAccount = data.parsed_account;
            displayAIAccountPreview(parsedAIAccount);
        } else {
            showAIError(data.message || 'Failed to parse the account description. Please try a different description.');
        }
    } catch (error) {
        console.error('AI Account Error:', error);
        showAIError('Could not connect to BallieAI. Please try again or create the account manually.');
    }
}

function displayAIAccountPreview(account) {
    document.getElementById('aiLoadingSection').classList.add('hidden');
    document.getElementById('aiPreviewSection').classList.remove('hidden');
    document.getElementById('aiApplyBtn').classList.remove('hidden');
    document.getElementById('aiSubmitBtn').classList.remove('hidden');

    document.getElementById('aiPreviewName').textContent = account.name || '—';
    document.getElementById('aiPreviewType').textContent = account.account_type || '—';
    document.getElementById('aiPreviewCode').textContent = account.code || '—';
    document.getElementById('aiPreviewBalance').textContent = account.balance_type === 'dr' ? 'Debit (Dr)' : 'Credit (Cr)';
    document.getElementById('aiPreviewOpening').textContent = account.opening_balance ? '\u20A6' + Number(account.opening_balance).toLocaleString() : '\u20A60';

    // Find group name
    const groupSelect = document.getElementById('account_group_id');
    const groupOption = groupSelect.querySelector(`option[value="${account.account_group_id}"]`);
    document.getElementById('aiPreviewGroup').textContent = groupOption ? groupOption.textContent.trim() : '—';

    // Interpretation
    const interpEl = document.getElementById('aiPreviewInterpretation');
    if (account.interpretation) {
        interpEl.textContent = account.interpretation;
        interpEl.classList.remove('hidden');
    } else {
        interpEl.classList.add('hidden');
    }
}

function showAIError(message) {
    document.getElementById('aiLoadingSection').classList.add('hidden');
    document.getElementById('aiErrorSection').classList.remove('hidden');
    document.getElementById('aiInputSection').classList.remove('hidden');
    document.getElementById('aiGenerateBtn').classList.remove('hidden');
    document.getElementById('aiErrorMessage').textContent = message;
}

function applyAIAccount() {
    if (!parsedAIAccount) return;

    const a = parsedAIAccount;

    // Fill form fields
    if (a.name) document.getElementById('name').value = a.name;
    if (a.code) {
        document.getElementById('code').value = a.code;
        document.getElementById('code').dataset.userEdited = 'true';
    }
    if (a.account_type) {
        const typeSelect = document.getElementById('account_type');
        typeSelect.value = a.account_type;
        typeSelect.dispatchEvent(new Event('change'));
    }
    if (a.account_group_id) {
        setTimeout(() => {
            document.getElementById('account_group_id').value = a.account_group_id;
        }, 100);
    }
    if (a.balance_type) document.getElementById('balance_type').value = a.balance_type;
    if (a.opening_balance) document.getElementById('opening_balance').value = a.opening_balance;
    if (a.description) document.getElementById('description').value = a.description;

    closeAIAccountModal();
    showToast('Account details applied to form!', 'success');
}

function submitAIAccount() {
    if (!parsedAIAccount) return;

    const a = parsedAIAccount;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("tenant.accounting.ledger-accounts.store", $tenant) }}';
    form.style.display = 'none';

    const fields = {
        '_token': csrfToken,
        'name': a.name || '',
        'code': a.code || '',
        'account_type': a.account_type || '',
        'account_group_id': a.account_group_id || '',
        'balance_type': a.balance_type || 'dr',
        'opening_balance': a.opening_balance || 0,
        'description': a.description || '',
        'is_active': '1'
    };

    for (const [key, value] of Object.entries(fields)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection

@push('styles')
<style>
input, select, textarea {
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
</style>
@endpush
