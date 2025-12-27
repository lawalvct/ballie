@extends('layouts.storefront')

@section('title', 'Checkout - ' . ($storeSettings->store_name ?? $tenant->name))

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('storefront.checkout.process', $tenant->slug) }}" method="POST" id="checkout-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Checkout Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Shipping Address</h2>

                        @if($addresses && $addresses->count() > 0)
                            <!-- Existing Addresses -->
                            <div class="space-y-3 mb-4">
                                @foreach($addresses as $address)
                                    <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer hover:border-blue-500 transition {{ $address->is_default ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                        <input type="radio"
                                               name="shipping_address_id"
                                               value="{{ $address->id }}"
                                               class="mt-1"
                                               {{ $address->is_default ? 'checked' : '' }}
                                               onchange="toggleNewAddress(false)">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800">{{ $address->name }}</div>
                                            <div class="text-gray-600 text-sm mt-1">
                                                {{ $address->address_line1 }}<br>
                                                @if($address->address_line2)
                                                    {{ $address->address_line2 }}<br>
                                                @endif
                                                {{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}<br>
                                                {{ $address->country }}<br>
                                                Phone: {{ $address->phone }}
                                            </div>
                                            @if($address->is_default)
                                                <span class="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Default</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <!-- Add New Address Option -->
                            <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio"
                                       name="address_option"
                                       value="new"
                                       onchange="toggleNewAddress(true)">
                                <span class="text-gray-700 font-medium">+ Add a new address</span>
                            </label>
                        @endif

                        <!-- New Address Form -->
                        <div id="new-address-form" class="mt-6 space-y-4 {{ $addresses && $addresses->count() > 0 ? 'hidden' : '' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                    <input type="text"
                                           name="new_address[name]"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ old('new_address.name') }}"
                                           required>
                                    @error('new_address.name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                    <input type="tel"
                                           name="new_address[phone]"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ old('new_address.phone') }}"
                                           required>
                                    @error('new_address.phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1 *</label>
                                <input type="text"
                                       name="new_address[address_line1]"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('new_address.address_line1') }}"
                                       placeholder="Street address, P.O. box, company name"
                                       required>
                                @error('new_address.address_line1')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                                <input type="text"
                                       name="new_address[address_line2]"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('new_address.address_line2') }}"
                                       placeholder="Apartment, suite, unit, building, floor, etc.">
                                @error('new_address.address_line2')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                    <input type="text"
                                           name="new_address[city]"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ old('new_address.city') }}"
                                           required>
                                    @error('new_address.city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                    <input type="text"
                                           name="new_address[state]"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ old('new_address.state') }}"
                                           required>
                                    @error('new_address.state')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                    <input type="text"
                                           name="new_address[postal_code]"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           value="{{ old('new_address.postal_code') }}">
                                    @error('new_address.postal_code')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                                <input type="text"
                                       name="new_address[country]"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ old('new_address.country', 'Nigeria') }}"
                                       required>
                                @error('new_address.country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Shipping Method</h2>

                        @if($shippingMethods && $shippingMethods->count() > 0)
                            <div class="space-y-3">
                                @foreach($shippingMethods as $method)
                                    <label class="flex items-center justify-between p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                        <div class="flex items-start gap-3">
                                            <input type="radio"
                                                   name="shipping_method_id"
                                                   value="{{ $method->id }}"
                                                   class="mt-1"
                                                   data-cost="{{ $method->cost }}"
                                                   onchange="updateOrderSummary()"
                                                   {{ $loop->first ? 'checked' : '' }}
                                                   required>
                                            <div>
                                                <div class="font-semibold text-gray-800">{{ $method->name }}</div>
                                                @if($method->description)
                                                    <div class="text-gray-600 text-sm mt-1">{{ $method->description }}</div>
                                                @endif
                                                @if($method->estimated_days)
                                                    <div class="text-gray-500 text-xs mt-1">Estimated: {{ $method->estimated_days }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="font-semibold text-gray-800">
                                            {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($method->cost, 2) }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No shipping methods available. Please contact support.</p>
                        @endif

                        @error('shipping_method_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Payment Method</h2>

                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio"
                                       name="payment_method"
                                       value="cash_on_delivery"
                                       class="mt-1"
                                       checked
                                       required>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800">Cash on Delivery</div>
                                    <div class="text-gray-600 text-sm mt-1">Pay with cash when your order is delivered</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio"
                                       name="payment_method"
                                       value="nomba"
                                       class="mt-1"
                                       required>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 flex items-center gap-2">
                                        Pay with Nomba
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Secure</span>
                                    </div>
                                    <div class="text-gray-600 text-sm mt-1">Pay securely with card, bank transfer, or USSD via Nomba</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition">
                                <input type="radio"
                                       name="payment_method"
                                       value="paystack"
                                       class="mt-1"
                                       required>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800 flex items-center gap-2">
                                        Pay with Paystack
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">Secure</span>
                                    </div>
                                    <div class="text-gray-600 text-sm mt-1">Pay securely with card, bank transfer, or USSD via Paystack</div>
                                </div>
                            </label>
                        </div>

                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Order Notes (Optional)</h2>

                        <textarea name="notes"
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any special instructions for your order?">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Order Summary</h2>

                        <!-- Cart Items -->
                        <div class="space-y-4 mb-6">
                            @foreach($cart->items as $item)
                                <div class="flex gap-3">
                                    <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
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
                                        <h4 class="text-sm font-medium text-gray-800">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-800">
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($item->product->sales_rate * $item->quantity, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Coupon Code -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code</label>
                            <div class="flex gap-2">
                                <input type="text"
                                       name="coupon_code"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Enter code"
                                       value="{{ old('coupon_code') }}">
                                <button type="button"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                    Apply
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4 space-y-3">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span id="subtotal">{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($cart->getSubtotal(), 2) }}</span>
                            </div>

                            @if($storeSettings->tax_enabled)
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax ({{ $storeSettings->tax_percentage }}%)</span>
                                    <span id="tax">{{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($cart->getSubtotal() * $storeSettings->tax_percentage / 100, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span id="shipping">
                                    @if($shippingMethods && $shippingMethods->count() > 0)
                                        {{ $storeSettings->default_currency ?? 'NGN' }} {{ number_format($shippingMethods->first()->cost, 2) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between text-lg font-bold text-gray-800 pt-3 border-t border-gray-200">
                                <span>Total</span>
                                <span id="total">
                                    {{ $storeSettings->default_currency ?? 'NGN' }}
                                    {{ number_format(
                                        $cart->getSubtotal() +
                                        ($storeSettings->tax_enabled ? $cart->getSubtotal() * $storeSettings->tax_percentage / 100 : 0) +
                                        ($shippingMethods && $shippingMethods->count() > 0 ? $shippingMethods->first()->cost : 0),
                                        2
                                    ) }}
                                </span>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Place Order
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-4">
                            By placing your order, you agree to our terms and conditions
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleNewAddress(show) {
    const newAddressForm = document.getElementById('new-address-form');
    const inputs = newAddressForm.querySelectorAll('input, textarea, select');

    if (show) {
        newAddressForm.classList.remove('hidden');
        inputs.forEach(input => {
            if (input.hasAttribute('required-hidden')) {
                input.setAttribute('required', '');
            }
        });
    } else {
        newAddressForm.classList.add('hidden');
        inputs.forEach(input => {
            if (input.hasAttribute('required')) {
                input.setAttribute('required-hidden', '');
                input.removeAttribute('required');
            }
        });
    }
}

function updateOrderSummary() {
    const selectedShipping = document.querySelector('input[name="shipping_method_id"]:checked');
    if (!selectedShipping) return;

    const shippingCost = parseFloat(selectedShipping.dataset.cost);
    const subtotal = {{ $cart->getSubtotal() }};
    const taxPercentage = {{ $storeSettings->tax_enabled ? $storeSettings->tax_percentage : 0 }};
    const taxAmount = (subtotal * taxPercentage) / 100;
    const total = subtotal + taxAmount + shippingCost;

    const currency = '{{ $storeSettings->default_currency ?? "NGN" }}';

    document.getElementById('shipping').textContent = currency + ' ' + shippingCost.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('total').textContent = currency + ' ' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set required attributes for new address form if it's visible
    const newAddressForm = document.getElementById('new-address-form');
    if (!newAddressForm.classList.contains('hidden')) {
        const inputs = newAddressForm.querySelectorAll('input[required-hidden]');
        inputs.forEach(input => {
            input.setAttribute('required', '');
            input.removeAttribute('required-hidden');
        });
    }
});
</script>
@endsection
