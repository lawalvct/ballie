<div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="quotationItems()">

    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">📦 @term('quotation') Items</h3>
            <div class="flex items-center gap-2">
                <button type="button"
                        @click="addItem('service')"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add @term('line_item')
                </button>
                <button type="button"
                        @click="addItem('product')"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add @term('product')
                </button>
            </div>
        </div>
    </div>

    <div class="p-4 md:p-6">
        <!-- Items Table -->
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap" style="width: 60px;">
                                Type
                            </th>
                            <th class="text-left py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                @term('product') / @term('line_item') <span class="text-red-500">*</span>
                            </th>
                            <th class="text-left py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap hidden md:table-cell">
                                Description
                            </th>
                            <th class="text-left py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Unit
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
                                <!-- Item Type Badge -->
                                <td class="py-2 md:py-3 px-2" style="width: 60px;">
                                    <input type="hidden"
                                           :name="`items[${index}][item_type]`"
                                           x-model="item.item_type">
                                    <span x-show="item.item_type === 'product'"
                                          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        @term('product')
                                    </span>
                                    <span x-show="item.item_type === 'service'"
                                          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        @term('line_item')
                                    </span>
                                </td>

                                <!-- Product Search (for product items) OR Service Description (for service items) -->
                                <td class="py-2 md:py-3 px-2 min-w-[180px] md:min-w-[200px]">
                                    <!-- Product search — shown only for product items -->
                                    <div x-show="item.item_type === 'product'" x-data="qProductSearch(index)" class="relative">
                                        <input type="text"
                                               x-model="searchTerm"
                                               @input="searchProducts()"
                                               @focus="searchProducts()"
                                               placeholder="Search {{ strtolower($term->label('product')) }}..."
                                               class="block w-full pl-2 md:pl-3 pr-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md">
                                        <input type="hidden"
                                               :name="item.item_type === 'product' ? `items[${index}][product_id]` : ''"
                                               x-model="selectedProductId"
                                               :required="item.item_type === 'product'">

                                        <!-- Dropdown -->
                                        <div x-show="showDropdown && (products.length > 0 || loading)"
                                             x-transition
                                             class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                            <div x-show="loading" class="px-3 py-2 text-gray-500 text-xs">Searching...</div>
                                            <template x-for="product in products" :key="product.id">
                                                <div @click="selectProduct(product)"
                                                     class="px-3 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0">
                                                    <div class="font-medium text-gray-900 text-xs" x-text="product.name"></div>
                                                    <div class="text-xs text-gray-500">
                                                        <span x-show="product.sku">SKU: <span x-text="product.sku"></span> | </span>
                                                        Rate: ₦<span x-text="product.sales_rate"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="!loading && products.length === 0"
                                                 class="px-3 py-2 text-gray-500 text-xs">
                                                No products found
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Service description — shown only for service items -->
                                    <div x-show="item.item_type === 'service'">
                                        <input type="text"
                                               :name="`items[${index}][description]`"
                                               x-model="item.description"
                                               class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500"
                                               placeholder="Service description (e.g. Consulting, Installation)">
                                        <input type="hidden"
                                               :name="item.item_type === 'service' ? `items[${index}][product_id]` : ''"
                                               value="">
                                    </div>
                                </td>

                                <!-- Description (visible for all, extra notes) -->
                                <td class="py-2 md:py-3 px-2 min-w-[150px] hidden md:table-cell">
                                    <input type="text"
                                           :name="item.item_type === 'product' ? `items[${index}][description]` : ''"
                                           x-model="item.description"
                                           class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                                           :placeholder="item.item_type === 'service' ? 'Additional notes' : 'Description'"
                                           :class="item.item_type === 'service' ? 'bg-green-50' : ''">
                                </td>

                                    <td class="py-2 md:py-3 px-2 min-w-[90px]">
                                     <input type="hidden"
                                         :name="`items[${index}][unit]`"
                                         x-model="item.unit">
                                     <input type="text"
                                         x-model="item.unit"
                                         class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md bg-gray-50 text-gray-600"
                                         placeholder="Auto"
                                         readonly>
                                    </td>

                                <td class="py-2 md:py-3 px-2 min-w-[80px]">
                                    <input type="number"
                                           :name="`items[${index}][quantity]`"
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
                                           :name="`items[${index}][rate]`"
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
                                           x-model="item.amount"
                                           class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md bg-gray-50 text-right"
                                           readonly>
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
                            <td colspan="5" class="md:hidden py-2 px-2 text-xs font-medium text-gray-700 text-right">
                                Subtotal:
                            </td>
                            <td colspan="6" class="hidden md:table-cell py-2 md:py-3 px-2 text-xs md:text-sm font-medium text-gray-700 text-right">
                                Subtotal (Items):
                            </td>
                            <td class="py-2 md:py-3 px-2 text-right text-xs md:text-sm font-medium text-gray-900">
                                ₦<span x-text="formatNumber(totalAmount)"></span>
                            </td>
                            <td class="py-2 md:py-3 px-2"></td>
                        </tr>
                    </tfoot>
                </table>
                <br /><br /><br />
            </div>
        </div>

        <!-- Additional Charges Section -->
        <div class="mt-4 md:mt-6 border-t border-gray-200 pt-4 md:pt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-0 mb-4">
                <h4 class="text-sm font-medium text-gray-900">Additional Charges (Optional)</h4>
                <button type="button"
                        @click="addCharge()"
                        class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Charge
                </button>
            </div>

            <div x-show="additionalCharges.length > 0" class="space-y-2">
                <template x-for="(charge, index) in additionalCharges" :key="index">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <input type="text"
                                   :name="`additional_charges[${index}][name]`"
                                   x-model="charge.name"
                                   class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 rounded-md"
                                   placeholder="Charge name (e.g. Transport, Setup Fee)"
                                   required>
                        </div>
                        <div class="w-full sm:w-32 md:w-48">
                            <input type="number"
                                   :name="`additional_charges[${index}][amount]`"
                                   x-model="charge.amount"
                                   @input="updateTotals()"
                                   class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-right"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>
                        <div class="w-full sm:flex-1 md:w-64">
                            <input type="text"
                                   :name="`additional_charges[${index}][narration]`"
                                   x-model="charge.narration"
                                   class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Description">
                        </div>
                        <button type="button"
                                @click="removeCharge(index)"
                                class="text-red-600 hover:text-red-900 p-1.5 md:p-2 rounded hover:bg-red-50 self-start sm:self-auto"
                                title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>

            <div x-show="additionalCharges.length === 0" class="text-xs md:text-sm text-gray-500 italic py-2">
                No additional charges. Click "Add Charge" to include transport, setup fees, etc.
            </div>
        </div>

        <!-- Grand Total Section -->
        <div class="mt-4 md:mt-6 border-t-2 border-gray-300 pt-4">
            <div class="flex justify-end">
                <div class="w-full sm:w-2/3 md:w-1/2">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs md:text-sm font-medium text-gray-700">Items Subtotal:</span>
                        <span class="text-xs md:text-sm font-medium text-gray-900">₦<span x-text="formatNumber(totalAmount)"></span></span>
                    </div>
                    <div x-show="chargesTotal > 0" class="flex justify-between items-center py-2">
                        <span class="text-xs md:text-sm font-medium text-gray-700">Additional Charges:</span>
                        <span class="text-xs md:text-sm font-medium text-gray-900">₦<span x-text="formatNumber(chargesTotal)"></span></span>
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
                                <span x-show="vatAppliesTo === 'items_only'">VAT calculated on items subtotal (₦<span x-text="formatNumber(totalAmount)"></span>)</span>
                                <span x-show="vatAppliesTo === 'items_and_charges'">VAT calculated on items + charges (₦<span x-text="formatNumber(totalAmount + chargesTotal)"></span>)</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-2 md:py-3 border-t border-gray-300 mt-2">
                        <span class="text-sm md:text-base font-bold text-gray-900">Grand Total:</span>
                        <span class="text-base md:text-lg font-bold text-gray-900">₦<span x-text="formatNumber(grandTotal)"></span></span>
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

