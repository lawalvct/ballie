@extends('layouts.tenant')

@section('title', 'Revenue Analysis')
@section('page-title', 'Revenue Analysis')
@section('page-description', 'Comprehensive revenue analytics and growth metrics')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('tenant.reports.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Reports
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-6 py-2 bg-gradient-to-r from-orange-400 to-orange-600 text-white rounded-lg hover:from-orange-500 hover:to-orange-700 transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90 mb-1">Current Period</p>
                    <p class="text-2xl font-bold">₦{{ number_format($stats['current_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90 mb-1">Previous Period</p>
                    <p class="text-2xl font-bold">₦{{ number_format($stats['previous_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90 mb-1">Growth Rate</p>
                    <p class="text-2xl font-bold">
                        {{ $stats['growth_rate'] > 0 ? '+' : '' }}{{ number_format($stats['growth_rate'], 2) }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm opacity-90 mb-1">Avg Order Value</p>
                    <p class="text-2xl font-bold">₦{{ number_format($stats['average_order_value'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue Trends Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Monthly Revenue Trends</h3>
        <div class="overflow-x-auto">
            <canvas id="monthlyRevenueChart" height="80"></canvas>
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue by Payment Status -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Revenue by Payment Status</h3>
            <div class="space-y-3">
                @foreach($revenueByPayment as $payment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($payment->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($payment->payment_status === 'partially_paid') bg-yellow-100 text-yellow-800
                            @elseif($payment->payment_status === 'unpaid') bg-red-100 text-red-800
                            @elseif($payment->payment_status === 'refunded') bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_status)) }}
                        </span>
                        <span class="font-semibold text-gray-800">₦{{ number_format($payment->total, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Revenue by Payment Method -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Revenue by Payment Method</h3>
            <div class="space-y-3">
                @foreach($revenueByMethod as $method)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $method->payment_method)) }}</span>
                            <span class="font-semibold text-gray-800">₦{{ number_format($method->total, 2) }}</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ number_format($method->count) }} transactions</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Monthly Revenue Details Table -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Monthly Revenue Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shipping</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($monthlyRevenue as $month)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ date('M Y', strtotime($month->month . '-01')) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ number_format($month->orders) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                ₦{{ number_format($month->subtotal, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                ₦{{ number_format($month->tax, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                ₦{{ number_format($month->shipping, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                -₦{{ number_format($month->discount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                ₦{{ number_format($month->total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Revenue Chart
    const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyRevenue->map(fn($m) => date('M Y', strtotime($m->month . '-01')))) !!},
            datasets: [{
                label: 'Subtotal',
                data: {!! json_encode($monthlyRevenue->pluck('subtotal')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
            }, {
                label: 'Tax',
                data: {!! json_encode($monthlyRevenue->pluck('tax')) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
            }, {
                label: 'Shipping',
                data: {!! json_encode($monthlyRevenue->pluck('shipping')) !!},
                backgroundColor: 'rgba(139, 92, 246, 0.8)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
