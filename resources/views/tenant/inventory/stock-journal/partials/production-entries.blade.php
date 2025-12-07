<!-- Production Entry Items (Two-sided: Consumption & Production) -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Production Entry</h3>

    <div class="grid grid-cols-2 gap-6">
        <!-- LEFT SIDE: Source/Consumption (OUT) -->
        <div class="border-r border-gray-200 pr-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-red-700">Source (Consumption) - OUT</h4>
                <button type="button" @click="addConsumptionItem()"
                        class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in consumptionItems" :key="index">
                    <div class="border border-red-200 rounded-lg p-3 bg-red-50">
                        <div class="space-y-2">
                            <div>
                                <select required x-model="item.product_id"
                                        @change="updateConsumptionStock(index, $event.target.value)"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-red-500 focus:border-red-500">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}" data-rate="{{ $product->cost_price }}">
                                            {{ $product->name }} ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <input type="text" readonly x-model="item.current_stock"
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100 text-gray-600"
                                           placeholder="Stock">
                                </div>
                                <div>
                                    <input type="number" required
                                           x-model="item.quantity" @input="calculateConsumptionAmount(index)"
                                           step="0.01" min="0.01"
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-red-500 focus:border-red-500"
                                           placeholder="Qty">
                                </div>
                                <div>
                                    <input type="number" required
                                           x-model="item.rate" @input="calculateConsumptionAmount(index)"
                                           step="0.01" min="0"
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-red-500 focus:border-red-500"
                                           placeholder="Rate">
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-red-700">
                                    Amount: ₦<span x-text="item.amount.toFixed(2)">0.00</span>
                                </div>
                                <button type="button" @click="removeConsumptionItem(index)"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][product_id]'" x-model="item.product_id">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][quantity]'" x-model="item.quantity">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][rate]'" x-model="item.rate">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][movement_type]'" value="out">
                        </div>
                    </div>
                </template>

                <div class="border-t-2 border-red-300 pt-3 mt-3">
                    <div class="flex justify-between items-center font-bold text-red-700">
                        <span>Total Consumption:</span>
                        <span>₦<span x-text="consumptionTotal.toFixed(2)">0.00</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: Destination/Production (IN) -->
        <div class="pl-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-green-700">Destination (Production) - IN</h4>
                <button type="button" @click="addProductionItem()"
                        class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in productionItems" :key="index">
                    <div class="border border-green-200 rounded-lg p-3 bg-green-50">
                        <div class="space-y-2">
                            <div>
                                <select required x-model="item.product_id"
                                        @change="updateProductionStock(index, $event.target.value)"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-green-500 focus:border-green-500">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}" data-rate="{{ $product->cost_price }}">
                                            {{ $product->name }} ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <input type="text" readonly x-model="item.current_stock"
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100 text-gray-600"
                                           placeholder="Stock">
                                </div>
                                <div>
                                    <input type="number" required
                                           x-model="item.quantity" @input="calculateProductionAmount(index)"
                                           step="0.01" min="0.01"
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-green-500 focus:border-green-500"
                                           placeholder="Qty">
                                </div>
                                <div>
                                    <input type="number" required
                                           x-model="item.rate" @input="calculateProductionAmount(index)"
                                           step="0.01" min="0"
                                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-green-500 focus:border-green-500"
                                           placeholder="Rate">
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-green-700">
                                    Amount: ₦<span x-text="item.amount.toFixed(2)">0.00</span>
                                </div>
                                <button type="button" @click="removeProductionItem(index)"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][product_id]'" x-model="item.product_id">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][quantity]'" x-model="item.quantity">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][rate]'" x-model="item.rate">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][movement_type]'" value="in">
                        </div>
                    </div>
                </template>

                <div class="border-t-2 border-green-300 pt-3 mt-3">
                    <div class="flex justify-between items-center font-bold text-green-700">
                        <span>Total Production:</span>
                        <span>₦<span x-text="productionTotal.toFixed(2)">0.00</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 pt-6 border-t-2 border-gray-300">
        <div class="flex justify-between items-center p-4 rounded-lg bg-blue-50 border border-blue-300">
            <div>
                <div class="font-semibold text-blue-800">Balance Check (Optional):</div>
                <div class="text-xs text-blue-600 mt-1">Consumption vs Production</div>
            </div>
            <div class="text-right">
                <div class="font-bold text-blue-800">
                    <template x-if="Math.abs(consumptionTotal - productionTotal) < 0.01">
                        <span class="text-green-600">✓ Balanced</span>
                    </template>
                    <template x-if="Math.abs(consumptionTotal - productionTotal) >= 0.01">
                        <span class="text-orange-600">Difference: ₦<span x-text="Math.abs(consumptionTotal - productionTotal).toFixed(2)">0.00</span></span>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex items-center justify-between mt-6">
    <a href="{{ route('tenant.inventory.stock-journal.index', ['tenant' => $tenant->slug]) }}"
       class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400">
        Cancel
    </a>

    <div class="flex space-x-3">
        <button type="submit" name="action" value="save"
                class="inline-flex items-center px-6 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            Save as Draft
        </button>

        <button type="submit" name="action" value="save_and_post"
                class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Save & Post
        </button>
    </div>
</div>
