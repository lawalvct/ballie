<!-- Transfer Entries: Two-sided view (FROM location → TO location) -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Stock Transfer Entry</h3>

    <!-- Location Selection -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                FROM Location/Branch <span class="text-red-500">*</span>
            </label>
            <input type="text" name="from_location" x-model="fromLocation" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500"
                   placeholder="e.g., Main Warehouse, Lagos Branch">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                TO Location/Branch <span class="text-red-500">*</span>
            </label>
            <input type="text" name="to_location" x-model="toLocation" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"
                   placeholder="e.g., Retail Store, Abuja Branch">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- LEFT SIDE: FROM Location (OUT) -->
        <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-red-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                    FROM Location (OUT)
                </h4>
                <button type="button" @click="addFromItem()"
                        class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                    + Add Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in fromItems" :key="index">
                    <div class="bg-white rounded p-3 border border-red-200">
                        <div class="grid grid-cols-12 gap-2 items-start">
                            <div class="col-span-5">
                                <label class="text-xs text-gray-600">Product</label>
                                <select x-model="item.product_id" @change="updateFromProductInfo(index)"
                                        class="w-full px-2 py-1 text-sm border rounded focus:ring-1 focus:ring-red-500">
                                    <option value="">Select</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-stock="{{ $product->current_stock ?? 0 }}"
                                                data-rate="{{ $product->purchase_rate ?? 0 }}">
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-600">Stock</label>
                                <input type="text" x-model="item.current_stock" readonly
                                       class="w-full px-2 py-1 text-sm border rounded bg-gray-50 text-gray-600">
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-600">Qty</label>
                                <input type="number" x-model="item.quantity" @input="calculateFromAmount(index)"
                                       class="w-full px-2 py-1 text-sm border rounded focus:ring-1 focus:ring-red-500" step="0.01" min="0">
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-600">Rate</label>
                                <input type="number" x-model="item.rate" @input="calculateFromAmount(index)"
                                       class="w-full px-2 py-1 text-sm border rounded focus:ring-1 focus:ring-red-500" step="0.01" min="0">
                            </div>
                            <div class="col-span-1 flex items-end">
                                <button type="button" @click="removeFromItem(index)"
                                        class="p-1 text-red-600 hover:bg-red-100 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 text-right">
                            <span class="text-xs text-gray-600">Amount: </span>
                            <span class="text-sm font-semibold text-red-700" x-text="'₦' + (item.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-4 pt-3 border-t border-red-300">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-red-800">Total OUT:</span>
                    <span class="text-lg font-bold text-red-700" x-text="'₦' + fromTotal.toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: TO Location (IN) -->
        <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-semibold text-green-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    TO Location (IN)
                </h4>
                <button type="button" @click="addToItem()"
                        class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    + Add Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in toItems" :key="index">
                    <div class="bg-white rounded p-3 border border-green-200">
                        <div class="grid grid-cols-12 gap-2 items-start">
                            <div class="col-span-5">
                                <label class="text-xs text-gray-600">Product</label>
                                <select x-model="item.product_id" @change="updateToProductInfo(index)"
                                        class="w-full px-2 py-1 text-sm border rounded focus:ring-1 focus:ring-green-500">
                                    <option value="">Select</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-stock="{{ $product->current_stock ?? 0 }}"
                                                data-rate="{{ $product->purchase_rate ?? 0 }}">
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-600">Stock</label>
                                <input type="text" x-model="item.current_stock" readonly
                                       class="w-full px-2 py-1 text-sm border rounded bg-gray-50 text-gray-600">
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-600">Qty</label>
                                <input type="number" x-model="item.quantity" @input="calculateToAmount(index)"
                                       class="w-full px-2 py-1 text-sm border rounded focus:ring-1 focus:ring-green-500" step="0.01" min="0">
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs text-gray-600">Rate</label>
                                <input type="number" x-model="item.rate" @input="calculateToAmount(index)"
                                       class="w-full px-2 py-1 text-sm border rounded focus:ring-1 focus:ring-green-500" step="0.01" min="0">
                            </div>
                            <div class="col-span-1 flex items-end">
                                <button type="button" @click="removeToItem(index)"
                                        class="p-1 text-red-600 hover:bg-red-100 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mt-2 text-right">
                            <span class="text-xs text-gray-600">Amount: </span>
                            <span class="text-sm font-semibold text-green-700" x-text="'₦' + (item.amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-4 pt-3 border-t border-green-300">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-green-800">Total IN:</span>
                    <span class="text-lg font-bold text-green-700" x-text="'₦' + toTotal.toLocaleString('en-NG', {minimumFractionDigits: 2})"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Check -->
    <div class="mt-6 p-4 rounded-lg" :class="isBalanced ? 'bg-green-100 border border-green-300' : 'bg-yellow-100 border border-yellow-300'">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" :class="isBalanced ? 'text-green-600' : 'text-yellow-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold" :class="isBalanced ? 'text-green-800' : 'text-yellow-800'">
                    <span x-show="isBalanced">Transfer Balanced</span>
                    <span x-show="!isBalanced">Transfer Not Balanced</span>
                </span>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-600">Difference:</div>
                <div class="text-lg font-bold" :class="isBalanced ? 'text-green-700' : 'text-yellow-700'" x-text="'₦' + Math.abs(difference).toLocaleString('en-NG', {minimumFractionDigits: 2})"></div>
            </div>
        </div>
    </div>

    <!-- Hidden inputs to format data for backend -->
    <template x-for="(item, index) in fromItems" :key="'from-' + index">
        <div>
            <input type="hidden" :name="`items[${index}][product_id]`" x-model="item.product_id">
            <input type="hidden" :name="`items[${index}][quantity]`" x-model="item.quantity">
            <input type="hidden" :name="`items[${index}][rate]`" x-model="item.rate">
            <input type="hidden" :name="`items[${index}][movement_type]`" value="out">
        </div>
    </template>

    <template x-for="(item, index) in toItems" :key="'to-' + index">
        <div>
            <input type="hidden" :name="`items[${fromItems.length + index}][product_id]`" x-model="item.product_id">
            <input type="hidden" :name="`items[${fromItems.length + index}][quantity]`" x-model="item.quantity">
            <input type="hidden" :name="`items[${fromItems.length + index}][rate]`" x-model="item.rate">
            <input type="hidden" :name="`items[${fromItems.length + index}][movement_type]`" value="in">
        </div>
    </template>

    <!-- Action Buttons -->
    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('tenant.inventory.stock-journal.index', $tenant->slug) }}"
           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" name="action" value="save"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            Save as Draft
        </button>
        <button type="submit" name="action" value="save_and_post"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Save & Post
        </button>
    </div>
</div>
