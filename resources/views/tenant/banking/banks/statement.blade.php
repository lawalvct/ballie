@extends('layouts.tenant')

@section('title', 'Bank Statement - ' . $bank->bank_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bank Statement</h1>
            <p class="mt-2 text-gray-600">{{ $bank->bank_name }} - {{ $bank->masked_account_number }}</p>

            <!-- Breadcrumb -->
            <nav class="flex mt-3" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('tenant.dashboard', $tenant) }}"
                           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-emerald-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-9 9a1 1 0 001.414 1.414L8 5.414V17a1 1 0 102 0V5.414l6.293 6.293a1 1 0 001.414-1.414l-9-9z"></path>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('tenant.banking.banks.index', $tenant) }}"
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-emerald-600 md:ml-2">
                                Bank Accounts
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('tenant.banking.banks.show', [$tenant, $bank->id]) }}"
                               class="ml-1 text-sm font-medium text-gray-700 hover:text-emerald-600 md:ml-2">
                                {{ $bank->bank_name }}
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Statement</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
        <div class="mt-4 lg:mt-0 flex space-x-3">
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 print:hidden">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
            <a href="{{ route('tenant.banking.banks.show', [$tenant, $bank->id]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 print:hidden">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Account
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 print:hidden">
        <div class="p-6">
            <form action="{{ route('tenant.banking.banks.statement', [$tenant, $bank->id]) }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ request('start_date', is_string($startDate) ? $startDate : $startDate->format('Y-m-d')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date"
                           name="end_date"
                           id="end_date"
                           value="{{ request('end_date', is_string($endDate) ? $endDate : $endDate->format('Y-m-d')) }}"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-emerald-600 hover:bg-emerald-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Generate Statement
                </button>
            </form>
        </div>
    </div>

    <!-- Statement Header (Print) -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Bank Account Statement</h2>
                <p class="text-gray-600 mt-2">{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
            </div>

            <!-- Bank Details -->
            <div class="grid grid-cols-2 gap-6 mb-8 pb-6 border-b border-gray-200">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Bank Details</h3>
                    <dl class="space-y-1">
                        <div>
                            <dt class="text-sm font-medium text-gray-900">{{ $bank->bank_name }}</dt>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Account Name: <span class="font-medium text-gray-900">{{ $bank->account_name }}</span></dt>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Account Number: <span class="font-mono font-medium text-gray-900">{{ $bank->account_number }}</span></dt>
                        </div>
                        @if($bank->branch_name)
                        <div>
                            <dt class="text-sm text-gray-600">Branch: <span class="font-medium text-gray-900">{{ $bank->branch_name }}</span></dt>
                        </div>
                        @endif
                    </dl>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Statement Period</h3>
                    <dl class="space-y-1">
                        <div>
                            <dt class="text-sm text-gray-600">From: <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</span></dt>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">To: <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span></dt>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Currency: <span class="font-medium text-gray-900">{{ $bank->currency }}</span></dt>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Generated: <span class="font-medium text-gray-900">{{ now()->format('M d, Y h:i A') }}</span></dt>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <!-- Opening Balance -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-blue-600 uppercase">Opening Balance</p>
                            <p class="mt-2 text-2xl font-bold text-blue-900">₦{{ number_format($openingBalanceAmount ?? 0, 2) }}</p>
                            <p class="mt-1 text-xs text-blue-600">{{ ($openingBalanceAmount ?? 0) >= 0 ? 'DR' : 'CR' }}</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Debits -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-green-600 uppercase">Total Debits</p>
                            <p class="mt-2 text-2xl font-bold text-green-900">₦{{ number_format($totalDebits ?? 0, 2) }}</p>
                            <p class="mt-1 text-xs text-green-600">Money In</p>
                        </div>
                        <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Credits -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-red-600 uppercase">Total Credits</p>
                            <p class="mt-2 text-2xl font-bold text-red-900">₦{{ number_format($totalCredits ?? 0, 2) }}</p>
                            <p class="mt-1 text-xs text-red-600">Money Out</p>
                        </div>
                        <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Closing Balance -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-purple-600 uppercase">Closing Balance</p>
                            <p class="mt-2 text-2xl font-bold text-purple-900">₦{{ number_format($closingBalance ?? 0, 2) }}</p>
                            <p class="mt-1 text-xs text-purple-600">{{ ($closingBalance ?? 0) >= 0 ? 'DR' : 'CR' }}</p>
                        </div>
                        <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="overflow-x-auto">
                @if(isset($transactionsWithBalance) && count($transactionsWithBalance) > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Particulars
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vch Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vch No.
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
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Opening Balance Row -->
                        <tr class="bg-blue-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium" colspan="3">
                                Opening Balance
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                -
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                -
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ ($openingBalanceAmount ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format(abs($openingBalanceAmount ?? 0), 2) }} {{ ($openingBalanceAmount ?? 0) >= 0 ? 'DR' : 'CR' }}
                            </td>
                        </tr>

                        <!-- Transaction Rows -->
                        @foreach($transactionsWithBalance as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $transaction['particulars'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction['voucher_type'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ $transaction['voucher_number'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $transaction['debit'] > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                {{ $transaction['debit'] > 0 ? number_format($transaction['debit'], 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $transaction['credit'] > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                {{ $transaction['credit'] > 0 ? number_format($transaction['credit'], 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium {{ $transaction['running_balance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format(abs($transaction['running_balance']), 2) }} {{ $transaction['running_balance'] >= 0 ? 'DR' : 'CR' }}
                            </td>
                        </tr>
                        @endforeach

                        <!-- Totals Row -->
                        <tr class="bg-gray-100 font-bold">
                            <td class="px-6 py-4 text-sm text-gray-900" colspan="4">
                                Closing Balance
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                                {{ number_format($totalDebits ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">
                                {{ number_format($totalCredits ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right {{ ($closingBalance ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format(abs($closingBalance ?? 0), 2) }} {{ ($closingBalance ?? 0) >= 0 ? 'DR' : 'CR' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No Transactions</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        No transactions found for the selected period.
                    </p>
                </div>
                @endif
            </div>

            <!-- Footer Note -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    This is a system-generated statement. For official bank statements, please contact your bank directly.
                </p>
                <p class="text-xs text-gray-400 text-center mt-2">
                    Generated on {{ now()->format('F d, Y \a\t h:i A') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .print\:hidden {
            display: none !important;
        }

        #content-to-print,
        #content-to-print * {
            visibility: visible;
        }

        #content-to-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

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

        @page {
            margin: 1cm;
        }
    }
</style>
@endpush
@endsection
