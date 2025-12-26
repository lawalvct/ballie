@extends('layouts.tenant')

@section('title', 'Coupons')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Discount Coupons</h1>
        <a href="{{ route('tenant.ecommerce.coupons.create', ['tenant' => $tenant->slug]) }}"
           class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
            + Create Coupon
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('tenant.ecommerce.coupons.index', ['tenant' => $tenant->slug]) }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Code</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by coupon code..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                <div class="flex items-end space-x-3">
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                        Apply Filters
                    </button>
                    <a href="{{ route('tenant.ecommerce.coupons.index', ['tenant' => $tenant->slug]) }}"
                       class="px-6 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all duration-200">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($coupons->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type & Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Period</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($coupons as $coupon)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900 font-mono">{{ $coupon->code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($coupon->type === 'percentage')
                                            <span class="font-medium text-green-600">{{ $coupon->value }}% OFF</span>
                                        @else
                                            <span class="font-medium text-green-600">{{ number_format($coupon->value, 2) }} OFF</span>
                                        @endif
                                    </div>
                                    @if($coupon->max_discount_amount)
                                        <div class="text-xs text-gray-500">Max: {{ number_format($coupon->max_discount_amount, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">
                                        {{ $coupon->min_order_amount ? number_format($coupon->min_order_amount, 2) : 'None' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}
                                    </div>
                                    @if($coupon->per_customer_limit)
                                        <div class="text-xs text-gray-500">{{ $coupon->per_customer_limit }} per customer</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($coupon->valid_from)
                                            {{ \Carbon\Carbon::parse($coupon->valid_from)->format('M d, Y') }}
                                        @else
                                            No start date
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        @if($coupon->valid_to)
                                            to {{ \Carbon\Carbon::parse($coupon->valid_to)->format('M d, Y') }}
                                            @if(\Carbon\Carbon::parse($coupon->valid_to)->isPast())
                                                <span class="text-red-500 font-medium">(Expired)</span>
                                            @endif
                                        @else
                                            No expiry
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('tenant.ecommerce.coupons.toggle', ['tenant' => $tenant->slug, 'coupon' => $coupon->id]) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors {{ $coupon->is_active ? 'bg-blue-600' : 'bg-gray-300' }}">
                                            <span class="sr-only">Toggle status</span>
                                            <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform {{ $coupon->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('tenant.ecommerce.coupons.edit', ['tenant' => $tenant->slug, 'coupon' => $coupon->id]) }}"
                                           class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('tenant.ecommerce.coupons.destroy', ['tenant' => $tenant->slug, 'coupon' => $coupon->id]) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this coupon? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($coupons->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $coupons->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                    <p class="text-gray-500 text-lg mb-2">No coupons yet</p>
                    <p class="text-gray-400 text-sm mb-4">Create discount coupons to offer special deals to your customers.</p>
                    <a href="{{ route('tenant.ecommerce.coupons.create', ['tenant' => $tenant->slug]) }}"
                       class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                        Create Coupon
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
