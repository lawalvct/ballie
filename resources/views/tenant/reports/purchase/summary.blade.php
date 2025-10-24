@extends('layouts.tenant')

@section('title', 'Purchase Summary Report')
@section('page-title', 'Purchase Summary Report')
@section('page-description')
    <span class="hidden md:inline">Overview of purchase performance and key metrics.</span>
@endsection
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tenant.reports.vendor-purchases', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
               Vendor Purchase Report
            </a>
            <a href="{{ route('tenant.reports.product-purchases', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                </svg>
               Product Purchase Report
            </a>

            <a href="{{ route('tenant.reports.purchases-by-period', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
               Purchases by Period
            </a>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tenant.reports.index', $tenant->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Purchase Register -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden purchase-register-container">
        <!-- Report Header -->
        <div class="bg-purple-700 text-white px-6 py-3 flex items-center justify-between purchase-register-header">
            <h3 class="text-lg font-bold">Purchase Register</h3>
            <div class="text-sm">
                <span class="font-semibold">{{ $tenant->company_name ?? $tenant->name }}</span>
            </div>
            <div class="text-sm">
                {{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }}
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="printPurchaseRegister(event); return false;"
                        type="button"
                        class="inline-flex items-center px-3 py-2 bg-purple-600 hover:bg-purple-500 border border-purple-500 rounded-md text-sm font-medium text-white transition-colors duration-150"
                        title="Print Purchase Register">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Header Section -->
            <div class="mb-6 text-center">
                <h4 class="text-xl font-bold text-gray-900">Purchases</h4>
                <p class="text-sm text-gray-600">{{ $tenant->company_name ?? $tenant->name }}</p>
                <p class="text-sm text-gray-600">{{ date('d-M-Y', strtotime($fromDate)) }} to {{ date('d-M-Y', strtotime($toDate)) }}</p>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto mb-8">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="border-b-2 border-gray-400">
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-700 bg-gray-100 border-r border-gray-300">Particulars</th>
                            <th class="px-4 py-2 text-center text-sm font-bold text-gray-700 bg-gray-100 border-r border-gray-300">Transactions</th>
                            <th class="px-4 py-2 text-right text-sm font-bold text-gray-700 bg-gray-100 border-r border-gray-300">Debit</th>
                            <th class="px-4 py-2 text-right text-sm font-bold text-gray-700 bg-gray-100 border-r border-gray-300">Credit</th>
                            <th class="px-4 py-2 text-right text-sm font-bold text-gray-700 bg-gray-100">Closing Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-cream">
                        @php
                            $runningBalance = 0;
                        @endphp
                        @forelse($purchaseTrend as $trend)
                            @php
                                $runningBalance += $trend->total_purchases;
                            @endphp
                            <tr class="border-b border-gray-200 hover:bg-purple-50">
                                <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200">{{ $trend->period }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-center border-r border-gray-200">{{ number_format($trend->purchase_count) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right border-r border-gray-200">{{ number_format($trend->total_purchases, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right border-r border-gray-200">--</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">{{ number_format($runningBalance, 2) }} Dr</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">No purchase data available for this period</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($purchaseTrend->count() > 0)
                    <tfoot>
                        <tr class="border-t-2 border-gray-400 bg-gray-100 font-bold">
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-300">Grand Total</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-center border-r border-gray-300">{{ number_format($purchaseTrend->sum('purchase_count')) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right border-r border-gray-300">{{ number_format($purchaseTrend->sum('total_purchases'), 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right border-r border-gray-300">--</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($purchaseTrend->sum('total_purchases'), 2) }} Dr</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function printPurchaseRegister(event) {
        if (event) {
            event.preventDefault();
        }

        const purchaseRegister = document.querySelector('.purchase-register-container');
        if (!purchaseRegister) {
            alert('Purchase register not found');
            return;
        }

        const printContent = purchaseRegister.cloneNode(true);
        const printButton = printContent.querySelector('button');
        if (printButton) {
            printButton.remove();
        }

        const printHTML = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Purchase Register - {{ $tenant->company_name ?? $tenant->name }}</title>
                <meta charset="utf-8">
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: Arial, sans-serif; padding: 20px; color: #000; }
                    .purchase-register-header {
                        background-color: #7e22ce !important;
                        color: white !important;
                        padding: 15px 20px;
                        margin-bottom: 20px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        print-color-adjust: exact;
                        -webkit-print-color-adjust: exact;
                    }
                    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    th, td { border: 1px solid #333; padding: 8px 12px; text-align: left; font-size: 12px; }
                    th { background-color: #e5e7eb; font-weight: bold; }
                    .text-center { text-align: center !important; }
                    .text-right { text-align: right !important; }
                    @media print {
                        @page { margin: 0.75in; size: A4 portrait; }
                    }
                </style>
            </head>
            <body>
                ${printContent.innerHTML}
            </body>
            </html>
        `;

        const printWindow = window.open('', '_blank', 'width=800,height=600');
        if (!printWindow) {
            alert('Please allow popups for this website to print');
            return;
        }

        printWindow.document.open();
        printWindow.document.write(printHTML);
        printWindow.document.close();

        printWindow.onload = function() {
            printWindow.focus();
            setTimeout(function() {
                printWindow.print();
            }, 500);
        };
    }
</script>
@endpush
@endsection
