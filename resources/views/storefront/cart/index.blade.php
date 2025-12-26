@extends('layouts.storefront')

@section('title', 'Shopping Cart - ' . ($storeSettings->store_name ?? $tenant->name))

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>

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

        @if($cart && $cart->items->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        @foreach($cart->items as $item)
                            <div class="flex items-center gap-4 p-6 border-b border-gray-200 last:border-b-0">
                                <!-- Product Image -->
                                <div class="w-24 h-24 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                    @if($item->product && $item->product->image_path)
                                        <img src="{{ Storage::disk('public')->url($item->product->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover">
                                    @elseif($item->product && $item->product->primaryImage)
                                        <img src="{{ Storage::disk('public')->url($item->product->primaryImage->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800">{{ $item->product->name }}</h3>
                                    @if($item->product->category)
                                        <p class="text-sm text-gray-500">{{ $item->product->category->name }}</p>
                                    @endif
                                    <p class="text-lg font-bold text-gray-900 mt-2">
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->unit_price, 2) }}
                                    </p>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-3">
                                    <form action="{{ route('storefront.cart.update', ['tenant' => $tenant->slug, 'item' => $item->id]) }}"
                                          method="POST" class="flex items-center border border-gray-300 rounded-lg">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button"
                                                onclick="this.nextElementSibling.stepDown(); this.closest('form').submit();"
                                                class="px-3 py-2 text-gray-600 hover:text-gray-800">
                                            -
                                        </button>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}"
                                               min="1" max="{{ $item->product->maintain_stock ? $item->product->current_stock : 999 }}"
                                               class="w-16 text-center border-x border-gray-300 py-2 focus:outline-none"
                                               onchange="this.form.submit()">
                                        <button type="button"
                                                onclick="this.previousElementSibling.stepUp(); this.closest('form').submit();"
                                                class="px-3 py-2 text-gray-600 hover:text-gray-800">
                                            +
                                        </button>
                                    </form>
                                </div>

                                <!-- Item Total -->
                                <div class="text-right min-w-[100px]">
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->total_price, 2) }}
                                    </p>
                                </div>

                                <!-- Remove Button -->
                                <form action="{{ route('storefront.cart.remove', ['tenant' => $tenant->slug, 'item' => $item->id]) }}"
                                      method="POST"
                                      onsubmit="return confirm('Remove this item from cart?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <!-- Clear Cart -->
                    <div class="mt-4">
                        <form action="{{ route('storefront.cart.clear', ['tenant' => $tenant->slug]) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to clear your cart?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div>
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Order Summary</h2>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal ({{ $cart->items->count() }} items)</span>
                                <span class="font-medium text-gray-900">
                                    {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($cart->getSubtotal(), 2) }}
                                </span>
                            </div>

                            @if($storeSettings->tax_enabled && $storeSettings->tax_percentage)
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax ({{ $storeSettings->tax_percentage }}%)</span>
                                    <span class="font-medium text-gray-900">
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format(($cart->getSubtotal() * $storeSettings->tax_percentage) / 100, 2) }}
                                    </span>
                                </div>
                            @endif

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between text-lg font-bold text-gray-900">
                                    <span>Total</span>
                                    <span>
                                        @php
                                            $total = $cart->getSubtotal();
                                            if($storeSettings->tax_enabled && $storeSettings->tax_percentage) {
                                                $total += ($cart->getSubtotal() * $storeSettings->tax_percentage) / 100;
                                            }
                                        @endphp
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($total, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('storefront.checkout', ['tenant' => $tenant->slug]) }}"
                           class="block w-full px-6 py-4 bg-blue-600 text-white font-semibold text-center rounded-lg hover:bg-blue-700 transition-colors">
                            Proceed to Checkout
                        </a>

                        <a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}"
                           class="block w-full px-6 py-3 bg-gray-100 text-gray-700 font-medium text-center rounded-lg hover:bg-gray-200 transition-colors mt-3">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Your cart is empty</h2>
                <p class="text-gray-600 mb-8">Add some products to get started!</p>
                <a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}"
                   class="inline-block px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
