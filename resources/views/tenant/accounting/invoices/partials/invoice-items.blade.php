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
                                                   @focus="showDropdown = true"
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
                                            <div x-show="!loading && products.length === 0 && searchTerm.length >= 2"
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
</div>

<!-- Quick Add Product Modal -->
<div id="quickAddProductModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeQuickAddProduct()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="quickAddProductForm" onsubmit="event.preventDefault(); submitQuickAddProduct();">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Quick Add Product
                            </h3>

                            <div class="space-y-4">
                                <!-- Product Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Type</label>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="type" value="item" checked class="mr-2" onchange="toggleQuickProductType()">
                                            <span class="text-sm">Item</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="type" value="service" class="mr-2" onchange="toggleQuickProductType()">
                                            <span class="text-sm">Service</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Product Name -->
                                <div>
                                    <label for="quick_product_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Product Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="quick_product_name" required
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="Enter product name">
                                </div>

                                <!-- SKU -->
                                <div>
                                    <label for="quick_product_sku" class="block text-sm font-medium text-gray-700 mb-1">
                                        SKU (Optional)
                                    </label>
                                    <input type="text" name="sku" id="quick_product_sku"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="Product code">
                                </div>

                                <!-- Sales Rate -->
                                <div>
                                    <label for="quick_sales_rate" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sales Rate <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="sales_rate" id="quick_sales_rate" required step="0.01" min="0"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="0.00">
                                </div>

                                <!-- Purchase Rate -->
                                <div>
                                    <label for="quick_purchase_rate" class="block text-sm font-medium text-gray-700 mb-1">
                                        Purchase Rate <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="purchase_rate" id="quick_purchase_rate" required step="0.01" min="0"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="0.00">
                                </div>

                                <!-- Unit (for items only) -->
                                <div id="quick_unit_section">
                                    <label for="quick_unit" class="block text-sm font-medium text-gray-700 mb-1">
                                        Unit <span class="text-red-500">*</span>
                                    </label>
                                    <select name="primary_unit_id" id="quick_unit" required
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm">
                                        <option value="">Select Unit</option>
                                        @if(isset($units))
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <!-- Opening Stock (for items only) -->
                                <div id="quick_stock_section">
                                    <label for="quick_opening_stock" class="block text-sm font-medium text-gray-700 mb-1">
                                        Opening Stock
                                    </label>
                                    <input type="number" name="opening_stock" id="quick_opening_stock" step="0.01" min="0" value="0"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 text-sm"
                                           placeholder="0.00">
                                </div>

                                <!-- Hidden fields -->
                                <input type="hidden" name="maintain_stock" value="1">
                                <input type="hidden" name="is_active" value="1">
                                <input type="hidden" name="is_saleable" value="1">
                                <input type="hidden" name="is_purchasable" value="1">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <span id="quick-product-submit-text">Create Product</span>
                        <svg id="quick-product-submit-loading" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                    <button type="button"
                            onclick="closeQuickAddProduct()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentProductRowIndex = null;

function openQuickAddProduct(index) {
    currentProductRowIndex = index;
    document.getElementById('quickAddProductModal').classList.remove('hidden');
    document.getElementById('quick_product_name').focus();
}

function closeQuickAddProduct() {
    document.getElementById('quickAddProductModal').classList.add('hidden');
    document.getElementById('quickAddProductForm').reset();
    document.getElementById('quick-product-submit-text').textContent = 'Create Product';
    document.getElementById('quick-product-submit-loading').classList.add('hidden');
    toggleQuickProductType();
    currentProductRowIndex = null;
}

function toggleQuickProductType() {
    const isService = document.querySelector('input[name="type"]:checked').value === 'service';
    const unitSection = document.getElementById('quick_unit_section');
    const stockSection = document.getElementById('quick_stock_section');
    const unitSelect = document.getElementById('quick_unit');

    if (isService) {
        unitSection.classList.add('hidden');
        stockSection.classList.add('hidden');
        unitSelect.required = false;
    } else {
        unitSection.classList.remove('hidden');
        stockSection.classList.remove('hidden');
        unitSelect.required = true;
    }
}

function submitQuickAddProduct() {
    const form = document.getElementById('quickAddProductForm');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const submitText = document.getElementById('quick-product-submit-text');
    const submitLoading = document.getElementById('quick-product-submit-loading');

    // Validate required fields
    const name = document.getElementById('quick_product_name').value.trim();
    const salesRate = document.getElementById('quick_sales_rate').value;
    const purchaseRate = document.getElementById('quick_purchase_rate').value;

    if (!name) {
        alert('Please enter product name');
        return;
    }

    if (!salesRate || salesRate < 0) {
        alert('Please enter valid sales rate');
        return;
    }

    if (!purchaseRate || purchaseRate < 0) {
        alert('Please enter valid purchase rate');
        return;
    }

    // Show loading state
    submitButton.disabled = true;
    submitText.textContent = 'Creating...';
    submitLoading.classList.remove('hidden');

    // Make AJAX request
    fetch(`{{ route('tenant.inventory.products.store', ['tenant' => $tenant->slug]) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            closeQuickAddProduct();

            // Show success notification
            showSuccessNotification('Product created successfully!');

            // If we have a row index, auto-select the product
            if (currentProductRowIndex !== null && data.product) {
                // Trigger product selection in the invoice items component
                setTimeout(() => {
                    const event = new CustomEvent('product-created', {
                        detail: {
                            index: currentProductRowIndex,
                            product: data.product
                        }
                    });
                    window.dispatchEvent(event);
                }, 300);
            }
        } else {
            alert(data.message || 'Error creating product. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating product. Please try again.');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitText.textContent = 'Create Product';
        submitLoading.classList.add('hidden');
    });
}

function showSuccessNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50 transform transition-transform duration-300 translate-x-full';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Listen for product created event to auto-select in invoice items
window.addEventListener('product-created', function(e) {
    const { index, product } = e.detail;

    // Find the Alpine component and update the item
    const invoiceItemsEl = document.querySelector('[x-data*="invoiceItems"]');
    if (invoiceItemsEl && invoiceItemsEl.__x) {
        const component = invoiceItemsEl.__x.$data;
        if (component.items && component.items[index]) {
            // Update the item with the new product
            component.items[index] = {
                ...component.items[index],
                product_id: product.id,
                name: product.name,
                rate: product.sales_rate,
                purchase_rate: product.purchase_rate,
                current_stock: product.current_stock,
                unit: product.unit_name
            };

            // Calculate amount
            if (component.calculateAmount) {
                component.calculateAmount(index);
            }
        }
    }
});
</script>

<!-- Invoice Items JavaScript is in the main create.blade.php file -->
