@extends('layouts.tenant')

@section('title', 'Create Coupon')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Create Discount Coupon</h1>
        <a href="{{ route('tenant.ecommerce.coupons.index', ['tenant' => $tenant->slug]) }}"
           class="px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-200">
            ← Back to Coupons
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('tenant.ecommerce.coupons.store', ['tenant' => $tenant->slug]) }}">
            @csrf

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono uppercase @error('code') border-red-500 @enderror"
                               placeholder="SUMMER2024"
                               style="text-transform: uppercase;">
                        <p class="text-xs text-gray-500 mt-1">Will be automatically converted to uppercase</p>
                        @error('code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type <span class="text-red-500">*</span></label>
                        <select name="type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                            <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Discount Value</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Value <span class="text-red-500">*</span></label>
                        <input type="number" name="value" value="{{ old('value') }}" step="0.01" min="0" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('value') border-red-500 @enderror"
                               placeholder="10">
                        <p class="text-xs text-gray-500 mt-1">Enter percentage (e.g., 10 for 10%) or fixed amount</p>
                        @error('value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Order Amount</label>
                        <input type="number" name="min_order_amount" value="{{ old('min_order_amount') }}" step="0.01" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('min_order_amount') border-red-500 @enderror"
                               placeholder="0.00">
                        <p class="text-xs text-gray-500 mt-1">Leave empty for no minimum</p>
                        @error('min_order_amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Discount Amount</label>
                        <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount') }}" step="0.01" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('max_discount_amount') border-red-500 @enderror"
                               placeholder="0.00">
                        <p class="text-xs text-gray-500 mt-1">Leave empty for no cap (useful for percentage discounts)</p>
                        @error('max_discount_amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Usage Limits</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Usage Limit</label>
                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('usage_limit') border-red-500 @enderror"
                               placeholder="Unlimited">
                        <p class="text-xs text-gray-500 mt-1">How many times can this coupon be used in total? Leave empty for unlimited.</p>
                        @error('usage_limit')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Per Customer Limit</label>
                        <input type="number" name="per_customer_limit" value="{{ old('per_customer_limit') }}" min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('per_customer_limit') border-red-500 @enderror"
                               placeholder="Unlimited">
                        <p class="text-xs text-gray-500 mt-1">How many times can one customer use this coupon? Leave empty for unlimited.</p>
                        @error('per_customer_limit')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Validity Period</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Valid From</label>
                        <input type="date" name="valid_from" value="{{ old('valid_from') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('valid_from') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to start immediately</p>
                        @error('valid_from')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Valid To</label>
                        <input type="date" name="valid_to" value="{{ old('valid_to') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('valid_to') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Leave empty for no expiry date</p>
                        @error('valid_to')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active (coupon can be used by customers)</label>
                </div>
            </div>

            <div class="flex space-x-3">
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                    Create Coupon
                </button>
                <a href="{{ route('tenant.ecommerce.coupons.index', ['tenant' => $tenant->slug]) }}"
                   class="px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