@push('scripts')
<script>
function quotationItems() {
    return {
        items: [],
        additionalCharges: [],
        vatEnabled: false,
        vatRate: 0.075,
        vatAppliesTo: 'items_only',

        get totalAmount() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
        },

        get chargesTotal() {
            return this.additionalCharges.reduce((sum, c) => sum + (parseFloat(c.amount) || 0), 0);
        },

        get vatAmount() {
            if (!this.vatEnabled) return 0;
            if (this.vatAppliesTo === 'items_only') {
                return this.totalAmount * this.vatRate;
            }
            return (this.totalAmount + this.chargesTotal) * this.vatRate;
        },

        get grandTotal() {
            return this.totalAmount + this.chargesTotal + this.vatAmount;
        },

        init() {
            @if(isset($quotation) && $quotation->items->count() > 0)
                @foreach($quotation->items as $item)
                    this.items.push({
                        item_type: '{{ $item->item_type ?? "product" }}',
                        product_id: {{ $item->product_id ?? 'null' }},
                        product_name: @json($item->product_name ?? ''),
                        description: @json($item->description ?? ''),
                        unit: @json($item->unit ?? ''),
                        quantity: {{ $item->quantity }},
                        rate: {{ $item->rate }},
                        amount: {{ ($item->quantity * $item->rate) }}
                    });
                @endforeach

                @if(!empty($quotation->additional_charges))
                    @foreach($quotation->additional_charges as $charge)
                        this.additionalCharges.push({
                            name: @json($charge['name'] ?? ''),
                            amount: {{ $charge['amount'] ?? 0 }},
                            narration: @json($charge['narration'] ?? '')
                        });
                    @endforeach
                @endif

                this.vatEnabled = {{ $quotation->vat_enabled ? 'true' : 'false' }};
                this.vatAppliesTo = '{{ $quotation->vat_applies_to ?? "items_only" }}';
            @else
                this.addItem('product');
            @endif
        },

        addItem(type) {
            this.items.push({
                item_type: type || 'product',
                product_id: '',
                product_name: '',
                description: '',
                unit: '',
                quantity: 1,
                rate: 0,
                amount: 0
            });
        },

        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },

        calculateAmount(index) {
            const item = this.items[index];
            const qty = parseFloat(item.quantity) || 0;
            const rate = parseFloat(item.rate) || 0;
            item.amount = (qty * rate).toFixed(2);
        },

        addCharge() {
            this.additionalCharges.push({ name: '', amount: 0, narration: '' });
        },

        removeCharge(index) {
            this.additionalCharges.splice(index, 1);
        },

        updateTotals() {
            // Computed properties handle this automatically
        },

        formatNumber(num) {
            if (!num || isNaN(num)) return '0.00';
            return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }
}

