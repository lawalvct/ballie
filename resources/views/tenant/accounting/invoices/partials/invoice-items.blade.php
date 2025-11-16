<div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="invoiceItems()">


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
                                    <div x-data="productSearch(index)" class="relative">
                                        <div class="flex gap-1">
                                            <input type="text"
                                                   x-model="searchTerm"
                                                   @input="searchProducts()"
                                                   @focus="searchProducts()"
                                                   placeholder="Search..."
                                                   class="block w-full pl-2 md:pl-3 pr-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md">
                                            <button type="button"
                                                    @click="openQuickAddProduct(index)"
                                                    class="px-2 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors flex-shrink-0"
                                                    title="Quick Add Product">
                                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <input type="hidden"
                                               :name="`inventory_items[${index}][product_id]`"
                                               x-model="selectedProductId"
                                               required>

                                        <!-- Dropdown -->
                                        <div x-show="showDropdown && (products.length > 0 || loading)"
                                             x-transition
                                             class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">

                                            <!-- Loading -->
                                            <div x-show="loading" class="px-3 py-2 text-gray-500 text-xs">
                                                Searching...
                                            </div>

                                            <!-- Results -->
                                            <template x-for="product in products" :key="product.id">
                                                <div @click="selectProduct(product)"
                                                     class="px-3 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0">
                                                    <div class="font-medium text-gray-900 text-xs" x-text="product.name"></div>
                                                    <div class="text-xs text-gray-500">
                                                        <span x-show="product.sku">SKU: <span x-text="product.sku"></span> | </span>
                                                        Stock: <span x-text="product.current_stock"></span> <span x-text="product.unit"></span> |
                                                        Rate: ₦<span x-text="product.sales_rate"></span>
                                                    </div>
                                                </div>
                                            </template>

                                            <!-- No results -->
                                            <div x-show="!loading && products.length === 0"
                                                 class="px-3 py-2 text-gray-500 text-xs">
                                                No products found
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500" x-show="item.current_stock !== null">
                                        Stock: <span x-text="item.current_stock"></span> <span x-text="item.unit"></span>
                                        <span x-show="parseFloat(item.quantity) > parseFloat(item.current_stock) && !isPurchaseInvoice()" class="text-red-600 font-medium">
                                            (Low!)
                                        </span>
                                    </div>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[150px] hidden md:table-cell">
                                    <input type="text"
                                           :name="`inventory_items[${index}][description]`"
                                           x-model="item.description"
                                           class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                           placeholder="Description">
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[80px]">
                                    <input type="number"
                                           :name="`inventory_items[${index}][quantity]`"
                                           x-model="item.quantity"
                                           @input="calculateAmount(index)"
                                           class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                           placeholder="0"
                                           step="0.01"
                                           min="0.01"
                                           required>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[90px]">
                                    <input type="number"
                                           :name="`inventory_items[${index}][rate]`"
                                           x-model="item.rate"
                                           @input="calculateAmount(index)"
                                           class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                           placeholder="0"
                                           step="0.01"
                                           min="0"
                                           required>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[100px]">
                                    <input type="number"
                                           :name="`inventory_items[${index}][amount]`"
                                           x-model="item.amount"
                                           class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md bg-gray-50 text-right"
                                           readonly>
                                    <!-- Hidden input for purchase_rate -->
                                    <input type="hidden"
                                           :name="`inventory_items[${index}][purchase_rate]`"
                                           x-model="item.purchase_rate">
                                </td>
                                <td class="py-2 md:py-3 px-2 text-center min-w-[60px]">
                                    <button type="button"
                                            @click="removeItem(index)"
                                            x-show="items.length > 1"
                                            class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                                            title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
                <br />  <br />  <br />
            </div>
        </div>

        <!-- Additional Ledger Accounts Section -->
        <div class="mt-4 md:mt-6 border-t border-gray-200 pt-4 md:pt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 mb-4">
                <h4 class="text-sm font-medium text-gray-900">Additional Charges (Optional)</h4>
                <button type="button"
                        @click="addLedgerAccount()"
                        class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Charge
                </button>
            </div>

            <div x-show="ledgerAccounts.length > 0" class="space-y-2">
                <template x-for="(ledger, index) in ledgerAccounts" :key="index">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div x-data="ledgerAccountSearch(index)" class="relative">
                                <input type="text"
                                       x-model="searchTerm"
                                       @input="searchLedgerAccounts()"
                                       @focus="showDropdown = true"
                                       placeholder="Search accounts..."
                                       class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md">
                                <input type="hidden"
                                       :name="`ledger_accounts[${index}][ledger_account_id]`"
                                       x-model="selectedLedgerAccountId"
                                       required>

                                <!-- Dropdown -->
                                <div x-show="showDropdown && (accounts.length > 0 || loading)"
                                     x-transition
                                     class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">

                                    <!-- Loading -->
                                    <div x-show="loading" class="px-3 py-2 text-gray-500 text-xs">
                                        Searching...
                                    </div>

                                    <!-- Results -->
                                    <template x-for="account in accounts" :key="account.id">
                                        <div @click="selectLedgerAccount(account)"
                                             class="px-3 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0">
                                            <div class="font-medium text-gray-900 text-xs" x-text="account.name"></div>
                                            <div class="text-xs text-gray-500">
                                                <span x-show="account.code">Code: <span x-text="account.code"></span> | </span>
                                                <span x-text="account.account_group_name"></span>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- No results -->
                                    <div x-show="!loading && accounts.length === 0 && searchTerm.length >= 2"
                                         class="px-3 py-2 text-gray-500 text-xs">
                                        No ledger accounts found
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full sm:w-32 md:w-48">
                            <input type="number"
                                   :name="`ledger_accounts[${index}][amount]`"
                                   x-model="ledger.amount"
                                   @input="updateTotals()"
                                   class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>
                        <div class="w-full sm:flex-1 md:w-64">
                            <input type="text"
                                   :name="`ledger_accounts[${index}][narration]`"
                                   x-model="ledger.narration"
                                   class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Description">
                        </div>
                        <button type="button"
                                @click="removeLedgerAccount(index)"
                                class="text-red-600 hover:text-red-900 p-1.5 md:p-2 rounded hover:bg-red-50 self-start sm:self-auto"
                                title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <div x-show="ledgerAccounts.length === 0" class="text-xs md:text-sm text-gray-500 italic py-2">
                No additional charges. Click "Add Charge" to include VAT, transport, etc.
            </div>
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

                    <!-- VAT Section -->
                    <div class="border-t border-gray-200 pt-3 mt-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           x-model="vatEnabled"
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-xs md:text-sm font-medium text-gray-700">Add VAT (7.5%)</span>
                            </label>
                            <span x-show="vatEnabled" class="text-xs md:text-sm font-medium text-gray-900">
                                ₦<span x-text="formatNumber(vatAmount)"></span>
                            </span>
                        </div>

                        <!-- VAT Calculation Options -->
                        <div x-show="vatEnabled" class="mt-3 space-y-2">
                            <label class="text-xs font-medium text-gray-700">VAT applies to:</label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio"
                                           x-model="vatAppliesTo"
                                           value="items_only"
                                           class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300">
                                    <span class="ml-2 text-xs text-gray-700">Items only</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio"
                                           x-model="vatAppliesTo"
                                           value="items_and_charges"
                                           class="h-3 w-3 text-primary-600 focus:ring-primary-500 border-gray-300">
                                    <span class="ml-2 text-xs text-gray-700">Items + Additional Charges</span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 italic">
                                <span x-show="vatAppliesTo === 'items_only'">VAT calculated on products subtotal (₦<span x-text="formatNumber(totalAmount)"></span>)</span>
                                <span x-show="vatAppliesTo === 'items_and_charges'">VAT calculated on products + charges (₦<span x-text="formatNumber(totalAmount + ledgerAccountsTotal)"></span>)</span>
                            </p>
                        </div>

                        <p class="text-xs text-gray-500 mt-2" x-show="vatEnabled">
                            VAT will be automatically posted to <span x-text="isPurchaseInvoice() ? 'VAT Input' : 'VAT Output'"></span> account
                        </p>
                    </div>

                    <div class="flex justify-between items-center py-2 md:py-3 border-t border-gray-300 mt-2">
                        <span class="text-sm md:text-base font-bold text-gray-900">Grand Total:</span>
                        <span class="text-base md:text-lg font-bold text-gray-900">₦<span x-text="formatNumber(grandTotal)"></span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Warning -->
        <div x-show="hasStockWarnings" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Stock Warning</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Some items have insufficient stock. Please review the quantities before proceeding.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden VAT Inputs -->
    <input type="hidden" name="vat_enabled" x-bind:value="vatEnabled ? 1 : 0">
    <input type="hidden" name="vat_amount" x-bind:value="vatAmount.toFixed(2)">
    <input type="hidden" name="vat_applies_to" x-bind:value="vatAppliesTo">
</div>
