@extends('layouts.tenant')

@section('title', $account_group->name . ' - Account Group Details - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-bold text-gray-900">{{ $account_group->name }}</h1>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                    {{ $account_group->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $account_group->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Account Group Details and Management
            </p>
        </div>
        <div class="mt-4 lg:mt-0 flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.account-groups.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Account Groups
            </a>
            <a href="{{ route('tenant.accounting.account-groups.edit', ['tenant' => $tenant->slug, 'account_group' => $account_group->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Account Group
            </a>
        </div>
    </div>

    <!-- Account Group Details Card -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Account Group Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Basic Information -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Basic Information</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Name</dt>
                            <dd class="text-sm text-gray-900">{{ $account_group->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Code</dt>
                            <dd class="text-sm text-gray-900 font-mono">{{ $account_group->code }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Nature</dt>
                            <dd>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $account_group->nature === 'assets' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $account_group->nature === 'liabilities' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $account_group->nature === 'equity' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $account_group->nature === 'income' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $account_group->nature === 'expenses' ? 'bg-orange-100 text-orange-800' : '' }}">
                                    {{ ucfirst($account_group->nature) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Status</dt>
                            <dd>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $account_group->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $account_group->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Hierarchy Information -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Hierarchy</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Level</dt>
                            <dd class="text-sm text-gray-900">
                                Level {{ $account_group->getLevel() + 1 }}
                                @if($account_group->parent)
                                    (Child Group)
                                @else
                                    (Parent Group)
                                @endif
                            </dd>
                        </div>
                        @if($account_group->parent)
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Parent Group</dt>
                                <dd class="text-sm text-gray-900">
                                    <a href="{{ route('tenant.accounting.account-groups.show', ['tenant' => $tenant->slug, 'account_group' => $account_group->parent->id]) }}"
                                       class="text-blue-600 hover:text-blue-900 font-medium">
                                        {{ $account_group->parent->name }} ({{ $account_group->parent->code }})
                                    </a>
                                </dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Full Path</dt>
                            <dd class="text-sm text-gray-900">{{ $account_group->getHierarchyPath() }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Child Groups</dt>
                            <dd class="text-sm text-gray-900">{{ $account_group->children->count() }} groups</dd>
                        </div>
                    </dl>
                </div>

                <!-- Statistics -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">Statistics</h4>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Direct Ledger Accounts</dt>
                           <dd class="text-sm text-gray-900">{{ $ledgerAccountsCount }} accounts</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Total Accounts (Inc. Children)</dt>
                            <dd class="text-sm text-gray-900">{{ $totalAccountsCount }} accounts</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Created Date</dt>
                            <dd class="text-sm text-gray-900">{{ $account_group->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-600">Last Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $account_group->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Child Groups -->
    @if($account_group->children->count() > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Child Groups ({{ $account_group->children->count() }})</h3>
                    <a href="{{ route('tenant.accounting.account-groups.create', ['tenant' => $tenant->slug, 'parent_id' => $account_group->id]) }}"
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Child Group
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name & Code
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Accounts
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($account_group->children->sortBy('name') as $childGroup)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ strtoupper(substr($childGroup->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $childGroup->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 font-mono">
                                                {{ $childGroup->code }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                        {{ $childGroup->ledgerAccounts()->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $childGroup->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $childGroup->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('tenant.accounting.account-groups.show', ['tenant' => $tenant->slug, 'account_group' => $childGroup->id]) }}"
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                           title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('tenant.accounting.account-groups.edit', ['tenant' => $tenant->slug, 'account_group' => $childGroup->id]) }}"
                                           class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50"
                                           title="Edit Group">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Ledger Accounts -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    Direct Ledger Accounts ({{ $ledgerAccounts->count() }})
                </h3>
                <a href="{{ route('tenant.accounting.ledger-accounts.create', ['tenant' => $tenant->slug, 'group_id' => $account_group->id]) }}"
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Ledger Account
                </a>
            </div>
        </div>

        @if($ledgerAccounts->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account Name & Code
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Opening Balance
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Current Balance
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($ledgerAccounts->sortBy('name') as $ledgerAccount)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ strtoupper(substr($ledgerAccount->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $ledgerAccount->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 font-mono">
                                                {{ $ledgerAccount->code }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    ₦{{ number_format($ledgerAccount->opening_balance, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    @php
                                        $balance = $ledgerAccount->getCurrentBalance();
                                        $balanceType = $ledgerAccount->getBalanceType($balance);
                                        $isCredit = $balanceType === 'cr';
                                    @endphp
                                    <span class="{{ $isCredit ? 'text-green-600' : 'text-blue-600' }}">
                                        ₦{{ number_format(abs($balance), 2) }} {{ strtoupper($balanceType) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $ledgerAccount->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $ledgerAccount->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('tenant.accounting.ledger-accounts.show', ['tenant' => $tenant->slug, 'ledger_account' => $ledgerAccount->id]) }}"
                                           class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50"
                                           title="View Account">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('tenant.accounting.ledger-accounts.edit', ['tenant' => $tenant->slug, 'ledger_account' => $ledgerAccount->id]) }}"
                                           class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50"
                                           title="Edit Account">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Ledger Accounts</h3>
                <p class="text-gray-500 mb-6">
                    This account group doesn't have any ledger accounts yet.
                </p>
                <a href="{{ route('tenant.accounting.ledger-accounts.create', ['tenant' => $tenant->slug, 'group_id' => $account_group->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create First Ledger Account
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('tenant.accounting.account-groups.create', ['tenant' => $tenant->slug, 'parent_id' => $account_group->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Child Group
            </a>

            <a href="{{ route('tenant.accounting.ledger-accounts.create', ['tenant' => $tenant->slug, 'group_id' => $account_group->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Ledger Account
            </a>

            <form method="POST" action="{{ route('tenant.accounting.account-groups.toggle', ['tenant' => $tenant->slug, 'account_group' => $account_group->id]) }}" class="inline">
                @csrf
                <button type="submit"
                        onclick="return confirm('Are you sure you want to {{ $account_group->is_active ? 'deactivate' : 'activate' }} this account group?')"
                        class="inline-flex items-center px-4 py-2 bg-{{ $account_group->is_active ? 'yellow' : 'green' }}-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-{{ $account_group->is_active ? 'yellow' : 'green' }}-700 focus:bg-{{ $account_group->is_active ? 'yellow' : 'green' }}-700 active:bg-{{ $account_group->is_active ? 'yellow' : 'green' }}-900 focus:outline-none focus:ring-2 focus:ring-{{ $account_group->is_active ? 'yellow' : 'green' }}-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    @if($account_group->is_active)
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Deactivate Group
                    @else
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-6 4h6m2-5a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Activate Group
                    @endif
                </button>
            </form>

            @if($directAccountsCount === 0 && $account_group->children->count() === 0)
                <form method="POST" action="{{ route('tenant.accounting.account-groups.destroy', ['tenant' => $tenant->slug, 'account_group' => $account_group->id]) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Are you sure you want to delete this account group? This action cannot be undone.')"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Group
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection