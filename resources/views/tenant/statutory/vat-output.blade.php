@extends('layouts.tenant')

@section('title', 'VAT Output (Sales) - ' . $tenant->name)
@section('page-title', 'VAT Output (Sales)')
@section('page-description', 'VAT collected from sales transactions')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">VAT Output Transactions</h2>
            <p class="text-gray-600 mt-1">VAT collected from customers on sales</p>
        </div>
        <a href="{{ route('tenant.statutory.index', $tenant->slug) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Statutory
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg shadow-sm border border-green-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-green-600">Total VAT Output</p>
                <p class="text-3xl font-bold text-green-900 mt-2">₦{{ number_format($totalVatOutput, 2) }}</p>
                <p class="text-xs text-green-600 mt-1">{{ date('M d, Y', strtotime($startDate)) }} - {{ date('M d, Y', strtotime($endDate)) }}</p>
            </div>
            <div class="p-4 bg-green-200 rounded-full">
                <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voucher Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voucher No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Narration</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">VAT Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->voucher->voucher_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $transaction->voucher->voucherType->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $transaction->voucher->voucher_number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $transaction->narration ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">
                            ₦{{ number_format($transaction->credit_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No VAT output transactions found for this period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
