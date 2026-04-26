@extends('layouts.tenant')

@section('title', 'Bin Card')
@section('page-title', 'Bin Card (Inventory Ledger)')
@section('page-description')
    <span class="hidden md:inline">Product-level ledger showing opening, inwards, outwards and closing balances</span>
@endsection

@section('content')
@php
    $selectedProduct = $product ?? $products->firstWhere('id', $productId);
    $productUnit = $selectedProduct?->primaryUnit?->symbol ?? $selectedProduct?->primaryUnit?->name ?? 'Unit';
    $pdfQuery = collect([
        'from_date' => $fromDate,
        'to_date' => $toDate,
        'product_id' => $productId,
    ])->filter(fn ($value) => filled($value))->all();
    $pdfUrl = route('tenant.reports.bin-card.pdf', array_merge(['tenant' => $tenant->slug], $pdfQuery));
    $pdfWithoutValuesUrl = route('tenant.reports.bin-card.pdf', array_merge(['tenant' => $tenant->slug], $pdfQuery, ['hide_values' => 1]));
    $netQtyClass = $netMovementQty > 0 ? 'text-green-700' : ($netMovementQty < 0 ? 'text-red-700' : 'text-gray-900');
    $netValueClass = $netMovementValue > 0 ? 'text-green-700' : ($netMovementValue < 0 ? 'text-red-700' : 'text-gray-900');
    $closingToneClass = $closingQty < 0 ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50';
@endphp

