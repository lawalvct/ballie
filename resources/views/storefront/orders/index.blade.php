@extends('layouts.storefront')

@section('title', 'My Orders - ' . ($storeSettings->store_name ?? $tenant->name))

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">My Orders</h1>

        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex flex-wrap items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Order #{{ $order->order_number }}</h3>
                                <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('F d, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'processing') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status === 'refunded') bg-purple-100 text-purple-800
                                    @else bg-orange-100 text-orange-800
                                    @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                @foreach($order->items->take(3) as $item)
                                    <div class="flex items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                                            @if($item->product && $item->product->image_path)
                                                <img src="{{ Storage::disk('public')->url($item->product->image_path) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="w-full h-full object-cover">
                                            @elseif($item->product && $item->product->primaryImage)
                                                <img src="{{ Storage::disk('public')->url($item->product->primaryImage->image_path) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $item->product ? $item->product->name : 'Product' }}
                                            </p>
                                            <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($order->items->count() > 3)
                                <p class="text-sm text-gray-600 mb-4">+{{ $order->items->count() - 3 }} more items</p>
                            @endif

                            <div class="flex flex-wrap items-center justify-between border-t border-gray-200 pt-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total Amount</p>
                                    <p class="text-xl font-bold text-gray-900">
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($order->total_amount, 2) }}
                                    </p>
                                </div>
                                <div class="flex gap-2 mt-4 md:mt-0">
                                    <a href="{{ route('storefront.order.detail', ['tenant' => $tenant->slug, 'order' => $order->id]) }}"
                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        View Details
                                    </a>
                                    <a href="{{ route('storefront.order.invoice', ['tenant' => $tenant->slug, 'order' => $order->id]) }}"
                                       target="_blank"
                                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                        Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">No orders yet</h2>
                <p class="text-gray-600 mb-8">Start shopping to create your first order!</p>
                <a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}"
                   class="inline-block px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
