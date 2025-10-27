@extends('layouts.tenant')

@section('title', $bank->account_name . ' - Bank Account Details')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-full {{ $bank->status === 'active' ? 'bg-emerald-100' : 'bg-gray-100' }} flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $bank->status === 'active' ? 'text-emerald-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $bank->bank_name }}</h1>
                    <p class="text-gray-600">{{ $bank->display_name }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            @if($bank->is_primary)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Primary
                </span>
            @endif
            @if($bank->is_payroll_account)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                    Payroll
                </span>
            @endif
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $bank->status_color }}">
                {{ ucfirst($bank->status) }}
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Account Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Balance Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">₦{{ number_format($bank->opening_balance, 2) }}</div>
                        <div class="text-sm text-gray-500 mt-1">Opening Balance</div>
                    </div>
                </div>
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600">₦{{ number_format($bank->getCurrentBalance(), 2) }}</div>
                        <div class="text-sm text-gray-500 mt-1">Current Balance</div>
                    </div>
                </div>
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">₦{{ number_format($bank->getAvailableBalance(), 2) }}</div>
                        <div class="text-sm text-gray-500 mt-1">Available Balance</div>
                        @if($bank->overdraft_limit > 0)
                            <div class="text-xs text-gray-400 mt-1">(Incl. ₦{{ number_format($bank->overdraft_limit, 2) }} overdraft)</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bank Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $bank->bank_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Holder</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bank->account_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $bank->masked_account_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Account Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bank->account_type_display }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Currency</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bank->currency }}</dd>
                        </div>
                        @if($bank->account_opening_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Opening Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($bank->account_opening_date)->format('M d, Y') }}</dd>
                        </div>
                        @endif
                        @if($bank->description)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bank->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Branch Information -->
            @if($bank->branch_name || $bank->branch_address)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Branch Details</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($bank->branch_name)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Branch Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bank->branch_name }}</dd>
                        </div>
                        @endif
                        @if($bank->branch_code)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Branch Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $bank->branch_code }}</dd>
                        </div>
                        @endif
                        @if($bank->branch_address)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $bank->full_branch_address }}</dd>
                        </div>
                        @endif
                        @if($bank->branch_phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="tel:{{ $bank->branch_phone }}" class="text-emerald-600 hover:text-emerald-500">{{ $bank->branch_phone }}</a>
                            </dd>
                        </div>
                        @endif
                        @if($bank->branch_email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $bank->branch_email }}" class="text-emerald-600 hover:text-emerald-500">{{ $bank->branch_email }}</a>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- International Codes -->
            @if($bank->swift_code || $bank->iban || $bank->routing_number)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">International Codes</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($bank->swift_code)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">SWIFT/BIC Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $bank->swift_code }}</dd>
                        </div>
                        @endif
                        @if($bank->iban)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">IBAN</dt>
                            <dd class="mt-1 text-xs text-gray-900 font-mono">{{ $bank->iban }}</dd>
                        </div>
                        @endif
                        @if($bank->routing_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Routing Number</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $bank->routing_number }}</dd>
                        </div>
                        @endif
                        @if($bank->sort_code)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sort Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $bank->sort_code }}</dd>
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
                    <a href="{{ route('tenant.banking.banks.statement', [$tenant, $bank->id]) }}"
                       class="text-sm text-emerald-600 hover:text-emerald-500">
                        View all transactions →
                    </a>
                </div>
                <div class="p-6">
                    @if($recentTransactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentTransactions as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->voucher->voucher_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ Str::limit($transaction->particulars ?? $transaction->voucher->voucherType->name, 50) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                @if($transaction->debit_amount > 0)
                                                    <span class="text-green-600 font-medium">+₦{{ number_format($transaction->debit_amount, 2) }}</span>
                                                @else
                                                    <span class="text-red-600 font-medium">-₦{{ number_format($transaction->credit_amount, 2) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->debit_amount > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $transaction->debit_amount > 0 ? 'Credit' : 'Debit' }}
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
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('tenant.banking.banks.edit', [$tenant, $bank->id]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Account
                    </a>

                    <a href="{{ route('tenant.banking.banks.statement', [$tenant, $bank->id]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Statement
                    </a>

                    @if($bank->canBeDeleted())
                        <button onclick="confirmDelete()"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Account
                        </button>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statistics</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Transaction Count</span>
                        <span class="text-sm font-medium text-gray-900">{{ $monthlyStats['transactions_count'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Account Age</span>
                        <span class="text-sm font-medium text-gray-900">{{ $monthlyStats['account_age_days'] ?? 0 }} days</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Reconciliation Status</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $monthlyStats['reconciliation_status'] === 'current' ? 'bg-green-100 text-green-800' : ($monthlyStats['reconciliation_status'] === 'due' ? 'bg-yellow-100 text-yellow-800' : ($monthlyStats['reconciliation_status'] === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($monthlyStats['reconciliation_status'] ?? 'unknown') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Created</span>
                        <span class="text-sm font-medium text-gray-900">{{ $bank->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Last Updated</span>
                        <span class="text-sm font-medium text-gray-900">{{ $bank->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Reconciliation Info -->
            @if($bank->enable_reconciliation)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Reconciliation</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $bank->getReconciliationStatus() === 'current' ? 'bg-green-100 text-green-800' : ($bank->getReconciliationStatus() === 'due' ? 'bg-yellow-100 text-yellow-800' : ($bank->getReconciliationStatus() === 'overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($bank->getReconciliationStatus()) }}
                            </span>
                        </dd>
                    </div>
                    @if($bank->last_reconciliation_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Last Reconciled</dt>
                        <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($bank->last_reconciliation_date)->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Reconciled Balance</dt>
                        <dd class="text-sm text-gray-900">₦{{ number_format($bank->last_reconciled_balance ?? 0, 2) }}</dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Navigation -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 space-y-3">
                    <a href="{{ route('tenant.banking.banks.index', $tenant) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        All Bank Accounts
                    </a>

                    <a href="{{ route('tenant.accounting.index', $tenant) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Accounting Dashboard
                    </a>
                </div>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Bank Account</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete "{{ $bank->bank_name }}"? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form method="POST" action="{{ route('tenant.banking.banks.destroy', [$tenant, $bank->id]) }}" class="inline">
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
function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const deleteModal = document.getElementById('deleteModal');
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }

    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        window.location.href = '{{ route("tenant.banking.banks.edit", [$tenant, $bank->id]) }}';
    }
});
</script>
@endpush
