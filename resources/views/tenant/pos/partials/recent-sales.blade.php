<div x-show="showRecentSales" class="space-y-4 animate-fade-in">
    <div class="flex items-center justify-between p-4 bg-white/60 dark:bg-gray-800/40 backdrop-blur-sm rounded-2xl border border-gray-200/80 dark:border-gray-700/50">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Sales</h2>
        <button @click="showRecentSales = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="grid gap-4">
        @foreach($recentSales as $sale)
        <div class="bg-white/80 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-gray-200/80 dark:border-gray-700/50 p-4 transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-gray-900 dark:text-white">{{ $sale->sale_number }}</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $sale->created_at->format('H:i') }}</span>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-300 mb-2">
                @if($sale->customer)
                    {{ $sale->customer->customer_type === 'individual'
                        ? $sale->customer->first_name . ' ' . $sale->customer->last_name
                        : $sale->customer->company_name }}
                @else
                    Walk-in Customer
                @endif
            </div>
            <div class="flex items-center justify-between">
                <span class="font-bold text-[var(--color-dark-purple)] dark:text-[var(--color-purple-accent)]">₦{{ number_format($sale->total_amount, 2) }}</span>
                <div class="flex space-x-2">
                    <a href="{{ route('tenant.pos.receipt', ['tenant' => $tenant->slug, 'sale' => $sale->id]) }}"
                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm" target="_blank">
                        <i class="fas fa-receipt"></i> View Receipt
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
