@extends('layouts.tenant')

@section('title', 'Edit Ledger Account - ' . $ledgerAccount->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Ledger Account</h1>
            <p class="mt-2 text-gray-600">Update account details and settings</p>

            <!-- Breadcrumb -->
            <nav class="flex mt-3" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('tenant.dashboard', $tenant) }}"
                           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
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
                            <a href="{{ route('tenant.accounting.index', $tenant) }}"
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                                Accounting
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('tenant.accounting.ledger-accounts.index', $tenant) }}"
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                                Ledger Accounts
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount]) }}"
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                                {{ $ledgerAccount->name }}
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
            <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Account
            </a>
            <a href="{{ route('tenant.accounting.ledger-accounts.index', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('tenant.accounting.ledger-accounts.update', [$tenant, $ledgerAccount]) }}"
          method="POST"
          id="accountForm"
          x-data="accountForm()"
          @submit="validateForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form - Takes 2 columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Update the basic details for the account</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Account Code -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="code"
                                       id="code"
                                       value="{{ old('code', $ledgerAccount->code) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('code') border-red-300 @enderror"
                                       placeholder="e.g., 1000, ACC001"
                                       required>
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Unique identifier for the account</p>
                            </div>

                            <!-- Account Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $ledgerAccount->name) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                                       placeholder="Enter account name"
                                       required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Account Type -->
                            <div>
                                <label for="account_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Type <span class="text-red-500">*</span>
                                </label>
                                <select name="account_type"
                                        id="account_type"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('account_type') border-red-300 @enderror"
                                        required
                                        x-on:change="handleAccountTypeChange">
                                    <option value="">Select Account Type</option>
                                    <option value="asset" {{ old('account_type', $ledgerAccount->account_type) === 'asset' ? 'selected' : '' }}>
                                        Asset
                                    </option>
                                    <option value="liability" {{ old('account_type', $ledgerAccount->account_type) === 'liability' ? 'selected' : '' }}>
                                        Liability
                                    </option>
                                    <option value="equity" {{ old('account_type', $ledgerAccount->account_type) === 'equity' ? 'selected' : '' }}>
                                        Equity
                                    </option>
                                    <option value="income" {{ old('account_type', $ledgerAccount->account_type) === 'income' ? 'selected' : '' }}>
                                        Income
                                    </option>
                                    <option value="expense" {{ old('account_type', $ledgerAccount->account_type) === 'expense' ? 'selected' : '' }}>
                                        Expense
                                    </option>
                                </select>
                                @error('account_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Account Group -->
                            <div>
                                <label for="account_group_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Account Group <span class="text-red-500">*</span>
                                </label>
                                <select name="account_group_id"
                                        id="account_group_id"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('account_group_id') border-red-300 @enderror"
                                        required>
                                    <option value="">Select Account Group</option>
                                    @foreach($accountGroups as $group)
                                        <option value="{{ $group->id }}"
                                                data-nature="{{ $group->nature }}"
                                                {{ old('account_group_id', $ledgerAccount->account_group_id) == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_group_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Parent Account -->
                            <div>
                                <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Parent Account
                                </label>
                                <select name="parent_id"
                                        id="parent_id"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('parent_id') border-red-300 @enderror">
                                    <option value="">No Parent (Main Account)</option>
                                    @foreach($parentAccounts as $parent)
                                        @if($parent->id !== $ledgerAccount->id)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $ledgerAccount->parent_id) == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->code }} - {{ $parent->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Optional: Select a parent account to create a sub-account</p>
                            </div>

                            <!-- Balance Type -->
                            <div>
                                <label for="balance_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Balance Type <span class="text-red-500">*</span>
                                </label>
                                <select name="balance_type"
                                        id="balance_type"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('balance_type') border-red-300 @enderror"
                                        required>
                                    <option value="dr" {{ old('balance_type', $ledgerAccount->balance_type) === 'dr' ? 'selected' : '' }}>
                                        Debit (Dr)
                                    </option>
                                    <option value="cr" {{ old('balance_type', $ledgerAccount->balance_type) === 'cr' ? 'selected' : '' }}>
                                        Credit (Cr)
                                    </option>
                                </select>
                                @error('balance_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
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
                                       value="{{ old('opening_balance', $ledgerAccount->opening_balance) }}"
                                       step="0.01"
                                       min="0"
                                       class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('opening_balance') border-red-300 @enderror"
                                       placeholder="0.00">
                            </div>
                            @error('opening_balance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Enter the opening balance for this account</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-300 @enderror"
                                      placeholder="Enter account description (optional)">{{ old('description', $ledgerAccount->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Optional contact details for this account</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Address
                            </label>
                            <textarea name="address"
                                      id="address"
                                      rows="3"
                                      class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('address') border-red-300 @enderror"
                                      placeholder="Enter address">{{ old('address', $ledgerAccount->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone
                                </label>
                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', $ledgerAccount->phone) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('phone') border-red-300 @enderror"
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email', $ledgerAccount->email) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('email') border-red-300 @enderror"
                                       placeholder="Enter email address">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Takes 1 column -->
            <div class="space-y-6">
                <!-- Current Account Info -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Current Account</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="h-12 w-12 rounded-full {{ $ledgerAccount->is_active ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                                <span class="text-lg font-bold {{ $ledgerAccount->is_active ? 'text-green-800' : 'text-gray-800' }}">
                                    {{ strtoupper(substr($ledgerAccount->code, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $ledgerAccount->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $ledgerAccount->code }}</p>
                            </div>
                        </div>

                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Current Balance</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    ₦{{ number_format($ledgerAccount->getCurrentBalance(), 2) }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Account Type</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ledgerAccount->account_type === 'asset' ? 'bg-green-100 text-green-800' : ($ledgerAccount->account_type === 'liability' ? 'bg-red-100 text-red-800' : ($ledgerAccount->account_type === 'equity' ? 'bg-yellow-100 text-yellow-800' : ($ledgerAccount->account_type === 'income' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'))) }}">
                                        {{ ucfirst($ledgerAccount->account_type) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Status</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    @if($ledgerAccount->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Created</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $ledgerAccount->created_at->format('M d, Y') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Account Status</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <label for="is_active" class="block text-sm font-medium text-gray-700">
                                    Active Status
                                </label>
                                <p class="text-xs text-gray-500 mt-1">
                                    Inactive accounts cannot be used in new transactions
                                </p>
                            </div>
                            <div class="ml-3">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="is_active"
                                           id="is_active"
                                           value="1"
                                           {{ old('is_active', $ledgerAccount->is_active) ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Account Warning -->
                @if($ledgerAccount->is_system_defined)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                System Account
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This is a system-defined account. Some fields may be restricted from editing to maintain system integrity.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6 space-y-3">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update Account
                        </button>

                        <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount]) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>

                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('tenant.accounting.ledger-accounts.index', $tenant) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                All Accounts
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Total Transactions</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $ledgerAccount->voucherEntries()->count() ?? 0 }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Sub Accounts</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $ledgerAccount->children()->count() }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Last Updated</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $ledgerAccount->updated_at->diffForHumans() }}
                                </dd>
                            </div>
                            @if($ledgerAccount->parent)
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Parent Account</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount->parent]) }}"
                                       class="text-primary-600 hover:text-primary-500">
                                        {{ $ledgerAccount->parent->name }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Help & Tips -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">
                                Editing Tips
                            </h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Account codes must be unique</li>
                                    <li>Balance type is auto-suggested based on account type</li>
                                    <li>Changing account type may affect reports</li>
                                    <li>Contact info is optional but useful for vendors/customers</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal"
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50"
     x-show="showConfirmation"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm Changes</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to update this account? This action will modify the account details.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button @click="submitForm()"
                        class="px-4 py-2 bg-primary-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-primary-700 mr-2">
                    Yes, Update
                </button>
                <button @click="showConfirmation = false"
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function accountForm() {
    return {
        showConfirmation: false,
        originalData: {},

        init() {
            // Store original form data
            this.storeOriginalData();

            // Set up account type change handler
            this.setupAccountTypeHandler();

            // Set up form validation
            this.setupFormValidation();
        },

        storeOriginalData() {
            const form = document.getElementById('accountForm');
            const formData = new FormData(form);
            this.originalData = Object.fromEntries(formData);
        },

        setupAccountTypeHandler() {
            const accountTypeSelect = document.getElementById('account_type');
            const balanceTypeSelect = document.getElementById('balance_type');
            const accountGroupSelect = document.getElementById('account_group_id');

            accountTypeSelect.addEventListener('change', (e) => {
                this.handleAccountTypeChange(e.target.value, balanceTypeSelect, accountGroupSelect);
            });
        },

        handleAccountTypeChange(accountType, balanceTypeSelect, accountGroupSelect) {
            // Auto-suggest balance type
            let suggestedBalanceType = 'dr';

            switch(accountType) {
                case 'asset':
                case 'expense':
                    suggestedBalanceType = 'dr';
                    break;
                case 'liability':
                case 'equity':
                case 'income':
                    suggestedBalanceType = 'cr';
                    break;
            }

            balanceTypeSelect.value = suggestedBalanceType;

            // Filter account groups
            this.filterAccountGroups(accountType, accountGroupSelect);
        },

        filterAccountGroups(accountType, accountGroupSelect) {
            const options = accountGroupSelect.querySelectorAll('option');

            options.forEach(option => {
                if (option.value === '') return;

                const nature = option.dataset.nature;
                let shouldShow = false;

                switch(accountType) {
                    case 'asset':
                        shouldShow = nature === 'assets';
                        break;
                    case 'liability':
                        shouldShow = nature === 'liabilities';
                        break;
                    case 'equity':
                        shouldShow = nature === 'equity';
                        break;
                    case 'income':
                        shouldShow = nature === 'income';
                        break;
                    case 'expense':
                        shouldShow = nature === 'expenses';
                        break;
                    default:
                        shouldShow = true;
                }

                option.style.display = shouldShow ? 'block' : 'none';
            });

            // Reset selection if current selection is now hidden
            const currentOption = accountGroupSelect.querySelector(`option[value="${accountGroupSelect.value}"]`);
            if (currentOption && currentOption.style.display === 'none') {
                accountGroupSelect.value = '';
            }
        },

        setupFormValidation() {
            // Real-time validation
            const requiredFields = ['code', 'name', 'account_type', 'account_group_id', 'balance_type'];

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field) {
                    field.addEventListener('input', () => {
                        this.validateField(field);
                    });

                    field.addEventListener('blur', () => {
                        this.validateField(field);
                    });
                }
            });
        },

        validateField(field) {
            const value = field.value.trim();
            const isValid = value !== '';

            if (isValid) {
                field.classList.remove('border-red-300');
                field.classList.add('border-gray-300');
            } else {
                field.classList.remove('border-gray-300');
                field.classList.add('border-red-300');
            }

            return isValid;
        },

        validateForm(event) {
            event.preventDefault();

            const requiredFields = ['code', 'name', 'account_type', 'account_group_id', 'balance_type'];
            let isValid = true;

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field && !this.validateField(field)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                this.showError('Please fill in all required fields.');
                return;
            }

            // Check if form has changes
            if (this.hasChanges()) {
                this.showConfirmation = true;
            } else {
                this.showInfo('No changes detected.');
            }
        },

        hasChanges() {
            const form = document.getElementById('accountForm');
            const currentData = new FormData(form);
            const current = Object.fromEntries(currentData);

            for (const key in current) {
                if (current[key] !== this.originalData[key]) {
                    return true;
                }
            }

            return false;
        },

        submitForm() {
            this.showConfirmation = false;
            document.getElementById('accountForm').submit();
        },

        showError(message) {
            this.showToast(message, 'error');
        },

        showInfo(message) {
            this.showToast(message, 'info');
        },

        showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500' : 'bg-blue-500';

            toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
            toast.textContent = message;

            document.body.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            // Remove after 5 seconds
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            },
5000);
        }
    }
}

// Initialize form when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate account code if name changes and code is empty
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');

    if (nameInput && codeInput) {
        nameInput.addEventListener('input', function() {
            if (!codeInput.value.trim()) {
                const code = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 10);
                codeInput.value = code;
            }
        });
    }

    // Format currency inputs
    const currencyInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    currencyInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            document.getElementById('accountForm').dispatchEvent(new Event('submit'));
        }

        // Escape to cancel
        if (e.key === 'Escape') {
            const confirmationModal = document.getElementById('confirmationModal');
            if (confirmationModal && !confirmationModal.classList.contains('hidden')) {
                // Close modal if open
                return;
            }

            // Otherwise navigate back
            window.location.href = '{{ route("tenant.accounting.ledger-accounts.show", [$tenant, $ledgerAccount]) }}';
        }
    });

    // Warn about unsaved changes
    let formChanged = false;
    const form = document.getElementById('accountForm');
    const inputs = form.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        input.addEventListener('change', () => {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    });

    // Mark form as submitted to avoid warning
    form.addEventListener('submit', () => {
        formChanged = false;
    });
});

