{{-- Contra Voucher Entries Partial --}}
<div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="contraEntries()">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Contra Voucher Entries</h3>
            <div class="text-sm text-gray-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    Bank/Cash Transfer
                </span>
            </div>
        </div>
        <p class="mt-1 text-sm text-gray-600">
            Contra vouchers record transfers between bank and cash accounts. No effect on profit/loss.
        </p>
    </div>

    <div class="p-6 space-y-6">
        {{-- From Account Section (Credit Side) --}}
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h4 class="text-md font-medium text-red-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" clip-rule="evenodd"/>
                </svg>
                From Account (Credit - Money Going Out)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cv_from_account" class="block text-sm font-medium text-gray-700 mb-1">
                        Bank/Cash Account <span class="text-red-500">*</span>
                    </label>
                    <select id="cv_from_account"
                            name="cv_from_account_id"`
                            x-model="fromAccountId"
                            @change="updateFromAccount()"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            required>
                        <option value="">Select From Account</option>
                        @foreach($ledgerAccounts->where('account_type', 'asset') as $account)
                            @if(stripos($account->name, 'bank') !== false || stripos($account->name, 'cash') !== false)
                                <option value="{{ $account->id }}" data-name="{{ $account->name }}" data-balance="{{ $account->closing_balance ?? 0 }}">
                                    {{ $account->name }} ({{ $account->code }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="cv_transfer_amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Transfer Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₦</span>
                        <input type="number"
                               id="cv_transfer_amount"
                               name="cv_transfer_amount"
                               x-model="transferAmount"
                               @input="calculateTotals()"
                               step="0.01"
                               min="0"
                               placeholder="0.00"
                               class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               required>
                    </div>
                </div>
            </div>

            <div x-show="fromAccountId" class="mt-3 p-3 bg-red-100 rounded-md">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-red-700">Current Balance:</span>
                    <span class="font-medium text-red-900" x-text="'₦ ' + formatNumber(fromAccountBalance)"></span>
                </div>
                <div class="flex justify-between items-center text-sm mt-1">
                    <span class="text-red-700">After Transfer:</span>
                    <span class="font-medium text-red-900" x-text="'₦ ' + formatNumber(fromAccountBalance - transferAmount)"></span>
                </div>
            </div>
        </div>

        {{-- To Account Section (Debit Side) --}}
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h4 class="text-md font-medium text-green-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" clip-rule="evenodd"/>
                </svg>
                To Account (Debit - Money Coming In)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cv_to_account" class="block text-sm font-medium text-gray-700 mb-1">
                        Bank/Cash Account <span class="text-red-500">*</span>
                    </label>
                    <select id="cv_to_account"
                            name="cv_to_account_id"
                            x-model="toAccountId"
                            @change="updateToAccount()"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            required>
                        <option value="">Select To Account</option>
                        @foreach($ledgerAccounts->where('account_type', 'asset') as $account)
                            @if(stripos($account->name, 'bank') !== false || stripos($account->name, 'cash') !== false)
                                <option value="{{ $account->id }}" data-name="{{ $account->name }}" data-balance="{{ $account->closing_balance ?? 0 }}">
                                    {{ $account->name }} ({{ $account->code }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="cv_particulars" class="block text-sm font-medium text-gray-700 mb-1">
                        Particulars/Description
                    </label>
                    <input type="text"
                           id="cv_particulars"
                           name="cv_particulars"
                           x-model="particulars"
                           placeholder="Transfer description"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <div x-show="toAccountId" class="mt-3 p-3 bg-green-100 rounded-md">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-green-700">Current Balance:</span>
                    <span class="font-medium text-green-900" x-text="'₦ ' + formatNumber(toAccountBalance)"></span>
                </div>
                <div class="flex justify-between items-center text-sm mt-1">
                    <span class="text-green-700">After Transfer:</span>
                    <span class="font-medium text-green-900" x-text="'₦ ' + formatNumber(toAccountBalance + transferAmount)"></span>
                </div>
            </div>
        </div>

        {{-- Transfer Summary --}}
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" clip-rule="evenodd"/>
                </svg>
                Transfer Summary
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-red-100 p-4 rounded-md border border-red-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-red-700 font-medium">From (Credit)</div>
                            <div class="text-xs text-red-600" x-text="fromAccountName"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-red-900" x-text="'₦ ' + formatNumber(transferAmount)"></div>
                            <div class="text-xs text-red-600">Going Out</div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-100 p-4 rounded-md border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-green-700 font-medium">To (Debit)</div>
                            <div class="text-xs text-green-600" x-text="toAccountName"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-green-900" x-text="'₦ ' + formatNumber(transferAmount)"></div>
                            <div class="text-xs text-green-600">Coming In</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Balance Check --}}
            <div class="mt-4 p-3 rounded-md" :class="isBalanced ? 'bg-green-100' : 'bg-yellow-100'">
                <div class="flex items-center justify-center">
                    <span x-show="isBalanced" class="text-green-600 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        ✓ Contra Voucher is Balanced
                    </span>
                    <span x-show="!isBalanced" class="text-yellow-600">
                        ⚠ Please select both accounts and enter transfer amount
                    </span>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                <span x-show="!isBalanced" class="text-red-600">⚠ Please complete all required fields</span>
                <span x-show="isBalanced" class="text-green-600">✓ Ready to save contra voucher</span>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('tenant.accounting.vouchers.index', ['tenant' => $tenant->slug]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </a>
                <button type="submit"
                        name="action"
                        value="save"
                        :disabled="!isBalanced || transferAmount <= 0"
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    Save as Draft
                </button>
                <button type="submit"
                        name="action"
                        value="save_and_post"
                        :disabled="!isBalanced || transferAmount <= 0"
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

@push('scripts')
<script>
function contraEntries() {
    return {
        // From account data (Credit side)
        fromAccountId: '',
        fromAccountName: '',
        fromAccountBalance: 0,

        // To account data (Debit side)
        toAccountId: '',
        toAccountName: '',
        toAccountBalance: 0,

        // Transfer data
        transferAmount: 0,
        particulars: '',

        // Calculated values
        isBalanced: false,

        init() {
            this.calculateTotals();
            console.log('✅ Contra Voucher entries initialized');
        },

        // Account update methods
        updateFromAccount() {
            if (this.fromAccountId) {
                const selectEl = document.getElementById('cv_from_account');
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                this.fromAccountName = selectedOption.dataset.name || '';
                this.fromAccountBalance = parseFloat(selectedOption.dataset.balance || 0);
            } else {
                this.fromAccountName = '';
                this.fromAccountBalance = 0;
            }
            this.calculateTotals();
        },

        updateToAccount() {
            if (this.toAccountId) {
                const selectEl = document.getElementById('cv_to_account');
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                this.toAccountName = selectedOption.dataset.name || '';
                this.toAccountBalance = parseFloat(selectedOption.dataset.balance || 0);
            } else {
                this.toAccountName = '';
                this.toAccountBalance = 0;
            }
            this.calculateTotals();
        },

        // Calculation methods
        calculateTotals() {
            // For contra vouchers, it's balanced when:
            // 1. Both accounts are selected
            // 2. Transfer amount is greater than 0
            // 3. From and To accounts are different
            this.isBalanced = this.fromAccountId !== ''
                            && this.toAccountId !== ''
                            && this.fromAccountId !== this.toAccountId
                            && parseFloat(this.transferAmount) > 0;
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
