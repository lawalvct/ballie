<!-- Transfer Entry: single-row layout (From Location -> To Location) -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Stock Transfer</h3>
            <p class="text-xs text-gray-500 mt-1">Move stock from one location to another. The same items leave the source and arrive at the destination.</p>
        </div>
        <span class="inline-flex items-center self-start rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-medium text-amber-800">
            <i class="fas fa-exchange-alt mr-1.5"></i> Transfer
        </span>
    </div>

    @php
        $hasLocations = !empty($stockLocationsEnabled) && isset($stockLocations) && $stockLocations->isNotEmpty();
    @endphp

    @if (!$hasLocations)
        <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 mb-6">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Stock Transfers require the <strong>Stock Locations</strong> module to be enabled and at least two locations to be configured.
            <a href="{{ route('tenant.inventory.stock-locations.index', ['tenant' => $tenant->slug]) }}" class="underline font-semibold">Manage locations</a>.
        </div>
    @endif

    <!-- Location Selection -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                From Location <span class="text-red-500">*</span>
            </label>
            <select name="from_stock_location_id" x-model="fromLocationId" @change="onFromLocationChange()" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                <option value="">Select source location...</option>
                @if ($hasLocations)
                    @foreach ($stockLocations as $loc)
                        <option value="{{ $loc->id }}"
                            {{ (string) old('from_stock_location_id', $stockJournal->from_stock_location_id ?? '') === (string) $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}{{ $loc->is_main ? ' (Main)' : '' }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                To Location <span class="text-red-500">*</span>
            </label>
            <select name="to_stock_location_id" x-model="toLocationId" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-500">
                <option value="">Select destination location...</option>
                @if ($hasLocations)
                    @foreach ($stockLocations as $loc)
                        <option value="{{ $loc->id }}"
                            {{ (string) old('to_stock_location_id', $stockJournal->to_stock_location_id ?? '') === (string) $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }}{{ $loc->is_main ? ' (Main)' : '' }}
                        </option>
                    @endforeach
                @endif
            </select>
            <p x-show="fromLocationId && toLocationId && fromLocationId === toLocationId" class="mt-1 text-xs text-red-600">
                From and To locations must be different.
            </p>
        </div>
    </div>

    <!-- Items Table -->
    <div class="border border-gray-200 rounded-xl">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
            <h4 class="text-sm font-semibold text-gray-800 flex items-center">
                <i class="fas fa-boxes mr-2 text-gray-500"></i> Items to Transfer
            </h4>
            <button type="button" @click="addTransferItem()"
                class="inline-flex items-center px-3 py-1.5 bg-primary-600 text-white text-xs font-semibold rounded-lg shadow-sm hover:bg-primary-700">
                <i class="fas fa-plus mr-1.5"></i> Add Item
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-2/5">Product</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Stock at Source</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Quantity</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Rate</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Amount</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Remarks</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-12"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="(item, index) in transferItems" :key="index">
                        <tr class="hover:bg-gray-50/60">
                            <td class="px-3 py-2 align-top">
                                @include('tenant.inventory.stock-journal.partials._product-picker', [
                                    'rateField' => 'purchase_rate',
                                    'onSelect'  => 'onTransferProductSelect(index)',
                                    'accent'    => 'primary',
                                ])
                                <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                <input type="hidden" :name="`items[${index}][movement_type]`" value="out">
                                <input type="hidden" :name="`items[${index}][stock_location_id]`" :value="fromLocationId">
                            </td>
                            <td class="px-3 py-2 align-top text-right">
                                <span class="text-sm text-gray-700" x-text="(parseFloat(item.current_stock) || 0).toFixed(2) + ' ' + (item.unit || '')"></span>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                       @input="onTransferAmountChange(index)"
                                       step="0.0001" min="0.0001" required
                                       class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 text-right">
                                <p x-show="parseFloat(item.quantity) > parseFloat(item.current_stock || 0)"
                                   class="mt-1 text-[11px] text-red-600">Exceeds source stock</p>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <input type="number" :name="`items[${index}][rate]`" x-model="item.rate"
                                       @input="onTransferAmountChange(index)"
                                       step="0.01" min="0" required
                                       class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 text-right">
                            </td>
                            <td class="px-3 py-2 align-top text-right">
                                <span class="text-sm font-medium text-gray-900" x-text="formatTransferCurrency(item.amount)"></span>
                            </td>
                            <td class="px-3 py-2 align-top">
                                <input type="text" :name="`items[${index}][remarks]`" x-model="item.remarks"
                                       placeholder="Optional"
                                       class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                            </td>
                            <td class="px-3 py-2 align-top text-center">
                                <button type="button" @click="removeTransferItem(index)"
                                        class="p-1 rounded text-red-600 hover:text-red-800 hover:bg-red-50">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="transferItems.length === 0">
                        <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500">
                            No items added yet. Click <span class="font-semibold">Add Item</span> to get started.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:justify-end gap-4 p-4 border-t border-gray-200 bg-gray-50 rounded-b-xl">
            <div class="text-sm text-gray-600">
                Items: <span class="font-semibold text-gray-900" x-text="transferItems.length"></span>
            </div>
            <div class="text-sm text-gray-600">
                Total Quantity: <span class="font-semibold text-gray-900" x-text="totalTransferQuantity()"></span>
            </div>
            <div class="text-sm text-gray-600">
                Total Value: <span class="font-semibold text-primary-700" x-text="formatTransferCurrency(totalTransferAmount())"></span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('tenant.inventory.stock-journal.index', ['tenant' => $tenant->slug]) }}"
           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            Cancel
        </a>
        <button type="submit" name="action" value="save"
                @click="$el.form.querySelector('#stock-journal-action').value = 'save'"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg shadow-sm hover:bg-gray-700 disabled:opacity-60 disabled:cursor-not-allowed">
            {{ isset($stockJournal) ? 'Update Transfer' : 'Save as Draft' }}
        </button>
        @if (!isset($stockJournal))
            <button type="submit" name="action" value="save_and_post"
                    :disabled="!canPostTransfer()"
                    @click="$el.form.querySelector('#stock-journal-action').value = 'save_and_post'"
                    class="px-4 py-2 bg-primary-600 text-white rounded-lg shadow-sm hover:bg-primary-700 disabled:opacity-60 disabled:cursor-not-allowed">
                Save &amp; Post
            </button>
        @endif
    </div>
</div>
