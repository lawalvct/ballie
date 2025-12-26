@extends('layouts.storefront')

@section('title', 'Order Success - ' . ($storeSettings->store_name ?? $tenant->name))

@section('content')
<div class="bg-gray-50 py-8 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Success Message -->
            <div class="bg-white rounded-lg shadow-sm p-8 text-center mb-6">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-800 mb-2">Order Placed Successfully!</h1>
                <p class="text-gray-600 mb-6">Thank you for your order. We'll send you a confirmation email shortly.</p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 mb-1">Order Number</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $order->order_number }}</p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('storefront.index', $tenant->slug) }}"
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        Continue Shopping
                    </a>
                    <a href="{{ route('storefront.products', $tenant->slug) }}"
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                        Browse Products
                    </a>
                </div>
            </div>

            <!-- Order Details -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Customer Information -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Customer Information</h3>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><span class="font-medium">Name:</span> {{ $order->customer_name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $order->customer_email }}</p>
                            @if($order->customer_phone)
                                <p><span class="font-medium">Phone:</span> {{ $order->customer_phone }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Shipping Address</h3>
                        @if($order->shippingAddress)
                            <div class="text-sm text-gray-600">
                                <p>{{ $order->shippingAddress->name }}</p>
                                <p>{{ $order->shippingAddress->address_line1 }}</p>
                                @if($order->shippingAddress->address_line2)
                                    <p>{{ $order->shippingAddress->address_line2 }}</p>
                                @endif
                                <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                                <p>{{ $order->shippingAddress->country }}</p>
                                <p class="mt-2">Phone: {{ $order->shippingAddress->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Order Items</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                    @if($item->product && $item->product->image_path)
                                        <img src="{{ Storage::disk('public')->url($item->product->image_path) }}"
                                             alt="{{ $item->product_name }}"
                                             class="w-full h-full object-cover">
                                    @elseif($item->product && $item->product->primaryImage)
                                        <img src="{{ Storage::disk('public')->url($item->product->primaryImage->image_path) }}"
                                             alt="{{ $item->product_name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">{{ $item->product_name }}</h4>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                    @if($item->product_sku)
                                        <p class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->total_price, 2) }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->unit_price, 2) }} each
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Summary</h2>

                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    @if($order->tax_amount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span>{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($order->shipping_amount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->shipping_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</span>
                            <span>-{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 pt-3 mt-3">
                        <div class="flex justify-between text-lg font-bold text-gray-800">
                            <span>Total</span>
                            <span>{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Payment Method</span>
                            <span class="font-semibold text-gray-800">
                                @if($order->payment_method === 'cash_on_delivery')
                                    Cash on Delivery
                                @elseif($order->payment_method === 'paystack')
                                    Paystack
                                @elseif($order->payment_method === 'flutterwave')
                                    Flutterwave
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-gray-600">Payment Status</span>
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'unpaid') bg-yellow-100 text-yellow-800
                                @elseif($order->payment_status === 'refunded') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-gray-600">Order Status</span>
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                @if($order->status === 'delivered') bg-green-100 text-green-800
                                @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                                @elseif($order->status === 'processing') bg-purple-100 text-purple-800
                                @elseif($order->status === 'confirmed') bg-indigo-100 text-indigo-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Order Notes</h3>
                        <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Additional Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
                <h3 class="font-semibold text-gray-800 mb-2">What's Next?</h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>You will receive an order confirmation email at <strong>{{ $order->customer_email }}</strong></span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>We'll notify you when your order is being processed and shipped</span>
                    </li>
                    @if($order->payment_method === 'cash_on_delivery')
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Please have <strong>{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->total_amount, 2) }}</strong> ready for payment upon delivery</span>
                        </li>
                    @endif
                </ul>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mt-6" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
