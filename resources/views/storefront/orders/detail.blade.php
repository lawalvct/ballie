@extends('layouts.storefront')

@section('title', 'Order #' . $order->order_number . ' - ' . ($storeSettings->store_name ?? $tenant->name))

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('storefront.orders', ['tenant' => $tenant->slug]) }}"
               class="text-blue-600 hover:text-blue-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Orders
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->order_number }}</h1>
                            <p class="text-gray-600 mt-1">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                        <a href="{{ route('storefront.order.invoice', ['tenant' => $tenant->slug, 'order' => $order->id]) }}"
                           target="_blank"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download Invoice
                        </a>
                    </div>

                    <div class="flex flex-wrap gap-4 mb-6">
                        <span class="px-4 py-2 rounded-full text-sm font-medium
                            @if($order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                            @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            Status: {{ ucfirst($order->status) }}
                        </span>
                        <span class="px-4 py-2 rounded-full text-sm font-medium
                            @if($order->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status === 'refunded') bg-purple-100 text-purple-800
                            @else bg-orange-100 text-orange-800
                            @endif">
                            Payment: {{ ucfirst($order->payment_status) }}
                        </span>

                        @if($order->payment_status !== 'paid' && $order->payment_method === 'nomba' && $order->status !== 'cancelled')
                            <form action="{{ route('storefront.order.retry-payment', ['tenant' => $tenant->slug, 'order' => $order->id]) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 bg-green-600 text-white rounded-full text-sm font-medium hover:bg-green-700 transition-colors flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Retry Payment
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Order Items -->
                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Order Items</h2>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-4 pb-4 border-b border-gray-100 last:border-b-0">
                                    <div class="w-20 h-20 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                                        @if($item->product && $item->product->image_path)
                                            <img src="{{ Storage::disk('public')->url($item->product->image_path) }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-full h-full object-cover">
                                        @elseif($item->product && $item->product->primaryImage)
                                            <img src="{{ Storage::disk('public')->url($item->product->primaryImage->image_path) }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">
                                            {{ $item->product ? $item->product->name : 'Product' }}
                                        </h3>
                                        <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->unit_price, 2) }} each
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">
                                            {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->total_price, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Submit Dispute Form -->
                @if(in_array($order->status, ['delivered', 'processing', 'shipped']))
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Have an Issue with this Order?</h2>
                        <form action="{{ route('storefront.order.dispute', ['tenant' => $tenant->slug, 'order' => $order->id]) }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="dispute_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                    Reason for Dispute *
                                </label>
                                <select id="dispute_reason" name="dispute_reason" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select a reason</option>
                                    <option value="damaged">Item Damaged</option>
                                    <option value="wrong_item">Wrong Item Received</option>
                                    <option value="not_delivered">Not Delivered</option>
                                    <option value="poor_quality">Poor Quality</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="dispute_message" class="block text-sm font-medium text-gray-700 mb-2">
                                    Describe Your Issue *
                                </label>
                                <textarea id="dispute_message" name="dispute_message" rows="4" required
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                          placeholder="Please provide details about your issue..."></textarea>
                            </div>

                            <button type="submit"
                                    class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                Submit Dispute
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Order Information Sidebar -->
            <div class="space-y-6">
                <!-- Order Totals -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Order Summary</h2>
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
                                <span>Discount</span>
                                <span>-{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-200 pt-3 flex justify-between text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span>{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                @if($order->shippingAddress)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Shipping Address</h2>
                        <div class="text-gray-700">
                            <p class="font-semibold">{{ $order->shippingAddress->name }}</p>
                            <p>{{ $order->shippingAddress->address_line1 }}</p>
                            @if($order->shippingAddress->address_line2)
                                <p>{{ $order->shippingAddress->address_line2 }}</p>
                            @endif
                            <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}</p>
                            <p>{{ $order->shippingAddress->zip_code }}</p>
                            @if($order->shippingAddress->phone)
                                <p class="mt-2">Phone: {{ $order->shippingAddress->phone }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Payment Method</h2>
                    <p class="text-gray-700 capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
