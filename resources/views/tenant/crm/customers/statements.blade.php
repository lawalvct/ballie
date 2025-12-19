@extends('layouts.tenant')

@section('title', 'Customer Statements')
@section('page-title', 'Customer Statements')
@section('page-description', 'View customer balances and generate statements')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customer Statements</h1>
                <p class="text-sm text-gray-600 mt-1">View customer balances and generate statements</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('tenant.crm.customers.index', tenant('slug')) }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 text-sm rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Customers
                </a>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 text-sm rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="p-4">
                <form method="GET" action="{{ route('tenant.crm.customers.statements', tenant('slug')) }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div class="md:col-span-1">
                            <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                            <input type="text"
                                   class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   id="search"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by name, email, phone...">
                        </div>
                        <div>
                            <label for="customer_type" class="block text-xs font-medium text-gray-700 mb-1">Customer Type</label>
                            <select name="customer_type" id="customer_type" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Types</option>
                                <option value="individual" {{ request('customer_type') === 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="business" {{ request('customer_type') === 'business' ? 'selected' : '' }}>Business</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="status" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label for="date_range" class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                            <input type="text" name="date_range" id="date_range" class="w-full text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Select date range">
                        </div>
                        <div class="flex flex-col justify-end">
                            <div class="flex space-x-2">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 text-sm rounded-lg transition-colors duration-200">
                                    <i class="fas fa-search mr-1"></i> Filter
                                </button>
                                <a href="{{ route('tenant.crm.customers.statements', tenant('slug')) }}"
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg transition-colors duration-200">
                                    <i class="fas fa-times mr-1"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs font-semibold opacity-90 mb-1">Total Customers</h3>
                        <p class="text-lg md:text-xl font-bold truncate">{{ number_format($totalCustomers) }}</p>
                    </div>
                    <div class="text-blue-200 ml-2 flex-shrink-0">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs font-semibold opacity-90 mb-1">Total Receivable (DR)</h3>
                        <p class="text-base md:text-lg font-bold break-words" title="₦{{ number_format($totalReceivable, 2) }}">₦{{ number_format($totalReceivable, 2) }}</p>
                    </div>
                    <div class="text-green-200 ml-2 flex-shrink-0">
                        <i class="fas fa-arrow-up text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs font-semibold opacity-90 mb-1">Total Payable (CR)</h3>
                        <p class="text-base md:text-lg font-bold break-words" title="₦{{ number_format($totalPayable, 2) }}">₦{{ number_format($totalPayable, 2) }}</p>
                    </div>
                    <div class="text-red-200 ml-2 flex-shrink-0">
                        <i class="fas fa-arrow-down text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs font-semibold opacity-90 mb-1">Net Balance</h3>
                        <p class="text-base md:text-lg font-bold break-words" title="₦{{ number_format(abs($netBalance), 2) }}">₦{{ number_format(abs($netBalance), 2) }}</p>
                        <p class="text-xs opacity-90 mt-1">{{ $netBalance >= 0 ? 'Receivable' : 'Payable' }}</p>
                    </div>
                    <div class="text-purple-200 ml-2 flex-shrink-0">
                        <i class="fas fa-balance-scale text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Statements Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Customer Account Statements</h2>
                    <span class="text-xs text-gray-500">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</span>
                </div>
            </div>

            @if($customers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_debits', 'direction' => $sort === 'total_debits' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-gray-500 hover:text-gray-700 flex items-center text-xs">
                                        Total Debits
                                        @if($sort === 'total_debits')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1 text-xs"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 opacity-50 text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_credits', 'direction' => $sort === 'total_credits' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-gray-500 hover:text-gray-700 flex items-center text-xs">
                                        Total Credits
                                        @if($sort === 'total_credits')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1 text-xs"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 opacity-50 text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'current_balance', 'direction' => $sort === 'current_balance' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-gray-500 hover:text-gray-700 flex items-center text-xs">
                                        Running Balance
                                        @if($sort === 'current_balance')
                                            <i class="fas fa-sort-{{ $direction === 'asc' ? 'up' : 'down' }} ml-1 text-xs"></i>
                                        @else
                                            <i class="fas fa-sort ml-1 opacity-50 text-xs"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Activity</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                                {{ strtoupper(substr($customer->first_name ?? $customer->company_name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-xs font-medium text-gray-900">
                                                    @if($customer->customer_type === 'individual')
                                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                                    @else
                                                        {{ $customer->company_name }}
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $customer->customer_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-xs text-gray-900">
                                            @if($customer->email)
                                                <div class="flex items-center mb-1">
                                                    <i class="fas fa-envelope text-gray-400 mr-1 text-xs"></i>
                                                    <span class="truncate max-w-[150px]">{{ $customer->email }}</span>
                                                </div>
                                            @endif
                                            @if($customer->phone)
                                                <div class="flex items-center">
                                                    <i class="fas fa-phone text-gray-400 mr-1 text-xs"></i>
                                                    {{ $customer->phone }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-xs font-semibold text-green-600">
                                            ₦{{ number_format($customer->total_debits, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-xs font-semibold text-red-600">
                                            ₦{{ number_format($customer->total_credits, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="text-xs font-semibold {{ $customer->running_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            ₦{{ number_format(abs($customer->running_balance), 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $customer->running_balance >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $customer->running_balance >= 0 ? 'DR' : 'CR' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($customer->status === 'active')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                        {{ $customer->updated_at ? $customer->updated_at->diffForHumans() : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($customer->customer_type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('tenant.crm.customers.statement', ['tenant' => tenant('slug'), 'customer' => $customer->id]) }}"
                                               class="text-purple-600 hover:text-purple-900 text-sm"
                                               title="View Detailed Statement">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            @if($customer->ledgerAccount)
                                                <a href="{{ route('tenant.accounting.ledger-accounts.print-ledger', ['tenant' => tenant('slug'), 'ledgerAccount' => $customer->ledgerAccount->id]) }}"
                                                   class="text-blue-600 hover:text-blue-900 text-sm"
                                                   title="Print Statement"
                                                   target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a href="{{ route('tenant.accounting.ledger-accounts.export-ledger', ['tenant' => tenant('slug'), 'ledgerAccount' => $customer->ledgerAccount->id]) }}"
                                                   class="text-green-600 hover:text-green-900 text-sm"
                                                   title="Download PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('tenant.crm.customers.show', ['tenant' => tenant('slug'), 'customer' => $customer->id]) }}"
                                               class="text-gray-600 hover:text-gray-900 text-sm"
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
                <div class="px-4 py-3 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-xs text-gray-500">
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
                    <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-base font-medium text-gray-900 mb-2">No Customers Found</h3>
                    <p class="text-sm text-gray-500 mb-6">No customers match your current filters.</p>
                    <a href="{{ route('tenant.crm.customers.create', tenant('slug')) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i> Add First Customer
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.getElementById('customer_type').addEventListener('change', function() {
        this.form.submit();
    });

    document.getElementById('status').addEventListener('change', function() {
        this.form.submit();
    });

    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
</script>
@endpush

@endsection
