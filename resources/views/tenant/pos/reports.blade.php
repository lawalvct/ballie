@extends('layouts.tenant')

@section('title', 'POS Reports')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">POS Reports</h1>
        <p class="text-gray-600">Point of sale analytics and insights</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('tenant.pos.reports.daily-sales', $tenant->slug) }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Daily Sales Report</h3>
            <p class="text-gray-600 text-sm">View daily sales performance and trends</p>
        </a>

        <a href="{{ route('tenant.pos.reports.top-products', $tenant->slug) }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Top Products</h3>
            <p class="text-gray-600 text-sm">Best selling products analysis</p>
        </a>

        <a href="{{ route('tenant.pos.transactions', $tenant->slug) }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Transaction History</h3>
            <p class="text-gray-600 text-sm">Complete transaction records</p>
        </a>
    </div>
</div>
@endsection
