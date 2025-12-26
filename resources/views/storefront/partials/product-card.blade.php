<div class="bg-white rounded-lg shadow-sm overflow-hidden group hover:shadow-lg transition-all duration-200">
    <a href="{{ route('storefront.product.show', ['tenant' => $tenant->slug, 'slug' => $product->slug]) }}" class="block">
        <div class="aspect-square bg-gray-100 overflow-hidden">
            @if($product->image_path)
                <img src="{{ Storage::disk('public')->url($product->image_path) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            @elseif($product->primaryImage)
                <img src="{{ Storage::disk('public')->url($product->primaryImage->image_path) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
        </div>
    </a>

    <div class="p-4">
        @if($product->category)
            <p class="text-xs text-gray-500 mb-1">{{ $product->category->name }}</p>
        @endif

        <a href="{{ route('storefront.product.show', ['tenant' => $tenant->slug, 'slug' => $product->slug]) }}"
           class="block">
            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600">
                {{ $product->name }}
            </h3>
        </a>

        @if($product->short_description)
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->short_description }}</p>
        @endif

        <div class="flex items-center justify-between">
            <div>
                <span class="text-2xl font-bold text-gray-900">
                    {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($product->sales_rate, 2) }}
                </span>
            </div>

            @if($product->maintain_stock && $product->current_stock <= 0)
                <span class="text-sm text-red-600 font-medium">Out of Stock</span>
            @else
                <form action="{{ route('storefront.cart.add', ['tenant' => $tenant->slug]) }}" method="POST" class="inline ajax-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                            class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
