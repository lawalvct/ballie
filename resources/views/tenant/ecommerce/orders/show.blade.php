@extends('layouts.tenant')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Order #{{ $order->order_number }}</h1>
            <p class="text-gray-600 mt-1">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
        </div>
        <a href="{{ route('tenant.ecommerce.orders.index', ['tenant' => $tenant->slug]) }}"
           class="px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-200">
            ← Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Order Items</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                        @if($item->product)
                                            <div class="text-xs text-gray-500">{{ $item->product->category->name ?? '' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ $item->product_sku ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm text-gray-900">{{ number_format($item->unit_price, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm text-gray-900">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($item->total_price, 2) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Customer Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Name</p>
                            <p class="text-base font-medium text-gray-900">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Email</p>
                            <p class="text-base font-medium text-gray-900">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Phone</p>
                            <p class="text-base font-medium text-gray-900">{{ $order->customer_phone ?? 'N/A' }}</p>
                        </div>
                        @if($order->customer)
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Customer ID</p>
                                <a href="{{ route('tenant.crm.customers.show', ['tenant' => $tenant->slug, 'customer' => $order->customer_id]) }}"
                                   class="text-base font-medium text-blue-600 hover:text-blue-800">
                                    #{{ $order->customer_id }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Addresses -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Shipping Address</h2>
                    </div>
                    <div class="p-6">
                        @if($order->shippingAddress)
                            <p class="font-medium text-gray-900">{{ $order->shippingAddress->name }}</p>
                            <p class="text-sm text-gray-600 mt-2">{{ $order->shippingAddress->phone }}</p>
                            <p class="text-sm text-gray-600 mt-2">
                                {{ $order->shippingAddress->address_line1 }}<br>
                                @if($order->shippingAddress->address_line2)
                                    {{ $order->shippingAddress->address_line2 }}<br>
                                @endif
                                {{ data_get($order->shippingAddress, 'city.name', $order->shippingAddress->city) }}, {{ data_get($order->shippingAddress, 'state.name', $order->shippingAddress->state) }}<br>
                                {{ $order->shippingAddress->postal_code }}<br>
                                {{ data_get($order->shippingAddress, 'country.name', $order->shippingAddress->country) }}
                            </p>
                        @else
                            <p class="text-gray-500">No shipping address provided</p>
                        @endif
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Billing Address</h2>
                    </div>
                    <div class="p-6">
                        @if($order->billingAddress)
                            <p class="font-medium text-gray-900">{{ $order->billingAddress->name }}</p>
                            <p class="text-sm text-gray-600 mt-2">{{ $order->billingAddress->phone }}</p>
                            <p class="text-sm text-gray-600 mt-2">
                                {{ $order->billingAddress->address_line1 }}<br>
                                @if($order->billingAddress->address_line2)
                                    {{ $order->billingAddress->address_line2 }}<br>
                                @endif
                                {{ data_get($order->billingAddress, 'city.name', $order->billingAddress->city) }}, {{ data_get($order->billingAddress, 'state.name', $order->billingAddress->state) }}<br>
                                {{ $order->billingAddress->postal_code }}<br>
                                {{ data_get($order->billingAddress, 'country.name', $order->billingAddress->country) }}
                            </p>
                        @else
                            <p class="text-gray-500 italic">Same as shipping address</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Order Summary</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium text-gray-900">{{ number_format($order->subtotal_amount, 2) }}</span>
                    </div>

                    @if($order->tax_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tax:</span>
                            <span class="font-medium text-gray-900">{{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($order->shipping_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="font-medium text-gray-900">{{ number_format($order->shipping_amount, 2) }}</span>
                        </div>
                    @endif

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center text-green-600">
                            <span>Discount:</span>
                            <span class="font-medium">-{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total:</span>
                            <span class="text-lg font-bold text-gray-900">{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status & Payment -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Status & Payment</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Order Status:</p>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'processing' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                            ];
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-lg border {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-2">Payment Status:</p>
                        @php
                            $paymentColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'paid' => 'bg-green-100 text-green-800 border-green-200',
                                'failed' => 'bg-red-100 text-red-800 border-red-200',
                                'refunded' => 'bg-gray-100 text-gray-800 border-gray-200',
                            ];
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-lg border {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800 border-gray-200' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 mb-1">Payment Method:</p>
                        <p class="font-medium text-gray-900">{{ strtoupper($order->payment_method) }}</p>
                    </div>

                    @if($order->transaction_id)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Transaction ID:</p>
                            <p class="text-sm font-mono text-gray-900 break-all">{{ $order->transaction_id }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Update Status</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('tenant.ecommerce.orders.update-status', ['tenant' => $tenant->slug, 'order' => $order->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                            <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                            <textarea name="admin_notes" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Optional notes...">{{ $order->admin_notes }}</textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Update Payment Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Update Payment</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('tenant.ecommerce.orders.update-payment', ['tenant' => $tenant->slug, 'order' => $order->id]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                            <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            </select>
                        </div>

                        @if($order->payment_status !== 'paid')
                            <button type="submit"
                                    class="w-full px-6 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200">
                                Update Payment
                            </button>
                        @else
                            <div class="text-center text-sm text-gray-500">Payment already recorded.</div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Create Invoice -->
            @if(!$order->voucher_id && $order->status !== 'cancelled')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Accounting</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">Create a sales invoice and accounting entries for this order.</p>
                        <form method="POST" action="{{ route('tenant.ecommerce.orders.create-invoice', ['tenant' => $tenant->slug, 'order' => $order->id]) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-medium rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200">
                                Create Invoice
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($order->voucher_id)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Linked Invoice</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-3">This order has been invoiced.</p>
                        <a href="{{ route('tenant.accounting.invoices.show', ['tenant' => $tenant->slug, 'invoice' => $order->voucher_id]) }}"
                           class="inline-block px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                            View Invoice
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