<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('tenant.reports.stock-summary', $tenant->slug) }}"
                   class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Stock Summary
                </a>

                <a href="{{ route('tenant.reports.low-stock-alert', $tenant->slug) }}"
                   class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Low Stock
                </a>

                <a href="{{ route('tenant.reports.stock-valuation', $tenant->slug) }}"
                   class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                    Valuation
                </a>

                <a href="{{ route('tenant.reports.stock-movement', $tenant->slug) }}"
                   class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                    Movement
                </a>

                <a href="{{ route('tenant.reports.bin-card', $tenant->slug) }}"
                   class="inline-flex items-center px-3 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Bin Card
                </a>
            </div>

            <div class="flex flex-wrap gap-2">
                <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
                </button>
                <a href="{{ route('tenant.reports.index', $tenant->slug) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $selectedProduct?->name ?? 'No product selected' }}</h2>
                    @if($selectedProduct?->sku)
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">SKU: {{ $selectedProduct->sku }}</span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                    @if($selectedProduct)
                        <span class="mx-2 text-gray-300">|</span>
                        {{ $selectedProduct->category->name ?? 'Uncategorized' }}
                        <span class="mx-2 text-gray-300">|</span>
                        Unit: {{ $productUnit }}
                    @endif
                </p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                <div class="rounded-lg border border-gray-200 px-3 py-2">
                    <div class="text-xs text-gray-500">Transactions</div>
                    <div class="font-semibold text-gray-900">{{ number_format($transactionCount) }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 px-3 py-2">
                    <div class="text-xs text-gray-500">Category</div>
                    <div class="font-semibold text-gray-900 truncate max-w-36">{{ $selectedProduct->category->name ?? 'N/A' }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 px-3 py-2">
                    <div class="text-xs text-gray-500">Unit</div>
                    <div class="font-semibold text-gray-900">{{ $selectedProduct ? $productUnit : 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-gray-200">
            <div class="p-5">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Opening</dt>
                <dd class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($openingQty, 2) }}</dd>
                <p class="mt-1 text-xs text-gray-500">NGN {{ number_format($openingValue, 2) }}</p>
            </div>
            <div class="p-5">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Inwards</dt>
                <dd class="mt-2 text-2xl font-bold text-green-700">{{ number_format($totalInQty, 2) }}</dd>
                <p class="mt-1 text-xs text-green-700">NGN {{ number_format($totalInValue, 2) }}</p>
            </div>
            <div class="p-5">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Outwards</dt>
                <dd class="mt-2 text-2xl font-bold text-red-700">{{ number_format($totalOutQty, 2) }}</dd>
                <p class="mt-1 text-xs text-red-700">NGN {{ number_format($totalOutValue, 2) }}</p>
            </div>
            <div class="p-5">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Net Movement</dt>
                <dd class="mt-2 text-2xl font-bold {{ $netQtyClass }}">{{ number_format($netMovementQty, 2) }}</dd>
                <p class="mt-1 text-xs {{ $netValueClass }}">NGN {{ number_format($netMovementValue, 2) }}</p>
            </div>
            <div class="p-5 {{ $closingToneClass }}">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Closing</dt>
                <dd class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($closingQty, 2) }}</dd>
                <p class="mt-1 text-xs text-gray-600">NGN {{ number_format($closingValue, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-2">
                <label for="from_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">From</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" />
            </div>
            <div class="lg:col-span-2">
                <label for="to_date" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">To</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}" class="mt-1 block w-full border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500" />
            </div>
            <div class="lg:col-span-4">
                <label for="product_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide">Product</label>
                <select name="product_id" id="product_id" class="mt-1 block w-full border-gray-300 rounded-lg focus:border-teal-500 focus:ring-teal-500">
                    <option value="">Select product</option>
                    @foreach($products as $productOption)
                        <option value="{{ $productOption->id }}" {{ $productId == $productOption->id ? 'selected' : '' }}>
                            {{ $productOption->name }} @if($productOption->sku) ({{ $productOption->sku }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <button type="submit" class="inline-flex flex-1 items-center justify-center px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-8.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    Apply
                </button>
                <a href="{{ $pdfUrl }}" class="inline-flex flex-1 items-center justify-center px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-700 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M4 4h16v4H4zM4 8v10a2 2 0 002 2h12a2 2 0 002-2V8" />
                    </svg>
                    PDF With Values
                </a>
                <a href="{{ $pdfWithoutValuesUrl }}" class="inline-flex flex-1 items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-800 rounded-lg text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M4 4h16v4H4zM4 8v10a2 2 0 002 2h12a2 2 0 002-2V8" />
                    </svg>
                    PDF No Values
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Product Flow Ledger</h3>
                <p class="text-sm text-gray-500">Chronological movement trail with running quantity and value.</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-700 border border-teal-200">
                {{ number_format($transactionCount) }} movement{{ $transactionCount === 1 ? '' : 's' }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Particulars</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Voucher</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">In Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">In Value (NGN)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Out Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Out Value (NGN)</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Closing Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Closing Value (NGN)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Remark</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($fromDate)->subDay()->format('d-M-Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">Opening Balance</td>
                        <td class="px-4 py-3 text-gray-500">-</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ number_format($openingQty, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">NGN {{ number_format($openingValue, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">-</td>
                        <td class="px-4 py-3 text-right text-gray-400">-</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ number_format($openingQty, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">NGN {{ number_format($openingValue, 2) }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>

                    @forelse($paginatedRows as $row)
                        @php
                            $movementBadgeClass = $row->in_qty > 0 ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200';
                            $movementLabel = $row->in_qty > 0 ? 'Inward' : 'Outward';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ \Carbon\Carbon::parse($row->date)->format('d-M-Y') }}</td>
                            <td class="px-4 py-3 min-w-64">
                                <div class="font-medium text-gray-900">{{ $row->particulars }}</div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $movementBadgeClass }}">{{ $movementLabel }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-gray-900">{{ $row->vch_type }}</div>
                                <div class="text-xs text-gray-500">{{ $row->vch_no }}</div>
                            </td>
                            <td class="px-4 py-3 text-right text-green-700 font-medium">{{ $row->in_qty ? number_format($row->in_qty, 2) : '-' }}</td>
                            <td class="px-4 py-3 text-right text-green-700">{{ $row->in_value ? 'NGN ' . number_format($row->in_value, 2) : '-' }}</td>
                            <td class="px-4 py-3 text-right text-red-700 font-medium">{{ $row->out_qty ? number_format($row->out_qty, 2) : '-' }}</td>
                            <td class="px-4 py-3 text-right text-red-700">{{ $row->out_value ? 'NGN ' . number_format($row->out_value, 2) : '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ number_format($row->closing_qty, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">NGN {{ number_format($row->closing_value, 2) }}</td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-sm text-gray-500">
                                No movements found for the selected period.
                            </td>
                        </tr>
                    @endforelse

                    <tr class="bg-gray-50 font-semibold">
                        <td colspan="3" class="px-4 py-3 text-right text-gray-900">Totals</td>
                        <td class="px-4 py-3 text-right text-green-700">{{ number_format($totalInQty, 2) }}</td>
                        <td class="px-4 py-3 text-right text-green-700">NGN {{ number_format($totalInValue, 2) }}</td>
                        <td class="px-4 py-3 text-right text-red-700">{{ number_format($totalOutQty, 2) }}</td>
                        <td class="px-4 py-3 text-right text-red-700">NGN {{ number_format($totalOutValue, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-900">{{ number_format($closingQty, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-900">NGN {{ number_format($closingValue, 2) }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if($paginatedRows->hasPages())
        <div class="px-2">
            {{ $paginatedRows->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
