@extends('layouts.tenant')

@section('title', 'Create Payroll Period - ' . $tenant->name)

@section('content')
<div class="space-y-6" x-data="payrollForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Payroll Period</h1>
            <p class="mt-1 text-sm text-gray-500">
                Set up a new payroll processing period
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.payroll.processing.index', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Processing
            </a>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('tenant.payroll.processing.store', $tenant) }}" class="space-y-6">
        @csrf

        <!-- Period Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Period Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Period Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Period Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $startDate->format('F Y') . ' Payroll') }}"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                               placeholder="Enter period name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">A descriptive name for this payroll period</p>
                    </div>

                    <!-- Payroll Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Payroll Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type"
                                id="type"
                                x-model="payrollType"
                                @change="updatePeriodDefaults()"
                                required
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg @error('type') border-red-300 @enderror">
                            <option value="">Select Type</option>
                            <option value="monthly" {{ old('type', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="weekly" {{ old('type') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="bi_weekly" {{ old('type') === 'bi_weekly' ? 'selected' : '' }}>Bi-Weekly</option>
                            <option value="contract" {{ old('type') === 'contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Period Preview -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Period Preview
                        </label>
                        <div class="block w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500">
                            <span x-text="periodPreview"></span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Based on selected dates</p>
                    </div>
                </div>

                <!-- Date Fields -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="start_date"
                               id="start_date"
                               x-model="startDate"
                               @change="updatePeriodCalculations()"
                               value="{{ old('start_date', $startDate->format('Y-m-d')) }}"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('start_date') border-red-300 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="end_date"
                               id="end_date"
                               x-model="endDate"
                               @change="updatePeriodCalculations()"
                               value="{{ old('end_date', $endDate->format('Y-m-d')) }}"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('end_date') border-red-300 @enderror">
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pay Date -->
                    <div>
                        <label for="pay_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Pay Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="pay_date"
                               id="pay_date"
                               x-model="payDate"
                               value="{{ old('pay_date', $payDate->format('Y-m-d')) }}"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('pay_date') border-red-300 @enderror">
                        @error('pay_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Date when salaries will be paid</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description/Notes
                    </label>
                    <textarea name="description"
                              id="description"
                              rows="2"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-300 @enderror"
                              placeholder="Optional notes about this payroll period">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Period Summary -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Period Summary</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Duration</p>
                                <p class="text-lg font-semibold text-blue-900" x-text="duration">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">Active Employees</p>
                                <p class="text-lg font-semibold text-green-900">{{ $activeEmployees ?? 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-purple-600">Estimated Cost</p>
                                <p class="text-lg font-semibold text-purple-900" x-text="formatCurrency(estimatedCost)">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-600">Working Days</p>
                                <p class="text-lg font-semibold text-yellow-900" x-text="workingDays">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-amber-800">Important Notes</h4>
                    <div class="mt-2 text-sm text-amber-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Ensure all employee information is up to date before creating the payroll period</li>
                            <li>The pay date should be after the end date of the period</li>
                            <li>Once created, you can generate payroll calculations for all active employees</li>
                            <li>Salary components and tax rates will be applied based on current settings</li>
                            <li>You can review and approve the payroll before finalizing payments</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Ready to create payroll period for </span>
                        <span class="text-lg font-bold text-gray-900">{{ $activeEmployees ?? 0 }} employees</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button"
                                onclick="window.history.back()"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit"
                                name="action"
                                value="save_draft"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Save as Draft
                        </button>
                        <button type="submit"
                                name="action"
                                value="create_period"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Create Payroll Period
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function payrollForm() {
    return {
        payrollType: '{{ old('type', 'monthly') }}',
        startDate: '{{ old('start_date', $startDate->format('Y-m-d')) }}',
        endDate: '{{ old('end_date', $endDate->format('Y-m-d')) }}',
        payDate: '{{ old('pay_date', $payDate->format('Y-m-d')) }}',
        activeEmployees: {{ $activeEmployees ?? 0 }},
        avgMonthlySalary: 150000, // You can get this from your database

        get periodPreview() {
            if (this.startDate && this.endDate) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                return start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) +
                       ' - ' +
                       end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }
            return 'Select dates';
        },

        get duration() {
            if (this.startDate && this.endDate) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                return `${diffDays} days`;
            }
            return '-';
        },

        get workingDays() {
            if (this.startDate && this.endDate) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                let workingDays = 0;
                let currentDate = new Date(start);

                while (currentDate <= end) {
                    const dayOfWeek = currentDate.getDay();
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) { // Not Sunday (0) or Saturday (6)
                        workingDays++;
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                return workingDays;
            }
            return '-';
        },

        get estimatedCost() {
            if (this.activeEmployees > 0) {
                return this.activeEmployees * this.avgMonthlySalary;
            }
            return 0;
        },

        formatCurrency(amount) {
            if (!amount || isNaN(amount)) return '₦0.00';
            return '₦' + parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        updatePeriodDefaults() {
            const today = new Date();
            let startDate, endDate, payDate;

            switch (this.payrollType) {
                case 'weekly':
                    // Start from Monday of current week
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - today.getDay() + 1);
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6);
                    payDate = new Date(endDate);
                    payDate.setDate(endDate.getDate() + 2);
                    break;

                case 'bi_weekly':
                    // Start from Monday of current week
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - today.getDay() + 1);
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 13);
                    payDate = new Date(endDate);
                    payDate.setDate(endDate.getDate() + 2);
                    break;

                case 'monthly':
                default:
                    // Start from first day of current month
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    payDate = new Date(endDate);
                    payDate.setDate(endDate.getDate() + 2);
                    break;
            }

            this.startDate = startDate.toISOString().split('T')[0];
            this.endDate = endDate.toISOString().split('T')[0];
            this.payDate = payDate.toISOString().split('T')[0];
        },

        updatePeriodCalculations() {
            if (this.endDate) {
                const endDate = new Date(this.endDate);
                const payDate = new Date(endDate);
                payDate.setDate(payDate.getDate() + 2);
                this.payDate = payDate.toISOString().split('T')[0];
            }
        },

        init() {
            console.log('✅ Payroll form initialized');
        }
    }
}
</script>
@endpush
@endsection
