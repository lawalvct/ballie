@extends('layouts.tenant')

@section('title', 'Issue Salary Advance - ' . $tenant->name)
@section('page-title', 'Issue Salary Advance (IOU)')
@section('page-description', 'Provide salary advance to employees and create automatic loan deduction')

@section('content')
<div class="space-y-6" x-data="salaryAdvanceForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Salary Advance / IOU</h2>
            <p class="mt-1 text-sm text-gray-600">Issue salary advance to employees with automatic payroll deduction</p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" @click="showInfo = !showInfo" :aria-expanded="showInfo.toString()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg :class="{'transform rotate-180': showInfo}" class="w-4 h-4 mr-2 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                Instructions
            </button>

            <a href="{{ route('tenant.payroll.loans.index', $tenant) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                View All Loans
            </a>
        </div>
    </div>

    <!-- Info Banner (collapsible) -->
    <div x-show="showInfo" x-cloak x-transition class="bg-blue-50 border-l-4 border-blue-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">How Salary Advance Works</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Creates an accounting voucher for the advance payment</li>
                        <li>Automatically creates an employee loan record</li>
                        <li>Calculates monthly deduction based on duration</li>
                        <li>Deduction will be applied automatically during payroll processing</li>
                        <li>Employee can view outstanding balance in their portal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('tenant.payroll.salary-advance.store', $tenant) }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Employee Selection -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Employee Details
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Employee <span class="text-red-500">*</span>
                                </label>
                                <select id="employee_id" name="employee_id" x-model="employeeId" @change="updateEmployeeInfo()"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        required>
                                    <option value="">-- Choose Employee --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}"
                                                data-name="{{ $employee->full_name }}"
                                                data-number="{{ $employee->employee_number }}"
                                                data-department="{{ $employee->department->name ?? 'N/A' }}"
                                                data-salary="{{ $employee->currentSalary->basic_salary ?? 0 }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }} ({{ $employee->employee_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Employee Info Display -->
                            <div x-show="employeeId" class="md:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Employee Number:</span>
                                        <span class="font-semibold text-gray-900" x-text="selectedEmployee.number"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Department:</span>
                                        <span class="font-semibold text-gray-900" x-text="selectedEmployee.department"></span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-600">Basic Salary:</span>
                                        <span class="font-semibold text-gray-900">₦<span x-text="formatNumber(selectedEmployee.salary)"></span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advance Details -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-teal-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Advance Amount & Repayment
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Advance Amount (₦) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="amount" name="amount" x-model.number="amount" @input="calculateMonthlyDeduction()"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                       placeholder="0.00" step="0.01" min="1" value="{{ old('amount') }}" required>
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration_months" class="block text-sm font-medium text-gray-700 mb-2">
                                    Repayment Duration (Months) <span class="text-red-500">*</span>
                                </label>
                                <select id="duration_months" name="duration_months" x-model.number="durationMonths" @change="calculateMonthlyDeduction()"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        required>
                                    <option value="">-- Select Duration --</option>
                                    <option value="1" {{ old('duration_months') == 1 ? 'selected' : '' }}>1 Month</option>
                                    <option value="2" {{ old('duration_months') == 2 ? 'selected' : '' }}>2 Months</option>
                                    <option value="3" {{ old('duration_months') == 3 ? 'selected' : '' }}>3 Months</option>
                                    <option value="4" {{ old('duration_months') == 4 ? 'selected' : '' }}>4 Months</option>
                                    <option value="5" {{ old('duration_months') == 5 ? 'selected' : '' }}>5 Months</option>
                                    <option value="6" {{ old('duration_months') == 6 ? 'selected' : '' }}>6 Months</option>
                                    <option value="9" {{ old('duration_months') == 9 ? 'selected' : '' }}>9 Months</option>
                                    <option value="12" {{ old('duration_months') == 12 ? 'selected' : '' }}>12 Months</option>
                                </select>
                                @error('duration_months')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2" x-show="monthlyDeduction > 0">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-green-800">Monthly Deduction from Salary</p>
                                            <p class="text-2xl font-bold text-green-900">₦<span x-text="formatNumber(monthlyDeduction)"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                                    Purpose / Notes
                                </label>
                                <textarea id="purpose" name="purpose" rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                          placeholder="Reason for salary advance (e.g., Medical emergency, School fees, etc.)">{{ old('purpose') }}</textarea>
                                @error('purpose')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-amber-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            Payment Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="voucher_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Voucher Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="voucher_date" name="voucher_date"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                       value="{{ old('voucher_date', date('Y-m-d')) }}" required>
                                @error('voucher_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                                    Payment Method <span class="text-red-500">*</span>
                                </label>
                                <select id="payment_method" name="payment_method"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                        required>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash Payment</option>
                                    <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">
                                    Payment Reference (Optional)
                                </label>
                                <input type="text" id="reference" name="reference"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                       placeholder="Check number, transfer reference, etc." value="{{ old('reference') }}">
                                @error('reference')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-lg rounded-lg border border-gray-200 sticky top-6">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-lg">
                        <h3 class="text-lg font-semibold text-white">Summary</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Advance Amount:</span>
                            <span class="font-bold text-lg text-gray-900">₦<span x-text="formatNumber(amount)"></span></span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-semibold text-gray-900"><span x-text="durationMonths || 0"></span> Month(s)</span>
                        </div>

                        <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                            <span class="text-gray-600">Monthly Deduction:</span>
                            <span class="font-semibold text-green-600">₦<span x-text="formatNumber(monthlyDeduction)"></span></span>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-4 mt-4">
                            <p class="text-xs text-blue-800">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                This creates:
                            </p>
                            <ul class="mt-2 text-xs text-blue-800 space-y-1">
                                <li>• Accounting Voucher (SA)</li>
                                <li>• Employee Loan Record</li>
                                <li>• Auto Payroll Deduction</li>
                            </ul>
                        </div>

                        <button type="submit"
                                class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Issue Salary Advance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function salaryAdvanceForm() {
    return {
        employeeId: '{{ old('employee_id') }}',
        amount: {{ old('amount', 0) }},
        durationMonths: {{ old('duration_months', 0) }},
        monthlyDeduction: 0,
        // controls visibility of the instructions/info banner (collapsed by default)
        showInfo: false,
        selectedEmployee: {
            name: '',
            number: '',
            department: '',
            salary: 0
        },

        init() {
            if (this.employeeId) {
                this.updateEmployeeInfo();
            }
            this.calculateMonthlyDeduction();
        },

        updateEmployeeInfo() {
            const select = document.getElementById('employee_id');
            const option = select.options[select.selectedIndex];

            if (option && option.value) {
                this.selectedEmployee = {
                    name: option.dataset.name || '',
                    number: option.dataset.number || '',
                    department: option.dataset.department || 'N/A',
                    salary: parseFloat(option.dataset.salary) || 0
                };
            }
        },

        calculateMonthlyDeduction() {
            if (this.amount > 0 && this.durationMonths > 0) {
                this.monthlyDeduction = this.amount / this.durationMonths;
            } else {
                this.monthlyDeduction = 0;
            }
        },

        formatNumber(value) {
            return new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(value || 0);
        }
    }
}
</script>
@endsection
