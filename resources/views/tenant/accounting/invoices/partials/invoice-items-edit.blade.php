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

        <!-- Grand Total Section -->
        <div class="mt-4 md:mt-6 border-t-2 border-gray-300 pt-4">
            <div class="flex justify-end">
                <div class="w-full sm:w-2/3 md:w-1/2">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs md:text-sm font-medium text-gray-700">Products Subtotal:</span>
                        <span class="text-xs md:text-sm font-medium text-gray-900">₦<span x-text="formatNumber(totalAmount)"></span></span>
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

        get totalAmount() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        },

        get grandTotal() {
            return this.totalAmount;
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
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
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
        },

        init() {
            console.log('Invoice items edit initialized with', this.items.length, 'items');
        }
    }
};
</script>
@endpush
