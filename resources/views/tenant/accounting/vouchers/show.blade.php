@extends('layouts.tenant')

@section('title', 'Voucher ' . $voucher->voucher_number . ' - ' . $tenant->name)

@section('page-title', 'Voucher Details')

@section('page-description', 'View detailed information about this voucher including entries, audit trail, and related information.')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">
                    {{ $voucher->voucher_number }}
                </h1>
                @if($voucher->status === 'draft')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 w-fit">
                        <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Draft
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 w-fit">
                        <svg class="w-2 h-2 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3"/>
                        </svg>
                        Posted
                    </span>
                @endif
            </div>
            <p class="mt-2 text-sm text-gray-500">
                {{ $voucher->voucherType->name }} • Created {{ $voucher->created_at->format('M d, Y \a\t g:i A') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>

            @if($voucher->status === 'draft')
                <a href="{{ route('tenant.accounting.vouchers.edit', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
                   class="inline-flex items-center px-3 py-2 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            @endif

            <a href="{{ route('tenant.accounting.vouchers.print', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
               class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50" target="_blank">
                <i class="fas fa-print mr-2"></i> Print
            </a>

            <a href="{{ route('tenant.accounting.vouchers.pdf', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}"
               class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100" target="_blank">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </a>

            @if($voucher->status === 'draft')
                <form method="POST" action="{{ route('tenant.accounting.vouchers.post', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100"
                            onclick="return confirm('Are you sure you want to post this voucher? This action cannot be undone.')">
                        <i class="fas fa-check mr-2"></i> Post
                    </button>
                </form>

                <form method="POST" action="{{ route('tenant.accounting.vouchers.destroy', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100"
                            onclick="return confirm('Are you sure you want to delete this voucher? This action cannot be undone.')">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('tenant.accounting.vouchers.unpost', ['tenant' => $tenant->slug, 'voucher' => $voucher->id]) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 border border-orange-300 rounded-lg text-sm font-medium text-orange-700 bg-orange-50 hover:bg-orange-100"
                            onclick="return confirm('Are you sure you want to unpost this voucher?')">
                        <i class="fas fa-undo mr-2"></i> Unpost
                    </button>
                </form>
            @endif
        </div>

    </div>

    <!-- Voucher Summary Card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $voucher->voucherType->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $voucher->voucherType->code }}</p>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900 mb-1">₦{{ number_format($voucher->total_amount, 2) }}</div>
                <p class="text-sm text-gray-600">{{ $voucher->voucher_date->format('M d, Y') }}</p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-1 gap-4 lg:text-right">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Reference</p>
                    <p class="text-sm font-medium text-gray-900">{{ $voucher->reference_number ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Entries</p>
                    <p class="text-sm font-medium text-gray-900">{{ $voucher->entries->count() }} items</p>
                </div>
            </div>
        </div>
        @if($voucher->narration)
            <div class="mt-4 pt-4 border-t border-blue-200">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Narration</p>
                <p class="text-sm text-gray-700">{{ $voucher->narration }}</p>
            </div>
        @endif
    </div>

    <!-- Voucher Entries -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Voucher Entries</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $voucher->entries->count() }} {{ Str::plural('entry', $voucher->entries->count()) }}
                </span>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="block sm:hidden">
            @foreach($voucher->entries as $entry)
                <div class="px-4 py-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @php
                                $colors = [
                                    'Assets' => 'blue',
                                    'Liabilities' => 'red',
                                    'Equity' => 'green',
                                    'Income' => 'purple',
                                    'Expenses' => 'orange'
                                ];
                                $color = $colors[$entry->ledgerAccount->accountGroup->name] ?? 'gray';
                            @endphp
                            <div class="h-10 w-10 rounded-lg bg-{{ $color }}-100 flex items-center justify-center">
                                <span class="text-sm font-medium text-{{ $color }}-600">
                                    {{ substr($entry->ledgerAccount->accountGroup->name, 0, 1) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">
                                {{ $entry->ledgerAccount->name }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $entry->ledgerAccount->accountGroup->name }}
                            </div>
                            @if($entry->particulars)
                                <div class="text-xs text-gray-600 mt-1">
                                    {{ $entry->particulars }}
                                </div>
                            @endif
                            @if($entry->document_path)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $entry->document_path) }}" target="_blank" class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                        </svg>
                                        View Document
                                    </a>
                                </div>
                            @endif
                            <div class="flex justify-between mt-2">
                                <div>
                                    <span class="text-xs text-gray-500">Debit:</span>
                                    <span class="text-sm font-medium {{ $entry->debit_amount > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $entry->debit_amount > 0 ? '₦' . number_format($entry->debit_amount, 2) : '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500">Credit:</span>
                                    <span class="text-sm font-medium {{ $entry->credit_amount > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                        {{ $entry->credit_amount > 0 ? '₦' . number_format($entry->credit_amount, 2) : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between text-sm font-medium text-gray-900">
                    <span>Total Debits:</span>
                    <span>₦{{ number_format($voucher->entries->sum('debit_amount'), 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-medium text-gray-900 mt-1">
                    <span>Total Credits:</span>
                    <span>₦{{ number_format($voucher->entries->sum('credit_amount'), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Desktop View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ledger Account
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Particulars
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Document
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Debit Amount
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Credit Amount
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($voucher->entries as $entry)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @php
                                            $colors = [
                                                'Assets' => 'blue',
                                                'Liabilities' => 'red',
                                                'Equity' => 'green',
                                                'Income' => 'purple',
                                                'Expenses' => 'orange'
                                            ];
                                            $color = $colors[$entry->ledgerAccount->accountGroup->name] ?? 'gray';
                                        @endphp
                                        <div class="h-10 w-10 rounded-lg bg-{{ $color }}-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-{{ $color }}-600">
                                                {{ substr($entry->ledgerAccount->accountGroup->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $entry->ledgerAccount->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $entry->ledgerAccount->accountGroup->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $entry->particulars ?: 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($entry->document_path)
                                    <a href="{{ asset('storage/' . $entry->document_path) }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                        </svg>
                                        View
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                @if($entry->debit_amount > 0)
                                    <span class="font-medium text-green-600">₦{{ number_format($entry->debit_amount, 2) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                @if($entry->credit_amount > 0)
                                    <span class="font-medium text-red-600">₦{{ number_format($entry->credit_amount, 2) }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-3 text-sm font-medium text-gray-900">
                            Total
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-green-600">
                            ₦{{ number_format($voucher->entries->sum('debit_amount'), 2) }}
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-red-600">
                            ₦{{ number_format($voucher->entries->sum('credit_amount'), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Audit Trail -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Audit Trail</h3>
        </div>
        <div class="p-4 sm:p-6">
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    <li>
                        <div class="relative pb-8">
                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-4 ring-white shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">
                                                Voucher created by <span class="font-medium text-gray-900">{{ $voucher->createdBy->name }}</span>
                                            </p>
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1 sm:mt-0">
                                            {{ $voucher->created_at->format('M d, Y \a\t g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    @if($voucher->updated_at != $voucher->created_at)
                        <li>
                            <div class="relative pb-8">
                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-10 w-10 rounded-full bg-yellow-500 flex items-center justify-center ring-4 ring-white shadow-sm">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    Voucher updated by <span class="font-medium text-gray-900">{{ $voucher->updatedBy->name ?? 'System' }}</span>
                                                </p>
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1 sm:mt-0">
                                                {{ $voucher->updated_at->format('M d, Y \a\t g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endif

                    @if($voucher->status === 'posted')
                        <li>
                            <div class="relative">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-4 ring-white shadow-sm">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">
                                                    Voucher posted by <span class="font-medium text-gray-900">{{ $voucher->postedBy->name ?? 'System' }}</span>
                                                </p>
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1 sm:mt-0">
                                                {{ $voucher->posted_at?->format('M d, Y \a\t g:i A') ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Related Information -->
    @if($voucher->reference_number || $voucher->voucherType->affects_inventory || $voucher->voucherType->affects_cashbank)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Related Information</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($voucher->reference_number)
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-900">Reference Document</h4>
                                    <p class="text-sm text-blue-700 font-mono">{{ $voucher->reference_number }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($voucher->voucherType->affects_inventory)
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-purple-900">Inventory Impact</h4>
                                    <p class="text-sm text-purple-700">Affects inventory levels</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($voucher->voucherType->affects_cashbank)
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-green-900">Cash/Bank Impact</h4>
                                    <p class="text-sm text-green-700">Affects cash or bank accounts</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
