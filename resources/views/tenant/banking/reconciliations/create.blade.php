@extends('layouts.tenant')

@section('title', 'Create Bank Reconciliation')
@section('page-title', 'Create Bank Reconciliation')
@section('page-description', 'Reconcile your bank statement with accounting records')

@section('content')
<div class="min-h-screen bg-gray-50 py-8" x-data="reconciliationForm()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Bank Reconciliation</h1>
                <p class="text-gray-600 mt-1">Reconcile your bank statement with your accounting records</p>
            </div>
            <a href="{{ route('tenant.banking.reconciliations.index', tenant('slug')) }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle mt-1 mr-2"></i>
                <div>
                    <p class="font-semibold">Please correct the following errors:</p>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Reconciliation Details</h2>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('tenant.banking.reconciliations.store', tenant('slug')) }}" method="POST" @submit="handleSubmit">
                            @csrf

                            <!-- Bank Account -->
                            <div class="mb-6">
                                <label for="bank_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank Account <span class="text-red-500">*</span>
                                </label>
                                <select name="bank_id" id="bank_id"
                                        class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 @error('bank_id') border-red-500 @enderror"
                                        x-model="formData.bank_id"
                                        @change="loadBankDetails"
                                        x-init="loadBankDetails()"
                                        required>
                                    <option value="">Select a bank account...</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}"
                                                data-balance="{{ $bank->getCurrentBalance() }}"
                                                data-last-reconciled="{{ optional($bank->last_reconciliation_date)->format('Y-m-d') }}"
                                                data-account-name="{{ $bank->account_name }}"
                                                data-account-type="{{ $bank->account_type }}"
                                                {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                            {{ $bank->bank_name }} - {{ $bank->account_number }}
                                            (Balance: ₦{{ number_format($bank->getCurrentBalance(), 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Reconciliation Date -->
                            <div class="mb-6">
                                <label for="reconciliation_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Reconciliation Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="reconciliation_date" id="reconciliation_date"
                                       class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 @error('reconciliation_date') border-red-500 @enderror"
                                       value="{{ old('reconciliation_date', date('Y-m-d')) }}"
                                       required>
                                @error('reconciliation_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Statement Period -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div>
                                    <label for="statement_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Statement Start Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="statement_start_date" id="statement_start_date"
                                           class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 @error('statement_start_date') border-red-500 @enderror"
                                           x-model="formData.statement_start_date"
                                           value="{{ old('statement_start_date') }}"
                                           required>
                                    @error('statement_start_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="statement_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Statement End Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="statement_end_date" id="statement_end_date"
                                           class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 @error('statement_end_date') border-red-500 @enderror"
                                           x-model="formData.statement_end_date"
                                           value="{{ old('statement_end_date') }}"
                                           required>
                                    @error('statement_end_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Closing Balance -->
                            <div class="mb-6">
                                <label for="closing_balance_per_bank" class="block text-sm font-medium text-gray-700 mb-2">
                                    Closing Balance (Per Bank Statement) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500">₦</span>
                                    <input type="number" step="0.01" name="closing_balance_per_bank" id="closing_balance_per_bank"
                                           class="w-full pl-8 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 @error('closing_balance_per_bank') border-red-500 @enderror"
                                           value="{{ old('closing_balance_per_bank') }}"
                                           placeholder="0.00"
                                           required>
                                </div>
                                @error('closing_balance_per_bank')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bank Charges -->
                            <div class="mb-6">
                                <label for="bank_charges" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bank Charges/Fees
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500">₦</span>
                                    <input type="number" step="0.01" name="bank_charges" id="bank_charges"
                                           class="w-full pl-8 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                           x-model="formData.bank_charges"
                                           value="{{ old('bank_charges', '0.00') }}"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <!-- Interest Earned -->
                            <div class="mb-6">
                                <label for="interest_earned" class="block text-sm font-medium text-gray-700 mb-2">
                                    Interest Earned
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500">₦</span>
                                    <input type="number" step="0.01" name="interest_earned" id="interest_earned"
                                           class="w-full pl-8 border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                           x-model="formData.interest_earned"
                                           value="{{ old('interest_earned', '0.00') }}"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="mb-6">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Notes/Comments
                                </label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="w-full border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500"
                                          placeholder="Add any notes or comments...">{{ old('notes') }}</textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3">
                                <button type="submit"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg transition-colors duration-200"
                                        :disabled="submitting">
                                    <span x-show="!submitting">
                                        <i class="fas fa-check mr-2"></i>Create & Start Reconciliation
                                    </span>
                                    <span x-show="submitting">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Creating...
                                    </span>
                                </button>
                                <a href="{{ route('tenant.banking.reconciliations.index', tenant('slug')) }}"
                                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Help Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-emerald-50">
                        <h3 class="text-lg font-semibold text-emerald-700">
                            <i class="fas fa-question-circle mr-2"></i>How to Reconcile
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">1</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Select Bank Account</h4>
                                <p class="text-sm text-gray-600">Choose the account to reconcile</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">2</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Enter Statement Period</h4>
                                <p class="text-sm text-gray-600">Specify the date range</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">3</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Enter Balances</h4>
                                <p class="text-sm text-gray-600">Input opening and closing balances</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">4</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Add Charges & Interest</h4>
                                <p class="text-sm text-gray-600">Include bank charges or interest</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">5</div>
                            <div>
                                <h4 class="font-medium text-gray-900">Match Transactions</h4>
                                <p class="text-sm text-gray-600">Match transactions on next screen</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200" x-show="formData.bank_id" x-cloak>
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                        <h3 class="text-lg font-semibold text-blue-700">
                            <i class="fas fa-info-circle mr-2"></i>Bank Information
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Account</p>
                            <p class="font-semibold text-gray-900" x-text="bankInfo.accountName || '-'">-</p>
                            <p class="text-sm text-gray-500">Type: <span x-text="bankInfo.accountType || '-'">-</span></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Current Book Balance</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(bankInfo.currentBalance)">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Last Reconciled</p>
                            <p class="text-gray-900" x-text="bankInfo.lastReconciled ? new Date(bankInfo.lastReconciled).toLocaleDateString() : 'Never'">-</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-lightbulb mr-1"></i>
                                <strong>Tip:</strong> Unreconciled transactions will be loaded automatically
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function reconciliationForm() {
        return {
            submitting: false,
            formData: {
                bank_id: '{{ old('bank_id') }}',
                statement_start_date: '{{ old('statement_start_date') }}',
                statement_end_date: '{{ old('statement_end_date') }}'
            },
            bankInfo: {
                id: null,
                currentBalance: 0,
                lastReconciled: null,
                accountName: null,
                accountType: null
            },

            loadBankDetails() {
                if (!this.formData.bank_id) {
                    this.bankInfo = { id: null, currentBalance: 0, lastReconciled: null, accountName: null, accountType: null };
                    return;
                }

                const selectElement = document.getElementById('bank_id');
                const selectedOption = selectElement.options[selectElement.selectedIndex];

                this.bankInfo.id = this.formData.bank_id;
                this.bankInfo.currentBalance = parseFloat(selectedOption.dataset.balance || 0);
                this.bankInfo.lastReconciled = selectedOption.dataset.lastReconciled || null;
                this.bankInfo.accountName = selectedOption.dataset.accountName;
                this.bankInfo.accountType = selectedOption.dataset.accountType;
            },

            formatCurrency(amount) {
                return '₦' + parseFloat(amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },

            handleSubmit(e) {
                if (this.formData.statement_start_date && this.formData.statement_end_date) {
                    const startDate = new Date(this.formData.statement_start_date);
                    const endDate = new Date(this.formData.statement_end_date);

                    if (endDate < startDate) {
                        e.preventDefault();
                        alert('Statement end date must be after the start date.');
                        return false;
                    }
                }

                this.submitting = true;
            }
        }
    }
</script>
@endpush
@endsection
