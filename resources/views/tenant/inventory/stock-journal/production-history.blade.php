@extends('layouts.tenant')

@section('title', 'Production History')
@section('page-title', 'Production History')
@section('page-description', 'Track production output, rejected and wastage quantities, and operator performance.')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Production History</h2>
            <p class="text-sm text-gray-500">
                {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tenant.inventory.stock-journal.index', ['tenant' => $tenant->slug]) }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Stock Journal
            </a>
            <a href="{{ route('tenant.inventory.stock-journal.create.type', ['tenant' => $tenant->slug, 'type' => 'production']) }}"
                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-primary-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Production Report
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('tenant.inventory.stock-journal.production-history', ['tenant' => $tenant->slug]) }}"
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? $fromDate }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? $toDate }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Operator</label>
                <select name="operator_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All Operators</option>
                    @foreach($operators as $op)
                        <option value="{{ $op->id }}" {{ ($filters['operator_id'] ?? '') == $op->id ? 'selected' : '' }}>
                            {{ $op->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Product</label>
                <select name="product_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All Products</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ ($filters['product_id'] ?? '') == $prod->id ? 'selected' : '' }}>
                            {{ $prod->name }} @if($prod->sku) ({{ $prod->sku }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="posted" {{ ($filters['status'] ?? '') === 'posted' ? 'selected' : '' }}>Posted</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Journal #, batch, work order"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div class="md:col-span-2 lg:col-span-6 flex items-center justify-end gap-2">
                <a href="{{ route('tenant.inventory.stock-journal.production-history', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    Reset
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-primary-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Aggregates -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm font-semibold text-gray-900 mb-3">Output by Unit (Posted)</div>
            <div class="divide-y divide-gray-100">
                @forelse($productionUnitTotals as $unitTotal)
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ number_format($unitTotal['quantity'], 4) }} {{ $unitTotal['unit'] }}</div>
                            <div class="text-xs text-gray-500">
                                Rejected: {{ number_format($unitTotal['rejected_quantity'], 4) }} {{ $unitTotal['unit'] }}
                                &middot; Wastage: {{ number_format($unitTotal['waste_quantity'], 4) }} {{ $unitTotal['unit'] }}
                            </div>
                        </div>
                        <div class="text-sm font-semibold text-green-700">₦{{ number_format($unitTotal['amount'], 2) }}</div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 py-3">No posted production output in this period.</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-sm font-semibold text-gray-900 mb-3">Top Produced Items</div>
            <div class="divide-y divide-gray-100">
                @forelse($productionProductTotals->take(8) as $productTotal)
                    <div class="py-2">
                        <div class="flex justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">{{ $productTotal['product']->name ?? 'Product' }}</div>
                                <div class="text-xs text-gray-500">{{ $productTotal['product']->sku ?? 'No SKU' }}</div>
                                <div class="text-xs text-gray-500">
                                    Rejected: {{ number_format($productTotal['rejected_quantity'], 4) }}
                                </div>
                            </div>
                            <div class="text-right text-sm font-semibold text-gray-900 whitespace-nowrap">
                                {{ number_format($productTotal['quantity'], 4) }} {{ $productTotal['unit'] }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500 py-3">No finished goods posted yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Production Reports Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Output</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rejected</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wastage</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($productionHistory as $entry)
                        @php
                            $outputs = $entry->items->where('movement_type', 'in');
                            $consumptions = $entry->items->where('movement_type', 'out');
                            $unitGroups = $outputs->groupBy(fn($item) => $item->unit_snapshot ?: ($item->product->primaryUnit->symbol ?? $item->product->primaryUnit->name ?? 'Unit'));
                            $wasteGroups = $consumptions->groupBy(fn($item) => $item->unit_snapshot ?: ($item->product->primaryUnit->symbol ?? $item->product->primaryUnit->name ?? 'Unit'));
                            $outputText = $unitGroups
                                ->map(fn($items, $unit) => number_format($items->sum('quantity'), 4) . ' ' . $unit)
                                ->implode(', ');
                            $rejectedText = $unitGroups
                                ->map(fn($items, $unit) => number_format($items->sum('rejected_quantity'), 4) . ' ' . $unit)
                                ->filter(fn($text) => !str_starts_with($text, '0.0000 '))
                                ->implode(', ');
                            $wasteText = $wasteGroups
                                ->map(fn($items, $unit) => number_format($items->sum('waste_quantity'), 4) . ' ' . $unit)
                                ->filter(fn($text) => !str_starts_with($text, '0.0000 '))
                                ->implode(', ');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('tenant.inventory.stock-journal.show', ['tenant' => $tenant->slug, 'stockJournal' => $entry->id]) }}"
                                   class="text-sm font-medium text-green-700 hover:text-green-900">{{ $entry->journal_number }}</a>
                                <div class="text-xs text-gray-500">{{ $entry->journal_date->format('d M Y') }}</div>
                                @if($entry->production_batch_number)
                                    <div class="text-xs text-gray-400">Batch: {{ $entry->production_batch_number }}</div>
                                @endif
                                @if($entry->work_order_number)
                                    <div class="text-xs text-gray-400">WO: {{ $entry->work_order_number }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">
                                {{ $entry->operator->full_name ?? 'Not assigned' }}
                                @if($entry->assistantOperator)
                                    <div class="text-xs text-gray-500">Assist: {{ $entry->assistantOperator->full_name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $outputText ?: '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $rejectedText ?: '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $wasteText ?: '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $entry->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $entry->status === 'posted' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $entry->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $entry->status_display }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('tenant.inventory.stock-journal.show', ['tenant' => $tenant->slug, 'stockJournal' => $entry->id]) }}"
                                   class="text-primary-600 hover:text-primary-800 mr-3">View</a>
                                <a href="{{ route('tenant.inventory.stock-journal.pdf', ['tenant' => $tenant->slug, 'stockJournal' => $entry->id]) }}"
                                   class="text-indigo-600 hover:text-indigo-800 mr-3">PDF</a>
                                <a href="{{ route('tenant.inventory.stock-journal.print', ['tenant' => $tenant->slug, 'stockJournal' => $entry->id]) }}"
                                   class="text-gray-600 hover:text-gray-800" target="_blank">Print</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">No production reports match the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($productionHistory->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $productionHistory->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
