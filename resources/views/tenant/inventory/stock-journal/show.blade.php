@extends('layouts.tenant')

@section('title', 'Stock Journal Entry - ' . $stockJournal->journal_number)
@section('page-title', 'Stock Journal Entry')
@section('page-description', 'View detailed journal entry information and perform actions.')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.inventory.stock-journal.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Journal Entries
            </a>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Status Badge -->
            @if($stockJournal->status === 'draft')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <div class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></div>
                    Draft
                </span>
            @elseif($stockJournal->status === 'posted')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                    Posted
                </span>
            @elseif($stockJournal->status === 'cancelled')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                    Cancelled
                </span>
            @endif

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                @if($stockJournal->status === 'draft')
                    <a href="{{ route('tenant.inventory.stock-journal.edit', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id]) }}"
                       class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>

                    <form method="POST" action="{{ route('tenant.inventory.stock-journal.post', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id]) }}" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Are you sure you want to post this journal entry? This action cannot be undone.')"
                                class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Post Entry
                        </button>
                    </form>
                @elseif($stockJournal->status === 'posted')
                    <form method="POST" action="{{ route('tenant.inventory.stock-journal.cancel', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id]) }}" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this journal entry? This will reverse all stock movements.')"
                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel Entry
                        </button>
                    </form>
                @endif

                <!-- Duplicate Button -->
                <a href="{{ route('tenant.inventory.stock-journal.create', ['tenant' => $tenant->slug, 'duplicate_from' => $stockJournal->id]) }}"
                   class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Duplicate
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your request</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Journal Entry Header Information -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Journal Entry Details</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Journal Number</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $stockJournal->journal_number }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Entry Type</label>
                    <p class="text-lg font-medium text-gray-900 capitalize">
                        {{ str_replace(['_', '-'], ' ', $stockJournal->entry_type) }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Journal Date</label>
                    <p class="text-lg font-medium text-gray-900">{{ $stockJournal->journal_date->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Total Amount</label>
                    <p class="text-lg font-semibold text-green-600">₦{{ number_format($stockJournal->total_amount, 2) }}</p>
                </div>
            </div>

            @if($stockJournal->reference_number)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Reference Number</label>
                <p class="text-gray-900">{{ $stockJournal->reference_number }}</p>
            </div>
            @endif

            @if($stockJournal->narration)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-500 mb-1">Narration</label>
                <p class="text-gray-900">{{ $stockJournal->narration }}</p>
            </div>
            @endif

            <!-- Audit Information -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div>
                        <span class="font-medium">Created:</span> {{ $stockJournal->created_at->format('d M Y H:i') }}
                        <br><span class="font-medium">By:</span> {{ $stockJournal->createdBy->name ?? 'System' }}
                    </div>
                    @if($stockJournal->updated_at->ne($stockJournal->created_at))
                    <div>
                        <span class="font-medium">Updated:</span> {{ $stockJournal->updated_at->format('d M Y H:i') }}
                        <br><span class="font-medium">By:</span> {{ $stockJournal->updatedBy->name ?? 'System' }}
                    </div>
                    @endif
                    @if($stockJournal->posted_at)
                    <div>
                        <span class="font-medium">Posted:</span> {{ $stockJournal->posted_at->format('d M Y H:i') }}
                        <br><span class="font-medium">By:</span> {{ $stockJournal->postedBy->name ?? 'System' }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Entry Items -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Journal Entry Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Movement</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Before</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stock After</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stockJournal->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                <div class="text-sm text-gray-500">SKU: {{ $item->product->sku ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-400">{{ $item->product->productCategory->name ?? 'Uncategorized' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->movement_type === 'in')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                    In
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8V20m0 0l4-4m-4 4l-4-4M7 4v12m0 0l-4-4m4 4l4-4"></path>
                                    </svg>
                                    Out
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            {{ number_format($item->stock_before, 4) }} {{ $item->product->primaryUnit->name ?? '' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($item->quantity, 4) }}</div>
                            <div class="text-xs text-gray-500">{{ $item->product->primaryUnit->name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            ₦{{ number_format($item->rate, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                            ₦{{ number_format($item->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            {{ number_format($item->stock_after, 4) }} {{ $item->product->primaryUnit->name ?? '' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($item->batch_number || $item->expiry_date)
                                <div class="text-sm">
                                    @if($item->batch_number)
                                        <div class="text-gray-900"><strong>Batch:</strong> {{ $item->batch_number }}</div>
                                    @endif
                                    @if($item->expiry_date)
                                        <div class="text-gray-500"><strong>Expiry:</strong> {{ $item->expiry_date->format('d M Y') }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">No batch details</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m0 0V6a1 1 0 011-1h2a1 1 0 011 1v1m0 0h2a2 2 0 012 2v4.01"></path>
                                </svg>
                                <p>No items found in this journal entry.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($stockJournal->items->count() > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total:</td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                            ₦{{ number_format($stockJournal->items->sum('amount'), 2) }}
                        </td>
                        <td colspan="2" class="px-6 py-3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Stock Movements (if posted) -->
    @if($stockJournal->status === 'posted' && $stockJournal->stockMovements && $stockJournal->stockMovements->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Generated Stock Movements</h3>
            <p class="text-sm text-gray-600 mt-1">Stock movements created when this journal was posted</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Movement Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stockJournal->stockMovements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $movement->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $movement->product->sku ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $movement->quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $movement->quantity > 0 ? 'In' : 'Out' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            {{ number_format(abs($movement->quantity), 4) }} {{ $movement->product->primaryUnit->name ?? '' }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                            {{ $movement->reference }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $movement->created_at->format('d M Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
