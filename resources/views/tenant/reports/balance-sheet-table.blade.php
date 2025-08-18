@extends('layouts.tenant')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Balance Sheet (Standard Table)</h1>
            <p class="text-sm text-gray-500">As of {{ \Carbon\Carbon::parse($asOfDate ?? now())->format('F d, Y') }}</p>
        </div>
        <a href="{{ route('tenant.reports.balance-sheet', ['tenant' => $tenant->slug, 'as_of_date' => $asOfDate]) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Modern View</a>
    </div>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance (₦)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Assets -->
                <tr class="bg-emerald-50">
                    <td colspan="3" class="px-6 py-2 font-bold text-emerald-700">Assets</td>
                </tr>
                @foreach($assets as $item)
                <tr>
                    <td class="px-6 py-2">{{ $item['account']->name }}</td>
                    <td class="px-6 py-2 text-right">Asset</td>
                    <td class="px-6 py-2 text-right">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="font-bold bg-emerald-100">
                    <td class="px-6 py-2 text-right" colspan="2">Total Assets</td>
                    <td class="px-6 py-2 text-right text-emerald-800">{{ number_format($totalAssets, 2) }}</td>
                </tr>
                <!-- Liabilities -->
                <tr class="bg-red-50">
                    <td colspan="3" class="px-6 py-2 font-bold text-red-700">Liabilities</td>
                </tr>
                @foreach($liabilities as $item)
                <tr>
                    <td class="px-6 py-2">{{ $item['account']->name }}</td>
                    <td class="px-6 py-2 text-right">Liability</td>
                    <td class="px-6 py-2 text-right">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="font-bold bg-red-100">
                    <td class="px-6 py-2 text-right" colspan="2">Total Liabilities</td>
                    <td class="px-6 py-2 text-right text-red-800">{{ number_format($totalLiabilities, 2) }}</td>
                </tr>
                <!-- Equity -->
                <tr class="bg-purple-50">
                    <td colspan="3" class="px-6 py-2 font-bold text-purple-700">Equity</td>
                </tr>
                @foreach($equity as $item)
                <tr>
                    <td class="px-6 py-2">{{ $item['account']->name }}</td>
                    <td class="px-6 py-2 text-right">Equity</td>
                    <td class="px-6 py-2 text-right">{{ number_format($item['balance'], 2) }}</td>
                </tr>
                @endforeach
                @if(isset($retainedEarnings) && abs($retainedEarnings) >= 0.01)
                <tr>
                    <td class="px-6 py-2">Retained Earnings</td>
                    <td class="px-6 py-2 text-right">Equity</td>
                    <td class="px-6 py-2 text-right">{{ number_format($retainedEarnings, 2) }}</td>
                </tr>
                @endif
                <tr class="font-bold bg-purple-100">
                    <td class="px-6 py-2 text-right" colspan="2">Total Equity</td>
                    <td class="px-6 py-2 text-right text-purple-800">{{ number_format($totalEquity, 2) }}</td>
                </tr>
                <!-- Totals -->
                <tr class="bg-gray-50 font-bold">
                    <td class="px-6 py-2 text-right" colspan="2">Total Liabilities & Equity</td>
                    <td class="px-6 py-2 text-right">{{ number_format($totalLiabilities + $totalEquity, 2) }}</td>
                </tr>
            </tbody>
        </table>
        <div class="p-4">
            @if($balanceCheck)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Ballie</span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Out of Balance (₦{{ number_format(abs($totalAssets - ($totalLiabilities + $totalEquity)), 2) }})</span>
            @endif
        </div>
    </div>
</div>
@endsection
