@extends('layouts.app')

@section('title', 'Process Payroll')

@section('content')
<div x-data="payrollProcessor()" class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Process Payroll</h1>
                    <p class="text-purple-100 text-lg">Calculate and process employee payroll for the current period</p>
                </div>
                <a href="{{ route('payroll.index') }}"
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Payroll
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Period Selection -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                Select Payroll Period
            </h3>

            <form action="{{ route('payroll.runs.store') }}" method="POST" @submit.prevent="processPayroll">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payroll Period <span class="text-red-500">*</span></label>
                        <select name="payroll_period_id" x-model="selectedPeriod" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Select Period</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" data-employees="{{ $period->active_employees_count }}">
                                    {{ $period->name }} ({{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Processing Date</label>
                        <input type="date" name="processing_date" value="{{ date('Y-m-d') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" name="description" placeholder="Optional description"
                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Employee Preview -->
                <div x-show="selectedPeriod" class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-200">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-users mr-2 text-purple-600"></i>
                        Employees to Process
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600" x-text="eligibleEmployees.length"></div>
                            <div class="text-sm text-gray-600">Eligible Employees</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600" x-text="calculateTotal('base_salary')"></div>
                            <div class="text-sm text-gray-600">Total Base Salary</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600" x-text="calculateTotal('estimated_net')"></div>
                            <div class="text-sm text-gray-600">Estimated Net Pay</div>
                        </div>
                    </div>

                    <!-- Employee List -->
                    <div class="bg-white rounded-lg p-4 max-h-64 overflow-y-auto">
                        <template x-for="employee in eligibleEmployees" :key="employee.id">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold mr-3">
                                        <span x-text="employee.initials"></span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900" x-text="employee.name"></div>
                                        <div class="text-sm text-gray-600" x-text="employee.position"></div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium text-gray-900" x-text="'₦' + employee.base_salary.toLocaleString()"></div>
                                    <div class="text-sm text-gray-600">Base Salary</div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Processing Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-cogs mr-2 text-blue-500"></i>
                            Processing Options
                        </h4>

                        <label class="flex items-center">
                            <input type="checkbox" name="auto_approve" value="1" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Auto-approve after processing</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="send_notifications" value="1" checked class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Send notifications to employees</span>
                        </label>

                        <label class="flex items-center">
                            <input type="checkbox" name="create_journal_entries" value="1" checked class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Create accounting journal entries</span>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-chart-line mr-2 text-green-500"></i>
                            Summary
                        </h4>

                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Employees:</span>
                                <span class="font-medium" x-text="eligibleEmployees.length"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gross Pay:</span>
                                <span class="font-medium" x-text="'₦' + calculateTotal('base_salary')"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Est. Deductions:</span>
                                <span class="font-medium text-red-600" x-text="'₦' + calculateTotal('deductions')"></span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-900 font-semibold">Est. Net Pay:</span>
                                <span class="font-bold text-green-600" x-text="'₦' + calculateTotal('estimated_net')"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mt-8">
                    <a href="{{ route('payroll.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg font-medium transition-colors duration-300">
                        Cancel
                    </a>
                    <button type="submit" x-bind:disabled="!selectedPeriod || processing"
                            class="bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white px-8 py-3 rounded-lg font-medium transition-colors duration-300 flex items-center">
                        <i class="fas fa-play mr-2" x-show="!processing"></i>
                        <i class="fas fa-spinner fa-spin mr-2" x-show="processing"></i>
                        <span x-text="processing ? 'Processing...' : 'Process Payroll'"></span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Processing Progress -->
        <div x-show="processing" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-spinner fa-spin mr-2 text-purple-500"></i>
                Processing Payroll
            </h3>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">Calculating salaries...</span>
                    <div class="w-6 h-6 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                </div>

                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" x-bind:style="'width: ' + progressPercentage + '%'"></div>
                </div>

                <div class="text-center text-sm text-gray-600" x-text="progressMessage"></div>
            </div>
        </div>
    </div>
</div>

<script>
function payrollProcessor() {
    return {
        selectedPeriod: '',
        processing: false,
        progressPercentage: 0,
        progressMessage: '',
        eligibleEmployees: @json($employees ?? []),

        calculateTotal(field) {
            return this.eligibleEmployees.reduce((total, employee) => {
                switch(field) {
                    case 'base_salary':
                        return total + (employee.base_salary || 0);
                    case 'deductions':
                        return total + (employee.base_salary * 0.18 || 0); // Estimated tax + pension
                    case 'estimated_net':
                        return total + ((employee.base_salary || 0) * 0.82); // Estimated after deductions
                    default:
                        return total;
                }
            }, 0).toLocaleString();
        },

        async processPayroll(event) {
            this.processing = true;
            this.progressPercentage = 0;
            this.progressMessage = 'Initializing payroll processing...';

            try {
                // Simulate processing steps
                await this.updateProgress(25, 'Calculating employee salaries...');
                await this.updateProgress(50, 'Computing tax deductions...');
                await this.updateProgress(75, 'Creating journal entries...');
                await this.updateProgress(100, 'Finalizing payroll...');

                // Submit the form
                event.target.submit();
            } catch (error) {
                this.processing = false;
                alert('An error occurred while processing payroll.');
            }
        },

        updateProgress(percentage, message) {
            return new Promise(resolve => {
                setTimeout(() => {
                    this.progressPercentage = percentage;
                    this.progressMessage = message;
                    resolve();
                }, 1000);
            });
        }
    }
}
</script>
@endsection
