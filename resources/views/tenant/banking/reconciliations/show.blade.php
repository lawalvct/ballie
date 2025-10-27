@extends('layouts.tenant')

@section('title', 'Bank Reconciliation - ' . $reconciliation->bank->bank_name)

@section('content')
<div class="container-fluid px-4" x-data="reconciliationShow({{ $reconciliation->id }})">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                Bank Reconciliation
                <span class="badge {{ $reconciliation->status === 'completed' ? 'bg-success' : ($reconciliation->status === 'in_progress' ? 'bg-primary' : 'bg-secondary') }}">
                    {{ ucfirst(str_replace('_', ' ', $reconciliation->status)) }}
                </span>
            </h1>
            <p class="text-muted mb-0">
                {{ $reconciliation->bank->bank_name }} - {{ $reconciliation->bank->account_number }}
                | {{ \Carbon\Carbon::parse($reconciliation->statement_start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($reconciliation->statement_end_date)->format('M d, Y') }}
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($reconciliation->canBeEdited())
                <button type="button" class="btn btn-success" @click="completeReconciliation" :disabled="!isBalanced || processing">
                    <span x-show="!processing">
                        <i class="fas fa-check-circle me-1"></i>Complete Reconciliation
                    </span>
                    <span x-show="processing">
                        <i class="fas fa-spinner fa-spin me-1"></i>Processing...
                    </span>
                </button>
                <button type="button" class="btn btn-warning" @click="cancelReconciliation" :disabled="processing">
                    <i class="fas fa-ban me-1"></i>Cancel
                </button>
            @endif
            <a href="{{ route('tenant.banking.reconciliations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Bank Statement Balance</div>
                    <h4 class="mb-0 text-dark">{{ tenant_currency() }}{{ number_format($reconciliation->closing_balance, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-info border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Book Balance</div>
                    <h4 class="mb-0 text-dark" x-text="formatCurrency(stats.bookBalance)">
                        {{ tenant_currency() }}{{ number_format($reconciliation->book_balance, 2) }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-4" :class="difference === 0 ? 'border-success' : 'border-warning'">
                <div class="card-body">
                    <div class="text-muted small mb-1">Difference</div>
                    <h4 class="mb-0" :class="difference === 0 ? 'text-success' : 'text-warning'" x-text="formatCurrency(difference)">
                        {{ tenant_currency() }}{{ number_format($reconciliation->difference, 2) }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-emerald-600 border-4">
                <div class="card-body">
                    <div class="text-muted small mb-1">Progress</div>
                    <h4 class="mb-0 text-emerald-600" x-text="stats.progressPercentage + '%'">
                        {{ number_format($reconciliation->getProgressPercentage(), 0) }}%
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Alert -->
    <div class="alert" :class="isBalanced ? 'alert-success' : 'alert-warning'" x-show="true">
        <div class="d-flex align-items-center">
            <i class="fas fa-2x me-3" :class="isBalanced ? 'fa-check-circle' : 'fa-exclamation-triangle'"></i>
            <div>
                <h5 class="alert-heading mb-1" x-text="isBalanced ? 'Reconciliation Balanced!' : 'Not Yet Balanced'"></h5>
                <p class="mb-0" x-text="isBalanced ? 'The bank statement and book balances match. You can now complete the reconciliation.' : 'Continue matching transactions until the difference is zero.'"></p>
            </div>
        </div>
    </div>

    <!-- Reconciliation Details Card -->
    <div class="row">
        <div class="col-lg-9">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#uncleared-tab" role="tab">
                        <i class="fas fa-clock me-1"></i>Uncleared Transactions
                        <span class="badge bg-warning ms-1" x-text="unclearedItems.length">{{ $reconciliation->items->where('cleared', false)->count() }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#cleared-tab" role="tab">
                        <i class="fas fa-check me-1"></i>Cleared Transactions
                        <span class="badge bg-success ms-1" x-text="clearedItems.length">{{ $reconciliation->items->where('cleared', true)->count() }}</span>
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Uncleared Transactions Tab -->
                <div class="tab-pane fade show active" id="uncleared-tab" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-warning">
                                    <i class="fas fa-clock me-2"></i>Uncleared Transactions
                                </h5>
                                <button type="button" class="btn btn-sm btn-success" @click="markAllAsCleared" :disabled="!unclearedItems.length || processing" x-show="unclearedItems.length > 0">
                                    <i class="fas fa-check-double me-1"></i>Mark All as Cleared
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50"></th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Reference</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                            <th width="100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="item in unclearedItems" :key="item.id">
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input" :checked="false" disabled>
                                                </td>
                                                <td x-text="formatDate(item.transaction_date)"></td>
                                                <td x-text="item.description"></td>
                                                <td x-text="item.reference_number || '-'"></td>
                                                <td class="text-end" x-text="item.transaction_type === 'debit' ? formatCurrency(item.amount) : '-'"></td>
                                                <td class="text-end" x-text="item.transaction_type === 'credit' ? formatCurrency(item.amount) : '-'"></td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-success"
                                                        @click="markAsCleared(item.id)"
                                                        :disabled="processing"
                                                    >
                                                        <i class="fas fa-check"></i> Clear
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="unclearedItems.length === 0">
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                                <p class="mb-0">All transactions have been cleared!</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cleared Transactions Tab -->
                <div class="tab-pane fade" id="cleared-tab" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-success">
                                    <i class="fas fa-check me-2"></i>Cleared Transactions
                                </h5>
                                <button type="button" class="btn btn-sm btn-warning" @click="markAllAsUncleared" :disabled="!clearedItems.length || processing" x-show="clearedItems.length > 0">
                                    <i class="fas fa-undo me-1"></i>Unmark All
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50"></th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Reference</th>
                                            <th class="text-end">Debit</th>
                                            <th class="text-end">Credit</th>
                                            <th width="100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="item in clearedItems" :key="item.id">
                                            <tr class="table-success">
                                                <td>
                                                    <input type="checkbox" class="form-check-input" :checked="true" disabled>
                                                </td>
                                                <td x-text="formatDate(item.transaction_date)"></td>
                                                <td x-text="item.description"></td>
                                                <td x-text="item.reference_number || '-'"></td>
                                                <td class="text-end" x-text="item.transaction_type === 'debit' ? formatCurrency(item.amount) : '-'"></td>
                                                <td class="text-end" x-text="item.transaction_type === 'credit' ? formatCurrency(item.amount) : '-'"></td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-warning"
                                                        @click="markAsUncleared(item.id)"
                                                        :disabled="processing"
                                                    >
                                                        <i class="fas fa-undo"></i> Unclear
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="clearedItems.length === 0">
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="fas fa-clock fa-3x mb-3"></i>
                                                <p class="mb-0">No transactions have been cleared yet.</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <!-- Summary Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-emerald-50 border-0 py-3">
                    <h5 class="mb-0 text-emerald-700">
                        <i class="fas fa-calculator me-2"></i>Reconciliation Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-1">Opening Balance</label>
                        <div class="fw-semibold">{{ tenant_currency() }}{{ number_format($reconciliation->opening_balance, 2) }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-1">Closing Balance (Statement)</label>
                        <div class="fw-semibold">{{ tenant_currency() }}{{ number_format($reconciliation->closing_balance, 2) }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-1">Bank Charges</label>
                        <div class="fw-semibold text-danger">{{ tenant_currency() }}{{ number_format($reconciliation->bank_charges, 2) }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-1">Interest Earned</label>
                        <div class="fw-semibold text-success">{{ tenant_currency() }}{{ number_format($reconciliation->interest_earned, 2) }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-1">Total Items</label>
                        <div class="fw-semibold" x-text="stats.totalItems">{{ $reconciliation->items->count() }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-1">Cleared Items</label>
                        <div class="fw-semibold text-success" x-text="stats.clearedItems">{{ $reconciliation->cleared_items_count }}</div>
                    </div>
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="text-muted small mb-1">Uncleared Items</label>
                        <div class="fw-semibold text-warning" x-text="stats.unclearedItems">{{ $reconciliation->uncleared_items_count }}</div>
                    </div>

                    @if($reconciliation->notes)
                    <div class="alert alert-info small mb-0">
                        <strong>Notes:</strong><br>
                        {{ $reconciliation->notes }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status Timeline (if completed) -->
            @if($reconciliation->status === 'completed')
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success-subtle border-0 py-3">
                    <h5 class="mb-0 text-success">
                        <i class="fas fa-check-circle me-2"></i>Completed
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Completed By</label>
                        <div>{{ $reconciliation->completedBy->name ?? 'System' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small mb-1">Completed At</label>
                        <div>{{ $reconciliation->completed_at ? \Carbon\Carbon::parse($reconciliation->completed_at)->format('M d, Y h:i A') : '-' }}</div>
                    </div>
                    <div class="alert alert-success small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        This reconciliation has been completed and cannot be modified.
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .border-emerald-600 {
        border-color: #059669 !important;
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
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        background-color: #fff;
        border-bottom: 3px solid #059669;
        color: #059669;
        font-weight: 600;
    }
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function reconciliationShow(reconciliationId) {
        return {
            reconciliationId: reconciliationId,
            processing: false,
            items: @json($reconciliation->items),
            stats: {
                bookBalance: {{ $reconciliation->book_balance }},
                totalItems: {{ $reconciliation->items->count() }},
                clearedItems: {{ $reconciliation->cleared_items_count }},
                unclearedItems: {{ $reconciliation->uncleared_items_count }},
                progressPercentage: {{ number_format($reconciliation->getProgressPercentage(), 0) }}
            },
            bankStatementBalance: {{ $reconciliation->closing_balance }},

            get clearedItems() {
                return this.items.filter(item => item.cleared);
            },

            get unclearedItems() {
                return this.items.filter(item => !item.cleared);
            },

            get difference() {
                return this.bankStatementBalance - this.stats.bookBalance;
            },

            get isBalanced() {
                return Math.abs(this.difference) < 0.01; // Account for floating point precision
            },

            async markAsCleared(itemId) {
                await this.updateItemStatus(itemId, true);
            },

            async markAsUncleared(itemId) {
                await this.updateItemStatus(itemId, false);
            },

            async markAllAsCleared() {
                if (!confirm('Are you sure you want to mark all uncleared transactions as cleared?')) {
                    return;
                }

                const promises = this.unclearedItems.map(item =>
                    this.updateItemStatus(item.id, true, false)
                );
                await Promise.all(promises);
                this.refreshStats();
            },

            async markAllAsUncleared() {
                if (!confirm('Are you sure you want to unmark all cleared transactions?')) {
                    return;
                }

                const promises = this.clearedItems.map(item =>
                    this.updateItemStatus(item.id, false, false)
                );
                await Promise.all(promises);
                this.refreshStats();
            },

            async updateItemStatus(itemId, cleared, refreshStats = true) {
                this.processing = true;

                try {
                    const response = await fetch(`/banking/reconciliations/${this.reconciliationId}/update-item`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            item_id: itemId,
                            cleared: cleared
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to update transaction status');
                    }

                    // Update local item
                    const item = this.items.find(i => i.id === itemId);
                    if (item) {
                        item.cleared = cleared;
                    }

                    if (refreshStats) {
                        this.refreshStats();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'Failed to update transaction status. Please try again.');
                } finally {
                    this.processing = false;
                }
            },

            refreshStats() {
                this.stats.clearedItems = this.clearedItems.length;
                this.stats.unclearedItems = this.unclearedItems.length;
                this.stats.progressPercentage = this.stats.totalItems > 0
                    ? Math.round((this.stats.clearedItems / this.stats.totalItems) * 100)
                    : 0;
            },

            async completeReconciliation() {
                if (!this.isBalanced) {
                    alert('The reconciliation must be balanced before it can be completed.');
                    return;
                }

                if (!confirm('Are you sure you want to complete this reconciliation? This action cannot be undone.')) {
                    return;
                }

                this.processing = true;

                try {
                    const response = await fetch(`/banking/reconciliations/${this.reconciliationId}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to complete reconciliation');
                    }

                    alert('Reconciliation completed successfully!');
                    window.location.reload();
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'Failed to complete reconciliation. Please try again.');
                    this.processing = false;
                }
            },

            async cancelReconciliation() {
                if (!confirm('Are you sure you want to cancel this reconciliation? This action cannot be undone.')) {
                    return;
                }

                this.processing = true;

                try {
                    const response = await fetch(`/banking/reconciliations/${this.reconciliationId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Failed to cancel reconciliation');
                    }

                    alert('Reconciliation cancelled successfully!');
                    window.location.href = '/banking/reconciliations';
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'Failed to cancel reconciliation. Please try again.');
                    this.processing = false;
                }
            },

            formatCurrency(amount) {
                return '{{ tenant_currency() }}' + parseFloat(amount).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },

            formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });
            }
        }
    }
</script>
@endpush
@endsection
