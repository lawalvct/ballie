@extends('layouts.tenant')

@section('title', 'Bin Card')
@section('page-title', 'Bin Card (Inventory Ledger)')
@section('page-description')
    <span class="hidden md:inline">Product-level ledger showing opening, inwards, outwards and closing balances</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tenant.reports.stock-summary', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Stock Summary
            </a>

            <a href="{{ route('tenant.reports.low-stock-alert', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Low Stock Alert
            </a>

            <a href="{{ route('tenant.reports.stock-valuation', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Stock Valuation
            </a>

            <a href="{{ route('tenant.reports.stock-movement', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
                Stock Movement
            </a>

            <a href="{{ route('tenant.reports.bin-card', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700 focus:bg-teal-700 active:bg-teal-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Bin Card
            </a>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ route('tenant.reports.index', $tenant->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>

    @if($productId)
    <!-- Product Info Bar -->
    <div class="bg-teal-50 border-l-4 border-teal-600 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-teal-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span class="text-sm font-medium text-teal-900">Product: {{ optional($products->firstWhere('id', $productId))->name }}</span>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700">From</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="mt-1 block w-full border-gray-300 rounded-md" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">To</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="mt-1 block w-full border-gray-300 rounded-md" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">Product</label>
                <select name="product_id" class="mt-1 block w-full border-gray-300 rounded-md">
                    <option value="">Select product</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ $productId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white rounded">Apply</button>
            </div>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Particulars</th>
                    <th class="px-4 py-2 text-left">Vch Type</th>
                    <th class="px-4 py-2 text-left">Vch No.</th>
                    <th class="px-4 py-2 text-right">In Qty</th>
                    <th class="px-4 py-2 text-right">In Value</th>
                    <th class="px-4 py-2 text-right">Out Qty</th>
                    <th class="px-4 py-2 text-right">Out Value</th>
                    <th class="px-4 py-2 text-right">Closing Qty</th>
                    <th class="px-4 py-2 text-right">Closing Value</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                {{-- Opening Balance Row --}}
                <tr class="bg-gray-50">
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($fromDate)->subDay()->format('d-M-Y') }}</td>
                    <td class="px-4 py-3">Opening Balance</td>
                    <td class="px-4 py-3">-</td>
                    <td class="px-4 py-3">-</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ number_format($openingQty, 2) }}</td>
                    <td class="px-4 py-3 text-right font-semibold">₦{{ number_format($openingValue, 2) }}</td>
                    <td class="px-4 py-3 text-right">-</td>
                    <td class="px-4 py-3 text-right">-</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ number_format($openingQty, 2) }}</td>
                    <td class="px-4 py-3 text-right font-semibold">₦{{ number_format($openingValue, 2) }}</td>
                </tr>

                @forelse($paginatedRows as $row)
                    <tr>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($row->date)->format('d-M-Y') }}</td>
                        <td class="px-4 py-2">{{ $row->particulars }}</td>
                        <td class="px-4 py-2">{{ $row->vch_type }}</td>
                        <td class="px-4 py-2">{{ $row->vch_no }}</td>
                        <td class="px-4 py-2 text-right text-green-600">{{ $row->in_qty ? number_format($row->in_qty, 2) : '-' }}</td>
                        <td class="px-4 py-2 text-right text-green-600">{{ $row->in_value ? '₦'.number_format($row->in_value, 2) : '-' }}</td>
                        <td class="px-4 py-2 text-right text-red-600">{{ $row->out_qty ? number_format($row->out_qty, 2) : '-' }}</td>
                        <td class="px-4 py-2 text-right text-red-600">{{ $row->out_value ? '₦'.number_format($row->out_value, 2) : '-' }}</td>
                        <td class="px-4 py-2 text-right font-semibold">{{ number_format($row->closing_qty, 2) }}</td>
                        <td class="px-4 py-2 text-right font-semibold">₦{{ number_format($row->closing_value, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-sm text-gray-500">
                            No movements found for the selected period.
                        </td>
                    </tr>
                @endforelse

                {{-- Totals Row --}}
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="4" class="px-4 py-3 text-right">Totals</td>
                    <td class="px-4 py-3 text-right text-green-700">{{ number_format($totalInQty, 2) }}</td>
                    <td class="px-4 py-3 text-right text-green-700">₦{{ number_format($totalInValue, 2) }}</td>
                    <td class="px-4 py-3 text-right text-red-700">{{ number_format($totalOutQty, 2) }}</td>
                    <td class="px-4 py-3 text-right text-red-700">₦{{ number_format($totalOutValue, 2) }}</td>
                    <td colspan="2" class="px-4 py-3 text-right">&nbsp;</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($paginatedRows->hasPages())
        <div class="px-2">
            {{ $paginatedRows->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
