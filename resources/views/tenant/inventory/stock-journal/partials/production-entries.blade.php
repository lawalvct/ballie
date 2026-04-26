<!-- Production Entry Items (Two-sided: Consumption & Production) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Production Entry</h3>

    @php
        $productionSource = $stockJournal ?? $duplicateFrom ?? null;
    @endphp
    <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50/60 p-4">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h4 class="text-sm font-semibold text-blue-900">Production Report Details</h4>
                <p class="text-xs text-blue-700 mt-1">These fields are stored with the production run and copied to stock movements when posted.</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-blue-700 border border-blue-200">Manufacturing</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Operator <span class="text-red-500">*</span></label>
                <select name="operator_id" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Select operator</option>
                    @foreach(($employees ?? collect()) as $employee)
                        <option value="{{ $employee->id }}" {{ (string) old('operator_id', $productionSource->operator_id ?? '') === (string) $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}{{ $employee->employee_number ? ' - ' . $employee->employee_number : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Assistant Operator</label>
                <select name="assistant_operator_id"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Select assistant</option>
                    @foreach(($employees ?? collect()) as $employee)
                        <option value="{{ $employee->id }}" {{ (string) old('assistant_operator_id', $productionSource->assistant_operator_id ?? '') === (string) $employee->id ? 'selected' : '' }}>
                            {{ $employee->full_name }}{{ $employee->employee_number ? ' - ' . $employee->employee_number : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Work Order</label>
                <input type="text" name="work_order_number" value="{{ old('work_order_number', $productionSource->work_order_number ?? '') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                    placeholder="WO-0001">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Production Batch</label>
                <input type="text" name="production_batch_number" value="{{ old('production_batch_number', $productionSource->production_batch_number ?? '') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                    placeholder="Batch / lot number">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Shift</label>
                <select name="production_shift"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @php $selectedShift = old('production_shift', $productionSource->production_shift ?? ''); @endphp
                    <option value="">Select shift</option>
                    @foreach(['Morning', 'Afternoon', 'Night', 'General'] as $shift)
                        <option value="{{ $shift }}" {{ $selectedShift === $shift ? 'selected' : '' }}>{{ $shift }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Machine / Line</label>
                <input type="text" name="machine_name" value="{{ old('machine_name', $productionSource->machine_name ?? '') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                    placeholder="Line 1, Mixer A...">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                <input type="time" name="production_started_at" value="{{ old('production_started_at', $productionSource->production_started_at ?? '') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                <input type="time" name="production_ended_at" value="{{ old('production_ended_at', $productionSource->production_ended_at ?? '') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Production Notes</label>
            <textarea name="production_notes" rows="2"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                placeholder="Downtime, quality observations, process notes...">{{ old('production_notes', $productionSource->production_notes ?? '') }}</textarea>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- LEFT SIDE: Source/Consumption (OUT) -->
        <div class="lg:border-r border-gray-200 lg:pr-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-red-700">Source (Consumption) - OUT</h4>
                <button type="button" @click="addConsumptionItem()"
                    class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded-lg shadow-sm hover:bg-red-700">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in consumptionItems" :key="index">
                    <div class="border border-red-200 rounded-lg p-3 bg-red-50/60">
                        <div class="space-y-2">
                            <div>
                                @include('tenant.inventory.stock-journal.partials._product-picker', [
                                    'rateField' => 'cost_price',
                                    'rateLabel' => 'Cost Rate',
                                    'onSelect'  => 'calculateConsumptionAmount(index)',
                                    'accent'    => 'red',
                                ])
                            </div>

                            <div class="grid grid-cols-4 gap-2">
                                <div>
                                    <label class="text-xs text-gray-600">Stock</label>
                                     <input type="text" readonly x-model="item.current_stock"
                                         class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                                           placeholder="Stock">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Qty Used</label>
                                    <input type="number" required
                                           x-model="item.quantity" @input="calculateConsumptionAmount(index)"
                                           step="0.01" min="0.01"
                                         class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                                           placeholder="Qty">
                                </div>
                                <div>
                                     <label class="text-xs text-gray-600">Rate</label>
                                    <input type="number" required
                                           x-model="item.rate" @input="calculateConsumptionAmount(index)"
                                           step="0.01" min="0"
                                         class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                                           placeholder="Rate">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Waste</label>
                                    <input type="number"
                                           x-model="item.waste_quantity"
                                           step="0.0001" min="0"
                                         class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                                           placeholder="Waste">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs text-gray-600">Raw Material Lot/Batch <span class="text-gray-400">(optional)</span></label>
                                    <input type="text" x-model="item.batch_number"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                                        placeholder="Supplier or warehouse batch no.">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Consumption Notes <span class="text-gray-400">(optional)</span></label>
                                    <input type="text" x-model="item.remarks"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                                        placeholder="e.g. issued to mixer line">
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-red-700">
                                    Amount: ₦<span x-text="item.amount.toFixed(2)">0.00</span>
                                </div>
                                <button type="button" @click="removeConsumptionItem(index)"
                                    class="p-1 rounded text-red-600 hover:text-red-800 hover:bg-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][product_id]'" x-model="item.product_id">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][quantity]'" x-model="item.quantity">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][rate]'" x-model="item.rate">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][waste_quantity]'" x-model="item.waste_quantity">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][rejected_quantity]'" value="0">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][batch_number]'" x-model="item.batch_number">
                            <input type="hidden" :name="'items['+consumptionItemIndex(index)+'][remarks]'" x-model="item.remarks">
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
        <div class="lg:pl-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-semibold text-green-700">Destination (Production) - IN</h4>
                <button type="button" @click="addProductionItem()"
                    class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-medium rounded-lg shadow-sm hover:bg-green-700">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in productionItems" :key="index">
                    <div class="border border-green-200 rounded-lg p-3 bg-green-50/60">
                        <div class="space-y-2">
                            <div>
                                @include('tenant.inventory.stock-journal.partials._product-picker', [
                                    'rateField' => 'sales_rate',
                                    'rateLabel' => 'Sales Rate',
                                    'onSelect'  => 'calculateProductionAmount(index)',
                                    'accent'    => 'green',
                                ])
                            </div>

                            <div class="grid grid-cols-4 gap-2">
                                <div>
                                    <label class="text-xs text-gray-600">Stock</label>
                                     <input type="text" readonly x-model="item.current_stock"
                                         class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                                           placeholder="Stock">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Good Qty</label>
                                    <input type="number" required
                                           x-model="item.quantity" @input="calculateProductionAmount(index)"
                                           step="0.01" min="0.01"
                                         class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500"
                                           placeholder="Qty">
                                </div>
                                <div>
                                     <label class="text-xs text-gray-600">Rejected</label>
                                     <input type="number"
                                         x-model="item.rejected_quantity"
                                         step="0.0001" min="0"
                                          class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500"
                                         placeholder="Reject">
                                    </div>
                                    <div>
                                     <label class="text-xs text-gray-600">Rate</label>
                                    <input type="number" required
                                           x-model="item.rate" @input="calculateProductionAmount(index)"
                                           step="0.01" min="0"
                                         class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500"
                                           placeholder="Rate">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="text-xs text-gray-600">Finished Goods Batch <span class="text-gray-400">(optional)</span></label>
                                    <input type="text" x-model="item.batch_number"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500"
                                        placeholder="Production batch no.">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Expiry Date <span class="text-gray-400">(optional)</span></label>
                                    <input type="date" x-model="item.expiry_date"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">Quality Notes <span class="text-gray-400">(optional)</span></label>
                                    <input type="text" x-model="item.remarks"
                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500/20 focus:border-green-500"
                                        placeholder="QC status or remarks">
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="text-sm font-semibold text-green-700">
                                    Amount: ₦<span x-text="item.amount.toFixed(2)">0.00</span>
                                </div>
                                <button type="button" @click="removeProductionItem(index)"
                                    class="p-1 rounded text-red-600 hover:text-red-800 hover:bg-red-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][product_id]'" x-model="item.product_id">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][quantity]'" x-model="item.quantity">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][rate]'" x-model="item.rate">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][rejected_quantity]'" x-model="item.rejected_quantity">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][waste_quantity]'" value="0">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][batch_number]'" x-model="item.batch_number">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][expiry_date]'" x-model="item.expiry_date">
                            <input type="hidden" :name="'items['+productionItemIndex(index)+'][remarks]'" x-model="item.remarks">
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

    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="flex justify-between items-center p-4 rounded-lg bg-blue-50 border border-blue-200">
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
                <div class="mt-1 text-xs text-blue-700">
                    Rejected: <span x-text="totalRejectedQuantity()">0.0000</span> | Waste: <span x-text="totalWasteQuantity()">0.0000</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex items-center justify-between mt-6">
    <a href="{{ route('tenant.inventory.stock-journal.index', ['tenant' => $tenant->slug]) }}"
       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
        Cancel
    </a>

    <div class="flex space-x-3">
        <button type="submit" name="action" value="save" @click="$el.form.querySelector('#stock-journal-action').value = 'save'"
            class="inline-flex items-center px-6 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-gray-700 disabled:opacity-60 disabled:cursor-not-allowed">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            Save as Draft
        </button>

        <button type="submit" name="action" value="save_and_post" @click="$el.form.querySelector('#stock-journal-action').value = 'save_and_post'"
            class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Save & Post
        </button>
    </div>
</div>
