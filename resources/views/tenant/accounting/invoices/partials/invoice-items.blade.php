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

    <div class="p-6">
        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product <span class="text-red-500">*</span>
                        </th>
                        <th class="text-left py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Qty <span class="text-red-500">*</span>
                        </th>
                        <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rate <span class="text-red-500">*</span>
                        </th>
                        <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="text-center py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in items" :key="index">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-2">
                                <select :name="`inventory_items[${index}][product_id]`"
                                x-model="item.product_id"
                                @change="updateProductDetails(index)"
                                class="block w-full pl-3 pr-10 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md"
                                required>
                                <option value="">Select Product</option>
                                @if(isset($products) && count($products) > 0)
                                @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-sales-rate="{{ $product->sales_rate }}"
                                    data-purchase-rate="{{ $product->purchase_rate }}"
                                    data-stock="{{ $product->current_stock }}"
                                    data-unit="{{ $product->primaryUnit->name ?? 'Pcs' }}"
                                        data-sku="{{ $product->sku }}">
                                        {{ $product->name }} @if($product->sku)({{ $product->sku }})@endif
                                        </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No products available</option>
                                    @endif
                                </select>
                                <div class="mt-1 text-xs text-gray-500" x-show="item.current_stock !== null">
                                    Stock: <span x-text="item.current_stock"></span> <span x-text="item.unit"></span>
                                    <span x-show="parseFloat(item.quantity) > parseFloat(item.current_stock)" class="text-red-600 font-medium">
                                        (Insufficient Stock!)
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-2">
                                <input type="text"
                                       :name="`inventory_items[${index}][description]`"
                                       x-model="item.description"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="Item description">
                            </td>
                            <td class="py-3 px-2">
                                <input type="number"
                                       :name="`inventory_items[${index}][quantity]`"
                                       x-model="item.quantity"
                                       @input="calculateAmount(index)"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0.01"
                                       required>
                            </td>
                            <td class="py-3 px-2">
                                <input type="number"
                                       :name="`inventory_items[${index}][rate]`"
                                       x-model="item.rate"
                                       @input="calculateAmount(index)"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0"
                                       required>
                            </td>
                            <td class="py-3 px-2">
                                <input type="number"
                                       :name="`inventory_items[${index}][amount]`"
                                       x-model="item.amount"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-50 text-right"
                                       readonly>
                                <!-- Hidden input for purchase_rate -->
                                <input type="hidden"
                                       :name="`inventory_items[${index}][purchase_rate]`"
                                       x-model="item.purchase_rate">
                            </td>
                            <td class="py-3 px-2 text-center">
                                <button type="button"
                                        @click="removeItem(index)"
                                        x-show="items.length > 1"
                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"
                                        title="Remove Item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-300 bg-gray-50">
                        <td colspan="4" class="py-3 px-2 text-sm font-medium text-gray-900 text-right">
                            Total Invoice Amount:
                        </td>
                        <td class="py-3 px-2 text-right text-sm font-medium text-gray-900">
                            ₦<span x-text="formatNumber(totalAmount)"></span>
                        </td>
                        <td class="py-3 px-2"></td>
                    </tr>
                </tfoot>
            </table>
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

<!-- Invoice Items JavaScript is in the main create.blade.php file -->
