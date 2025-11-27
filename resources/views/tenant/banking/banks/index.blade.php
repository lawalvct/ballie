@extends('layouts.tenant')

@section('title', 'Bank Accounts')
@section('page-title', 'Bank Accounts')
@section('page-description', 'Manage your business bank accounts and track balances.')

@php
    $breadcrumbs = [];
@endphp


@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Action Buttons -->
        <div class="flex justify-end items-center gap-3 mb-6">
            <a href="{{ route('tenant.banking.banks.create', ['tenant' => $tenant->slug]) }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Bank Account
            </a>
            <button onclick="window.print()"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </button>
            <form method="GET" action="{{ route('tenant.banking.banks.index', ['tenant' => $tenant->slug]) }}" class="inline">
                <input type="hidden" name="export" value="excel">
                @foreach(request()->except('export') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Export
                </button>
            </form>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Banks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Banks</p>
                        <h3 class="text-lg font-bold text-gray-900">{{ $stats['total_banks'] }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-university text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Active Banks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Active Accounts</p>
                        <h3 class="text-lg font-bold text-green-600">{{ $stats['active_banks'] }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Total Balance -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-1">Total Balance</p>
                        <h3 class="text-lg font-bold text-emerald-600 break-words">₦{{ number_format($stats['total_balance'], 2) }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0 ml-2">
                        <i class="fas fa-wallet text-2xl text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <!-- Needs Reconciliation -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Needs Reconciliation</p>
                        <h3 class="text-lg font-bold {{ $stats['needs_reconciliation'] > 0 ? 'text-orange-600' : 'text-gray-900' }}">
                            {{ $stats['needs_reconciliation'] }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-2xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('tenant.banking.banks.index', ['tenant' => $tenant->slug]) }}" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Bank name, account number..."
                               class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status"
                                id="status"
                                class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>

                    <!-- Bank Name Filter -->
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">Bank</label>
                        <select name="bank_name"
                                id="bank_name"
                                class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Banks</option>
                            @foreach($bankNames as $name)
                                <option value="{{ $name }}" {{ request('bank_name') === $name ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end space-x-2">
                        <button type="submit"
                                class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-search mr-2"></i> Filter
                        </button>
                        <a href="{{ route('tenant.banking.banks.index', ['tenant' => $tenant->slug]) }}"
                           class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors text-center">
                            <i class="fas fa-redo mr-2"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Banks List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900">Bank Accounts ({{ $banks->total() }})</h2>
            </div>

            @if($banks->count() > 0)
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bank Account
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Current Balance
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Flags
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reconciliation
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($banks as $bank)
                        <tr class="hover:bg-gray-50">
                            <!-- Bank Account -->
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-university text-emerald-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $bank->bank_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $bank->masked_account_number }}</div>
                                        <div class="text-xs text-gray-400">{{ $bank->account_name }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Account Type -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $bank->account_type_display }}</div>
                                @if($bank->branch_name)
                                <div class="text-xs text-gray-500">{{ $bank->branch_name }}</div>
                                @endif
                            </td>

                            <!-- Current Balance -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">
                                    ₦{{ number_format($bank->getCurrentBalance(), 2) }}
                                </div>
                                @if($bank->overdraft_limit > 0)
                                <div class="text-xs text-gray-500">
                                    Available: ₦{{ number_format($bank->getAvailableBalance(), 2) }}
                                </div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $bank->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $bank->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $bank->status === 'closed' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $bank->status === 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ ucfirst($bank->status) }}
                                </span>
                            </td>

                            <!-- Flags -->
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($bank->is_primary)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-star mr-1"></i>Primary
                                    </span>
                                    @endif
                                    @if($bank->is_payroll_account)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-money-bill-wave mr-1"></i>Payroll
                                    </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Reconciliation -->
                            <td class="px-6 py-4">
                                @php
                                    $reconStatus = $bank->getReconciliationStatus();
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $reconStatus === 'current' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $reconStatus === 'due' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $reconStatus === 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $reconStatus === 'never' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $reconStatus === 'disabled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($reconStatus) }}
                                </span>
                                @if($bank->last_reconciliation_date)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $bank->last_reconciliation_date->format('M d, Y') }}
                                </div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('tenant.banking.banks.show', ['tenant' => $tenant->slug, 'bank' => $bank->id]) }}"
                                       class="text-blue-600 hover:text-blue-900"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tenant.banking.banks.statement', ['tenant' => $tenant->slug, 'bank' => $bank->id]) }}"
                                       class="text-emerald-600 hover:text-emerald-900"
                                       title="View Statement">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    <a href="{{ route('tenant.banking.banks.edit', ['tenant' => $tenant->slug, 'bank' => $bank->id]) }}"
                                       class="text-yellow-600 hover:text-yellow-900"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($bank->canBeDeleted())
                                    <form action="{{ route('tenant.banking.banks.destroy', ['tenant' => $tenant->slug, 'bank' => $bank->id]) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this bank account?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-900"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $banks->links() }}
            </div>

            @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i class="fas fa-university text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No bank accounts found</h3>
                <p class="text-gray-500 mb-6">Get started by adding your first bank account.</p>
                <a href="{{ route('tenant.banking.banks.create', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i> Add Bank Account
                </a>
            </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
// Debounced search
let searchTimeout;
document.getElementById('search').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filterForm').submit();
    }, 500);
});
</script>
@endpush

@endsection
