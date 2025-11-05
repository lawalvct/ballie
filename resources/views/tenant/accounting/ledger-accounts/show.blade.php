@extends('layouts.tenant')

@section('title', $ledgerAccount->name . ' - Ledger Account Details')

@section('page-title', 'Ledger Account Details')

@section('page-description', 'View detailed information about this ledger account including balance, transactions, and account settings.')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-full {{ $ledgerAccount->is_active ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                    <span class="text-lg font-bold {{ $ledgerAccount->is_active ? 'text-green-800' : 'text-gray-800' }}">
                        {{ strtoupper(substr($ledgerAccount->code, 0, 2)) }}
                    </span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $ledgerAccount->name }}</h1>
                    <p class="text-gray-600">Code: <span class="font-mono">{{ $ledgerAccount->code }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <dd class="text-lg font-medium text-gray-900">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Account Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $ledgerAccount->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ledgerAccount->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Type</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ledgerAccount->account_type === 'asset' ? 'bg-green-100 text-green-800' : ($ledgerAccount->account_type === 'liability' ? 'bg-red-100 text-red-800' : ($ledgerAccount->account_type === 'equity' ? 'bg-yellow-100 text-yellow-800' : ($ledgerAccount->account_type === 'income' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'))) }}">
                                    {{ ucfirst($ledgerAccount->account_type) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Group</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ledgerAccount->accountGroup->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Balance Type</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ledgerAccount->balance_type === 'dr' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $ledgerAccount->balance_type === 'dr' ? 'Debit (Dr)' : 'Credit (Cr)' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Parent Account</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($ledgerAccount->parent)
                                    <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount->parent]) }}"
                                       class="text-primary-600 hover:text-primary-500">
                                        {{ $ledgerAccount->parent->name }}
                                    </a>
                                @else
                                    <span class="text-gray-500">None (Main Account)</span>
                                @endif
                            </dd>
                        </div>
                        @if($ledgerAccount->description)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ledgerAccount->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Balance Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Balance Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">₦{{ number_format($ledgerAccount->opening_balance, 2) }}</div>
                            <div class="text-sm text-gray-500">Opening Balance</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold {{ $currentBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ₦{{ number_format(abs($currentBalance), 2) }}
                                <span class="text-sm">{{ $currentBalance >= 0 ? 'Dr' : 'Cr' }}</span>
                            </div>
                            <div class="text-sm text-gray-500">Current Balance</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-600">{{ $transactionCount ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Total Transactions</div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-6 text-center">
                            <div>
                                <div class="text-lg font-semibold text-green-600">₦{{ number_format($totalDebits ?? 0, 2) }}</div>
                                <div class="text-sm text-gray-500">Total Debits</div>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-red-600">₦{{ number_format($totalCredits ?? 0, 2) }}</div>
                                <div class="text-sm text-gray-500">Total Credits</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            @if($ledgerAccount->address || $ledgerAccount->phone || $ledgerAccount->email)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($ledgerAccount->address)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ledgerAccount->address }}</dd>
                        </div>
                        @endif
                        @if($ledgerAccount->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="tel:{{ $ledgerAccount->phone }}" class="text-primary-600 hover:text-primary-500">
                                    {{ $ledgerAccount->phone }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        @if($ledgerAccount->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $ledgerAccount->email }}" class="text-primary-600 hover:text-primary-500">
                                    {{ $ledgerAccount->email }}
                                </a>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Recent Transactions -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
                    @if($recentTransactions->count() > 0)
                        <a href="{{ route('tenant.accounting.vouchers.index', [$tenant, 'account_id' => $ledgerAccount->id]) }}"
                           class="text-sm text-primary-600 hover:text-primary-500">
                            View all transactions →
                        </a>
                    @endif
                </div>
                <div class="p-6">
                    @if($recentTransactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voucher</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php $runningBalance = $ledgerAccount->opening_balance; @endphp
                                    @foreach($recentTransactions as $transaction)
                                        @php
                                            $runningBalance += ($transaction->debit_amount - $transaction->credit_amount);
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->voucher->voucher_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <a href="{{ route('tenant.accounting.vouchers.show', [$tenant, $transaction->voucher]) }}"
                                                   class="text-primary-600 hover:text-primary-500 font-medium">
                                                    {{ $transaction->voucher->voucher_number }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ Str::limit($transaction->particulars ?? 'Transaction', 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                @if($transaction->debit_amount > 0)
                                                    <span class="text-green-600 font-medium">₦{{ number_format($transaction->debit_amount, 2) }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                @if($transaction->credit_amount > 0)
                                                    <span class="text-red-600 font-medium">₦{{ number_format($transaction->credit_amount, 2) }}</span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                <span class="font-medium {{ $runningBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    ₦{{ number_format(abs($runningBalance), 2) }}
                                                    <span class="text-xs ml-1">{{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}</span>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions</h3>
                            <p class="mt-1 text-sm text-gray-500">This account has no transaction history yet.</p>
                            <div class="mt-6">
                                <a href="{{ route('tenant.accounting.vouchers.create', [$tenant, 'account_id' => $ledgerAccount->id]) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Transaction
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sub Accounts -->
            @if($ledgerAccount->children->count() > 0)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sub Accounts</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($ledgerAccount->children as $child)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-medium text-gray-900">
                                            <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $child]) }}"
                                               class="text-primary-600 hover:text-primary-500">
                                                {{ $child->name }}
                                            </a>
                                        </h4>
                                        <p class="text-sm text-gray-500">{{ $child->code }}</p>
                                    </div>
                                    <div class="text-right">
                                        @php $childBalance = $child->getCurrentBalance(); @endphp
                                        <div class="text-sm font-medium {{ $childBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ₦{{ number_format(abs($childBalance), 2) }}
                                            <span class="text-xs">{{ $childBalance >= 0 ? 'Dr' : 'Cr' }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($child->is_active)
                                                <span class="text-green-600">Active</span>
                                            @else
                                                <span class="text-red-600">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('tenant.accounting.ledger-accounts.edit', [$tenant, $ledgerAccount]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Account
                    </a>

                    <a href="{{ route('tenant.accounting.vouchers.create', [$tenant, 'account_id' => $ledgerAccount->id]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Transaction
                    </a>

                    <a href="{{ route('tenant.accounting.ledger-accounts.statement', [$tenant, $ledgerAccount]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Statement
                    </a>

                    <a href="{{ route('tenant.accounting.ledger-accounts.export-ledger', [$tenant, $ledgerAccount]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Ledger
                    </a>

                    <a href="{{ route('tenant.accounting.ledger-accounts.print-ledger', [$tenant, $ledgerAccount]) }}"
                       target="_blank"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Ledger
                    </a>

                    @if(!$ledgerAccount->is_system_defined)
                        <button onclick="toggleAccountStatus()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md {{ $ledgerAccount->is_active ? 'text-red-700 hover:bg-red-50' : 'text-green-700 hover:bg-green-50' }} bg-white transition-colors">
                            @if($ledgerAccount->is_active)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                Deactivate Account
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Activate Account
                            @endif
                        </button>
                    @endif
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statistics</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Created</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ledgerAccount->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Last Updated</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ledgerAccount->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Total Transactions</span>
                        <span class="text-sm font-medium text-gray-900">{{ $transactionCount ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Sub Accounts</span>
                        <span class="text-sm font-medium text-gray-900">{{ $ledgerAccount->children->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Last Transaction</span>
                        <span class="text-sm font-medium text-gray-900">
                            @if($lastTransaction ?? null)
                                {{ $lastTransaction->created_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Account Type</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ledgerAccount->is_system_defined ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $ledgerAccount->is_system_defined ? 'System' : 'Custom' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Navigation</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('tenant.accounting.ledger-accounts.index', $tenant) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        All Accounts
                    </a>

                    <a href="{{ route('tenant.accounting.ledger-accounts.create', $tenant) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Account
                    </a>

                    @if($ledgerAccount->parent)
                        <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount->parent]) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                            </svg>
                            Parent Account
                        </a>
                    @endif

                    <a href="{{ route('tenant.accounting.index', $tenant) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Accounting Dashboard
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            @if(!$ledgerAccount->is_system_defined && ($transactionCount ?? 0) === 0 && $ledgerAccount->children->count() === 0)
            <div class="bg-white shadow-sm rounded-lg border border-red-200">
                <div class="px-6 py-4 border-b border-red-200">
                    <h3 class="text-lg font-medium text-red-900">Danger Zone</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-red-700 mb-4">
                        Once you delete this account, there is no going back. Please be certain.
                    </p>
                    <button onclick="confirmDelete()"
                            class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Account
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Toggle Modal -->
<div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full {{ $ledgerAccount->is_active ? 'bg-red-100' : 'bg-green-100' }}">
                @if($ledgerAccount->is_active)
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                @else
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @endif
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">
                {{ $ledgerAccount->is_active ? 'Deactivate' : 'Activate' }} Account
            </h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to {{ $ledgerAccount->is_active ? 'deactivate' : 'activate' }} this account?
                    @if($ledgerAccount->is_active)
                        This will prevent new transactions from being added to this account.
                    @else
                        This will allow transactions to be added to this account again.
                    @endif
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form method="POST" action="{{ route('tenant.accounting.ledger-accounts.toggle-status', [$tenant, $ledgerAccount]) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2 {{ $ledgerAccount->is_active ? 'bg-red-500 hover:bg-red-700' : 'bg-green-500 hover:bg-green-700' }} text-white text-base font-medium rounded-md shadow-sm mr-2">
                        {{ $ledgerAccount->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <button onclick="closeStatusModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Account</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete "{{ $ledgerAccount->name }}"? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form method="POST" action="{{ route('tenant.accounting.ledger-accounts.destroy', [$tenant, $ledgerAccount]) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 mr-2">
                        Delete
                    </button>
                </form>
                <button onclick="closeDeleteModal()"
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
// Toggle account status
function toggleAccountStatus() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

// Delete confirmation
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const statusModal = document.getElementById('statusModal');
    const deleteModal = document.getElementById('deleteModal');

    if (event.target === statusModal) {
        closeStatusModal();
    }

    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape key to close modals
    if (e.key === 'Escape') {
        closeStatusModal();
        closeDeleteModal();
    }

    // Ctrl+E to edit
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        window.location.href = '{{ route("tenant.accounting.ledger-accounts.edit", [$tenant, $ledgerAccount]) }}';
    }
});

// Auto-refresh balance every 30 seconds
setInterval(function() {
    fetch('{{ route("tenant.accounting.ledger-accounts.balance", [$tenant, $ledgerAccount]) }}')
        .then(response => response.json())
        .then(data => {
            // Update balance display if needed
            console.log('Balance updated:', data);
        })
        .catch(error => console.error('Error fetching balance:', error));
}, 30000);

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Print functionality
function printLedger() {
    window.open('{{ route("tenant.accounting.ledger-accounts.print-ledger", [$tenant, $ledgerAccount]) }}', '_blank');
}

// Copy account code to clipboard
function copyAccountCode() {
    navigator.clipboard.writeText('{{ $ledgerAccount->code }}').then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
        toast.textContent = 'Account code copied to clipboard!';
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    });
}

// Initialize tooltips if using a tooltip library
document.addEventListener('DOMContentLoaded', function() {
    // Add click handler for account code to copy
    const accountCode = document.querySelector('.font-mono');
    if (accountCode) {
        accountCode.style.cursor = 'pointer';
        accountCode.title = 'Click to copy';
        accountCode.addEventListener('click', copyAccountCode);
    }
});
</script>
@endpush

@push('styles')
<style>
/* Custom animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}

/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar
{
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Balance color transitions */
.balance-positive {
    color: #059669;
    transition: color 0.2s ease;
}

.balance-negative {
    color: #dc2626;
    transition: color 0.2s ease;
}

.balance-zero {
    color: #6b7280;
    transition: color 0.2s ease;
}

/* Card hover effects */
.card-hover:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.2s ease-in-out;
}

/* Button loading state */
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

/* Status indicators */
.status-active {
    background-color: #dcfce7;
    color: #166534;
}

.status-inactive {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .table-responsive th,
    .table-responsive td {
        padding: 0.5rem 0.25rem;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .print-only {
        display: block !important;
    }

    body {
        font-size: 12pt;
        line-height: 1.4;
    }

    .bg-white {
        background: white !important;
    }

    .shadow-sm,
    .shadow {
        box-shadow: none !important;
    }

    .border {
        border: 1px solid #000 !important;
    }
}

/* Focus styles for accessibility */
button:focus,
a:focus,
input:focus,
select:focus,
textarea:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .text-gray-500 {
        color: #000000;
    }

    .text-gray-600 {
        color: #000000;
    }

    .border-gray-200 {
        border-color: #000000;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Dark mode support (if you implement it later) */
@media (prefers-color-scheme: dark) {
    .dark-mode-support {
        background-color: #1f2937;
        color: #f9fafb;
    }
}

/* Custom utility classes */
.text-balance {
    text-wrap: balance;
}

.grid-auto-fit {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}

.aspect-square {
    aspect-ratio: 1 / 1;
}

/* Loading skeleton animation */
@keyframes skeleton-loading {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}

.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: skeleton-loading 1.5s infinite;
}

/* Improved focus indicators */
.focus-ring:focus {
    outline: 2px solid transparent;
    outline-offset: 2px;
    box-shadow: 0 0 0 2px #3b82f6;
}


</style>
@endpush
