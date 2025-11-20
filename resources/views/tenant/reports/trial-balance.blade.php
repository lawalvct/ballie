@extends('layouts.tenant')



@section('page-title', 'Trial Balance')
@section('page-description')
 @if(isset($fromDate) && isset($toDate))
                    Statement of all ledger account balances from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
                @else
                    Statement of all ledger account balances as of {{ \Carbon\Carbon::parse($asOfDate ?? now())->format('F d, Y') }}
                @endif

                @endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form method="GET" class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date"
                       name="from_date"
                       id="from_date"
                       value="{{ $fromDate ?? now()->startOfMonth()->format('Y-m-d') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date"
                       name="to_date"
                       id="to_date"
                       value="{{ $toDate ?? now()->format('Y-m-d') }}"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Update
            </button>
        </form>
        
        <div class="flex flex-wrap items-center gap-2">
            @if(count($trialBalanceData ?? []) > 0)
                <button onclick="exportToExcel()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </button>
                <a href="{{ route('tenant.accounting.trial-balance', array_merge(['tenant' => $tenant->slug], request()->all(), ['download' => 'pdf'])) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    Download PDF
                </a>
            @endif
            <button onclick="printReport()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Trial Balance Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Trial Balance</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(isset($fromDate) && isset($toDate))
                    All active accounts with non-zero balances from {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
                @else
                    All active accounts with non-zero balances as of {{ \Carbon\Carbon::parse($asOfDate ?? now())->format('F d, Y') }}
                @endif
            </p>
        </div>

        @if(count($trialBalanceData) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account Code
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Account Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Opening Balance
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
                        @foreach($trialBalanceData as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $data['account']->code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('tenant.accounting.ledger-accounts.show', ['tenant' => $tenant->slug, 'ledgerAccount' => $data['account']->id]) }}"
                                           class="text-primary-600 hover:text-primary-900 hover:underline">
                                            {{ $data['account']->name }}
                                        </a>
                                    </div>
                                    @if($data['account']->accountGroup)
                                        <div class="text-sm text-gray-500">{{ $data['account']->accountGroup->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($data['account']->account_type == 'asset') bg-blue-100 text-blue-800
                                        @elseif($data['account']->account_type == 'liability') bg-red-100 text-red-800
                                        @elseif($data['account']->account_type == 'equity') bg-purple-100 text-purple-800
                                        @elseif($data['account']->account_type == 'income') bg-green-100 text-green-800
                                        @elseif($data['account']->account_type == 'expense') bg-orange-100 text-orange-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($data['account']->account_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-mono">
                                    {{ number_format($data['opening_balance'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono">
                                    @if($data['debit_amount'] > 0)
                                        <span class="text-blue-600 font-medium">{{ number_format($data['debit_amount'], 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-mono">
                                    @if($data['credit_amount'] > 0)
                                        <span class="text-red-600 font-medium">{{ number_format($data['credit_amount'], 2) }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <!-- Totals Row -->
                    <tfoot class="bg-gray-50">
                        <tr class="border-t-2 border-gray-300">
                            <td colspan="4" class="px-6 py-4 text-sm font-bold text-gray-900">
                                TOTAL
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600 text-right font-mono border-t-2 border-gray-300">
                                {{ number_format($totalDebits, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600 text-right font-mono border-t-2 border-gray-300">
                                {{ number_format($totalCredits, 2) }}
                            </td>
                        </tr>
                        <!-- Balance Check Row -->
                        <tr class="bg-gray-100">
                            <td colspan="4" class="px-6 py-2 text-sm font-medium text-gray-700">
                                Balance Check:
                            </td>
                            <td colspan="2" class="px-6 py-2 text-sm font-medium text-right">
                                @if(abs($totalDebits - $totalCredits) < 0.01)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Ballie
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Out of Balance ({{ number_format(abs($totalDebits - $totalCredits), 2) }})
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Account Balances</h3>
                <p class="mt-1 text-sm text-gray-500">
                    There are no accounts with balances as of the selected date.
                </p>
                <div class="mt-6">
                    <a href="{{ route('tenant.accounting.vouchers.create', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Voucher
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Summary Cards -->
    @if(count($trialBalanceData) > 0)
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 overflow-hidden shadow-sm rounded-lg border border-blue-200">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-blue-600">Total Debits</p>
                            <p class="text-xl font-bold text-blue-900">₦{{ number_format($totalDebits, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-50 to-red-100 overflow-hidden shadow-sm rounded-lg border border-red-200">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-red-600">Total Credits</p>
                            <p class="text-xl font-bold text-red-900">₦{{ number_format($totalCredits, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r {{ abs($totalDebits - $totalCredits) < 0.01 ? 'from-green-50 to-green-100 border-green-200' : 'from-yellow-50 to-yellow-100 border-yellow-200' }} overflow-hidden shadow-sm rounded-lg border">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 {{ abs($totalDebits - $totalCredits) < 0.01 ? 'bg-green-500' : 'bg-yellow-500' }} rounded-lg flex items-center justify-center">
                                @if(abs($totalDebits - $totalCredits) < 0.01)
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium {{ abs($totalDebits - $totalCredits) < 0.01 ? 'text-green-600' : 'text-yellow-600' }}">Balance Status</p>
                            <p class="text-lg font-bold {{ abs($totalDebits - $totalCredits) < 0.01 ? 'text-green-900' : 'text-yellow-900' }}">
                                {{ abs($totalDebits - $totalCredits) < 0.01 ? 'Balanced' : '₦' . number_format(abs($totalDebits - $totalCredits), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-purple-100 overflow-hidden shadow-sm rounded-lg border border-purple-200">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-purple-600">Total Accounts</p>
                            <p class="text-xl font-bold text-purple-900">{{ count($trialBalanceData) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Account Type Breakdown -->
    @if(count($trialBalanceData) > 0)
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Account Type Breakdown</h3>
                <p class="mt-1 text-sm text-gray-500">Summary of balances by account type</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    @php
                        $typeBreakdown = [];
                        foreach($trialBalanceData as $data) {
                            $type = $data['account']->account_type;
                            if (!isset($typeBreakdown[$type])) {
                                $typeBreakdown[$type] = ['count' => 0, 'total_debit' => 0, 'total_credit' => 0];
                            }
                            $typeBreakdown[$type]['count']++;
                            $typeBreakdown[$type]['total_debit'] += $data['debit_amount'];
                            $typeBreakdown[$type]['total_credit'] += $data['credit_amount'];
                        }
                    @endphp

                    @foreach(['asset', 'liability', 'equity', 'income', 'expense'] as $type)
                        @if(isset($typeBreakdown[$type]))
                            <div class="text-center p-4 border-2 rounded-lg transition-all hover:shadow-md
                                @if($type === 'asset') border-blue-200 bg-blue-50 hover:bg-blue-100
                                @elseif($type === 'liability') border-red-200 bg-red-50 hover:bg-red-100
                                @elseif($type === 'equity') border-purple-200 bg-purple-50 hover:bg-purple-100
                                @elseif($type === 'income') border-green-200 bg-green-50 hover:bg-green-100
                                @elseif($type === 'expense') border-orange-200 bg-orange-50 hover:bg-orange-100
                                @endif">
                                <div class="text-sm font-bold uppercase tracking-wide
                                    @if($type === 'asset') text-blue-700
                                    @elseif($type === 'liability') text-red-700
                                    @elseif($type === 'equity') text-purple-700
                                    @elseif($type === 'income') text-green-700
                                    @elseif($type === 'expense') text-orange-700
                                    @endif">{{ $type }}</div>
                                <div class="mt-2 text-2xl font-bold text-gray-900">{{ $typeBreakdown[$type]['count'] }}</div>
                                <div class="text-sm font-medium mt-1
                                    @if($type === 'asset' || $type === 'expense') text-blue-600
                                    @else text-red-600
                                    @endif">
                                    @if($type === 'asset' || $type === 'expense')
                                        ₦{{ number_format($typeBreakdown[$type]['total_debit'], 2) }}
                                    @else
                                        ₦{{ number_format($typeBreakdown[$type]['total_credit'], 2) }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            font-size: 12px;
            color: black !important;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .print-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .print-date {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        table {
            width: 100% !important;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #000 !important;
            padding: 8px !important;
            text-align: left;
        }
        
        th {
            background-color: #f0f0f0 !important;
            font-weight: bold;
        }
        
        .text-right {
            text-align: right !important;
        }
        
        .font-bold {
            font-weight: bold !important;
        }
        
        .bg-gray-50, .bg-gray-100 {
            background-color: #f9f9f9 !important;
        }
        
        .text-blue-600, .text-red-600 {
            color: black !important;
        }
        
        .rounded-full {
            border-radius: 0 !important;
            padding: 2px 6px !important;
            border: 1px solid #000 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function exportToExcel() {
    // Get table data
    const table = document.querySelector('.min-w-full');
    let csvContent = "data:text/csv;charset=utf-8,";

    // Add header row
    csvContent += "Account Code,Account Name,Account Type,Opening Balance,Debit Amount,Credit Amount
";

    // Add data rows
    @foreach($trialBalanceData as $data)
        csvContent += "{{ $data['account']->code }}," +
                     "{{ addslashes($data['account']->name) }}," +
                     "{{ ucfirst($data['account']->account_type) }}," +
                     "{{ number_format($data['opening_balance'], 2) }}," +
                     "{{ $data['debit_amount'] > 0 ? number_format($data['debit_amount'], 2) : '0.00' }}," +
                     "{{ $data['credit_amount'] > 0 ? number_format($data['credit_amount'], 2) : '0.00' }}
";
    @endforeach

    // Add totals row
    csvContent += ",,TOTAL,,{{ number_format($totalDebits, 2) }},{{ number_format($totalCredits, 2) }}
";

    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);

    // Generate filename based on date range
    let filename = "trial_balance";
    @if(isset($fromDate) && isset($toDate))
        filename += "_{{ $fromDate }}_to_{{ $toDate }}";
    @else
        filename += "_{{ $asOfDate ?? now()->format('Y-m-d') }}";
    @endif
    filename += ".csv";

    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printReport() {
    // Create print header
    const printHeader = document.createElement('div');
    printHeader.className = 'print-header';
    printHeader.innerHTML = `
        <div class="print-title">{{ $tenant->name ?? 'Company Name' }}</div>
        <div class="print-title">TRIAL BALANCE</div>
        <div class="print-date">
            @if(isset($fromDate) && isset($toDate))
                From {{ \Carbon\Carbon::parse($fromDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('F d, Y') }}
            @else
                As of {{ \Carbon\Carbon::parse($asOfDate ?? now())->format('F d, Y') }}
            @endif
        </div>
    `;
    
    // Insert header before the table
    const tableContainer = document.querySelector('.bg-white.shadow-sm.rounded-lg');
    if (tableContainer) {
        tableContainer.insertBefore(printHeader, tableContainer.firstChild);
    }
    
    // Print
    window.print();
    
    // Remove header after printing
    setTimeout(() => {
        if (printHeader.parentNode) {
            printHeader.parentNode.removeChild(printHeader);
        }
    }, 1000);
}

// Add date range validation
document.getElementById('from_date').addEventListener('change', function() {
    const fromDate = new Date(this.value);
    const toDate = new Date(document.getElementById('to_date').value);
    const today = new Date();

    if (fromDate > today) {
        alert('From date cannot be in the future');
        this.value = today.toISOString().split('T')[0];
    }

    if (toDate && fromDate > toDate) {
        alert('From date cannot be later than To date');
        this.value = '';
    }
});

document.getElementById('to_date').addEventListener('change', function() {
    const toDate = new Date(this.value);
    const fromDate = new Date(document.getElementById('from_date').value);
    const today = new Date();

    if (toDate > today) {
        alert('To date cannot be in the future');
        this.value = today.toISOString().split('T')[0];
    }

    if (fromDate && toDate < fromDate) {
        alert('To date cannot be earlier than From date');
        this.value = '';
    }
});

// Hide summary cards and breakdown when printing
window.addEventListener('beforeprint', function() {
    const summaryCards = document.querySelectorAll('.grid.grid-cols-2.lg\\:grid-cols-4, .bg-white.shadow-sm.rounded-lg:not(:first-of-type)');
    summaryCards.forEach(card => {
        if (!card.querySelector('table')) {
            card.classList.add('no-print');
        }
    });
});

window.addEventListener('afterprint', function() {
    const hiddenElements = document.querySelectorAll('.no-print');
    hiddenElements.forEach(element => {
        element.classList.remove('no-print');
    });
});
</script>
@endpush
@endsection
