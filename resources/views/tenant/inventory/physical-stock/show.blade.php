@extends('layouts.tenant')

@section('title', 'Physical Stock Voucher - ' . $voucher->voucher_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $voucher->voucher_number }}</h1>
                    <p class="text-gray-600">Physical Stock Voucher Details</p>
                </div>
            </div>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Physical Stock
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500">{{ $voucher->voucher_number }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="mt-4 lg:mt-0 flex items-center space-x-3">
            <!-- Status Badge -->
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                @if($voucher->status === 'draft') bg-gray-100 text-gray-800
                @elseif($voucher->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($voucher->status === 'approved') bg-green-100 text-green-800
                @else bg-red-100 text-red-800
                @endif">
                @if($voucher->status === 'draft')
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                @elseif($voucher->status === 'pending')
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @elseif($voucher->status === 'approved')
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @endif
                {{ $voucher->status_display }}
            </span>
            
            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                @if($voucher->canEdit())
                    <a href="{{ route('tenant.inventory.physical-stock.edit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                @endif

                @if($voucher->status === 'draft')
                    <form method="POST" action="{{ route('tenant.inventory.physical-stock.submit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Submit for Approval
                        </button>
                    </form>
                @endif

                @if($voucher->canApprove())
                    <form method="POST" action="{{ route('tenant.inventory.physical-stock.approve', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to approve this voucher? This will create stock movements.')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approve
                        </button>
                    </form>
                @endif

                <a href="{{ route('tenant.inventory.physical-stock.index', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Voucher Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Voucher Information</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Voucher Number</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $voucher->voucher_number }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Voucher Date</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $voucher->voucher_date->format('M d, Y') }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Reference</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $voucher->reference ?? 'N/A' }}</dd>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Created By</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $voucher->creator->name ?? 'System' }}</dd>
                        <dd class="text-sm text-gray-500">{{ $voucher->created_at->format('M d, Y g:i A') }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Total Items</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $voucher->entries->count() }}</dd>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Adjustment Type</dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            @if($voucher->entries->sum(function($entry) { return $entry->physical_quantity - $entry->book_quantity; }) > 0)
                                <span class="text-green-600">Excess</span>
                            @elseif($voucher->entries->sum(function($entry) { return $entry->physical_quantity - $entry->book_quantity; }) < 0)
                                <span class="text-red-600">Shortage</span>
                            @else
                                <span class="text-gray-600">Balanced</span>
                            @endif
                        </dd>
                    </div>
                </div>

                @if($voucher->remarks)
                    <div class="mt-6">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <dt class="text-sm font-medium text-blue-700 mb-2">Remarks</dt>
                            <dd class="text-gray-900">{{ $voucher->remarks }}</dd>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Product Entries -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center mb-6">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Product Entries</h3>
                </div>

                @if($voucher->entries->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Book Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Physical Qty</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($voucher->entries as $entry)
                                    @php
                                        $difference = $entry->physical_quantity - $entry->book_quantity;
                                        $value = $difference * $entry->rate;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $entry->product->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $entry->product->sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                            {{ number_format($entry->book_quantity, 4) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                            {{ number_format($entry->physical_quantity, 4) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($difference > 0) bg-green-100 text-green-800
                                                @elseif($difference < 0) bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                @if($difference > 0)
                                                    +{{ number_format($difference, 4) }}
                                                @else
                                                    {{ number_format($difference, 4) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                            ₦{{ number_format($entry->rate, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <span class="font-medium
                                                @if($value > 0) text-green-600
                                                @elseif($value < 0) text-red-600
                                                @else text-gray-600
                                                @endif">
                                                ₦{{ number_format($value, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($entry->batch_number)
                                                <div class="text-xs">Batch: {{ $entry->batch_number }}</div>
                                            @endif
                                            @if($entry->expiry_date)
                                                <div class="text-xs">Exp: {{ $entry->expiry_date->format('M d, Y') }}</div>
                                            @endif
                                            @if($entry->location)
                                                <div class="text-xs">Location: {{ $entry->location }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Row -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $voucher->entries->count() }}</div>
                                    <div class="text-sm text-blue-700 font-medium">Total Items</div>
                                </div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">
                                        ₦{{ number_format($voucher->entries->where('physical_quantity', '>', 'book_quantity')->sum(function($entry) { return ($entry->physical_quantity - $entry->book_quantity) * $entry->rate; }), 2) }}
                                    </div>
                                    <div class="text-sm text-green-700 font-medium">Total Excess Value</div>
                                </div>
                            </div>
                            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">
                                        ₦{{ number_format(abs($voucher->entries->where('physical_quantity', '<', 'book_quantity')->sum(function($entry) { return ($entry->physical_quantity - $entry->book_quantity) * $entry->rate; })), 2) }}
                                    </div>
                                    <div class="text-sm text-red-700 font-medium">Total Shortage Value</div>
                                </div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                                <div class="text-center">
                                    @php
                                        $netAdjustment = $voucher->entries->sum(function($entry) { return ($entry->physical_quantity - $entry->book_quantity) * $entry->rate; });
                                    @endphp
                                    <div class="text-2xl font-bold
                                        @if($netAdjustment > 0) text-green-600
                                        @elseif($netAdjustment < 0) text-red-600
                                        @else text-gray-600
                                        @endif">
                                        ₦{{ number_format($netAdjustment, 2) }}
                                    </div>
                                    <div class="text-sm text-purple-700 font-medium">Net Adjustment</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No entries found</h3>
                        <p class="mt-1 text-sm text-gray-500">No entries found for this voucher.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status & Actions -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Status & Actions</h3>
                
                <div class="mb-6 text-center">
                    <div class="text-sm font-medium text-gray-500 mb-2">Current Status</div>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        @if($voucher->status === 'draft') bg-gray-100 text-gray-800
                        @elseif($voucher->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($voucher->status === 'approved') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $voucher->status_display }}
                    </span>
                </div>

                @if($voucher->status === 'draft')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    This voucher is in draft status. You can edit it or submit for approval.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($voucher->status === 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    This voucher is pending approval.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($voucher->status === 'approved')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    This voucher has been approved and stock movements have been created.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($voucher->status === 'cancelled')
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    This voucher has been cancelled.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-3">
                    @if($voucher->canEdit())
                        <a href="{{ route('tenant.inventory.physical-stock.edit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Voucher
                        </a>
                    @endif

                    @if($voucher->status === 'draft')
                        <form method="POST" action="{{ route('tenant.inventory.physical-stock.submit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Submit for Approval
                            </button>
                        </form>
                    @endif

                    @if($voucher->canApprove())
                        <form method="POST" action="{{ route('tenant.inventory.physical-stock.approve', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to approve this voucher? This will create stock movements.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Approve Voucher
                            </button>
                        </form>
                    @endif

                    @if(in_array($voucher->status, ['draft', 'pending']))
                        <form method="POST" action="{{ route('tenant.inventory.physical-stock.cancel', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to cancel this voucher?')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Cancel Voucher
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Audit Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Audit Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $voucher->creator->name ?? 'System' }}</dd>
                        <dd class="text-xs text-gray-500">{{ $voucher->created_at->format('M d, Y g:i A') }}</dd>
                    </div>

                    @if($voucher->updater && $voucher->updated_at != $voucher->created_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $voucher->updater->name }}</dd>
                            <dd class="text-xs text-gray-500">{{ $voucher->updated_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    @endif

                    @if($voucher->approver)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $voucher->approver->name }}</dd>
                            <dd class="text-xs text-gray-500">{{ $voucher->approved_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    @endif
                </div>

                @if($voucher->status === 'approved')
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="text-center">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Stock movements created
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection