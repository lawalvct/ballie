@extends('layouts.tenant')

@section('title', 'Payroll Dashboard')
@section('page-title', 'Payroll')
@section('page-description')
    <span class="hidden md:inline">
        Effortlessly manage employee salaries, payroll processing, payslips, statutory deductions, and compliance—all in one secure place.
    </span>
@endsection

@section('content')
<div x-data="{
    moreActionsExpanded: false,
    toggleMoreActions() {
        this.moreActionsExpanded = !this.moreActionsExpanded;
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">

        <!-- Action Buttons -->
        <div class="mt-4 lg:mt-0 flex flex-wrap gap-3">
            <a href="{{ route('tenant.payroll.employees.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Manage Employees
            </a>

            <a href="{{ route('tenant.payroll.processing.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                View Payrolls
            </a>

              <a href="{{ route('tenant.payroll.employees.create', ['tenant' => $tenant->slug]) }}"
             class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>

                Add Employee
            </a>

             <a href="{{ route('tenant.payroll.attendance.index', ['tenant' => $tenant->slug]) }}"
             class="inline-flex items-center justify-center px-3 sm:px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>

                Manage Attendance
            </a>



            <!-- More Actions Button -->
            <button @click="toggleMoreActions()"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                </svg>
                <span x-text="moreActionsExpanded ? 'Hide Actions' : 'More Actions'"></span>
                <svg class="w-4 h-4 ml-2 transition-transform duration-200"
                     :class="{ 'rotate-180': moreActionsExpanded }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Include the More Actions Expandable Section -->
    @include('tenant.payroll.partials.more-actions-section')

    <!-- Payroll Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEmployees ?? 0 }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Active employees
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monthly Payroll</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($monthlyPayrollCost ?? 0, 2) }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            Current month
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Payrolls</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendingPayrolls ?? 0 }}</p>
                    <p class="text-sm text-orange-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Awaiting processing
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-teal-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ now()->format('M Y') }}</p>
                    <p class="text-sm text-blue-600 mt-1">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Current period
                        </span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payroll Activity -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Recent Payroll Activity</h3>
                <a href="{{ route('tenant.payroll.processing.index', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>
            </div>
            <div class="space-y-4">
                @forelse($recentPayrolls ?? [] as $payroll)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-{{ $payroll->status === 'completed' ? 'green' : ($payroll->status === 'processing' ? 'orange' : 'blue') }}-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-{{ $payroll->status === 'completed' ? 'green' : ($payroll->status === 'processing' ? 'orange' : 'blue') }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($payroll->status === 'completed')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    @elseif($payroll->status === 'processing')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    @endif
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $payroll->name ?? $payroll->period_name ?? 'Payroll Period' }}</p>
                                <p class="text-sm text-gray-500">{{ $payroll->created_at->format('M d, Y') ?? now()->format('M d, Y') }} • {{ ucfirst($payroll->status ?? 'pending') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">
                                ₦{{ number_format($payroll->total_gross ?? 0, 2) }}
                            </p>
                            <p class="text-sm text-gray-500">{{ $payroll->employee_count ?? 0 }} employees</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500">No payroll activity yet</p>
                        <a href="{{ route('tenant.payroll.employees.create', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">Add your first employee</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Department Summary -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Department Summary</h3>
                <a href="{{ route('tenant.payroll.departments.index', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manage Departments</a>
            </div>
            <div class="space-y-4">
                @forelse($departmentSummary ?? [] as $department)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $department->name ?? 'General' }}</p>
                                <p class="text-sm text-gray-500">{{ $department->employee_count ?? 0 }} employees</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">₦{{ number_format($department->monthly_cost ?? 0, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500">No departments created</p>
                        <a href="{{ route('tenant.payroll.departments.index', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">Manage departments</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="bg-white rounded-2xl p-6 shadow-lg" x-data="payrollChart()">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Monthly Payroll Overview</h3>
                <p class="text-sm text-gray-500 mt-1">Last 12 months payroll trends</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Legend -->
                <div class="flex items-center space-x-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Gross Pay</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-600">Net Pay</span>
                    </div>
                </div>
                <!-- View Toggle -->
                <div class="flex space-x-2 bg-gray-100 rounded-lg p-1">
                    <button @click="showGross = true; showNet = true"
                            :class="showGross && showNet ? 'bg-white shadow-sm' : 'text-gray-600'"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200">Both</button>
                    <button @click="showGross = true; showNet = false"
                            :class="showGross && !showNet ? 'bg-white shadow-sm' : 'text-gray-600'"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200">Gross</button>
                    <button @click="showGross = false; showNet = true"
                            :class="!showGross && showNet ? 'bg-white shadow-sm' : 'text-gray-600'"
                            class="px-3 py-1.5 text-xs font-medium rounded-md transition-all duration-200">Net</button>
                </div>
            </div>
        </div>

        <!-- Chart Canvas -->
        <div class="relative" style="height: 320px;">
            <canvas id="payrollChart"></canvas>
        </div>

        <!-- Summary Stats Below Chart -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-1">Average Monthly Gross</p>
                <p class="text-lg font-bold text-gray-900" x-text="averageGross"></p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-1">Average Monthly Net</p>
                <p class="text-lg font-bold text-gray-900" x-text="averageNet"></p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-1">Total Year to Date</p>
                <p class="text-lg font-bold text-gray-900" x-text="totalYTD"></p>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
// Alpine.js component for payroll chart
function payrollChart() {
    return {
        showGross: true,
        showNet: true,
        chart: null,
        chartData: @json($monthlyPayrollData ?? []),
        averageGross: '₦0.00',
        averageNet: '₦0.00',
        totalYTD: '₦0.00',

        init() {
            this.calculateStats();
            this.initChart();

            // Watch for changes to update chart
            this.$watch('showGross', () => this.updateChart());
            this.$watch('showNet', () => this.updateChart());
        },

        calculateStats() {
            if (!this.chartData || this.chartData.length === 0) {
                return;
            }

            const totalGross = this.chartData.reduce((sum, item) => sum + item.gross, 0);
            const totalNet = this.chartData.reduce((sum, item) => sum + item.net, 0);
            const count = this.chartData.length;

            this.averageGross = '₦' + this.formatNumber(totalGross / count);
            this.averageNet = '₦' + this.formatNumber(totalNet / count);
            this.totalYTD = '₦' + this.formatNumber(totalGross);
        },

        formatNumber(num) {
            return new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        },

        initChart() {
            const ctx = document.getElementById('payrollChart');
            if (!ctx) return;

            const labels = this.chartData.map(item => item.month_short);
            const grossData = this.chartData.map(item => item.gross);
            const netData = this.chartData.map(item => item.net);

            const datasets = [];

            if (this.showGross) {
                datasets.push({
                    label: 'Gross Pay',
                    data: grossData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                });
            }

            if (this.showNet) {
                datasets.push({
                    label: 'Net Pay',
                    data: netData,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                });
            }

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            displayColors: true,
                            callbacks: {
                                label: (context) => {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '₦' + this.formatNumber(context.parsed.y);
                                    return label;
                                },
                                footer: (tooltipItems) => {
                                    const dataIndex = tooltipItems[0].dataIndex;
                                    const employees = this.chartData[dataIndex].employees;
                                    return employees > 0 ? `${employees} employees` : '';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false,
                            },
                            ticks: {
                                callback: (value) => {
                                    return '₦' + (value >= 1000000 ?
                                        (value / 1000000).toFixed(1) + 'M' :
                                        (value >= 1000 ? (value / 1000).toFixed(0) + 'K' : value));
                                },
                                color: '#6B7280',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                color: '#6B7280',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        },

        updateChart() {
            if (!this.chart) return;

            const grossData = this.chartData.map(item => item.gross);
            const netData = this.chartData.map(item => item.net);

            const datasets = [];

            if (this.showGross) {
                datasets.push({
                    label: 'Gross Pay',
                    data: grossData,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                });
            }

            if (this.showNet) {
                datasets.push({
                    label: 'Net Pay',
                    data: netData,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                });
            }

            this.chart.data.datasets = datasets;
            this.chart.update('active');
        }
    };
}
</script>

<style>
.quick-action-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.quick-action-btn:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.quick-action-btn:active {
    transform: translateY(0) scale(1.02);
}

/* Modal Action Cards Styles */
.modal-action-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.modal-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.8s;
}

.modal-action-card:hover::before {
    left: 100%;
}

.modal-action-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.modal-action-card:active {
    transform: translateY(-2px) scale(1.01);
}

/* Custom scrollbar for modal */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Animation for dropdown items */
.dropdown-item {
    transition: all 0.2s ease-in-out;
}

.dropdown-item:hover {
    transform: translateX(4px);
}

/* Modal backdrop blur effect */
.modal-backdrop {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* Pulse animation for modal cards */
@keyframes pulse-subtle {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.modal-action-card:hover .w-14 {
    animation: pulse-subtle 2s infinite;
}

/* Gradient animations */
.gradient-bg-primary {
    background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
    background-size: 400% 400%;
    animation: gradient-shift 15s ease infinite;
}

@keyframes gradient-shift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Hover effects for transaction items */
.transaction-item {
    transition: all 0.3s ease;
}

.transaction-item:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Loading skeleton animation */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Modal entrance animation */
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-content {
    animation: modalSlideIn 0.3s ease-out;
}

/* Card hover glow effect */
.modal-action-card:hover {
    box-shadow: 0 0 30px rgba(255, 255, 255, 0.2);
}

/* Responsive modal adjustments */
@media (max-width: 768px) {
    .modal-action-card {
        padding: 1rem;
    }

    .modal-action-card .w-14 {
        width: 2.5rem;
        height: 2.5rem;
    }

    .modal-action-card .w-7 {
        width: 1.25rem;
        height: 1.25rem;
    }
}

/* Ripple effect */
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth hover effects to cards
    const cards = document.querySelectorAll('.bg-white.rounded-2xl');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        });
    });

    // Add click animation to quick action buttons
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add click animation to modal action cards
    const modalActionCards = document.querySelectorAll('.modal-action-card');
    modalActionCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Add click feedback
            this.style.transform = 'translateY(-2px) scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Alt + E for Employee Management
        if (e.altKey && e.key === 'e') {
            e.preventDefault();
            window.location.href = "{{ route('tenant.payroll.employees.index', ['tenant' => $tenant->slug]) }}";
        }

        // Alt + P for Process Payroll
        if (e.altKey && e.key === 'p') {
            e.preventDefault();
            window.location.href = "{{ route('tenant.payroll.processing.create', ['tenant' => $tenant->slug]) }}";
        }

        // Alt + R for Reports
        if (e.altKey && e.key === 'r') {
            e.preventDefault();
            window.location.href = "{{ route('tenant.payroll.reports.tax-report', ['tenant' => $tenant->slug]) }}";
        }

        // Alt + M for More Actions Modal
        if (e.altKey && e.key === 'm') {
            e.preventDefault();
            // Trigger the Alpine.js modal
            const moreActionsBtn = document.querySelector('[x-data] button');
            if (moreActionsBtn) {
                moreActionsBtn.click();
            }
        }

        // Escape to close modal
        if (e.key === 'Escape') {
            // This will be handled by Alpine.js automatically
        }
    });

    // Auto-refresh data every 5 minutes
    setInterval(function() {
        // You can implement AJAX refresh here if needed
        console.log('Auto-refresh triggered');
    }, 300000); // 5 minutes
});
</script>
@endpush
@endsection
