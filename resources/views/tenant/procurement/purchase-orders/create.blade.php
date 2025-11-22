@extends('layouts.tenant')

@section('title', 'Create Purchase Order - ' . $tenant->name)

@section('content')
<div class="space-y-6" x-data="purchaseOrderForm()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Purchase Order (LPO)</h1>
            <p class="mt-2 text-gray-600">Create a new local purchase order for vendor</p>
        </div>
        <a href="{{ route('tenant.procurement.purchase-orders.index', $tenant->slug) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>

    <form action="{{ route('tenant.procurement.purchase-orders.store', $tenant->slug) }}" method="POST">
        @csrf
        
        <div class="space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">LPO Number</label>
                        <input type="text" value="{{ $lpoNumber }}" readonly
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">LPO Date *</label>
                        <input type="date" name="lpo_date" value="{{ old('lpo_date', now()->format('Y-m-d')) }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Vendor *</label>
                        <select name="vendor_id" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->getFullNameAttribute() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Items</h3>
                
                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-start border-b pb-3">
                            <div class="col-span-3">
                                <select x-model="item.product_id" @change="selectProduct(index, $event.target.value)" :name="'items['+index+'][product_id]'" required
                                        class="block w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->purchase_rate }}" data-unit="{{ $product->primaryUnit->symbol ?? 'Pcs' }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <input type="number" x-model="item.quantity" @input="calculateItem(index)" :name="'items['+index+'][quantity]'" step="0.01" min="0.01" required
                                       class="block w-full px-2 py-1 text-sm border border-gray-300 rounded" placeholder="Qty">
                            </div>
                            <div class="col-span-2">
                                <input type="number" x-model="item.unit_price" @input="calculateItem(index)" :name="'items['+index+'][unit_price]'" step="0.01" min="0" required
                                       class="block w-full px-2 py-1 text-sm border border-gray-300 rounded" placeholder="Price">
                            </div>
                            <div class="col-span-2">
                                <input type="number" x-model="item.discount" @input="calculateItem(index)" :name="'items['+index+'][discount]'" step="0.01" min="0"
                                       class="block w-full px-2 py-1 text-sm border border-gray-300 rounded" placeholder="Discount">
                            </div>
                            <div class="col-span-2">
                                <input type="text" x-model="item.total" readonly
                                       class="block w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-50">
                            </div>
                            <div class="col-span-1">
                                <button type="button" @click="removeItem(index)"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <button type="button" @click="addItem"
                        class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Item
                </button>

                <div class="mt-4 flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal:</span>
                            <span x-text="'₦' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span x-text="'₦' + total.toFixed(2)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                        <textarea name="terms_conditions" rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('terms_conditions') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex gap-3 justify-end">
                    <button type="submit" name="action" value="draft"
                            class="inline-flex items-center px-6 py-3 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
                        Save as Draft
                    </button>
                    <button type="submit" name="action" value="send"
                            class="inline-flex items-center px-6 py-3 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700">
                        Save & Send
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function purchaseOrderForm() {
    return {
        items: [{ product_id: '', quantity: 1, unit_price: 0, discount: 0, total: 0 }],
        subtotal: 0,
        total: 0,
        
        addItem() {
            this.items.push({ product_id: '', quantity: 1, unit_price: 0, discount: 0, total: 0 });
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },
        
        selectProduct(index, productId) {
            const select = event.target;
            const option = select.options[select.selectedIndex];
            if (option) {
                this.items[index].unit_price = parseFloat(option.dataset.price) || 0;
                this.calculateItem(index);
            }
        },
        
        calculateItem(index) {
            const item = this.items[index];
            item.total = (item.quantity * item.unit_price) - (item.discount || 0);
            this.calculateTotals();
        },
        
        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
            this.total = this.subtotal;
        }
    }
}
</script>
@endsection
