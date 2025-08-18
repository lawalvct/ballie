@extends('layouts.tenant')

@section('title', 'Customer Statements')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Customer Statements</h1>
                <p class="text-gray-600 mt-1">View customer balances and generate statements</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tenant.crm.customers.index', ['tenant' => $tenant->slug]) }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Customers
                </a>
                <a href="{{ route('tenant.crm.customers.export', ['tenant' => $tenant->slug]) }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i> Export
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6">
                <form method="GET" action="{{ route('tenant.crm.customers.statements', ['tenant' => $tenant->slug]) }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text"
                                   class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   id="search"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Search by name, email, phone...">
                        </div>
                        <div>
                            <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-2">Customer Type</label>
                            <select name="customer_type" id="customer_type" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                <option value="individual" {{ $customer_type === 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="business" {{ $customer_type === 'business' ? 'selected' : '' }}>Business</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="flex flex-col justify-end">
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-search mr-2"></i> Filter
                                </button>
                                <a href="{{ route('tenant.crm.customers.statements', ['tenant' => $tenant->slug]) }}"
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold opacity-90">Total Customers</h3>
                        <p class="text-3xl font-bold">{{ $customers->total() }}</p>
                    </div>
                    <div class="text-blue-200">
                        <i class="fas fa-users text-3xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold opacity-90">Total Debits</h3>
                        <p class="text-3xl font-bold">₦{{ number_format($customers->sum('total_debits'), 2) }}</p>
                    </div>
                    <div class="text-green-200">
                        <i class="fas fa-arrow-up text-3xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold opacity-90">Total Credits</h3>
                        <p class="text-3xl font-bold">₦{{ number_format($customers->sum('total_credits'), 2) }}</p>
                    </div>
                    <div class="text-red-200">
                        <i class="fas fa-arrow-down text-3xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold opacity-90">Net Balance</h3>
                        <p class="text-3xl font-bold">₦{{ number_format($customers->sum('running_balance'), 2) }}</p>
                    </div>
                    <div class="text-purple-200">
                        <i class="fas fa-balance-scale text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Statements Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-900">Customer Account Statements</h2>
                    <span class="text-sm text-gray-500">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</span>
                </div>
            </div>

            @if($customers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_debits', 'direction' => $sort === 'total_debits' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-gray-500 hover:text-gray-700 flex items-center">
                                        Total Debits
                                        @if($sort === 'total_debits')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 opacity-50"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_credits', 'direction' => $sort === 'total_credits' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-gray-500 hover:text-gray-700 flex items-center">
                                        Total Credits
                                        @if($sort === 'total_credits')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 opacity-50"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'current_balance', 'direction' => $sort === 'current_balance' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-gray-500 hover:text-gray-700 flex items-center">
                                        Running Balance
                                        @if($sort === 'current_balance')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 opacity-50"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                                {{ strtoupper(substr($customer->first_name ?? $customer->company_name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    @if($customer->customer_type === 'individual')
                                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                                    @else
                                                        {{ $customer->company_name }}
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $customer->customer_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($customer->email)
                                                <div class="flex items-center mb-1">
                                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                                    {{ $customer->email }}
                                                </div>
                                            @endif
                                            @if($customer->phone)
                                                <div class="flex items-center">
                                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                                    {{ $customer->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-green-600">
                                            ₦{{ number_format($customer->total_debits, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-red-600">
                                            ₦{{ number_format($customer->total_credits, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold {{ $customer->running_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ₦{{ number_format($customer->current_balance, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $customer->balance_type === 'dr' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ strtoupper($customer->balance_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($customer->status === 'active')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2">
                                            @if($customer->ledgerAccount)
                                                <a href="{{ route('tenant.accounting.ledger-accounts.print-ledger', ['tenant' => $tenant->slug, 'ledgerAccount' => $customer->ledgerAccount->id]) }}"
                                                   class="text-blue-600 hover:text-blue-900 text-sm"
                                                   title="Print Statement"
                                                   target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="{{ route('tenant.accounting.ledger-accounts.export-ledger', ['tenant' => $tenant->slug, 'ledgerAccount' => $customer->ledgerAccount->id]) }}"
                                                   class="text-green-600 hover:text-green-900 text-sm"
                                                   title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('tenant.crm.customers.show', ['tenant' => $tenant->slug, 'customer' => $customer->id]) }}"
                                               class="text-purple-600 hover:text-purple-900 text-sm"
                                               title="View Customer">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-sm text-gray-500">
                                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                            </span>
                        </div>
                        <div>
                            {{ $customers->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-5xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Customers Found</h3>
                    <p class="text-gray-500 mb-6">No customers match your current filters.</p>
                    <a href="{{ route('tenant.crm.customers.create', ['tenant' => $tenant->slug]) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i> Add First Customer
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
