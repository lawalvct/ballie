<div class="bg-white shadow-sm rounded-lg border border-gray-200 mt-6" x-data="inventoryEntries()" x-show="showInventorySection">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">📦 Inventory/Product Items</h3>
            <button type="button"
                    @click="addInventoryItem()"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
            </button>
        </div>
    </div>

    <div class="p-6">
        <!-- Inventory Items Table -->
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
                            Qty
                        </th>
                        <th class="text-right py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rate
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
                    <template x-for="(item, index) in inventoryItems" :key="index">
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-2">
                                <div x-data="{ search: '', showDropdown: false }" class="relative">
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="showDropdown = true"
                                        @click.away="showDropdown = false"
                                        placeholder="Search product..."
                                        class="block w-full pl-3 pr-10 py-2 text-sm border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md"
                                    >
                                    <input type="hidden" :name="`inventory_items[${index}][product_id]`" x-model="item.product_id" required>
                                    <div x-show="showDropdown" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                        @foreach($products ?? [] as $product)
                                            <div
                                                x-show="search === '' || '{{ strtolower($product->name . ' ' . $product->sku) }}'.includes(search.toLowerCase())"
                                                @click="item.product_id = {{ $product->id }}; item.product_name = '{{ $product->name }}'; item.rate = {{ $product->sales_rate }}; item.purchase_rate = {{ $product->purchase_rate }}; item.current_stock = {{ $product->current_stock }}; item.unit = '{{ $product->primaryUnit->name ?? 'Pcs' }}'; item.description = item.description || '{{ $product->name }}'; search = '{{ $product->name }} ({{ $product->sku }})'; showDropdown = false; $parent.$parent.calculateAmount(index)"
                                                class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm border-b border-gray-100 last:border-b-0"
                                            >
                                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-xs text-gray-500">SKU: {{ $product->sku }} | Stock: {{ $product->current_stock }} {{ $product->primaryUnit->name ?? 'Pcs' }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500" x-show="item.current_stock">
                                        Stock: <span x-text="item.current_stock"></span> <span x-text="item.unit"></span>
                                    </div>
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
                                       min="0">
                            </td>
                            <td class="py-3 px-2">
                                <input type="number"
                                       :name="`inventory_items[${index}][rate]`"
                                       x-model="item.rate"
                                       @input="calculateAmount(index)"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="0">
                            </td>
                            <td class="py-3 px-2">
                                <input type="number"
                                       :name="`inventory_items[${index}][amount]`"
                                       x-model="item.amount"
                                       class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-gray-50 text-right"
                                       readonly>
                            </td>
                            <td class="py-3 px-2 text-center">
                                <button type="button"
                                        @click="removeInventoryItem(index)"
                                        x-show="inventoryItems.length > 1"
                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
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
                            Total Inventory Value:
                        </td>
                        <td class="py-3 px-2 text-right text-sm font-medium text-gray-900">
                            ₦<span x-text="formatNumber(totalInventoryAmount)"></span>
                        </td>
                        <td class="py-3 px-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Auto-generate Voucher Entries Button -->
        <div class="mt-4 flex justify-end">
            <button type="button"
                    @click="generateVoucherEntries()"
                    x-show="inventoryItems.length > 0 && totalInventoryAmount > 0"
                    class="inline-flex items-center px-4 py-2 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Auto-Generate Voucher Entries
            </button>
        </div>
    </div>
</div>

<script>
window.inventoryEntries = function() {
    return {
        inventoryItems: [
            {
                product_id: '',
                description: '',
                quantity: '',
                rate: '',
                amount: '',
                current_stock: '',
                unit: 'Pcs'
            }
        ],
        showInventorySection: false,
        voucherType: '',

        get totalInventoryAmount() {
            return this.inventoryItems.reduce((sum, item) => {
                return sum + (parseFloat(item.amount) || 0);
            }, 0);
        },

        formatNumber(num) {
            if (!num || isNaN(num)) return '0.00';
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        addInventoryItem() {
            this.inventoryItems.push({
                product_id: '',
                description: '',
                quantity: '',
                rate: '',
                amount: '',
                current_stock: '',
                unit: 'Pcs'
            });
        },

        removeInventoryItem(index) {
            if (this.inventoryItems.length > 1) {
                this.inventoryItems.splice(index, 1);
            }
        },

        updateProductDetails(index) {
            const item = this.inventoryItems[index];
            if (item.product_id) {
                const selectElement = document.querySelector(`select[name="inventory_items[${index}][product_id]"]`);
                const selectedOption = selectElement.options[selectElement.selectedIndex];

                if (selectedOption) {
                    const productName = selectedOption.getAttribute('data-name');
                    const salesRate = selectedOption.getAttribute('data-rate');
                    const purchaseRate = selectedOption.getAttribute('data-purchase-rate');
                    const currentStock = selectedOption.getAttribute('data-stock');
                    const unit = selectedOption.getAttribute('data-unit');

                    // Set description if empty
                    if (!item.description) {
                        item.description = productName;
                    }

                    // Set rate based on voucher type (sales vs purchase)
                    if (this.voucherType && this.voucherType.includes('sales')) {
                        item.rate = salesRate;
                    } else if (this.voucherType && this.voucherType.includes('purchase')) {
                        item.rate = purchaseRate;
                    } else {
                        item.rate = salesRate; // default to sales rate
                    }

                    item.current_stock = currentStock;
                    item.unit = unit;

                    // Calculate amount if quantity is already set
                    if (item.quantity) {
                        this.calculateAmount(index);
                    }
                }
            }
        },

        calculateAmount(index) {
            const item = this.inventoryItems[index];
            const quantity = parseFloat(item.quantity) || 0;
            const rate = parseFloat(item.rate) || 0;
            item.amount = (quantity * rate).toFixed(2);
        },

        generateVoucherEntries() {
            // This will communicate with the main voucher entries component
            const event = new CustomEvent('generate-voucher-entries', {
                detail: {
                    inventoryItems: this.inventoryItems,
                    totalAmount: this.totalInventoryAmount,
                    voucherType: this.voucherType
                }
            });
            document.dispatchEvent(event);
        },

        init() {
            // Listen for voucher type changes
            document.addEventListener('voucher-type-changed', (e) => {
                const voucherType = e.detail.voucherType;
                this.voucherType = voucherType;
                this.showInventorySection = voucherType && voucherType.affects_inventory;
            });

            console.log('✅ Inventory entries component initialized');
        }
    }
};
</script>