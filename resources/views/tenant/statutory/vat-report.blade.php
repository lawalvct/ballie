@extends('layouts.tenant')

@section('title', 'VAT Report - ' . $tenant->name)
@section('page-title', 'VAT Report')
@section('page-description', 'Comprehensive VAT return report')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #printable-report, #printable-report * { visibility: visible; }
        #printable-report { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between no-print">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">VAT Return Report</h2>
            <p class="text-gray-600 mt-1">{{ date('M d, Y', strtotime($startDate)) }} - {{ date('M d, Y', strtotime($endDate)) }}</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Print Report
            </button>
            <a href="{{ route('tenant.statutory.index', $tenant->slug) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Statutory
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 no-print">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- VAT Summary Report -->
    <div id="printable-report" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="text-center mb-8">
            <h3 class="text-2xl font-bold text-gray-900">{{ $tenant->name }}</h3>
            <p class="text-gray-600 mt-1">VAT Return Report</p>
            <p class="text-sm text-gray-500 mt-1">Period: {{ date('M d, Y', strtotime($startDate)) }} - {{ date('M d, Y', strtotime($endDate)) }}</p>
        </div>

        <div class="space-y-6">
            <!-- VAT Output Section -->
            <div class="border-b border-gray-200 pb-4">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-lg font-semibold text-gray-900">VAT Output (Sales)</h4>
                    <span class="text-lg font-bold text-green-600">₦{{ number_format($vatOutput, 2) }}</span>
                </div>
                <p class="text-sm text-gray-600">VAT collected from customers on sales transactions</p>
            </div>

            <!-- VAT Input Section -->
            <div class="border-b border-gray-200 pb-4">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-lg font-semibold text-gray-900">VAT Input (Purchases)</h4>
                    <span class="text-lg font-bold text-blue-600">₦{{ number_format($vatInput, 2) }}</span>
                </div>
                <p class="text-sm text-gray-600">VAT paid to suppliers on purchases and expenses</p>
            </div>

            <!-- Net VAT Calculation -->
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-xl font-bold text-gray-900">Net VAT Payable</h4>
                    <span class="text-2xl font-bold {{ $netVatPayable >= 0 ? 'text-amber-600' : 'text-red-600' }}">
                        ₦{{ number_format(abs($netVatPayable), 2) }}
                    </span>
                </div>
                <div class="text-sm text-gray-600">
                    <p class="mb-2">Calculation: VAT Output - VAT Input</p>
                    <p class="mb-2">= ₦{{ number_format($vatOutput, 2) }} - ₦{{ number_format($vatInput, 2) }}</p>
                    <p class="font-semibold {{ $netVatPayable >= 0 ? 'text-amber-700' : 'text-red-700' }}">
                        = ₦{{ number_format($netVatPayable, 2) }}
                    </p>
                </div>
                <div class="mt-4 p-4 {{ $netVatPayable >= 0 ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200' }} rounded-lg">
                    <p class="text-sm font-semibold {{ $netVatPayable >= 0 ? 'text-amber-800' : 'text-red-800' }}">
                        @if($netVatPayable >= 0)
                            ✓ Amount to be paid to tax authority
                        @else
                            ✓ VAT Credit/Refund - Amount claimable or carried forward
                        @endif
                    </p>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="mt-8">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount (₦)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">VAT Output (Sales)</td>
                            <td class="px-6 py-4 text-sm text-right text-green-600 font-semibold">{{ number_format($vatOutput, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">VAT Input (Purchases)</td>
                            <td class="px-6 py-4 text-sm text-right text-blue-600 font-semibold">({{ number_format($vatInput, 2) }})</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">Net VAT {{ $netVatPayable >= 0 ? 'Payable' : 'Credit' }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold {{ $netVatPayable >= 0 ? 'text-amber-600' : 'text-red-600' }}">
                                {{ number_format(abs($netVatPayable), 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center">
                This report is generated automatically from your accounting records. Please verify all amounts before filing with tax authorities.
            </p>
            <p class="text-xs text-gray-500 text-center mt-2">
                Generated on {{ now()->format('M d, Y \a\t h:i A') }}
            </p>
        </div>
    </div>
</div>
@endsection
