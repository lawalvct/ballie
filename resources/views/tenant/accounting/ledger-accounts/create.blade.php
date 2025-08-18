@extends('layouts.tenant')

@section('title', 'Create Ledger Account')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Ledger Account</h1>
            <p class="mt-2 text-gray-600">Add a new account to your chart of accounts</p>

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
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="mt-4 lg:mt-0">
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
    <form action="{{ route('tenant.accounting.ledger-accounts.store', $tenant) }}"
          method="POST"
          id="accountForm"
          x-data="accountForm()"
          @submit="validateForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form - Takes 2 columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                        <p class="mt-1 text-sm text-gray-500">Enter the basic details for the account</p>
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
                                       value="{{ old('code') }}"
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
                                       value="{{ old('name') }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                                       placeholder="Enter account name"
                                       required
                                       x-on:blur="generateCodeFromName">
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
                                    <option value="asset" {{ old('account_type') === 'asset' ? 'selected' : '' }}>
                                        Asset
                                    </option>
                                    <option value="liability" {{ old('account_type') === 'liability' ? 'selected' : '' }}>
                                        Liability
                                    </option>
                                    <option value="equity" {{ old('account_type') === 'equity' ? 'selected' : '' }}>
                                        Equity
                                    </option>
                                    <option value="income" {{ old('account_type') === 'income' ? 'selected' : '' }}>
                                        Income
                                    </option>
                                    <option value="expense" {{ old('account_type') === 'expense' ? 'selected' : '' }}>
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
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->code }} - {{ $parent->name }}
                                        </option>
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
                                    <option value="dr" {{ old('balance_type', 'dr') === 'dr' ? 'selected' : '' }}>
                                        Debit (Dr)
                                    </option>
                                    <option value="cr" {{ old('balance_type') === 'cr' ? 'selected' : '' }}>
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
                                       value="{{ old('opening_balance', 0) }}"
                                       class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('opening_balance') border-red-300 @enderror"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00">
                                @error('opening_balance')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Enter the opening balance for this account</p>
                            </div>
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
                                      placeholder="Enter account description (optional)">{{ old('description') }}</textarea>
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
                                      placeholder="Enter address">{{ old('address') }}</textarea>
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
                                       value="{{ old('phone') }}"
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
                                       value="{{ old('email') }}"
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
                <!-- AI Assistant & Account Guide -->
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <h3 class="ml-3 text-lg font-medium text-purple-900">AI Account Assistant</h3>
                        </div>
                        <!-- Help Button -->
                        <button type="button"
                                onclick="toggleAccountingHelp()"
                                class="inline-flex items-center px-3 py-2 border border-blue-300 text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Help
                        </button>
                    </div>

                    <!-- AI Assistant Button -->
                    <button type="button"
                            onclick="toggleAccountAI()"
                            class="w-full inline-flex items-center justify-center px-4 py-3 border border-purple-300 text-sm font-medium rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 mb-4">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Ask Ballie AI for Account Code & Setup
                    </button>

                    <!-- Account Code Generator -->
                    <div class="bg-white rounded-lg p-4 border border-purple-100 mb-4">
                        <h4 class="text-sm font-semibold text-purple-800 mb-3">🎯 Smart Code Generator</h4>
                        <div class="space-y-2 text-xs text-purple-700">
                            <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                                <span><strong>Asset:</strong> 1XXX</span>
                                <span class="text-green-600">e.g., 1001 - Cash</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-red-50 rounded">
                                <span><strong>Liability:</strong> 2XXX</span>
                                <span class="text-red-600">e.g., 2001 - Payable</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                                <span><strong>Equity:</strong> 3XXX</span>
                                <span class="text-yellow-600">e.g., 3001 - Capital</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-blue-50 rounded">
                                <span><strong>Income:</strong> 4XXX</span>
                                <span class="text-blue-600">e.g., 4001 - Sales</span>
                            </div>
                            <div class="flex items-center justify-between p-2 bg-purple-50 rounded">
                                <span><strong>Expense:</strong> 5XXX</span>
                                <span class="text-purple-600">e.g., 5001 - Salary</span>
                            </div>
                        </div>
                        <button type="button"
                                onclick="generateAccountCode()"
                                class="w-full mt-3 px-3 py-2 text-xs font-medium text-purple-700 bg-purple-100 rounded hover:bg-purple-200 transition-colors">
                            🔄 Generate Next Available Code
                        </button>
                    </div>
                </div>

                <!-- AI Assistant Panel -->
                <div id="account-ai-panel" class="hidden bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-purple-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                🤖 AI Assistant
                            </h4>
                            <button onclick="toggleAccountAI()" class="text-purple-600 hover:text-purple-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- AI Chat Interface -->
                        <div class="space-y-3">
                            <div class="bg-white rounded-lg p-3 border">
                                <textarea id="ai-question"
                                         class="w-full p-2 text-sm border border-gray-300 rounded resize-none focus:outline-none focus:ring-2 focus:ring-purple-500"
                                         rows="3"
                                         placeholder="Ask AI: 'Help me create a Utilities Expense account' or 'What code should I use for Office Rent?'"></textarea>
                                <div class="flex justify-between items-center mt-2">
                                    <button type="button"
                                            onclick="clearAIChat()"
                                            class="text-xs text-gray-500 hover:text-gray-700">
                                        Clear
                                    </button>
                                    <button type="button"
                                            onclick="askAccountAI()"
                                            class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700">
                                        Ask Ballie
                                    </button>
                                </div>
                            </div>
                            <div id="ai-response" class="hidden bg-white rounded-lg p-3 border border-green-200">
                                <div id="ai-response-content"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Types Help Panel -->
                <div id="accounting-help-panel" class="hidden bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-blue-800">📚 Account Types Guide</h4>
                            <button onclick="toggleAccountingHelp()" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex items-start bg-white p-3 rounded border">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3 mt-0.5">Asset</span>
                                <div>
                                    <div class="font-medium text-green-800">Resources owned by the business</div>
                                    <div class="text-green-600 text-xs">Cash, Bank, Inventory, Equipment, Buildings</div>
                                </div>
                            </div>
                            <div class="flex items-start bg-white p-3 rounded border">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-3 mt-0.5">Liability</span>
                                <div>
                                    <div class="font-medium text-red-800">Debts owed by the business</div>
                                    <div class="text-red-600 text-xs">Loans, Accounts Payable, Accrued Expenses</div>
                                </div>
                            </div>
                            <div class="flex items-start bg-white p-3 rounded border">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mr-3 mt-0.5">Equity</span>
                                <div>
                                    <div class="font-medium text-yellow-800">Owner's stake in the business</div>
                                    <div class="text-yellow-600 text-xs">Capital, Retained Earnings, Owner's Equity</div>
                                </div>
                            </div>
                            <div class="flex items-start bg-white p-3 rounded border">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3 mt-0.5">Income</span>
                                <div>
                                    <div class="font-medium text-blue-800">Revenue earned by the business</div>
                                    <div class="text-blue-600 text-xs">Sales, Service Income, Interest Income</div>
                                </div>
                            </div>
                            <div class="flex items-start bg-white p-3 rounded border">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-3 mt-0.5">Expense</span>
                                <div>
                                    <div class="font-medium text-purple-800">Costs incurred by the business</div>
                                    <div class="text-purple-600 text-xs">Rent, Utilities, Salaries, Office Supplies</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balance Type Helper -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-lg p-6" x-show="showBalanceHelper" x-transition>
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-amber-900">Balance Type</h3>
                    </div>
                    <div class="space-y-3 text-sm text-amber-700">
                        <div>
                            <strong>Debit (Dr):</strong> Assets and Expenses normally have debit balances
                        </div>
                        <div>
                            <strong>Credit (Cr):</strong> Liabilities, Equity, and Income normally have credit balances
                        </div>
                    </div>
                </div>

                <!-- Status Settings -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Status Settings</h3>
                        <p class="mt-1 text-sm text-gray-500">Configure account status</p>
                    </div>
                    <div class="p-6">
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
                                <p class="text-gray-500">Account will be available for transactions when active</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isSubmitting"
                                x-text="isSubmitting ? 'Creating Account...' : 'Create Account'">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isSubmitting">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" x-show="isSubmitting" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>

                        <button type="button"
                                onclick="resetForm()"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Form
                        </button>

                        <a href="{{ route('tenant.accounting.ledger-accounts.index', $tenant) }}"
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Save & Continue -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-3">
                   <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h4 class="text-sm font-medium text-green-900">Quick Save Options</h4>
                    </div>
                    <div class="space-y-2">
                        <button type="button"
                                onclick="saveAndContinue()"
                                class="w-full text-left px-3 py-2 text-sm text-green-700 hover:bg-green-100 rounded-md transition-colors">
                            Save & Create Another
                        </button>
                        <button type="button"
                                onclick="saveAndView()"
                                class="w-full text-left px-3 py-2 text-sm text-green-700 hover:bg-green-100 rounded-md transition-colors">
                            Save & View Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-transition
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
    <div x-data="{ show: true }"
         x-show="show"
         x-transition
         class="fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg max-w-md">
        <div class="flex items-start">
            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h4 class="font-medium mb-1">Please fix the following errors:</h4>
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
function accountForm() {
    return {
        isSubmitting: false,
        showBalanceHelper: false,

        init() {
            // Show balance helper when account type is selected
            this.$watch('$refs.accountType?.value', (value) => {
                this.showBalanceHelper = !!value;
            });
        },

        validateForm(event) {
            const requiredFields = ['code', 'name', 'account_type', 'account_group_id', 'balance_type'];
            let isValid = true;
            let firstErrorField = null;

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    field.classList.add('border-red-300');
                    isValid = false;
                    if (!firstErrorField) {
                        firstErrorField = field;
                    }
                } else {
                    field.classList.remove('border-red-300');
                }
            });

            if (!isValid) {
                event.preventDefault();
                firstErrorField?.focus();
                this.showNotification('Please fill in all required fields.', 'error');
                return false;
            }

            this.isSubmitting = true;
            return true;
        },

        generateCodeFromName() {
            const nameField = document.getElementById('name');
            const codeField = document.getElementById('code');

            if (!codeField.value && nameField.value) {
                // Generate code from name (first 3 letters + random number)
                const nameCode = nameField.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase().substring(0, 6);
                const randomNum = Math.floor(Math.random() * 100).toString().padStart(2, '0');
                codeField.value = nameCode + randomNum;
            }
        },

        handleAccountTypeChange(event) {
            const accountType = event.target.value;
            const balanceTypeSelect = document.getElementById('balance_type');
            const accountGroupSelect = document.getElementById('account_group_id');

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
            this.showBalanceHelper = !!accountType;
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

        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
                type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
                'bg-blue-100 border border-blue-400 text-blue-700'
            }`;

            notification.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current hover:opacity-70">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
    }
}

// AI Assistant Functions
window.toggleAccountAI = function() {
    const panel = document.getElementById('account-ai-panel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        document.getElementById('ai-question').focus();
    }
};

window.toggleAccountingHelp = function() {
    const panel = document.getElementById('accounting-help-panel');
    panel.classList.toggle('hidden');
};

window.clearAIChat = function() {
    document.getElementById('ai-question').value = '';
    document.getElementById('ai-response').classList.add('hidden');
    document.getElementById('ai-question').focus();
};

window.askAccountAI = function() {
    const question = document.getElementById('ai-question').value.trim();
    if (!question) {
        alert('Please enter a question for the AI assistant.');
        return;
    }

    const responseDiv = document.getElementById('ai-response');
    const responseContent = document.getElementById('ai-response-content');

    // Show loading state
    responseDiv.classList.remove('hidden');
    responseContent.innerHTML = `
        <div class="flex items-center text-purple-600">
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            AI is thinking...
        </div>
    `;

    // Simulate AI response (in real implementation, this would call your AI API)
    setTimeout(() => {
        const aiResponse = generateAIResponse(question);
        responseContent.innerHTML = aiResponse;
    }, 1500);
};

function generateAIResponse(question) {
    const lowerQuestion = question.toLowerCase();

    // Expense account suggestions
    if (lowerQuestion.includes('expense') || lowerQuestion.includes('cost') || lowerQuestion.includes('salary') || lowerQuestion.includes('rent') || lowerQuestion.includes('utilities')) {
        const suggestedCode = getNextAccountCode('5');
        return `
            <div class="space-y-3">
                <h5 class="font-semibold text-purple-800 mb-2">🤖 AI Suggestion</h5>
                <div class="bg-purple-50 p-3 rounded border-l-4 border-purple-400">
                    <div class="font-medium text-purple-800">For Expense Accounts:</div>
                    <div class="text-sm text-purple-600 mt-1">
                        • Suggested Code: <strong>${suggestedCode}</strong><br>
                        • Account Type: <strong>Expense</strong><br>
                        • Balance Type: <strong>Debit (Dr)</strong>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="applyAISuggestion('${suggestedCode}', 'expense', 'dr')"
                            class="px-3 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700">
                        Apply Suggestion
                    </button>
                    <button onclick="generateAccountCode()"
                            class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                        Generate Code
                    </button>
                </div>
            </div>
        `;
    }

    // Asset account suggestions
    if (lowerQuestion.includes('asset') || lowerQuestion.includes('cash') || lowerQuestion.includes('bank') || lowerQuestion.includes('equipment')) {
        const suggestedCode = getNextAccountCode('1');
        return `
            <div class="space-y-3">
                <h5 class="font-semibold text-green-800 mb-2">🤖 AI Suggestion</h5>
                <div class="bg-green-50 p-3 rounded border-l-4 border-green-400">
                    <div class="font-medium text-green-800">For Asset Accounts:</div>
                    <div class="text-sm text-green-600 mt-1">
                        • Suggested Code: <strong>${suggestedCode}</strong><br>
                        • Account Type: <strong>Asset</strong><br>
                        • Balance Type: <strong>Debit (Dr)</strong>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="applyAISuggestion('${suggestedCode}', 'asset', 'dr')"
                            class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                        Apply Suggestion
                    </button>
                    <button onclick="generateAccountCode()"
                            class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                        Generate Code
                    </button>
                </div>
            </div>
        `;
    }

    // Income account suggestions
    if (lowerQuestion.includes('income') || lowerQuestion.includes('revenue') || lowerQuestion.includes('sales') || lowerQuestion.includes('service')) {
        const suggestedCode = getNextAccountCode('4');
        return `
            <div class="space-y-3">
                <h5 class="font-semibold text-blue-800 mb-2">🤖 AI Suggestion</h5>
                <div class="bg-blue-50 p-3 rounded border-l-4 border-blue-400">
                    <div class="font-medium text-blue-800">For Income Accounts:</div>
                    <div class="text-sm text-blue-600 mt-1">
                        • Suggested Code: <strong>${suggestedCode}</strong><br>
                        • Account Type: <strong>Income</strong><br>
                        • Balance Type: <strong>Credit (Cr)</strong>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="applyAISuggestion('${suggestedCode}', 'income', 'cr')"
                            class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                        Apply Suggestion
                    </button>
                    <button onclick="generateAccountCode()"
                            class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                        Generate Code
                    </button>
                </div>
            </div>
        `;
    }

    // Liability account suggestions
    if (lowerQuestion.includes('liability') || lowerQuestion.includes('payable') || lowerQuestion.includes('loan') || lowerQuestion.includes('debt')) {
        const suggestedCode = getNextAccountCode('2');
        return `
            <div class="space-y-3">
                <h5 class="font-semibold text-red-800 mb-2">🤖 AI Suggestion</h5>
                <div class="bg-red-50 p-3 rounded border-l-4 border-red-400">
                    <div class="font-medium text-red-800">For Liability Accounts:</div>
                    <div class="text-sm text-red-600 mt-1">
                        • Suggested Code: <strong>${suggestedCode}</strong><br>
                        • Account Type: <strong>Liability</strong><br>
                        • Balance Type: <strong>Credit (Cr)</strong>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="applyAISuggestion('${suggestedCode}', 'liability', 'cr')"
                            class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                        Apply Suggestion
                    </button>
                    <button onclick="generateAccountCode()"
                            class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                        Generate Code
                    </button>
                </div>
            </div>
        `;
    }

    // Equity account suggestions
    if (lowerQuestion.includes('equity') || lowerQuestion.includes('capital') || lowerQuestion.includes('owner')) {
        const suggestedCode = getNextAccountCode('3');
        return `
            <div class="space-y-3">
                <h5 class="font-semibold text-yellow-800 mb-2">🤖 AI Suggestion</h5>
                <div class="bg-yellow-50 p-3 rounded border-l-4 border-yellow-400">
                    <div class="font-medium text-yellow-800">For Equity Accounts:</div>
                    <div class="text-sm text-yellow-600 mt-1">
                        • Suggested Code: <strong>${suggestedCode}</strong><br>
                        • Account Type: <strong>Equity</strong><br>
                        • Balance Type: <strong>Credit (Cr)</strong>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="applyAISuggestion('${suggestedCode}', 'equity', 'cr')"
                            class="px-3 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">
                        Apply Suggestion
                    </button>
                    <button onclick="generateAccountCode()"
                            class="px-3 py-1 bg-gray-500 text-white text-xs rounded hover:bg-gray-600">
                        Generate Code
                    </button>
                </div>
            </div>
        `;
    }

    // General help
    return `
        <div class="space-y-3">
            <h5 class="font-semibold text-gray-800 mb-2">🤖 AI Assistant</h5>
            <div class="bg-gray-50 p-3 rounded border-l-4 border-gray-400">
                <div class="font-medium text-gray-800">I can help you with:</div>
                <div class="text-sm text-gray-600 mt-2 space-y-1">
                    • Suggest account codes for different account types<br>
                    • Recommend account types based on your description<br>
                    • Help with balance types (Debit vs Credit)<br>
                    • Provide examples for specific accounts
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    Try asking: "Help me create a Utilities Expense account" or "What code should I use for Office Rent?"
                </div>
            </div>
        </div>
    `;
}

window.applyAISuggestion = function(code, accountType, balanceType) {
    // Apply the AI suggestion to the form
    document.getElementById('code').value = code;
    document.getElementById('account_type').value = accountType;
    document.getElementById('balance_type').value = balanceType;

    // Trigger account type change to filter account groups
    const accountTypeSelect = document.getElementById('account_type');
    accountTypeSelect.dispatchEvent(new Event('change'));

    // Show success message
    showNotification('AI suggestion applied successfully!', 'success');

    // Close AI panel
    document.getElementById('account-ai-panel').classList.add('hidden');
};

function getNextAccountCode(prefix) {
    // This would typically fetch from server to get next available code
    // For now, we'll simulate it
    const baseCode = prefix + '00';
    const randomSuffix = Math.floor(Math.random() * 90) + 10; // 10-99
    return baseCode + randomSuffix;
}

window.generateAccountCode = function() {
    const accountType = document.getElementById('account_type').value;
    if (!accountType) {
        alert('Please select an account type first.');
        return;
    }

    let prefix = '';
    switch(accountType) {
        case 'asset': prefix = '1'; break;
        case 'liability': prefix = '2'; break;
        case 'equity': prefix = '3'; break;
        case 'income': prefix = '4'; break;
        case 'expense': prefix = '5'; break;
    }

    if (prefix) {
        const newCode = getNextAccountCode(prefix);
        document.getElementById('code').value = newCode;
        showNotification(`Generated code: ${newCode}`, 'success');
    }
};

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
        'bg-blue-100 border border-blue-400 text-blue-700'
    }`;

    notification.innerHTML = `
        <div class="flex items-center">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current hover:opacity-70 flex-shrink-0">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Additional utility functions
function resetForm() {
    if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
        document.getElementById('accountForm').reset();
        // Remove any error styling
        document.querySelectorAll('.border-red-300').forEach(field => {
            field.classList.remove('border-red-300');
        });
    }
}

function saveAndContinue() {
    const form = document.getElementById('accountForm');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'save_and_continue';
    input.value = '1';
    form.appendChild(input);
    form.submit();
}

function saveAndView() {
    const form = document.getElementById('accountForm');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'save_and_view';
    input.value = '1';
    form.appendChild(input);
    form.submit();
}

// Remove validation errors on input
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('border-red-300');
        });

        field.addEventListener('change', function() {
            this.classList.remove('border-red-300');
        });
    });

    // Auto-focus first field
    const firstField = document.getElementById('code');
    if (firstField) {
        firstField.focus();
    }
});
</script>
@endsection

@push('styles')
<style>
    /* Custom styles for better UX */
    .form-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Smooth transitions for form elements */
    input, select, textarea {
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    /* Loading state for submit button */
    .btn-loading {
        position: relative;
    }

    .btn-loading:disabled {
        cursor: not-allowed;
    }

    /* Custom checkbox styling */
    input[type="checkbox"]:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    /* Notification animations */
    .notification-enter {
        transform: translateX(100%);
        opacity: 0;
    }

    .notification-enter-active {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.3s ease-out;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .grid-cols-1.md\\:grid-cols-2 {
            gap: 1rem;
        }

        .lg\\:col-span-2 {
            order: 1;
        }

        .space-y-6 > * + * {
            margin-top: 1.5rem;
        }
    }

    /* Form validation styling */
    .field-error {
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    /* Improved focus states */
    .focus-ring:focus {
        outline: none;
        ring: 2px;
        ring-color: #3b82f6;
        ring-opacity: 0.5;
        ring-offset: 2px;
    }

    /* Better disabled states */
    .disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Custom scrollbar for long dropdowns */
    select {
        max-height: 200px;
        overflow-y: auto;
    }

    select::-webkit-scrollbar {
        width: 8px;
    }

    select::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    select::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    select::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Gradient backgrounds for info cards */
    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .success-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    /* Improved button hover states */
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Loading spinner animation */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
<script>
// Advanced form validation
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;

        // Add real-time validation
        this.form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => this.clearFieldError(field));
        });
    }

    validateField(field) {
        const rules = this.getValidationRules(field);
        const value = field.value.trim();

        // Clear previous errors
        this.clearFieldError(field);

        // Apply validation rules
        for (const rule of rules) {
            if (!rule.test(value)) {
                this.showFieldError(field, rule.message);
                return false;
            }
        }

        return true;
    }

    getValidationRules(field) {
        const rules = [];

        // Required fields
        if (field.hasAttribute('required')) {
            rules.push({
                test: (value) => value.length > 0,
                message: 'This field is required'
            });
        }

        // Email validation
        if (field.type === 'email' && field.value) {
            rules.push({
                test: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
                message: 'Please enter a valid email address'
            });
        }

        // Number validation
        if (field.type === 'number') {
            const min = parseFloat(field.getAttribute('min'));
            const max = parseFloat(field.getAttribute('max'));

            if (!isNaN(min)) {
                rules.push({
                    test: (value) => !value || parseFloat(value) >= min,
                    message: `Value must be at least ${min}`
                });
            }

            if (!isNaN(max)) {
                rules.push({
                    test: (value) => !value || parseFloat(value) <= max,
                    message: `Value must not exceed ${max}`
                });
            }
        }

        // Custom validation for account code
        if (field.name === 'code') {
            rules.push({
                test: (value) => /^[A-Z0-9]{2,10}$/.test(value),
                message: 'Account code must be 2-10 characters, letters and numbers only'
            });
        }

        return rules;
    }

    showFieldError(field, message) {
        field.classList.add('border-red-300', 'field-error');

        // Remove existing error message
        const existingError = field.parentNode.querySelector('.field-error-message');
        if (existingError) {
            existingError.remove();
        }

        // Add new error message
        const errorElement = document.createElement('p');
        errorElement.className = 'field-error-message mt-1 text-sm text-red-600';
        errorElement.textContent = message;
        field.parentNode.appendChild(errorElement);

        this.errors[field.name] = message;
    }

    clearFieldError(field) {
        field.classList.remove('border-red-300', 'field-error');

        const errorElement = field.parentNode.querySelector('.field-error-message');
        if (errorElement) {
            errorElement.remove();
        }

        delete this.errors[field.name];
    }

    validateForm() {
        let isValid = true;

        this.form.querySelectorAll('input, select, textarea').forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }
}

// Initialize form validator
document.addEventListener('DOMContentLoaded', function() {
    const validator = new FormValidator('accountForm');

    // Enhanced form submission
    document.getElementById('accountForm').addEventListener('submit', function(e) {
        if (!validator.validateForm()) {
            e.preventDefault();

            // Focus first error field
            const firstErrorField = this.querySelector('.border-red-300');
            if (firstErrorField) {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Show error summary
            const errorCount = Object.keys(validator.errors).length;
            if (errorCount > 0) {
                showNotification(`Please fix ${errorCount} error${errorCount > 1 ? 's' : ''} before submitting.`, 'error');
            }
        }
    });

    // Auto-save draft functionality
    let autoSaveTimer;
    const autoSaveDelay = 30000; // 30 seconds

    function autoSaveDraft() {
        const formData = new FormData(document.getElementById('accountForm'));
        const draftData = {};

        for (let [key, value] of formData.entries()) {
            draftData[key] = value;
        }

        localStorage.setItem('ledger_account_draft', JSON.stringify(draftData));
        showNotification('Draft saved automatically', 'info');
    }

    function loadDraft() {
        const draft = localStorage.getItem('ledger_account_draft');
        if (draft) {
            try {
                const draftData = JSON.parse(draft);

                // Ask user if they want to restore draft
                if (confirm('A draft of this form was found. Would you like to restore it?')) {
                    Object.keys(draftData).forEach(key => {
                        const field = document.querySelector(`[name="${key}"]`);
                        if (field) {
                            if (field.type === 'checkbox') {
                                field.checked = draftData[key] === '1';
                            } else {
                                field.value = draftData[key];
                            }
                        }
                    });

                    showNotification('Draft restored successfully', 'success');
                } else {
                    localStorage.removeItem('ledger_account_draft');
                }
            } catch (e) {
                console.error('Error loading draft:', e);
                localStorage.removeItem('ledger_account_draft');
            }
        }
    }

    // Load draft on page load
    loadDraft();

    // Set up auto-save
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(autoSaveDraft, autoSaveDelay);
        });
    });

    // Clear draft on successful submission
    document.getElementById('accountForm').addEventListener('submit', function() {
        localStorage.removeItem('ledger_account_draft');
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S or Cmd+S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            document.getElementById('accountForm').dispatchEvent(new Event('submit'));
        }

        // Ctrl+R or Cmd+R to reset (with confirmation)
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            resetForm();
        }

        // Escape to cancel
        if (e.key === 'Escape') {
            if (confirm('Are you sure you want to leave this page?')) {
                window.location.href = '{{ route("tenant.accounting.ledger-accounts.index", $tenant) }}';
            }
        }
    });
});

// Enhanced notification system
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    const icons = {
        success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>',
        error: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>',
        info: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>'
    };

    const colors = {
        success: 'bg-green-100 border-green-400 text-green-700',
        error: 'bg-red-100 border-red-400 text-red-700',
        info: 'bg-blue-100 border-blue-400 text-blue-700'
    };

    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg border transition-all duration-300 transform translate-x-full ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                ${icons[type]}
            </svg>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current hover:opacity-70 flex-shrink-0">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, duration);
}
</script>
@endpush
