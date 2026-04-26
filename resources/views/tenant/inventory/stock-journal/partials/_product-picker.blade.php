@php
    /**
     * Searchable, list-on-focus product picker partial.
     *
     * Required outer scope (provided by parent Alpine component / x-for):
     *   - item   (object with product_id, current_stock, rate, unit)
     *   - index  (current row index)
     *
     * Optional Blade params:
     *   - $rateField  : JS field name on the product to copy into item.rate (default 'purchase_rate')
     *   - $onSelect   : JS expression to run after choosing a product (e.g. "calculateFromAmount(index)")
     *   - $accent     : 'green' | 'red' | 'blue' (visual accent for highlight)
     *   - $placeholder: input placeholder text
     */
    $rateField   = $rateField   ?? 'purchase_rate';
    $onSelect    = $onSelect    ?? '';
    $accent      = $accent      ?? 'blue';
    $placeholder = $placeholder ?? 'Search product by name or SKU...';

    $accentClasses = [
        'green' => ['ring' => 'focus:ring-green-500/30 focus:border-green-500', 'hl' => 'bg-green-50'],
        'red'   => ['ring' => 'focus:ring-red-500/30 focus:border-red-500',     'hl' => 'bg-red-50'],
        'blue'  => ['ring' => 'focus:ring-blue-500/30 focus:border-blue-500',   'hl' => 'bg-blue-50'],
    ];
    $a = $accentClasses[$accent] ?? $accentClasses['blue'];
@endphp
<div x-data="{
        search: '',
        open: false,
        highlight: 0,
        rateField: '{{ $rateField }}',
        get filtered() {
            const list = window.__sjProducts || [];
            const q = (this.search || '').toLowerCase().trim();
            // When the input value already matches the selected product label, show full list (list-on-focus behaviour).
            if (!q || q === (this.selectedLabel() || '').toLowerCase()) return list;
            return list.filter(p =>
                (p.name && p.name.toLowerCase().includes(q)) ||
                (p.sku  && p.sku.toLowerCase().includes(q))
            );
        },
        selectedLabel() {
            if (!item.product_id) return '';
            const p = (window.__sjProducts || []).find(x => String(x.id) === String(item.product_id));
            if (!p) return '';
            return p.sku ? (p.name + ' (' + p.sku + ')') : p.name;
        },
        choose(p) {
            item.product_id     = String(p.id);
            item.current_stock  = parseFloat(p.stock || 0);
            item.rate           = parseFloat(p[this.rateField] ?? p.purchase_rate ?? p.cost_price ?? 0);
            item.unit           = p.unit || '';
            this.search         = p.sku ? (p.name + ' (' + p.sku + ')') : p.name;
            this.open           = false;
            {!! $onSelect !!}
        },
        clear() {
            item.product_id    = '';
            item.current_stock = 0;
            item.rate          = 0;
            item.unit          = '';
            this.search        = '';
            this.open          = true;
        },
        formatMoney(n) {
            return Number(n || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
     }"
     x-init="search = selectedLabel()"
     x-effect="if (item.product_id && !search) search = selectedLabel()"
     @click.away="open = false"
     @keydown.escape.window="open = false"
     class="relative">
    <div class="relative">
        <svg class="w-4 h-4 absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
        </svg>
        <input type="text"
               x-model="search"
               @focus="open = true; $nextTick(() => $event.target.select && $event.target.select())"
               @input="open = true; highlight = 0"
               @keydown.arrow-down.prevent="open = true; highlight = Math.min(highlight + 1, filtered.length - 1)"
               @keydown.arrow-up.prevent="highlight = Math.max(highlight - 1, 0)"
               @keydown.enter.prevent="if (filtered[highlight]) choose(filtered[highlight])"
               placeholder="{{ $placeholder }}"
               autocomplete="off"
               class="w-full pl-7 pr-7 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 {{ $a['ring'] }} bg-white">
        <button type="button" x-show="search.length > 0" @click="clear()"
                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div x-show="open"
         x-transition.opacity.duration.150ms
         x-cloak
         class="absolute z-30 mt-1 w-72 max-w-[22rem] max-h-64 overflow-auto bg-white border border-gray-200 rounded-lg shadow-lg">
        <template x-for="(p, i) in filtered" :key="p.id">
            <div @mousedown.prevent="choose(p)"
                 @mouseenter="highlight = i"
                 :class="highlight === i ? '{{ $a['hl'] }}' : ''"
                 class="px-3 py-2 cursor-pointer border-b border-gray-100 last:border-b-0">
                <div class="flex items-center justify-between gap-2">
                    <div class="text-sm font-medium text-gray-900 truncate" x-text="p.name"></div>
                    <div class="text-xs text-gray-400 whitespace-nowrap" x-text="p.sku ? '#' + p.sku : ''"></div>
                </div>
                <div class="mt-0.5 flex items-center gap-3 text-xs text-gray-500">
                    <span>
                        <span class="text-gray-400">Stock:</span>
                        <span class="font-medium text-gray-700"
                              x-text="(p.stock ?? 0) + (p.unit ? (' ' + p.unit) : '')"></span>
                    </span>
                    <span>
                        <span class="text-gray-400">Rate:</span>
                        <span class="font-medium text-gray-700" x-text="'₦' + formatMoney(p[rateField] ?? p.purchase_rate ?? p.cost_price ?? 0)"></span>
                    </span>
                </div>
            </div>
        </template>
        <div x-show="filtered.length === 0" class="px-3 py-3 text-sm text-gray-500 text-center">
            <svg class="w-5 h-5 mx-auto mb-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2"/>
            </svg>
            No products match "<span x-text="search"></span>"
        </div>
    </div>
</div>
