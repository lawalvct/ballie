{{-- Debit Note Entries Partial - Tally ERP Style --}}
<div x-data="debitNoteEntries()" class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Debit Note Entries</h3>
            <div class="text-sm text-gray-500">
                Total: <span class="font-medium text-gray-900" x-text="formatCurrency(totalAmount)"></span>
            </div>
        </div>
        <p class="mt-1 text-sm text-gray-600">
            Debit note increases customer receivables (additional charges, interest, billing corrections)
        </p>
    </div>

    <div class="p-6 space-y-6">
        {{-- Customer/Party Selection (Auto-calculated Debit Amount) --}}
        <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-4 border border-orange-200">
            <div class="flex items-center mb-3">
                <svg class="h-5 w-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <h4 class="text-sm font-semibold text-orange-900">Customer Account (Debit)</h4>
                <span class="ml-2 text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full">Auto-calculated</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="customer_account_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Customer/Party <span class="text-red-500">*</span>
                    </label>
                    <select id="customer_account_id"
                            name="customer_account_id"
                            x-model="customerAccountId"
                            @change="updateCustomerAccount()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            required>
                        <option value="">Select Customer/Party</option>
                        @foreach($ledgerAccounts->where('account_group_id', 4) as $account)
                            <option value="{{ $account->id }}"
                                    data-name="{{ $account->name }}"
                                    {{ old('customer_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Debit Amount</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                        <input type="text"
                               x-model="formatNumber(totalAmount)"
                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700 cursor-not-allowed"
                               readonly>
                    </div>
                </div>
            </div>
        </div>

        {{-- Debit Note Entries (Credit Accounts) --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-900 flex items-center">
                    <svg class="h-4 w-4 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Additional Charges (Credit)
                </h4>
                <button type="button"
                        @click="addEntry()"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Entry
                </button>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <template x-for="(entry, index) in entries" :key="entry.id">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-3 p-3 bg-white rounded-lg border border-gray-200">
                        {{-- Account Selection --}}
                        <div class="md:col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Income/Charge Account <span class="text-red-500">*</span>
                            </label>
                            <select x-model="entry.account_id"
                                    @change="updateEntry(index)"
                                    :name="'entries[' + index + '][account_id]'"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    required>
                                <option value="">Select Account</option>
                                @foreach($ledgerAccounts->whereIn('account_group_id', [1, 3]) as $account)
                                    <option value="{{ $account->id }}"
                                            data-name="{{ $account->name }}">
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text"
                                   x-model="entry.description"
                                   :name="'entries[' + index + '][description]'"
                                   placeholder="Additional charge details..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        {{-- Credit Amount --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Credit Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                                <input type="number"
                                       x-model.number="entry.amount"
                                       @input="updateTotals()"
                                       :name="'entries[' + index + '][amount]'"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                       required>
                            </div>
                        </div>

                        {{-- Remove Button --}}
                        <div class="md:col-span-1 flex items-end">
                            <button type="button"
                                    @click="removeEntry(index)"
                                    x-show="entries.length > 1"
                                    class="w-full px-2 py-2 text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="h-4 w-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Hidden inputs for form submission --}}
                        <input type="hidden" :name="'entries[' + index + '][type]'" value="credit">
                        <input type="hidden" :name="'entries[' + index + '][account_name]'" x-model="entry.account_name">
                    </div>
                </template>

                <div x-show="entries.length === 0" class="text-center py-8 text-gray-500">
                    <svg class="h-8 w-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <p>No additional charges added yet.</p>
                    <button type="button"
                            @click="addEntry()"
                            class="mt-2 text-purple-600 hover:text-purple-700 font-medium">
                        Add your first charge
                    </button>
                </div>
            </div>
        </div>

        {{-- Summary Card --}}
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="text-sm text-gray-600">Total Entries</div>
                    <div class="text-lg font-semibold text-gray-900" x-text="entries.length"></div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-600">Total Debit</div>
                    <div class="text-lg font-semibold text-orange-600" x-text="formatCurrency(totalAmount)"></div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-600">Total Credit</div>
                    <div class="text-lg font-semibold text-purple-600" x-text="formatCurrency(totalAmount)"></div>
                </div>
            </div>

            <div class="mt-3 pt-3 border-t border-gray-300">
                <div class="flex items-center justify-center">
                    <span class="text-sm text-gray-600 mr-2">Balance:</span>
                    <span class="font-semibold"
                          :class="balanceAmount === 0 ? 'text-green-600' : 'text-red-600'"
                          x-text="balanceAmount === 0 ? 'Balanced ✓' : 'Unbalanced (' + formatCurrency(Math.abs(balanceAmount)) + ')'">
                    </span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <button type="button"
                    onclick="window.location.href='{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}'"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Cancel
            </button>

            <div class="space-x-3">
                <button type="button"
                        @click="saveDraft()"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Save as Draft
                </button>

                <button type="submit"
                        :disabled="!isFormValid"
                        :class="isFormValid ? 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' : 'bg-gray-400 cursor-not-allowed'"
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Debit Note
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden Customer Account Entry for Form Submission --}}
    <input type="hidden" name="customer_entry[account_id]" x-model="customerAccountId">
    <input type="hidden" name="customer_entry[account_name]" x-model="customerAccountName">
    <input type="hidden" name="customer_entry[amount]" x-model="totalAmount">
    <input type="hidden" name="customer_entry[type]" value="debit">
    <input type="hidden" name="customer_entry[description]" value="Debit Note">
</div>

<script>
function debitNoteEntries() {
    return {
        entries: [
            {
                id: Date.now(),
                account_id: '',
                account_name: '',
                description: '',
                amount: 0
            }
        ],
        customerAccountId: '{{ old('customer_account_id', '') }}',
        customerAccountName: '',
        totalAmount: 0,
        balanceAmount: 0,

        get isFormValid() {
            return this.customerAccountId &&
                   this.entries.length > 0 &&
                   this.entries.every(entry => entry.account_id && entry.amount > 0) &&
                   this.balanceAmount === 0;
        },

        init() {
            this.updateTotals();
            console.log('✅ Debit Note entries initialized');
        },

        addEntry() {
            this.entries.push({
                id: Date.now() + Math.random(),
                account_id: '',
                account_name: '',
                description: '',
                amount: 0
            });
        },

        removeEntry(index) {
            if (this.entries.length > 1) {
                this.entries.splice(index, 1);
                this.updateTotals();
            }
        },

        updateEntry(index) {
            const entry = this.entries[index];
            const select = document.querySelector(`select[name="entries[${index}][account_id]"]`);
            if (select && entry.account_id) {
                const option = select.querySelector(`option[value="${entry.account_id}"]`);
                entry.account_name = option ? option.dataset.name : '';
            }
            this.updateTotals();
        },

        updateCustomerAccount() {
            const select = document.getElementById('customer_account_id');
            if (select && this.customerAccountId) {
                const option = select.querySelector(`option[value="${this.customerAccountId}"]`);
                this.customerAccountName = option ? option.dataset.name : '';
            }
        },

        updateTotals() {
            // Calculate total credit amount
            this.totalAmount = this.entries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.amount) || 0);
            }, 0);

            // For debit notes, credits should equal debits (customer debit = total credits)
            this.balanceAmount = 0; // Always balanced in this structure
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('en-NG', {
                style: 'currency',
                currency: 'NGN',
                minimumFractionDigits: 2
            }).format(amount || 0);
        },

        formatNumber(amount) {
            return new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount || 0);
        },

        saveDraft() {
            // Add draft logic here
            console.log('Saving debit note as draft...');
        }
    }
}
</script>
