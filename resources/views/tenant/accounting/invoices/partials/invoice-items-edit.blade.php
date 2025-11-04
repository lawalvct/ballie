<div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="invoiceItemsEdit()">

    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">📦 Invoice Items</h3>
            <button type="button"
                    @click="addItem()"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
            </button>
        </div>
    </div>

    <div class="p-4 md:p-6">
        <!-- Items Table -->
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Product <span class="text-red-500">*</span>
                            </th>
                            <th class="text-left py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap hidden md:table-cell">
                                Description
                            </th>
                            <th class="text-right py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Qty <span class="text-red-500">*</span>
                            </th>
                            <th class="text-right py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Rate <span class="text-red-500">*</span>
                            </th>
                            <th class="text-right py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Amount
                            </th>
                            <th class="text-center py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 md:py-3 px-2 min-w-[180px] md:min-w-[200px]">
                                    <select :name="`inventory_items[${index}][product_id]`"
                                            x-model="item.product_id"
                                            @change="onProductChange(index)"
                                            required
                                            class="block w-full px-2 py-1.5 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-name="{{ $product->name }}"
                                                    data-rate="{{ $product->sales_rate ?? 0 }}"
                                                    data-purchase-rate="{{ $product->purchase_rate ?? 0 }}"
                                                    data-description="{{ $product->description ?? '' }}"
                                                    data-stock="{{ $product->current_stock ?? 0 }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-1 text-xs text-gray-500" x-show="item.current_stock !== null">
                                        <span>Stock: </span>
                                        <span x-text="item.current_stock"></span>
                                    </div>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[150px] hidden md:table-cell">
                                    <input type="text"
                                           :name="`inventory_items[${index}][description]`"
                                           x-model="item.description"
                                           class="block w-full px-2 py-1.5 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                           placeholder="Description">
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[80px]">
                                    <input type="number"
                                           :name="`inventory_items[${index}][quantity]`"
                                           x-model="item.quantity"
                                           @input="updateItemAmount(index)"
                                           class="block w-full px-2 py-1.5 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                           placeholder="0"
                                           step="0.01"
                                           min="0.01"
                                           required>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[90px]">
                                    <input type="number"
                                           :name="`inventory_items[${index}][rate]`"
                                           x-model="item.rate"
                                           @input="updateItemAmount(index)"
                                           class="block w-full px-2 py-1.5 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                           placeholder="0.00"
                                           step="0.01"
                                           min="0"
                                           required>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[100px]">
                                    <input type="number"
                                           x-model="item.amount"
                                           class="block w-full px-2 py-1.5 text-xs md:text-sm border border-gray-300 bg-gray-50 rounded-md text-right"
                                           readonly>
                                    <input type="hidden"
                                           :name="`inventory_items[${index}][purchase_rate]`"
                                           x-model="item.purchase_rate">
                                </td>
                                <td class="py-2 md:py-3 px-2 text-center min-w-[60px]">
                                    <button type="button"
                                            @click="removeItem(index)"
                                            class="text-red-600 hover:text-red-900 p-1.5 rounded hover:bg-red-50"
                                            :disabled="items.length === 1"
                                            :class="{'opacity-50 cursor-not-allowed': items.length === 1}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-200 bg-gray-50">
                            <td colspan="3" class="md:hidden py-2 px-2 text-xs font-medium text-gray-700 text-right">
                                Subtotal:
                            </td>
                            <td colspan="4" class="hidden md:table-cell py-2 md:py-3 px-2 text-xs md:text-sm font-medium text-gray-700 text-right">
                                Subtotal (Products):
                            </td>
                            <td class="py-2 md:py-3 px-2 text-right text-xs md:text-sm font-medium text-gray-900">
                                ₦<span x-text="formatNumber(totalAmount)"></span>
                            </td>
                            <td class="py-2 md:py-3 px-2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Additional Charges Section -->
        <div class="mt-6 border-t border-gray-200 pt-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-medium text-gray-900">💰 Additional Charges</h4>
                <button type="button"
                        @click="addLedgerAccount()"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Charge
                </button>
            </div>

            <div x-show="ledgerAccounts.length > 0" class="space-y-3">
                <template x-for="(ledger, index) in ledgerAccounts" :key="index">
                    <div class="grid grid-cols-12 gap-2 items-start border border-gray-200 rounded-lg p-3 bg-gray-50">
                        <div class="col-span-12 md:col-span-5">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Account</label>
                            <select :name="`ledger_accounts[${index}][ledger_account_id]`"
                                    x-model="ledger.ledger_account_id"
                                    required
                                    class="block w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Account</option>
                                @foreach($ledgerAccounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->name }} ({{ $account->accountGroup->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 md:col-span-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Amount</label>
                            <input type="number"
                                   :name="`ledger_accounts[${index}][amount]`"
                                   x-model="ledger.amount"
                                   @input="updateTotals()"
                                   class="block w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>
                        <div class="col-span-5 md:col-span-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Note</label>
                            <input type="text"
                                   :name="`ledger_accounts[${index}][narration]`"
                                   x-model="ledger.narration"
                                   class="block w-full px-2 py-1.5 text-xs border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Optional note">
                        </div>
                        <div class="col-span-1 flex items-end">
                            <button type="button"
                                    @click="removeLedgerAccount(index)"
                                    class="text-red-600 hover:text-red-900 p-1.5 rounded hover:bg-red-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- VAT Section -->
        <div class="mt-6 border-t border-gray-200 pt-6">
            <div class="flex items-center mb-4">
                <input type="checkbox"
                       name="vat_enabled"
                       id="vat_enabled"
                       value="1"
                       x-model="vatEnabled"
                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="vat_enabled" class="ml-2 block text-sm font-medium text-gray-900">
                    Enable VAT (7.5%)
                </label>
            </div>

            <div x-show="vatEnabled" x-transition class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">VAT Applies To:</label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <input type="radio"
                                   name="vat_applies_to"
                                   value="items_only"
                                   x-model="vatAppliesTo"
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Products Only</span>
                        </label>
                        <label class="inline-flex items-center ml-6">
                            <input type="radio"
                                   name="vat_applies_to"
                                   value="items_and_charges"
                                   x-model="vatAppliesTo"
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Products + Additional Charges</span>
                        </label>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-sm">
                            <span class="font-medium text-blue-900">VAT Amount: </span>
                            <span class="text-blue-700">₦<span x-text="formatNumber(vatAmount)"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="vat_amount" x-bind:value="vatAmount.toFixed(2)">
        </div>

        <!-- Grand Total Section -->
        <div class="mt-4 md:mt-6 border-t-2 border-gray-300 pt-4">
            <div class="flex justify-end">
                <div class="w-full sm:w-2/3 md:w-1/2">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs md:text-sm font-medium text-gray-700">Products Subtotal:</span>
                        <span class="text-xs md:text-sm font-medium text-gray-900">₦<span x-text="formatNumber(totalAmount)"></span></span>
                    </div>

                    <div x-show="ledgerAccountsTotal > 0" class="flex justify-between items-center py-2">
                        <span class="text-xs md:text-sm font-medium text-gray-700">Additional Charges:</span>
                        <span class="text-xs md:text-sm font-medium text-gray-900">₦<span x-text="formatNumber(ledgerAccountsTotal)"></span></span>
                    </div>

                    <div x-show="vatEnabled && vatAmount > 0" class="flex justify-between items-center py-2">
                        <span class="text-xs md:text-sm font-medium text-gray-700">VAT (7.5%):</span>
                        <span class="text-xs md:text-sm font-medium text-gray-900">₦<span x-text="formatNumber(vatAmount)"></span></span>
                    </div>

                    <div class="flex justify-between items-center py-2 md:py-3 border-t border-gray-300 mt-2">
                        <span class="text-sm md:text-base font-bold text-gray-900">Grand Total:</span>
                        <span class="text-base md:text-lg font-bold text-gray-900">₦<span x-text="formatNumber(grandTotal)"></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden total input -->
    <input type="hidden" name="total_amount" x-bind:value="grandTotal.toFixed(2)">
</div>

@push('scripts')
<script>
// Invoice Items Edit Component
window.invoiceItemsEdit = function() {
    return {
        items: {!! json_encode($inventoryItems->map(function($item) {
            $quantity = $item['quantity'] ?? 1;
            $rate = $item['rate'] ?? 0;
            return [
                'product_id' => $item['product_id'] ?? '',
                'product_name' => $item['product_name'] ?? '',
                'description' => $item['description'] ?? '',
                'quantity' => $quantity,
                'rate' => $rate,
                'purchase_rate' => $item['purchase_rate'] ?? 0,
                'amount' => $quantity * $rate,
                'current_stock' => null
            ];
        })->values()) !!},
        ledgerAccounts: [],
        vatEnabled: false,
        vatRate: 0.075, // 7.5%
        vatAppliesTo: 'items_only', // 'items_only' or 'items_and_charges'
        _updateTimeout: null,

        get totalAmount() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        },

        get ledgerAccountsTotal() {
            return this.ledgerAccounts.reduce((sum, ledger) => sum + (parseFloat(ledger.amount) || 0), 0);
        },

        get vatAmount() {
            if (!this.vatEnabled) return 0;
            if (this.vatAppliesTo === 'items_only') {
                return this.totalAmount * this.vatRate;
            } else {
                return (this.totalAmount + this.ledgerAccountsTotal) * this.vatRate;
            }
        },

        get grandTotal() {
            return this.totalAmount + this.ledgerAccountsTotal + this.vatAmount;
        },

        formatNumber(num) {
            return new Intl.NumberFormat('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        },

        addItem() {
            this.items.push({
                product_id: '',
                product_name: '',
                description: '',
                quantity: 1,
                rate: 0,
                purchase_rate: 0,
                amount: 0,
                current_stock: null
            });
            this.updateTotals();
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
                this.updateTotals();
            }
        },

        addLedgerAccount() {
            this.ledgerAccounts.push({
                ledger_account_id: '',
                amount: 0,
                narration: ''
            });
        },

        removeLedgerAccount(index) {
            this.ledgerAccounts.splice(index, 1);
            this.updateTotals();
        },

        updateTotals() {
            if (this._updateTimeout) {
                clearTimeout(this._updateTimeout);
            }
            this._updateTimeout = setTimeout(() => {
                this.$dispatch('invoice-total-changed', {
                    subtotal: this.totalAmount,
                    ledgerTotal: this.ledgerAccountsTotal,
                    grandTotal: this.grandTotal
                });
            }, 100);
        },

        onProductChange(index) {
            const select = document.querySelector(`select[name="inventory_items[${index}][product_id]"]`);
            const option = select.options[select.selectedIndex];

            if (option.value) {
                this.items[index].product_name = option.getAttribute('data-name') || '';
                this.items[index].rate = parseFloat(option.getAttribute('data-rate')) || 0;
                this.items[index].purchase_rate = parseFloat(option.getAttribute('data-purchase-rate')) || 0;
                this.items[index].description = option.getAttribute('data-description') || '';
                this.items[index].current_stock = parseFloat(option.getAttribute('data-stock')) || null;

                this.updateItemAmount(index);
            }
        },

        updateItemAmount(index) {
            const quantity = parseFloat(this.items[index].quantity) || 0;
            const rate = parseFloat(this.items[index].rate) || 0;
            this.items[index].amount = quantity * rate;
            this.updateTotals();
        },

        init() {
            this.$watch('vatEnabled', () => this.updateTotals());
            this.$watch('vatAppliesTo', () => this.updateTotals());
            console.log('Invoice items edit initialized with', this.items.length, 'items');
        }
    }
};
</script>
@endpush
