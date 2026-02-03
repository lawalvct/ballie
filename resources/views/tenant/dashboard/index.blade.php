@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('page-title')
    <span class="md:hidden">Dashboard</span>
    <span class="hidden md:inline">Dashboard Overview</span>
@endsection

@section('page-description')
    <span class="hidden md:inline">View your business metrics and insights</span>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Tour Banner -->
    @if(isset($showTour) && $showTour)
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="flex items-start space-x-4 mb-4 md:mb-0">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="text-white">
                        <h3 class="text-2xl md:text-3xl font-bold mb-2">Welcome to @brand!</h3>
                        <p class="text-blue-100 text-base md:text-lg mb-1">Need help getting started?</p>
                        <p class="text-blue-200 text-sm">Check out our comprehensive documentation and guides.</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('tenant.tour.skip', $tenant->slug) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-xl transition-all">
                            Maybe Later
                        </button>
                    </form>
                    <a href="{{ route('tenant.help', $tenant->slug) }}" class="px-8 py-3 bg-white text-indigo-600 font-bold rounded-xl hover:bg-blue-50 transition-all">
                        Help & Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Alerts -->
    @if(count($alerts) > 0)
    <div class="space-y-3">
        @foreach($alerts as $alert)
        <div id="alert-{{ $alert['type'] }}" class="bg-{{ $alert['color'] }}-50 border-l-4 border-{{ $alert['color'] }}-400 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-{{ $alert['color'] }}-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-{{ $alert['color'] }}-800">{{ $alert['title'] }}</h4>
                        <p class="text-sm text-{{ $alert['color'] }}-700">{{ $alert['message'] }}</p>
                    </div>
                </div>
                <button onclick="dismissAlert('{{ $alert['type'] }}')" class="text-{{ $alert['color'] }}-400 hover:text-{{ $alert['color'] }}-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-sm {{ $quickStats['monthly_sales_percentage'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                        {{ $quickStats['monthly_sales_percentage'] >= 0 ? '+' : '' }}{{ number_format($quickStats['monthly_sales_percentage'], 1) }}% from last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSalesCount) }}</p>
                    <p class="text-sm text-gray-500 mt-1">This month</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Purchase</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($totalPurchase, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-1">This month</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Customers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalCustomers) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Registered</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Revenue Trend</h3>
                <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Sales by Category</h3>
                <select class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Activities</h3>
            <div class="space-y-4">
                @forelse($recentActivities as $activity)
                <div class="flex items-start">
                    <div class="w-8 h-8 bg-{{ $activity['icon_color'] }}-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <svg class="w-4 h-4 text-{{ $activity['icon_color'] }}-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                        <p class="text-xs text-gray-500">{{ $activity['details'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm text-center py-4">No recent activities</p>
                @endforelse
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold mb-4">Top Selling Products</h3>
            <div class="space-y-4">
                @forelse($topProducts as $index => $product)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="w-6 h-6 bg-gray-100 rounded flex items-center justify-center text-xs font-semibold mr-3">{{ $index + 1 }}</span>
                        <div>
                            <p class="text-sm font-medium">{{ $product['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $product['sales'] }} sold</p>
                        </div>
                    </div>
                    <p class="text-sm font-semibold">₦{{ number_format($product['revenue']) }}</p>
                </div>
                @empty
                <p class="text-gray-500 text-sm text-center py-4">No sales data yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold mb-4">Top Customers</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($topCustomers as $customer)
            <div class="border rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-sm font-semibold text-blue-600">{{ substr($customer['name'], 0, 2) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ $customer['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ $customer['orders'] }} orders</p>
                    </div>
                </div>
                <p class="text-lg font-bold text-gray-900">₦{{ number_format($customer['spent']) }}</p>
            </div>
            @empty
            <div class="col-span-3 text-center py-8">
                <p class="text-gray-500 text-sm">No customer data yet</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <a href="{{ route('tenant.accounting.invoices.create', ['tenant' => $tenant->slug, 'type' => 'sv']) }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium">New Invoice</p>
            </a>

            <a href="{{ route('tenant.crm.customers.create', $tenant->slug) }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium">Add Customer</p>
            </a>

            <a href="{{ route('tenant.inventory.products.create', $tenant->slug) }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium">Add Product</p>
            </a>

            <a href="{{ route('tenant.reports.financial', $tenant->slug) }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium">P&L Report</p>
            </a>

            <a href="{{ route('tenant.reports.sales', $tenant->slug) }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium">Sales Report</p>
            </a>

            <a href="{{ route('tenant.accounting.vouchers.create', $tenant->slug) }}" class="p-4 border rounded-lg hover:bg-gray-50 text-center">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                <p class="text-xs font-medium">New Voucher</p>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dismissAlert(alertType) {
    document.getElementById('alert-' + alertType).style.display = 'none';
    localStorage.setItem('dismissed_alert_' + alertType, Date.now());
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle alert dismissals
    const alerts = ['low_stock', 'out_of_stock'];
    alerts.forEach(function(alertType) {
        const dismissed = localStorage.getItem('dismissed_alert_' + alertType);
        if (dismissed) {
            const dismissedTime = parseInt(dismissed);
            const now = Date.now();
            const oneDay = 24 * 60 * 60 * 1000;

            if (now - dismissedTime < oneDay) {
                const alertElement = document.getElementById('alert-' + alertType);
                if (alertElement) {
                    alertElement.style.display = 'none';
                }
            }
        }
    });

    // Revenue Chart with real data from backend
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($chartData['revenue']) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₦' + (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Electronics', 'Clothing', 'Home & Garden', 'Sports', 'Books', 'Others'],
            datasets: [{
                data: [35, 25, 15, 12, 8, 5],
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)',
                    'rgb(139, 92, 246)',
                    'rgb(107, 114, 128)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});
</script>
@endpush


