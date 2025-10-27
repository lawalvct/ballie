@extends('layouts.tenant')

@section('title', 'Create Bank Reconciliation')

@section('content')
<div class="container-fluid px-4" x-data="reconciliationForm()">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Create Bank Reconciliation</h1>
            <p class="text-muted mb-0">Reconcile your bank statement with your accounting records</p>
        </div>
        <a href="{{ route('tenant.banking.reconciliations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Reconciliation Form Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 text-emerald-600">
                        <i class="fas fa-file-invoice me-2"></i>Reconciliation Details
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.banking.reconciliations.store') }}" method="POST" @submit="handleSubmit">
                        @csrf

                        <!-- Bank Account Selection -->
                        <div class="mb-4">
                            <label for="bank_id" class="form-label fw-semibold">
                                Bank Account <span class="text-danger">*</span>
                            </label>
                            <select
                                name="bank_id"
                                id="bank_id"
                                class="form-select @error('bank_id') is-invalid @enderror"
                                x-model="formData.bank_id"
                                @change="loadBankDetails"
                                required
                            >
                                <option value="">Select a bank account...</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->bank_name }} - {{ $bank->account_number }}
                                        (Balance: {{ tenant_currency() }}{{ number_format($bank->getCurrentBalance(), 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Select the bank account you want to reconcile
                            </small>
                        </div>

                        <!-- Statement Period -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="statement_start_date" class="form-label fw-semibold">
                                    Statement Start Date <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="statement_start_date"
                                    id="statement_start_date"
                                    class="form-control @error('statement_start_date') is-invalid @enderror"
                                    x-model="formData.statement_start_date"
                                    value="{{ old('statement_start_date') }}"
                                    required
                                >
                                @error('statement_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="statement_end_date" class="form-label fw-semibold">
                                    Statement End Date <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="statement_end_date"
                                    id="statement_end_date"
                                    class="form-control @error('statement_end_date') is-invalid @enderror"
                                    x-model="formData.statement_end_date"
                                    value="{{ old('statement_end_date') }}"
                                    required
                                >
                                @error('statement_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Opening Balance -->
                        <div class="mb-4">
                            <label for="opening_balance" class="form-label fw-semibold">
                                Opening Balance (Per Bank Statement) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ tenant_currency() }}</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="opening_balance"
                                    id="opening_balance"
                                    class="form-control @error('opening_balance') is-invalid @enderror"
                                    x-model="formData.opening_balance"
                                    value="{{ old('opening_balance') }}"
                                    placeholder="0.00"
                                    required
                                >
                                @error('opening_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Enter the opening balance from your bank statement
                            </small>
                        </div>

                        <!-- Closing Balance -->
                        <div class="mb-4">
                            <label for="closing_balance" class="form-label fw-semibold">
                                Closing Balance (Per Bank Statement) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ tenant_currency() }}</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="closing_balance"
                                    id="closing_balance"
                                    class="form-control @error('closing_balance') is-invalid @enderror"
                                    x-model="formData.closing_balance"
                                    value="{{ old('closing_balance') }}"
                                    placeholder="0.00"
                                    required
                                >
                                @error('closing_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Enter the closing balance from your bank statement
                            </small>
                        </div>

                        <!-- Bank Charges -->
                        <div class="mb-4">
                            <label for="bank_charges" class="form-label fw-semibold">
                                Bank Charges/Fees
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ tenant_currency() }}</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="bank_charges"
                                    id="bank_charges"
                                    class="form-control @error('bank_charges') is-invalid @enderror"
                                    x-model="formData.bank_charges"
                                    value="{{ old('bank_charges', '0.00') }}"
                                    placeholder="0.00"
                                >
                                @error('bank_charges')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Enter any bank charges that appear on the statement
                            </small>
                        </div>

                        <!-- Interest Earned -->
                        <div class="mb-4">
                            <label for="interest_earned" class="form-label fw-semibold">
                                Interest Earned
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">{{ tenant_currency() }}</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="interest_earned"
                                    id="interest_earned"
                                    class="form-control @error('interest_earned') is-invalid @enderror"
                                    x-model="formData.interest_earned"
                                    value="{{ old('interest_earned', '0.00') }}"
                                    placeholder="0.00"
                                >
                                @error('interest_earned')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Enter any interest credited on the statement
                            </small>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-semibold">
                                Notes/Comments
                            </label>
                            <textarea
                                name="notes"
                                id="notes"
                                class="form-control @error('notes') is-invalid @enderror"
                                rows="3"
                                placeholder="Add any notes or comments about this reconciliation..."
                            >{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-emerald-600 px-4" :disabled="submitting">
                                <span x-show="!submitting">
                                    <i class="fas fa-check me-2"></i>Create & Start Reconciliation
                                </span>
                                <span x-show="submitting">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Creating...
                                </span>
                            </button>
                            <a href="{{ route('tenant.banking.reconciliations.index') }}" class="btn btn-light px-4">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Help Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-emerald-50 border-0 py-3">
                    <h5 class="mb-0 text-emerald-700">
                        <i class="fas fa-question-circle me-2"></i>How to Reconcile
                    </h5>
                </div>
                <div class="card-body">
                    <div class="reconciliation-steps">
                        <div class="step mb-3">
                            <div class="d-flex align-items-start">
                                <div class="step-number bg-emerald-600 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
                                    1
                                </div>
                                <div>
                                    <h6 class="mb-1">Select Bank Account</h6>
                                    <p class="text-muted small mb-0">Choose the bank account you want to reconcile.</p>
                                </div>
                            </div>
                        </div>

                        <div class="step mb-3">
                            <div class="d-flex align-items-start">
                                <div class="step-number bg-emerald-600 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
                                    2
                                </div>
                                <div>
                                    <h6 class="mb-1">Enter Statement Period</h6>
                                    <p class="text-muted small mb-0">Specify the date range from your bank statement.</p>
                                </div>
                            </div>
                        </div>

                        <div class="step mb-3">
                            <div class="d-flex align-items-start">
                                <div class="step-number bg-emerald-600 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
                                    3
                                </div>
                                <div>
                                    <h6 class="mb-1">Enter Balances</h6>
                                    <p class="text-muted small mb-0">Input opening and closing balances from your statement.</p>
                                </div>
                            </div>
                        </div>

                        <div class="step mb-3">
                            <div class="d-flex align-items-start">
                                <div class="step-number bg-emerald-600 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
                                    4
                                </div>
                                <div>
                                    <h6 class="mb-1">Add Charges & Interest</h6>
                                    <p class="text-muted small mb-0">Include any bank charges or interest earned.</p>
                                </div>
                            </div>
                        </div>

                        <div class="step">
                            <div class="d-flex align-items-start">
                                <div class="step-number bg-emerald-600 text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; min-width: 32px;">
                                    5
                                </div>
                                <div>
                                    <h6 class="mb-1">Match Transactions</h6>
                                    <p class="text-muted small mb-0">On the next screen, match transactions from your books with the bank statement.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Balance Card (Shows when bank selected) -->
            <div class="card shadow-sm border-0" x-show="formData.bank_id" x-cloak>
                <div class="card-header bg-info-subtle border-0 py-3">
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-info-circle me-2"></i>Bank Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Current Book Balance</label>
                        <h4 class="mb-0 text-dark" x-text="formatCurrency(bankInfo.currentBalance)">-</h4>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Last Reconciled</label>
                        <p class="mb-0" x-text="bankInfo.lastReconciled || 'Never'">-</p>
                    </div>
                    <div class="alert alert-info mb-0 small">
                        <i class="fas fa-lightbulb me-1"></i>
                        <strong>Tip:</strong> The system will automatically load all unreconciled transactions for the selected period.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .btn-emerald-600 {
        background-color: #059669;
        border-color: #059669;
        color: white;
    }
    .btn-emerald-600:hover {
        background-color: #047857;
        border-color: #047857;
        color: white;
    }
    .btn-emerald-600:disabled {
        background-color: #6ee7b7;
        border-color: #6ee7b7;
    }
    .text-emerald-600 {
        color: #059669;
    }
    .text-emerald-700 {
        color: #047857;
    }
    .bg-emerald-50 {
        background-color: #f0fdf4;
    }
    .bg-emerald-600 {
        background-color: #059669;
    }
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function reconciliationForm() {
        return {
            submitting: false,
            formData: {
                bank_id: '{{ old('bank_id') }}',
                statement_start_date: '{{ old('statement_start_date') }}',
                statement_end_date: '{{ old('statement_end_date') }}',
                opening_balance: '{{ old('opening_balance') }}',
                closing_balance: '{{ old('closing_balance') }}',
                bank_charges: '{{ old('bank_charges', '0.00') }}',
                interest_earned: '{{ old('interest_earned', '0.00') }}'
            },
            bankInfo: {
                currentBalance: 0,
                lastReconciled: null
            },

            loadBankDetails() {
                if (!this.formData.bank_id) {
                    this.bankInfo = { currentBalance: 0, lastReconciled: null };
                    return;
                }

                // Get bank details from the selected option
                const selectElement = document.getElementById('bank_id');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const balanceText = selectedOption.text.match(/Balance: [^(]+\(([^)]+)\)/);

                if (balanceText) {
                    const balanceStr = balanceText[1].replace(/[^\d.-]/g, '');
                    this.bankInfo.currentBalance = parseFloat(balanceStr) || 0;
                }
            },

            formatCurrency(amount) {
                return '{{ tenant_currency() }}' + parseFloat(amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },

            handleSubmit(e) {
                // Validate dates
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
