@extends('layouts.tenant')

@section('title', 'Purchases by Period Report')
@section('page-title', 'Purchases by Period Report')
@section('page-description')
    <span class="hidden md:inline">Time-based purchase analysis with trends and comparisons</span>
@endsection
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tenant.reports.vendor-purchases', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Vendor Purchases
            </a>
            <a href="{{ route('tenant.reports.purchase-summary', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                </svg>
                Purchase Summary
            </a>
            <a href="{{ route('tenant.reports.product-purchases', $tenant->slug) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7"></path>
                </svg>
                Product Purchases
            </a>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ route('tenant.reports.index', $tenant->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Purchases</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($totalPurchases, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Best Period</p>
                    <p class="text-lg font-bold text-gray-900">₦{{ number_format($bestPeriod['total_purchases'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ $fromDate }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ $toDate }}" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="period_type" class="block text-sm font-medium text-gray-700 mb-1">Period Type</label>
                <select name="period_type" id="period_type" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="daily" {{ $periodType === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $periodType === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $periodType === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ $periodType === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="yearly" {{ $periodType === 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
            </div>
            <div>
                <label for="compare_with" class="block text-sm font-medium text-gray-700 mb-1">Compare With</label>
                <select name="compare_with" id="compare_with" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">No Comparison</option>
                    <option value="previous_period" {{ $compareWith === 'previous_period' ? 'selected' : '' }}>Previous Period</option>
                    <option value="previous_year" {{ $compareWith === 'previous_year' ? 'selected' : '' }}>Previous Year</option>
                </select>
            </div>
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="inline-flex justify-center items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Period Purchases Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Purchases by {{ ucfirst($periodType) }} Period</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Purchases</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Purchase</th>
                        @if($compareWith)
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Growth %</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($periodPurchases as $index => $period)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $period['period'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($period['period_start'])->format('M d') }} - {{ \Carbon\Carbon::parse($period['period_end'])->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">₦{{ number_format($period['total_purchases'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($period['purchase_count']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">₦{{ number_format($period['avg_purchase'], 2) }}</td>
                            @if($compareWith)
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    @if(isset($period['growth_rate']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $period['growth_rate'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $period['growth_rate'] >= 0 ? '↑' : '↓' }} {{ number_format(abs($period['growth_rate']), 1) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $compareWith ? 6 : 5 }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">No purchase data found for the selected period</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($periodPurchases) > 0)
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-6 py-3 text-sm font-bold text-gray-900">Total</td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">₦{{ number_format($totalPurchases, 2) }}</td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">{{ number_format($totalOrders) }}</td>
                            <td class="px-6 py-3 text-sm font-bold text-gray-900 text-right">₦{{ number_format($totalOrders > 0 ? $totalPurchases / $totalOrders : 0, 2) }}</td>
                            @if($compareWith)
                                <td class="px-6 py-3"></td>
                            @endif
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Performance Insights -->
    @if($bestPeriod && $worstPeriod)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200 p-6">
                <div class="flex items-center mb-3">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <h4 class="text-lg font-semibold text-green-900">Highest Purchase Period</h4>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-green-700">Period:</span>
                        <span class="text-sm font-semibold text-green-900">{{ $bestPeriod['period'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-green-700">Total Purchases:</span>
                        <span class="text-sm font-bold text-green-900">₦{{ number_format($bestPeriod['total_purchases'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-green-700">Orders:</span>
                        <span class="text-sm font-semibold text-green-900">{{ number_format($bestPeriod['purchase_count']) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg border border-red-200 p-6">
                <div class="flex items-center mb-3">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                    <h4 class="text-lg font-semibold text-red-900">Lowest Purchase Period</h4>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-red-700">Period:</span>
                        <span class="text-sm font-semibold text-red-900">{{ $worstPeriod['period'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-red-700">Total Purchases:</span>
                        <span class="text-sm font-bold text-red-900">₦{{ number_format($worstPeriod['total_purchases'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-red-700">Orders:</span>
                        <span class="text-sm font-semibold text-red-900">{{ number_format($worstPeriod['purchase_count']) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
