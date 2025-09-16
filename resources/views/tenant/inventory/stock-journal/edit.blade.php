@extends('layouts.tenant')

@section('title', 'Edit Stock Journal Entry')
@section('page-title', 'Edit Stock Journal Entry')
@section('page-description', 'Update stock movements with detailed journal entries.')

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.inventory.stock-journal.show', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Journal Entry
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Editing: {{ $stockJournal->journal_number }}</span>
            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
        </div>
    </div>

    <!-- Display validation errors -->
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Form -->
    <form method="POST"
          action="{{ route('tenant.inventory.stock-journal.update', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id]) }}"
          x-data="journalEntryForm()"
          x-init="init()"
          class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Header Information Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Journal Entry Details</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Journal Date -->
                <div>
                    <label for="journal_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Journal Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="journal_date" id="journal_date" required
                           value="{{ old('journal_date', $stockJournal->journal_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>

                <!-- Entry Type -->
                <div>
                    <label for="entry_type" class="block text-sm font-medium text-gray-700 mb-1">
                        Entry Type <span class="text-red-500">*</span>
                    </label>
                    <select name="entry_type" id="entry_type" required x-model="entryType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="consumption" {{ old('entry_type', $stockJournal->entry_type) === 'consumption' ? 'selected' : '' }}>Material Consumption</option>
                        <option value="production" {{ old('entry_type', $stockJournal->entry_type) === 'production' ? 'selected' : '' }}>Production Receipt</option>
                        <option value="adjustment" {{ old('entry_type', $stockJournal->entry_type) === 'adjustment' ? 'selected' : '' }}>Stock Adjustment</option>
                        <option value="transfer" {{ old('entry_type', $stockJournal->entry_type) === 'transfer' ? 'selected' : '' }}>Stock Transfer</option>
                    </select>
                </div>

                <!-- Reference Number -->
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">
                        Reference Number
                    </label>
                    <input type="text" name="reference_number" id="reference_number"
                           value="{{ old('reference_number', $stockJournal->reference_number) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500"
                           placeholder="Optional reference">
                </div>

                <!-- Journal Number (Display Only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Journal Number</label>
                    <input type="text" readonly value="{{ $stockJournal->journal_number }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500">
                </div>
            </div>

            <!-- Narration -->
            <div class="mt-6">
                <label for="narration" class="block text-sm font-medium text-gray-700 mb-1">
                    Narration
                </label>
                <textarea name="narration" id="narration" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500"
                          placeholder="Enter narration or description for this journal entry">{{ old('narration', $stockJournal->narration) }}</textarea>
            </div>
        </div>

        <!-- Line Items Card (Tally-like interface) -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Journal Entry Items</h3>
                <button type="button" @click="addItem()"
                        class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Item
                </button>
            </div>

            <!-- Items Table Header -->
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-700 w-1/5">Product</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-700 w-20">Movement</th>
                            <th class="text-right py-2 px-3 text-sm font-medium text-gray-700 w-24">Current Stock</th>
                            <th class="text-right py-2 px-3 text-sm font-medium text-gray-700 w-20">Quantity</th>
                            <th class="text-right py-2 px-3 text-sm font-medium text-gray-700 w-20">Rate</th>
                            <th class="text-right py-2 px-3 text-sm font-medium text-gray-700 w-24">Amount</th>
                            <th class="text-left py-2 px-3 text-sm font-medium text-gray-700 w-24">Batch/Expiry</th>
                            <th class="text-center py-2 px-3 text-sm font-medium text-gray-700 w-16">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic Items will be rendered here by Alpine.js -->
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="border-b border-gray-100">
                                <!-- Product Selection -->
                                <td class="py-2 px-3">
                                    <select :name="`items[${index}][product_id]`" x-model="item.product_id"
                                            @change="updateProductInfo(index)"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-stock="{{ $product->current_stock ?? 0 }}"
                                                    data-unit="{{ $product->primaryUnit->name ?? '' }}"
                                                    data-rate="{{ $product->purchase_rate ?? 0 }}">
                                                {{ $product->name }} ({{ $product->sku ?? 'No SKU' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <!-- Movement Type -->
                                <td class="py-2 px-3">
                                    <select :name="`items[${index}][movement_type]`" x-model="item.movement_type"
                                            @change="calculateAmount(index)"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                                        <option value="in" x-show="entryType === 'production' || entryType === 'adjustment'">In</option>
                                        <option value="out" x-show="entryType === 'consumption' || entryType === 'adjustment' || entryType === 'transfer'">Out</option>
                                    </select>
                                </td>

                                <!-- Current Stock (Display) -->
                                <td class="py-2 px-3 text-right">
                                    <span class="text-sm text-gray-600" x-text="item.current_stock + ' ' + item.unit"></span>
                                </td>

                                <!-- Quantity -->
                                <td class="py-2 px-3">
                                    <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                           @input="calculateAmount(index)"
                                           step="0.0001" min="0.0001" required
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-right">
                                </td>

                                <!-- Rate -->
                                <td class="py-2 px-3">
                                    <input type="number" :name="`items[${index}][rate]`" x-model="item.rate"
                                           @input="calculateAmount(index)"
                                           step="0.01" min="0" required
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 text-right">
                                </td>

                                <!-- Amount (Calculated) -->
                                <td class="py-2 px-3 text-right">
                                    <span class="text-sm font-medium" x-text="formatCurrency(item.amount)"></span>
                                </td>

                                <!-- Batch Number & Expiry -->
                                <td class="py-2 px-3">
                                    <input type="text" :name="`items[${index}][batch_number]`" x-model="item.batch_number"
                                           placeholder="Batch"
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 mb-1">
                                    <input type="date" :name="`items[${index}][expiry_date]`" x-model="item.expiry_date"
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500">
                                </td>

                                <!-- Remove Button -->
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeItem(index)"
                                            class="text-red-600 hover:text-red-900 focus:outline-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <tr x-show="items.length === 0">
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m0 0V6a1 1 0 011-1h2a1 1 0 011 1v1m0 0h2a2 2 0 012 2v4.01"></path>
                                    </svg>
                                    <p class="text-sm">No items added yet. Click "Add Item" to get started.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total Summary -->
            <div class="mt-6 flex justify-end">
                <div class="bg-gray-50 rounded-lg p-4 min-w-64">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-medium text-gray-700">Total Items:</span>
                        <span x-text="items.length"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="font-medium text-gray-700">Total Quantity:</span>
                        <span x-text="totalQuantity()"></span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-semibold mt-2 pt-2 border-t border-gray-200">
                        <span class="text-gray-900">Total Amount:</span>
                        <span class="text-green-600" x-text="formatCurrency(totalAmount())"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <div class="flex space-x-3">
                <a href="{{ route('tenant.inventory.stock-journal.show', ['tenant' => $tenant->slug, 'stockJournal' => $stockJournal->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </a>
            </div>

            <div class="flex space-x-3">
                <button type="submit" name="action" value="save"
                        class="inline-flex items-center px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Update Entry
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function journalEntryForm() {
    return {
        entryType: '{{ old("entry_type", $stockJournal->entry_type) }}',
        items: [],

        init() {
            // Load existing items for edit mode
            this.loadExistingItems();

            // Set default movement type based on entry type
            this.updateMovementTypes();
        },

        loadExistingItems() {
            @foreach($stockJournal->items as $item)
                this.items.push({
                    product_id: '{{ $item->product_id }}',
                    movement_type: '{{ $item->movement_type }}',
                    quantity: {{ $item->quantity }},
                    rate: {{ $item->rate }},
                    amount: {{ $item->amount }},
                    current_stock: {{ $item->product->current_stock ?? 0 }},
                    unit: '{{ $item->product->primaryUnit->name ?? "" }}',
                    batch_number: '{{ $item->batch_number ?? "" }}',
                    expiry_date: '{{ $item->expiry_date ? $item->expiry_date->format("Y-m-d") : "" }}',
                });
            @endforeach
        },

        addItem() {
            const defaultMovementType = this.getDefaultMovementType();
            this.items.push({
                product_id: '',
                movement_type: defaultMovementType,
                quantity: 0,
                rate: 0,
                amount: 0,
                current_stock: 0,
                unit: '',
                batch_number: '',
                expiry_date: '',
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        getDefaultMovementType() {
            if (this.entryType === 'production') return 'in';
            if (this.entryType === 'consumption') return 'out';
            return 'out'; // Default for adjustment and transfer
        },

        updateMovementTypes() {
            // Update movement types for existing items when entry type changes
            this.items.forEach(item => {
                if ((this.entryType === 'production' && item.movement_type === 'out') ||
                    (this.entryType === 'consumption' && item.movement_type === 'in')) {
                    item.movement_type = this.getDefaultMovementType();
                }
            });
        },

        async updateProductInfo(index) {
            const item = this.items[index];
            if (!item.product_id) return;

            try {
                const response = await fetch(`{{ route('tenant.inventory.stock-journal.ajax.product-stock', ['tenant' => $tenant->slug, 'product' => '__PRODUCT__']) }}`.replace('__PRODUCT__', item.product_id));
                const data = await response.json();

                item.current_stock = data.current_stock;
                item.unit = data.unit;
                if (item.rate === 0) {
                    item.rate = data.rate;
                }

                this.calculateAmount(index);
            } catch (error) {
                console.error('Error fetching product info:', error);
            }
        },

        calculateAmount(index) {
            const item = this.items[index];
            item.amount = (parseFloat(item.quantity) || 0) * (parseFloat(item.rate) || 0);
        },

        totalQuantity() {
            return this.items.reduce((total, item) => total + (parseFloat(item.quantity) || 0), 0).toFixed(4);
        },

        totalAmount() {
            return this.items.reduce((total, item) => total + (parseFloat(item.amount) || 0), 0);
        },

        formatCurrency(amount) {
            return '₦' + parseFloat(amount || 0).toLocaleString('en-NG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }
}
</script>
@endpush
@endsection
