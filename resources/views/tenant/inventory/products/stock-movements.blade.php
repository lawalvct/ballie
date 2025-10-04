@extends('layouts.tenant')

@section('title', 'Stock Movements - ' . $product->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}"
                           class="text-gray-400 hover:text-gray-500">
                            Products
                        </a>
                    </li>
                    <li class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <a href="{{ route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
                           class="ml-4 text-gray-400 hover:text-gray-500">
                            {{ $product->name }}
                        </a>
                    </li>
                    <li class="flex items-center">
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="ml-4 text-gray-500">Stock Movements</span>
                    </li>
                </ol>
            </nav>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Stock Movements</h1>
            <p class="mt-2 text-gray-600">{{ $product->name }} - Movement History</p>
        </div>
        <div class="mt-4 lg:mt-0">
            <a href="{{ route('tenant.inventory.stock-journal.create', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Stock Entry
            </a>
        </div>
    </div>

    <!-- Product Summary Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Current Stock</h3>
                <p class="mt-1 text-2xl font-semibold text-gray-900">
                    {{ number_format($product->getStockAsOfDate(now()), 2) }}
                    {{ $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? '' }}
                </p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Starting Balance</h3>
                <p class="mt-1 text-2xl font-semibold text-gray-900">
                    {{ number_format($startingStock ?? 0, 2) }}
                    {{ $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? '' }}
                </p>
                <p class="text-xs text-gray-500">As of {{ \Carbon\Carbon::parse($fromDate)->format('M d, Y') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Purchase Rate</h3>
                <p class="mt-1 text-lg font-semibold text-green-600">
                    ₦{{ number_format($product->purchase_rate, 2) }}
                </p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500">Sales Rate</h3>
                <p class="mt-1 text-lg font-semibold text-blue-600">
                    ₦{{ number_format($product->sales_rate, 2) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date"
                       id="from_date"
                       name="from_date"
                       value="{{ $fromDate }}"
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date"
                       id="to_date"
                       name="to_date"
                       value="{{ $toDate }}"
                       {{-- max="{{ now()->toDateString() }}" --}}
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                <select id="transaction_type"
                        name="transaction_type"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="">All Types</option>
                    @foreach($transactionTypes as $type)
                        <option value="{{ $type }}" {{ $transactionType === $type ? 'selected' : '' }}>
                            {{ ucwords(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                Filter
            </button>

            <a href="{{ route('tenant.inventory.products.stock-movements', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Reset
            </a>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        @if($movements->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Transaction
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                In
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Out
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Rate
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Running Balance
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reference
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($movements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $movement->transaction_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $movement->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $movement->type_display }}
                                    </div>
                                    @if($movement->transaction_reference)
                                        <div class="text-xs text-gray-500 font-mono">
                                            {{ $movement->transaction_reference }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($movement->quantity > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                            </svg>
                                            Stock In
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                            </svg>
                                            Stock Out
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($movement->quantity > 0)
                                        <div class="text-sm font-medium text-green-600">
                                            {{ number_format($movement->quantity, 2) }}
                                            {{ $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? '' }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($movement->quantity < 0)
                                        <div class="text-sm font-medium text-red-600">
                                            {{ number_format(abs($movement->quantity), 2) }}
                                            {{ $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? '' }}
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-400">-</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        ₦{{ number_format($movement->rate, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ number_format($movement->running_stock ?? 0, 2) }}
                                        {{ $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? '' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $movement->reference }}
                                    </div>
                                    @if($movement->remarks)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $movement->remarks }}
                                        </div>
                                    @endif
                                    @if($movement->creator)
                                        <div class="text-xs text-gray-400 mt-1">
                                            by {{ $movement->creator->name }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($movements->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $movements->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No stock movements found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['from_date', 'to_date', 'transaction_type']))
                        Try adjusting your filters to see more results.
                    @else
                        This product doesn't have any stock movements yet.
                    @endif
                </p>
                <div class="mt-6">
                    <a href="{{ route('tenant.inventory.stock-journal.create', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Stock Entry
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