// Auto-save draft functionality (optional)
function autoSaveDraft() {
    const form = document.getElementById('accountForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    // Save to localStorage
    localStorage.setItem('ledger_account_edit_draft_{{ $ledgerAccount->id }}', JSON.stringify(data));

    // Show auto-save indicator
    const indicator = document.createElement('div');
    indicator.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-3 py-1 rounded text-sm z-50';
    indicator.textContent = 'Draft saved';
    document.body.appendChild(indicator);

    setTimeout(() => {
        if (indicator.parentNode) {
            indicator.parentNode.removeChild(indicator);
        }
    }, 2000);
}

// Load draft on page load
function loadDraft() {
    const draftData = localStorage.getItem('ledger_account_edit_draft_{{ $ledgerAccount->id }}');
    if (draftData) {
        try {
            const data = JSON.parse(draftData);
            const form = document.getElementById('accountForm');

            Object.keys(data).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'checkbox') {
                        field.checked = data[key] === '1';
                    } else {
                        field.value = data[key];
                    }
                }
            });

            // Show draft loaded message
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Draft loaded
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-yellow-200 hover:text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);

        } catch (e) {
            console.error('Error loading draft:', e);
        }
    }
}

// Clear draft when form is successfully submitted
function clearDraft() {
    localStorage.removeItem('ledger_account_edit_draft_{{ $ledgerAccount->id }}');
}

// Set up auto-save (every 30 seconds)
setInterval(autoSaveDraft, 30000);

// Load draft on page load
document.addEventListener('DOMContentLoaded', loadDraft);

// Clear draft on successful form submission
document.getElementById('accountForm').addEventListener('submit', function() {
    setTimeout(clearDraft, 1000); // Clear after a delay to ensure submission
});
</script>
@endpush

@push('styles')
<style>
/* Custom toggle switch styling */
.peer:checked ~ .peer-checked\:bg-primary-600 {
    background-color: rgb(37 99 235);
}

.peer:focus ~ .peer-focus\:ring-primary-300 {
    --tw-ring-color: rgb(147 197 253);
}

/* Enhanced form styling */
.form-input:focus {
    border-color: rgb(37 99 235);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Loading state for submit button */
.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Enhanced focus states */
input:focus,
select:focus,
textarea:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
    border-color: rgb(37 99 235);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for form sections */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-in-up {
    animation: slideInUp 0.3s ease-out;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .lg\:col-span-2 {
        grid-column: span 1;
    }

    .space-y-6 > * + * {
        margin-top: 1rem;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    body {
        font-size: 12pt;
        line-height: 1.4;
    }

    .shadow-sm,
    .shadow {
        box-shadow: none !important;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .text-gray-500 {
        color: #000000;
    }

    .border-gray-300 {
        border-color: #000000;
    }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Form validation styles */
.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}

.is-valid {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

/* Tooltip styles */
.tooltip {
    position: relative;
}

.tooltip::before {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #1f2937;
    color: white;
    padding: 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
    z-index: 1000;
}

.tooltip:hover::before {
    opacity: 1;
}

/* Custom badge styles */
.badge-asset {
    background-color: #dcfce7;
    color: #166534;
}

.badge-liability {
    background-color: #fee2e2;
    color: #991b1b;
}

.badge-equity {
    background-color: #fef3c7;
    color: #92400e;
}

.badge-income {
    background-color: #dbeafe;
    color: #1e40af;
}

.badge-expense {
    background-color: #f3e8ff;
    color: #7c3aed;
}
</style>
@endpush
