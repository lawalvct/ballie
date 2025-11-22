<!-- Quotation Items Section -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="quotationItems()">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Quotation Items</h3>
        <p class="text-sm text-gray-600 mt-1">Add products and services to your quotation</p>
    </div>

    <div class="p-6">
        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Rate</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="quotation-items-body">
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span x-text="index + 1"></span>
                            </td>

                            <!-- Product Search -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="relative" x-data="productSearch(index)">
                                    <input type="hidden" :name="`quotation_items[${index}][product_id]`" x-model="selectedProductId">

                                    <input type="text"
                                           x-model="searchTerm"
                                           @input="searchProducts()"
                                           @focus="searchProducts()"
                                           placeholder="Search products..."
                                           class="block w-full pl-3 pr-10 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg"
                                           :class="selectedProductId ? 'bg-green-50 border-green-300' : ''">

                                    <!-- Selected indicator -->
                                    <div x-show="selectedProductId" class="absolute right-3 top-2.5">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>

                                    <!-- Dropdown -->
                                    <div x-show="showDropdown && (products.length > 0 || loading)"
                                         x-transition
                                         @click.away="showDropdown = false"
                                         class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">

                                        <!-- Loading -->
                                        <div x-show="loading" class="px-3 py-2 text-gray-500 flex items-center">
                                            <svg class="animate-spin h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Searching...
                                        </div>

                                        <!-- Results -->
                                        <template x-for="product in products" :key="product.id">
                                            <div @click="selectProduct(product)"
                                                 class="px-3 py-2 cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-b-0 transition-colors">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <div class="font-medium text-gray-900" x-text="product.name"></div>
                                                        <div class="text-xs text-gray-500 mt-0.5" x-text="'₦' + (product.sales_rate || '0.00') + ' | Stock: ' + (product.current_stock || '0')"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- No results -->
                                        <div x-show="!loading && products.length === 0"
                                             class="px-3 py-2 text-gray-500 text-center">
                                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            No products found
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Add Product Button -->
                                <button type="button"
                                        @click="openQuickAddProduct(index)"
                                        class="mt-1 px-2 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                    + Quick Add
                                </button>
                            </td>

                            <!-- Description -->
                            <td class="px-4 py-4">
                                <textarea :name="`quotation_items[${index}][description]`"
                                          x-model="item.description"
                                          rows="2"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm"
                                          placeholder="Product description"></textarea>
                            </td>

                            <!-- Quantity -->
                            <td class="px-4 py-4">
                                <input type="number"
                                       :name="`quotation_items[${index}][quantity]`"
                                       x-model="item.quantity"
                                       @input="calculateAmount(index)"
                                       step="0.01"
                                       min="0.01"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm"
                                       required>
                            </td>

                            <!-- Unit -->
                            <td class="px-4 py-4">
                                <input type="text"
                                       :name="`quotation_items[${index}][unit]`"
                                       x-model="item.unit"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm"
                                       placeholder="Pcs">
                            </td>

                            <!-- Rate -->
                            <td class="px-4 py-4">
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm">₦</span>
                                    <input type="number"
                                           :name="`quotation_items[${index}][rate]`"
                                           x-model="item.rate"
                                           @input="calculateAmount(index)"
                                           step="0.01"
                                           min="0"
                                           class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-sm"
                                           required>
                                </div>
                            </td>

                            <!-- Amount -->
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm">₦</span>
                                    <input type="text"
                                           :name="`quotation_items[${index}][amount]`"
                                           x-model="item.amount"
                                           readonly
                                           class="block w-full pl-7 pr-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm font-medium">
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <button type="button"
                                        @click="removeItem(index)"
                                        :disabled="items.length === 1"
                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        title="Remove Item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Add Item Button -->
        <div class="mt-4 flex justify-between items-center">
            <button type="button"
                    @click="addItem()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
            </button>

            <!-- Total Display -->
            <div class="text-right">
                <div class="text-sm text-gray-600">Subtotal</div>
                <div class="text-lg font-bold text-gray-900">₦<span x-text="formatNumber(totalAmount)">0.00</span></div>
            </div>
        </div>
    </div>
</div>
