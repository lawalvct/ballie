<div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="quotationItems()">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">📦 Quotation Items</h3>
            <button type="button" @click="addItem()"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Item
            </button>
        </div>
    </div>

    <div class="p-4 md:p-6">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <div class="inline-block min-w-full align-middle">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Product <span class="text-red-500">*</span></th>
                            <th class="text-left py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap hidden md:table-cell">Description</th>
                            <th class="text-right py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Qty <span class="text-red-500">*</span></th>
                            <th class="text-right py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Rate <span class="text-red-500">*</span></th>
                            <th class="text-right py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Discount</th>
                            <th class="text-right py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tax %</th>
                            <th class="text-right py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Amount</th>
                            <th class="text-center py-2 md:py-3 px-2 text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 md:py-3 px-2 min-w-[180px] md:min-w-[200px]">
                                    <div x-data="productSearch(index)" class="relative" @click.away="showDropdown = false">
                                        <input type="text" x-model="searchTerm" @input="searchProducts()" @focus="searchProducts()"
                                               placeholder="Search product..." class="block w-full pl-2 md:pl-3 pr-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md">
                                        <input type="hidden" :name="`items[${index}][product_id]`" x-model="selectedProductId" required>

                                        <div x-show="showDropdown && (products.length > 0 || loading)" x-transition
                                             class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                            <div x-show="loading" class="px-3 py-2 text-gray-500 text-xs">Searching...</div>
                                            <template x-for="product in products" :key="product.id">
                                                <div @click.stop="selectProduct(product)" class="px-3 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-100 last:border-b-0">
                                                    <div class="font-medium text-gray-900 text-xs" x-text="product.name"></div>
                                                    <div class="text-xs text-gray-500">
                                                        <span x-show="product.sku">SKU: <span x-text="product.sku"></span> | </span>
                                                        Rate: ₦<span x-text="product.sales_rate"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div x-show="!loading && products.length === 0" class="px-3 py-2 text-gray-500 text-xs">No products found</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[150px] hidden md:table-cell">
                                    <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                           class="block w-full px-2 md:px-3 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500" placeholder="Description">
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[80px]">
                                    <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" @input="calculateAmount(index)"
                                           class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right" placeholder="0" step="0.01" min="0.01" required>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[90px]">
                                    <input type="number" :name="`items[${index}][rate]`" x-model="item.rate" @input="calculateAmount(index)"
                                           class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right" placeholder="0" step="0.01" min="0" required>
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[80px]">
                                    <input type="number" :name="`items[${index}][discount]`" x-model="item.discount" @input="calculateAmount(index)"
                                           class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right" placeholder="0" step="0.01" min="0">
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[70px]">
                                    <input type="number" :name="`items[${index}][tax]`" x-model="item.tax" @input="calculateAmount(index)"
                                           class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 text-right" placeholder="0" step="0.01" min="0">
                                </td>
                                <td class="py-2 md:py-3 px-2 min-w-[100px]">
                                    <input type="number" x-model="item.amount" class="block w-full px-2 py-1.5 md:py-2 text-xs md:text-sm border border-gray-300 rounded-md bg-gray-50 text-right" readonly>
                                </td>
                                <td class="py-2 md:py-3 px-2 text-center min-w-[60px]">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                            class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50" title="Remove">
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
                            <td colspan="5" class="md:hidden py-2 px-2 text-xs font-medium text-gray-700 text-right">Subtotal:</td>
                            <td colspan="6" class="hidden md:table-cell py-2 md:py-3 px-2 text-xs md:text-sm font-medium text-gray-700 text-right">Subtotal:</td>
                            <td class="py-2 md:py-3 px-2 text-right text-xs md:text-sm font-medium text-gray-900">₦<span x-text="formatNumber(subtotal)"></span></td>
                            <td class="py-2 md:py-3 px-2"></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="6" class="hidden md:table-cell py-2 px-2 text-xs font-medium text-gray-700 text-right">Total Discount:</td>
                            <td class="py-2 px-2 text-right text-xs font-medium text-red-600">₦<span x-text="formatNumber(totalDiscount)"></span></td>
                            <td class="py-2 px-2"></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="6" class="hidden md:table-cell py-2 px-2 text-xs font-medium text-gray-700 text-right">Total Tax:</td>
                            <td class="py-2 px-2 text-right text-xs font-medium text-gray-900">₦<span x-text="formatNumber(totalTax)"></span></td>
                            <td class="py-2 px-2"></td>
                        </tr>
                        <tr class="border-t-2 border-gray-300 bg-gray-100">
                            <td colspan="6" class="hidden md:table-cell py-3 px-2 text-sm font-bold text-gray-900 text-right">Grand Total:</td>
                            <td class="py-3 px-2 text-right text-sm font-bold text-emerald-600">₦<span x-text="formatNumber(grandTotal)"></span></td>
                            <td class="py-3 px-2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function quotationItems() {
    return {
        items: [],
        subtotal: 0,
        totalDiscount: 0,
        totalTax: 0,
        grandTotal: 0,

        init() {
            @if(isset($quotation) && $quotation->items->count() > 0)
                @foreach($quotation->items as $item)
                    this.items.push({
                        product_id: {{ $item->product_id }},
                        product_name: '{{ $item->product_name }}',
                        description: '{{ $item->description }}',
                        quantity: {{ $item->quantity }},
                        rate: {{ $item->rate }},
                        discount: {{ $item->discount }},
                        tax: {{ $item->tax }},
                        amount: {{ $item->getTotal() }}
                    });
                @endforeach
            @else
                this.addItem();
            @endif
            this.updateTotals();
        },

        addItem() {
            this.items.push({
                product_id: '',
                product_name: '',
                description: '',
                quantity: 1,
                rate: 0,
                discount: 0,
                tax: 0,
                amount: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.updateTotals();
        },

        calculateAmount(index) {
            const item = this.items[index];
            const qty = parseFloat(item.quantity) || 0;
            const rate = parseFloat(item.rate) || 0;
            const discount = parseFloat(item.discount) || 0;
            const tax = parseFloat(item.tax) || 0;

            const itemTotal = (qty * rate) - discount;
            const taxAmount = itemTotal * (tax / 100);
            item.amount = itemTotal + taxAmount;

            this.updateTotals();
        },

        updateTotals() {
            this.subtotal = 0;
            this.totalDiscount = 0;
            this.totalTax = 0;

            this.items.forEach(item => {
                const qty = parseFloat(item.quantity) || 0;
                const rate = parseFloat(item.rate) || 0;
                const discount = parseFloat(item.discount) || 0;
                const tax = parseFloat(item.tax) || 0;

                this.subtotal += qty * rate;
                this.totalDiscount += discount;
                const itemTotal = (qty * rate) - discount;
                this.totalTax += itemTotal * (tax / 100);
            });

            this.grandTotal = this.subtotal - this.totalDiscount + this.totalTax;
        },

        formatNumber(num) {
            return parseFloat(num || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    }
}

function productSearch(itemIndex) {
    return {
        searchTerm: '',
        products: [],
        loading: false,
        showDropdown: false,
        selectedProductId: '',

        async searchProducts() {
            if (this.searchTerm.length < 2) {
                this.products = [];
                this.showDropdown = false;
                return;
            }

            this.loading = true;
            this.showDropdown = true;

            try {
                const response = await fetch(`{{ route('tenant.accounting.quotations.search.products', $tenant->slug) }}?q=${encodeURIComponent(this.searchTerm)}`);
                this.products = await response.json();
            } catch (error) {
                console.error('Error searching products:', error);
                this.products = [];
            } finally {
                this.loading = false;
            }
        },

        selectProduct(product) {
            // Find the parent Alpine component
            const quotationComponent = Alpine.$data(document.querySelector('[x-data="quotationItems()"]'));
            
            if (quotationComponent && quotationComponent.items[itemIndex]) {
                quotationComponent.items[itemIndex].product_id = product.id;
                quotationComponent.items[itemIndex].product_name = product.name;
                quotationComponent.items[itemIndex].rate = product.sales_rate;
                quotationComponent.items[itemIndex].description = product.description || '';
                quotationComponent.calculateAmount(itemIndex);
            }

            this.searchTerm = product.name;
            this.selectedProductId = product.id;
            this.showDropdown = false;
        }
    }
}
</script>
@endpush
