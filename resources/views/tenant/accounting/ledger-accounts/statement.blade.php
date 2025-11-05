@extends('layouts.tenant')

@section('title', $ledgerAccount->name . ' - Account Statement')

@section('page-title', 'Account Statement')

@section('page-description', 'View complete transaction history and balance details for this ledger account')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header with Account Info -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg rounded-lg border border-blue-700 text-white">
        <div class="px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <span class="text-lg font-bold">{{ strtoupper(substr($ledgerAccount->code, 0, 2)) }}</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">{{ $ledgerAccount->name }}</h1>
                            <p class="text-blue-100">Account Code: <span class="font-mono font-semibold">{{ $ledgerAccount->code }}</span></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-6 text-sm text-blue-100 mt-4">
                        <div>
                            <span class="opacity-75">Type:</span>
                            <span class="font-semibold ml-1">{{ ucfirst($ledgerAccount->account_type) }}</span>
                        </div>
                        <div>
                            <span class="opacity-75">Group:</span>
                            <span class="font-semibold ml-1">{{ $ledgerAccount->accountGroup->name ?? 'N/A' }}</span>
                        </div>
                        @if($ledgerAccount->parent)
                        <div>
                            <span class="opacity-75">Parent:</span>
                            <span class="font-semibold ml-1">{{ $ledgerAccount->parent->name }}</span>
                        </div>
                        @endif
                        <div>
                            <span class="opacity-75">Status:</span>
                            <span class="font-semibold ml-1">{{ $ledgerAccount->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-blue-100 mb-1">Current Balance</div>
                    <div class="text-4xl font-bold">
                        ₦{{ number_format(abs($currentBalance), 2) }}
                    </div>
                    <div class="text-lg text-blue-100">{{ $currentBalance >= 0 ? 'Debit' : 'Credit' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Opening Balance</p>
                    <p class="text-2xl font-bold text-blue-600">₦{{ number_format(abs($ledgerAccount->opening_balance), 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $ledgerAccount->opening_balance >= 0 ? 'Dr' : 'Cr' }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Debits</p>
                    <p class="text-2xl font-bold text-green-600">₦{{ number_format($totalDebits, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Incoming</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Credits</p>
                    <p class="text-2xl font-bold text-red-600">₦{{ number_format($totalCredits, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Outgoing</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Net Movement</p>
                    @php $netMovement = $totalDebits - $totalCredits; @endphp
                    <p class="text-2xl font-bold {{ $netMovement >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ₦{{ number_format(abs($netMovement), 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $netMovement >= 0 ? 'Dr' : 'Cr' }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('tenant.accounting.ledger-accounts.show', [$tenant, $ledgerAccount]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Account
                </a>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('tenant.accounting.ledger-accounts.print-ledger', [$tenant, $ledgerAccount]) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </a>
                <a href="{{ route('tenant.accounting.ledger-accounts.export-ledger', [$tenant, $ledgerAccount]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </a>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Transaction History</h3>
                <span class="text-sm text-gray-500">{{ $transactions->count() }} transactions</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($transactions->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Voucher #
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Debit (₦)
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Credit (₦)
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Balance (₦)
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dr/Cr
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Opening Balance Row -->
                        <tr class="bg-blue-50 font-semibold">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">-</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <span class="font-semibold">Opening Balance</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">-</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">-</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">
                                {{ number_format(abs($ledgerAccount->opening_balance), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ledgerAccount->opening_balance >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $ledgerAccount->opening_balance >= 0 ? 'Dr' : 'Cr' }}
                                </span>
                            </td>
                        </tr>

                        <!-- Transaction Rows -->
                        @foreach($transactionsWithBalance as $item)
                            @php
                                $transaction = $item['transaction'];
                                $runningBalance = $item['running_balance'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->voucher->voucher_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('tenant.accounting.vouchers.show', [$tenant, $transaction->voucher]) }}"
                                       class="text-blue-600 hover:text-blue-900 font-medium">
                                        {{ $transaction->voucher->voucher_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $transaction->particulars ?? $transaction->voucher->narration ?? 'Transaction' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    @if($transaction->debit_amount > 0)
                                        <span class="font-semibold text-green-600">{{ number_format($transaction->debit_amount, 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    @if($transaction->credit_amount > 0)
                                        <span class="font-semibold text-red-600">{{ number_format($transaction->credit_amount, 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                                    {{ number_format(abs($runningBalance), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $runningBalance >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $runningBalance >= 0 ? 'Dr' : 'Cr' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach

                        <!-- Closing Balance Summary Row -->
                        <tr class="bg-gray-100 font-bold border-t-2 border-gray-300">
                            <td colspan="3" class="px-6 py-4 text-right text-sm text-gray-900">
                                <strong>TOTALS:</strong>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                                {{ number_format($totalDebits, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">
                                {{ number_format($totalCredits, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600">
                                {{ number_format(abs($currentBalance), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $currentBalance >= 0 ? 'Dr' : 'Cr' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions</h3>
                    <p class="mt-1 text-sm text-gray-500">This account has no transaction history yet.</p>
                    <div class="mt-6">
                        <a href="{{ route('tenant.accounting.vouchers.create', [$tenant, 'account_id' => $ledgerAccount->id]) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add First Transaction
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer Info -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between text-sm text-gray-500">
            <div>
                <strong>{{ $tenant->name ?? 'Ballie Business Management' }}</strong> - Accounting System
            </div>
            <div>
                Generated on: {{ now()->format('l, F j, Y \a\t g:i A') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        /* Hide navigation and actions when printing */
        .no-print {
            display: none !important;
        }

        /* Adjust layout for printing */
        body {
            background: white;
        }

        .container {
            max-width: none;
            margin: 0;
            padding: 0;
        }

        /* Make table print-friendly */
        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        /* Remove shadows and borders for print */
        .shadow-sm,
        .shadow-lg {
            box-shadow: none !important;
        }

        .rounded-lg {
            border-radius: 0 !important;
        }
    }

    /* Custom scrollbar for table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush
