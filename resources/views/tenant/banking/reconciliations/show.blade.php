@extends('layouts.tenant')

@section('title', 'Bank Reconciliation - ' . $reconciliation->bank->bank_name)
@section('page-title', 'Bank Reconciliation')
@section('page-description', $reconciliation->bank->bank_name . ' - ' . $reconciliation->bank->account_number)

@section('content')
<div class="min-h-screen bg-gray-50 py-8" x-data="reconciliationShow({{ $reconciliation->id }})">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Bank Reconciliation
                    <span class="ml-3 px-3 py-1 text-sm rounded-full {{ $reconciliation->status === 'completed' ? 'bg-green-100 text-green-800' : ($reconciliation->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $reconciliation->status)) }}
                    </span>
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ $reconciliation->bank->bank_name }} - {{ $reconciliation->bank->account_number }}
                    | {{ \Carbon\Carbon::parse($reconciliation->statement_start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($reconciliation->statement_end_date)->format('M d, Y') }}
                </p>
            </div>
            <div class="flex space-x-3">
                @if($reconciliation->canBeEdited())
                    <button type="button" 
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition-colors"
                            @click="completeReconciliation" 
                            :disabled="!isBalanced || processing">
                        <span x-show="!processing">
                            <i class="fas fa-check-circle mr-2"></i>Complete
                        </span>
                        <span x-show="processing">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                        </span>
                    </button>
                    <button type="button" 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors"
                            @click="cancelReconciliation" 
                            :disabled="processing">
                        <i class="fas fa-ban mr-2"></i>Cancel
                    </button>
                @endif
                <a href="{{ route('tenant.banking.reconciliations.index', tenant('slug')) }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-4">
                <div class="text-sm text-gray-600 mb-1">Bank Statement Balance</div>
                <div class="text-lg font-bold text-gray-900">{{ tenant_currency() }}{{ number_format($reconciliation->closing_balance_per_bank, 2) }}</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-cyan-500 p-4">
                <div class="text-sm text-gray-600 mb-1">Book Balance</div>
                <div class="text-lg font-bold text-gray-900" x-text="formatCurrency(stats.bookBalance)">
                    {{ tenant_currency() }}{{ number_format($reconciliation->closing_balance_per_books, 2) }}
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border-l-4 p-4" :class="difference === 0 ? 'border-green-500' : 'border-yellow-500'">
                <div class="text-sm text-gray-600 mb-1">Difference</div>
                <div class="text-lg font-bold" :class="difference === 0 ? 'text-green-600' : 'text-yellow-600'" x-text="formatCurrency(difference)">
                    {{ tenant_currency() }}{{ number_format($reconciliation->difference, 2) }}
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-emerald-500 p-4">
                <div class="text-sm text-gray-600 mb-1">Progress</div>
                <div class="text-lg font-bold text-emerald-600" x-text="stats.progressPercentage + '%'">
                    {{ number_format($reconciliation->getProgressPercentage(), 0) }}%
                </div>
            </div>
        </div>

        <!-- Balance Alert -->
        <div class="rounded-lg p-4 mb-6" :class="isBalanced ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200'">
            <div class="flex items-center">
                <i class="text-3xl mr-4" :class="isBalanced ? 'fas fa-check-circle text-green-600' : 'fas fa-exclamation-triangle text-yellow-600'"></i>
                <div>
                    <h5 class="font-semibold mb-1" :class="isBalanced ? 'text-green-800' : 'text-yellow-800'" x-text="isBalanced ? 'Reconciliation Balanced!' : 'Not Yet Balanced'"></h5>
                    <p class="text-sm" :class="isBalanced ? 'text-green-700' : 'text-yellow-700'" x-text="isBalanced ? 'The bank statement and book balances match. You can now complete the reconciliation.' : 'Continue matching transactions until the difference is zero.'"></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Transactions Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button @click="activeTab = 'uncleared'" 
                                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors"
                                    :class="activeTab === 'uncleared' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                                <i class="fas fa-clock mr-2"></i>Uncleared
                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800" x-text="unclearedItems.length"></span>
                            </button>
                            <button @click="activeTab = 'cleared'" 
                                    class="px-6 py-3 text-sm font-medium border-b-2 transition-colors"
                                    :class="activeTab === 'cleared' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                                <i class="fas fa-check mr-2"></i>Cleared
                                <span class="ml-2 px-2 py-1 text-xs rounded-full bg-green-100 text-green-800" x-text="clearedItems.length"></span>
                            </button>
                        </nav>
                    </div>

                    <!-- Uncleared Tab -->
                    <div x-show="activeTab === 'uncleared'">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">Uncleared Transactions</h3>
                            <button type="button" 
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-sm transition-colors"
                                    @click="markAllAsCleared" 
                                    :disabled="!unclearedItems.length || processing" 
                                    x-show="unclearedItems.length > 0">
                                <i class="fas fa-check-double mr-1"></i>Mark All
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="item in unclearedItems" :key="item.id">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm text-gray-900" x-text="formatDate(item.transaction_date)"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900" x-text="item.description"></td>
                                            <td class="px-4 py-3 text-sm text-gray-600" x-text="item.reference_number || '-'"></td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900" x-text="item.debit_amount > 0 ? formatCurrency(item.debit_amount) : '-'"></td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900" x-text="item.credit_amount > 0 ? formatCurrency(item.credit_amount) : '-'"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button"
                                                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-xs transition-colors"
                                                        @click="markAsCleared(item.id)"
                                                        :disabled="processing">
                                                    <i class="fas fa-check"></i> Clear
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="unclearedItems.length === 0">
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                                            <p>All transactions have been cleared!</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Cleared Tab -->
                    <div x-show="activeTab === 'cleared'">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-semibold text-gray-900">Cleared Transactions</h3>
                            <button type="button" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm transition-colors"
                                    @click="markAllAsUncleared" 
                                    :disabled="!clearedItems.length || processing" 
                                    x-show="clearedItems.length > 0">
                                <i class="fas fa-undo mr-1"></i>Unmark All
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <template x-for="item in clearedItems" :key="item.id">
                                        <tr class="bg-green-50 hover:bg-green-100">
                                            <td class="px-4 py-3 text-sm text-gray-900" x-text="formatDate(item.transaction_date)"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900" x-text="item.description"></td>
                                            <td class="px-4 py-3 text-sm text-gray-600" x-text="item.reference_number || '-'"></td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900" x-text="item.debit_amount > 0 ? formatCurrency(item.debit_amount) : '-'"></td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-900" x-text="item.credit_amount > 0 ? formatCurrency(item.credit_amount) : '-'"></td>
                                            <td class="px-4 py-3 text-center">
                                                <button type="button"
                                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs transition-colors"
                                                        @click="markAsUncleared(item.id)"
                                                        :disabled="processing">
                                                    <i class="fas fa-undo"></i> Unclear
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="clearedItems.length === 0">
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-clock text-4xl text-gray-400 mb-2"></i>
                                            <p>No transactions have been cleared yet.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-emerald-50">
                        <h3 class="text-lg font-semibold text-emerald-700">
                            <i class="fas fa-calculator mr-2"></i>Summary
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="pb-3 border-b border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Opening Balance</div>
                            <div class="font-semibold text-gray-900">{{ tenant_currency() }}{{ number_format($reconciliation->opening_balance, 2) }}</div>
                        </div>
                        <div class="pb-3 border-b border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Closing Balance (Statement)</div>
                            <div class="font-semibold text-gray-900">{{ tenant_currency() }}{{ number_format($reconciliation->closing_balance_per_bank, 2) }}</div>
                        </div>
                        <div class="pb-3 border-b border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Bank Charges</div>
                            <div class="font-semibold text-red-600">{{ tenant_currency() }}{{ number_format($reconciliation->bank_charges, 2) }}</div>
                        </div>
                        <div class="pb-3 border-b border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Interest Earned</div>
                            <div class="font-semibold text-green-600">{{ tenant_currency() }}{{ number_format($reconciliation->interest_earned, 2) }}</div>
                        </div>
                        <div class="pb-3 border-b border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Total Items</div>
                            <div class="font-semibold text-gray-900" x-text="stats.totalItems">{{ $reconciliation->items->count() }}</div>
                        </div>
                        <div class="pb-3 border-b border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Cleared Items</div>
                            <div class="font-semibold text-green-600" x-text="stats.clearedItems">{{ $reconciliation->items->where('status', 'cleared')->count() }}</div>
                        </div>
                        <div class="pb-3 border-b border-gray-200">
                            <div class="text-sm text-gray-600 mb-1">Uncleared Items</div>
                            <div class="font-semibold text-yellow-600" x-text="stats.unclearedItems">{{ $reconciliation->items->where('status', 'uncleared')->count() }}</div>
                        </div>
                        @if($reconciliation->notes)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                            <strong>Notes:</strong><br>{{ $reconciliation->notes }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Completed Status -->
                @if($reconciliation->status === 'completed')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                        <h3 class="text-lg font-semibold text-green-700">
                            <i class="fas fa-check-circle mr-2"></i>Completed
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Completed By</div>
                            <div class="text-gray-900">{{ $reconciliation->completedBy->name ?? 'System' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Completed At</div>
                            <div class="text-gray-900">{{ $reconciliation->completed_at ? \Carbon\Carbon::parse($reconciliation->completed_at)->format('M d, Y h:i A') : '-' }}</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            This reconciliation has been completed and cannot be modified.
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function reconciliationShow(reconciliationId) {
        return {
            reconciliationId: reconciliationId,
            processing: false,
            activeTab: 'uncleared',
            items: @json($reconciliation->items),
            stats: {
                bookBalance: {{ $reconciliation->closing_balance_per_books }},
                totalItems: {{ $reconciliation->items->count() }},
                clearedItems: {{ $reconciliation->items->where('status', 'cleared')->count() }},
                unclearedItems: {{ $reconciliation->items->where('status', 'uncleared')->count() }},
                progressPercentage: {{ number_format($reconciliation->getProgressPercentage(), 0) }}
            },
            bankStatementBalance: {{ $reconciliation->closing_balance_per_bank }},

            get clearedItems() {
                return this.items.filter(item => item.status === 'cleared');
            },

            get unclearedItems() {
                return this.items.filter(item => item.status === 'uncleared');
            },

            get difference() {
                return this.bankStatementBalance - this.stats.bookBalance;
            },

            get isBalanced() {
                return Math.abs(this.difference) < 0.01;
            },

            async markAsCleared(itemId) {
                await this.updateItemStatus(itemId, 'cleared');
            },

            async markAsUncleared(itemId) {
                await this.updateItemStatus(itemId, 'uncleared');
            },

            async markAllAsCleared() {
                if (!confirm('Mark all uncleared transactions as cleared?')) return;
                const promises = this.unclearedItems.map(item => this.updateItemStatus(item.id, 'cleared', false));
                await Promise.all(promises);
                this.refreshStats();
            },

            async markAllAsUncleared() {
                if (!confirm('Unmark all cleared transactions?')) return;
                const promises = this.clearedItems.map(item => this.updateItemStatus(item.id, 'uncleared', false));
                await Promise.all(promises);
                this.refreshStats();
            },

            async updateItemStatus(itemId, status, refreshStats = true) {
                this.processing = true;
                try {
                    const response = await fetch(`{{ route('tenant.banking.reconciliations.update-item', ['tenant' => tenant('slug'), 'reconciliation' => '__ID__']) }}`.replace('__ID__', this.reconciliationId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ item_id: itemId, status: status })
                    });

                    if (!response.ok) throw new Error('Failed to update transaction status');

                    const item = this.items.find(i => i.id === itemId);
                    if (item) item.status = status;

                    if (refreshStats) this.refreshStats();
                } catch (error) {
                    alert(error.message || 'Failed to update transaction status');
                } finally {
                    this.processing = false;
                }
            },

            refreshStats() {
                this.stats.clearedItems = this.clearedItems.length;
                this.stats.unclearedItems = this.unclearedItems.length;
                this.stats.progressPercentage = this.stats.totalItems > 0 ? Math.round((this.stats.clearedItems / this.stats.totalItems) * 100) : 0;
            },

            async completeReconciliation() {
                if (!this.isBalanced) {
                    alert('The reconciliation must be balanced before it can be completed.');
                    return;
                }
                if (!confirm('Complete this reconciliation? This action cannot be undone.')) return;

                this.processing = true;
                try {
                    const response = await fetch(`{{ route('tenant.banking.reconciliations.complete', ['tenant' => tenant('slug'), 'reconciliation' => '__ID__']) }}`.replace('__ID__', this.reconciliationId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to complete reconciliation');
                    alert('Reconciliation completed successfully!');
                    window.location.reload();
                } catch (error) {
                    alert(error.message);
                    this.processing = false;
                }
            },

            async cancelReconciliation() {
                if (!confirm('Cancel this reconciliation? This action cannot be undone.')) return;

                this.processing = true;
                try {
                    const response = await fetch(`{{ route('tenant.banking.reconciliations.cancel', ['tenant' => tenant('slug'), 'reconciliation' => '__ID__']) }}`.replace('__ID__', this.reconciliationId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to cancel reconciliation');
                    window.location.href = '{{ route('tenant.banking.reconciliations.index', tenant('slug')) }}';
                } catch (error) {
                    alert(error.message);
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
                return new Date(dateString).toLocaleDateString('en-US', {
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
