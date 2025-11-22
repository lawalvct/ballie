@extends('layouts.tenant')

@section('title', 'Payment Reports')
@section('page-title', 'Payment Reports')

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <form method="GET" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="block w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="block w-full rounded-lg border-gray-300">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <p class="text-sm text-green-600 font-medium">Total Payments</p>
                <p class="text-2xl font-bold text-green-700">₦{{ number_format($totalPayments, 2) }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <p class="text-sm text-blue-600 font-medium">Payment Count</p>
                <p class="text-2xl font-bold text-blue-700">{{ $paymentCount }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voucher No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer/Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->voucher->voucher_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->voucher->voucher_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->ledgerAccount->name }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-green-600">₦{{ number_format($payment->credit_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No payments found for the selected period</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