function qProductSearch(itemIndex) {
    return {
        searchTerm: '',
        products: [],
        loading: false,
        showDropdown: false,
        selectedProductId: '',
        searchTimeout: null,

        searchProducts() {
            const query = this.searchTerm || '';
            if (this.searchTimeout) clearTimeout(this.searchTimeout);

            this.searchTimeout = setTimeout(() => {
                this.loading = true;
                this.showDropdown = true;

                fetch(`{{ route('tenant.accounting.quotations.search.products', $tenant->slug) }}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => { this.products = data; this.loading = false; })
                    .catch(() => { this.products = []; this.loading = false; });
            }, 300);
        },

        selectProduct(product) {
            const comp = Alpine.$data(this.$el.closest('[x-data="quotationItems()"]'));
            if (comp && comp.items[itemIndex]) {
                const primaryUnit = product.primary_unit || null;
                comp.items[itemIndex].product_id = product.id;
                comp.items[itemIndex].product_name = product.name;
                comp.items[itemIndex].rate = parseFloat(product.sales_rate) || 0;
                comp.items[itemIndex].description = product.description || '';
                comp.items[itemIndex].unit = primaryUnit
                    ? (primaryUnit.abbreviation || primaryUnit.symbol || primaryUnit.name || '')
                    : (product.unit || '');
                comp.calculateAmount(itemIndex);
            }
            this.searchTerm = product.name;
            this.selectedProductId = product.id;
            this.showDropdown = false;
            this.products = [];
        },

        init() {
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) this.showDropdown = false;
            });
        }
    }
}
</script>
@endpush
