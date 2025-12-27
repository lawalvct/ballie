@extends('layouts.storefront')

@section('title', $product->name . ' - ' . ($storeSettings->store_name ?? $tenant->name))

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="mb-6">
            <a href="{{ route('storefront.index', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-700">Home</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}" class="text-blue-600 hover:text-blue-700">Products</a>
            @if($product->category)
                <span class="mx-2 text-gray-500">/</span>
                <a href="{{ route('storefront.products', ['tenant' => $tenant->slug, 'category' => $product->category_id]) }}"
                   class="text-blue-600 hover:text-blue-700">{{ $product->category->name }}</a>
            @endif
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-gray-700">{{ $product->name }}</span>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Product Images -->
            <div>
                <!-- Main Image Display -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                    @if($product->image_path)
                        <img src="{{ Storage::disk('public')->url($product->image_path) }}"
                             alt="{{ $product->name }}"
                             id="main-image"
                             class="w-full h-96 object-contain">
                    @elseif($product->primaryImage)
                        <img src="{{ Storage::disk('public')->url($product->primaryImage->image_path) }}"
                             alt="{{ $product->name }}"
                             id="main-image"
                             class="w-full h-96 object-contain">
                    @else
                        <div class="w-full h-96 flex items-center justify-center bg-gray-100 text-gray-400">
                            <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Thumbnail Gallery -->
                @if($product->images->count() > 0 || $product->image_path)
                    <div class="grid grid-cols-4 gap-2">
                        <!-- Primary image thumbnail -->
                        @if($product->image_path)
                            <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-blue-600 hover:border-blue-700 transition-colors">
                                <img src="{{ Storage::disk('public')->url($product->image_path) }}"
                                     alt="{{ $product->name }}"
                                     onclick="changeMainImage(this.src)"
                                     class="w-full h-20 object-cover">
                            </div>
                        @endif

                        <!-- Gallery images thumbnails -->
                        @foreach($product->images as $image)
                            <div class="cursor-pointer rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-600 transition-colors">
                                <img src="{{ Storage::disk('public')->url($image->image_path) }}"
                                     alt="{{ $product->name }}"
                                     onclick="changeMainImage(this.src)"
                                     class="w-full h-20 object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                @if($product->category)
                    <p class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</p>
                @endif

                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>

                @if($product->short_description)
                    <p class="text-gray-600 mb-6">{{ $product->short_description }}</p>
                @endif

                <div class="mb-6">
                    <span class="text-4xl font-bold text-gray-900">
                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($product->sales_rate, 2) }}
                    </span>
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    @if($product->maintain_stock)
                        @if($product->current_stock > 0)
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                In Stock ({{ $product->current_stock }} available)
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                Out of Stock
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Available
                        </span>
                    @endif
                </div>

                <!-- Add to Cart Form -->
                @if(!$product->maintain_stock || $product->current_stock > 0)
                    <form id="add-to-cart-form" action="{{ route('storefront.cart.add', ['tenant' => $tenant->slug]) }}" method="POST" class="mb-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="flex items-center gap-4 mb-4">
                            <label class="text-sm font-medium text-gray-700">Quantity:</label>
                            <input type="number" name="quantity" id="quantity-input" value="1" min="1"
                                   max="{{ $product->maintain_stock ? $product->current_stock : 999 }}"
                                   class="w-24 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <button type="submit" id="add-to-cart-btn"
                                class="w-full px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span id="btn-text">Add to Cart</span>
                        </button>
                    </form>

                    <!-- View Cart Button (Initially Hidden) -->
                    <a href="{{ route('storefront.cart', ['tenant' => $tenant->slug]) }}"
                       id="view-cart-btn"
                       class="hidden w-full px-8 py-4 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors items-center justify-center gap-2 mt-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>View Cart</span>
                    </a>
                @else
                    <button disabled class="w-full px-8 py-4 bg-gray-300 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
                        Out of Stock
                    </button>
                @endif

                <!-- Product Details -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Product Details</h3>
                    <dl class="space-y-2 text-sm">
                        @if($product->sku)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">SKU:</dt>
                                <dd class="font-medium text-gray-900">{{ $product->sku }}</dd>
                            </div>
                        @endif
                        @if($product->category)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Category:</dt>
                                <dd class="font-medium text-gray-900">{{ $product->category->name }}</dd>
                            </div>
                        @endif
                        @if($product->unit)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Unit:</dt>
                                <dd class="font-medium text-gray-900">{{ $product->unit->name }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        @if($product->long_description)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Description</h2>
                <div class="prose max-w-none text-gray-600">
                    {!! nl2br(e($product->long_description)) !!}
                </div>
            </div>
        @endif

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        @include('storefront.partials.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Function to change main image when clicking thumbnails
function changeMainImage(src) {
    const mainImage = document.getElementById('main-image');
    if (mainImage) {
        mainImage.src = src;

        // Update active thumbnail border
        document.querySelectorAll('.grid div').forEach(div => {
            if (div.querySelector('img')?.src === src) {
                div.classList.remove('border-transparent');
                div.classList.add('border-blue-600');
            } else {
                div.classList.remove('border-blue-600');
                div.classList.add('border-transparent');
            }
        });
    }
}

// AJAX Add to Cart
document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const btn = document.getElementById('add-to-cart-btn');
    const btnText = document.getElementById('btn-text');
    const originalText = btnText.textContent;

    // Disable button and show loading state
    btn.disabled = true;
    btnText.textContent = 'Adding...';

    // Get form data
    const formData = new FormData(form);

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send AJAX request
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            document.getElementById('cart-count').textContent = data.cart_count;

            // Show "View Cart" button
            const viewCartBtn = document.getElementById('view-cart-btn');
            if (viewCartBtn) {
                viewCartBtn.classList.remove('hidden');
                viewCartBtn.classList.add('flex');
            }

            // Show success notification
            showNotification(data.message, 'success');

            // Reset quantity to 1
            document.getElementById('quantity-input').value = 1;
        } else {
            showNotification(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Re-enable button
        btn.disabled = false;
        btnText.textContent = originalText;
    });
});
</script>
@endpush
@endsection
