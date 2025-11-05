{{-- Debit Note Entries Partial --}}
<div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="debitNoteEntries()">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Debit Note Entries</h3>
            <div class="text-sm text-gray-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Dr. Note (Increases Customer Balance)
                </span>
            </div>
        </div>
        <p class="mt-1 text-sm text-gray-600">
            Debit notes increase customer receivables. Used for additional charges, interest, or billing corrections.
        </p>
    </div>

    <div class="p-6 space-y-6">
        {{-- Customer Section --}}
        <div class="bg-yellow-100 border border-blue-200 rounded-lg p-4">
            <h4 class="text-md font-medium text-blue-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                Customer Account (Debit - Increases Receivable)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="dn_customer_account" class="block text-sm font-medium text-gray-700 mb-1">
                        Customer Account <span class="text-red-500">*</span>
                    </label>
                    <select id="dn_customer_account"
                            name="dn_customer_account_id"
                            x-model="customerAccountId"
                            @change="updateCustomerAccount()"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="">Select Customer Account</option>
                         @foreach($ledgerAccounts->filter(function($account) { return str_contains($account->code, 'CUST-'); }) as $account)
                            <option value="{{ $account->id }}" data-name="{{ $account->name }}" data-balance="{{ $account->closing_balance ?? 0 }}">
                                {{ $account->name }} ({{ $account->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="dn_customer_amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Debit Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                        <input type="number"
                               id="dn_customer_amount"
                               name="dn_customer_amount"
                               x-model="customerAmount"
                               @input="calculateTotals()"
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                </div>
            </div>

            <div x-show="customerAccountId" class="mt-3 p-3 bg-blue-100 rounded-md">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-blue-700">Current Balance:</span>
                    <span class="font-medium text-blue-900" x-text="'₦ ' + formatNumber(customerBalance)"></span>
                </div>
                <div class="flex justify-between items-center text-sm mt-1">
                    <span class="text-blue-700">After Debit Note:</span>
                    <span class="font-medium text-blue-900" x-text="'₦ ' + formatNumber(customerBalance + customerAmount)"></span>
                </div>
            </div>
        </div>

        {{-- Credit Entries Section --}}
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-md font-medium text-green-900 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    Additional Charge Accounts (Credit)
                </h4>
                <button type="button"
                        @click="addCreditEntry()"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Entry
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(entry, index) in creditEntries" :key="index">
                    <div class="grid grid-cols-12 gap-3 items-end p-3 bg-white border border-green-200 rounded-md">
                        {{-- Account Selection --}}
                        <div class="col-span-5">
                            <label :for="'dn_account_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                                Account <span class="text-red-500">*</span>
                            </label>
                            <select :id="'dn_account_' + index"
                                    :name="'credit_entries[' + index + '][account_id]'"
                                    x-model="entry.accountId"
                                    @change="updateEntryAccount(index)"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                    required>
                                <option value="">Select Account</option>
                                @foreach($ledgerAccounts->where('account_type', 'income') as $account)
                                    <option value="{{ $account->id }}" data-name="{{ $account->name }}" data-code="{{ $account->code }}">
                                        {{ $account->name }} ({{ $account->code }})
                                    </option>
                                @endforeach
                                @foreach($ledgerAccounts->where('account_type', 'revenue') as $account)
                                    <option value="{{ $account->id }}" data-name="{{ $account->name }}" data-code="{{ $account->code }}">
                                        {{ $account->name }} ({{ $account->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="col-span-4">
                            <label :for="'dn_description_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <input type="text"
                                   :id="'dn_description_' + index"
                                   :name="'credit_entries[' + index + '][description]'"
                                   x-model="entry.description"
                                   placeholder="Enter description"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm">
                        </div>

                        {{-- Amount --}}
                        <div class="col-span-2">
                            <label :for="'dn_amount_' + index" class="block text-sm font-medium text-gray-700 mb-1">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-gray-500 text-sm">₦</span>
                                <input type="number"
                                       :id="'dn_amount_' + index"
                                       :name="'credit_entries[' + index + '][amount]'"
                                       x-model="entry.amount"
                                       @input="calculateTotals()"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       class="block w-full pl-6 pr-2 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                       required>
                            </div>
                        </div>

                        {{-- Remove Button --}}
                        <div class="col-span-1">
                            <button type="button"
                                    @click="removeCreditEntry(index)"
                                    x-show="creditEntries.length > 1"
                                    class="w-full inline-flex justify-center items-center px-2 py-2 border border-red-300 rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                                </template>
            </div>
        </div>

        {{-- Totals Section --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-100 p-3 rounded-md">
                    <div class="text-sm text-blue-700">Total Debit</div>
                    <div class="text-lg font-semibold text-blue-900" x-text="'₦ ' + formatNumber(customerAmount)"></div>
                    <div class="text-xs text-blue-600">Customer Account</div>
                </div>

                <div class="bg-green-100 p-3 rounded-md">
                    <div class="text-sm text-green-700">Total Credit</div>
                    <div class="text-lg font-semibold text-green-900" x-text="'₦ ' + formatNumber(totalCreditAmount)"></div>
                    <div class="text-xs text-green-600">Additional Charges</div>
                </div>

                <div class="p-3 rounded-md" :class="isBalanced ? 'bg-green-100' : 'bg-red-100'">
                    <div class="text-sm" :class="isBalanced ? 'text-green-700' : 'text-red-700'">Difference</div>
                    <div class="text-lg font-semibold" :class="isBalanced ? 'text-green-900' : 'text-red-900'" x-text="'₦ ' + formatNumber(Math.abs(customerAmount - totalCreditAmount))"></div>
                    <div class="text-xs" :class="isBalanced ? 'text-green-600' : 'text-red-600'" x-text="isBalanced ? 'Balanced' : 'Not Balanced'"></div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                <span x-show="!isBalanced" class="text-red-600">⚠ Please ensure debit and credit amounts are balanced</span>
                <span x-show="isBalanced" class="text-green-600">✓ Voucher is balanced</span>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </a>
                <button type="submit"
                        name="action"
                        value="save"
                        :disabled="!isBalanced || customerAmount <= 0"
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    Save as Draft
                </button>
                <button type="submit"
                        name="action"
                        value="save_and_post"
                        :disabled="!isBalanced || customerAmount <= 0"
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save & Post
                </button>
            </div>
        </div>
    </div>
</div>

<script>
@push('scripts')
<script>
function debitNoteEntries() {
    return {
        // Customer account data
        customerAccountId: '',
        customerAccountName: '',
        customerBalance: 0,
        customerAmount: 0,

        // Credit entries
        creditEntries: [
            {
                accountId: '',
                accountName: '',
                description: '',
                amount: 0
            }
        ],

        // Calculated totals
        totalCreditAmount: 0,
        isBalanced: false,

        init() {
            this.calculateTotals();
            console.log('✅ Debit Note entries initialized');
        },

        // Customer account methods
        updateCustomerAccount() {
            if (this.customerAccountId) {
                const selectEl = document.getElementById('dn_customer_account');
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                this.customerAccountName = selectedOption.dataset.name || '';
                this.customerBalance = parseFloat(selectedOption.dataset.balance || 0);
            } else {
                this.customerAccountName = '';
                this.customerBalance = 0;
            }
            this.calculateTotals();
        },

        // Credit entries methods
        addCreditEntry() {
            this.creditEntries.push({
                accountId: '',
                accountName: '',
                description: '',
                amount: 0
            });
        },

        removeCreditEntry(index) {
            if (this.creditEntries.length > 1) {
                this.creditEntries.splice(index, 1);
                this.calculateTotals();
            }
        },

        updateEntryAccount(index) {
            const entry = this.creditEntries[index];
            if (entry.accountId) {
                const selectEl = document.getElementById(`dn_account_${index}`);
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                entry.accountName = selectedOption.dataset.name || '';
            } else {
                entry.accountName = '';
            }
        },

        // Calculation methods
        calculateTotals() {
            // Calculate total credit amount
            this.totalCreditAmount = this.creditEntries.reduce((sum, entry) => {
                return sum + (parseFloat(entry.amount) || 0);
            }, 0);

            // Check if balanced (customer amount should equal total credit amount)
            this.isBalanced = Math.abs(parseFloat(this.customerAmount) - this.totalCreditAmount) < 0.01
                            && parseFloat(this.customerAmount) > 0;
        },

        // Utility methods
        formatNumber(number) {
            return new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number || 0);
        }
    }
}
</script>
@endpush
