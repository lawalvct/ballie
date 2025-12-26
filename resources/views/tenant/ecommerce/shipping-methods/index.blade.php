@extends('layouts.tenant')

@section('title', 'Shipping Methods')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Shipping Methods</h1>
        <a href="{{ route('tenant.ecommerce.shipping-methods.create', ['tenant' => $tenant->slug]) }}"
           class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
            + Add Shipping Method
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

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($shippingMethods->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimated Days</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($shippingMethods as $method)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $method->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $method->description ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($method->cost, 2) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $method->estimated_days ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm text-gray-900">{{ $method->sort_order }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('tenant.ecommerce.shipping-methods.toggle', ['tenant' => $tenant->slug, 'shipping_method' => $method->id]) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors {{ $method->is_active ? 'bg-blue-600' : 'bg-gray-300' }}">
                                            <span class="sr-only">Toggle status</span>
                                            <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform {{ $method->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('tenant.ecommerce.shipping-methods.edit', ['tenant' => $tenant->slug, 'shipping_method' => $method->id]) }}"
                                           class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('tenant.ecommerce.shipping-methods.destroy', ['tenant' => $tenant->slug, 'shipping_method' => $method->id]) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this shipping method?');">
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
        @else
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                    <p class="text-gray-500 text-lg mb-2">No shipping methods yet</p>
                    <p class="text-gray-400 text-sm mb-4">Create your first shipping method to start offering delivery options to customers.</p>
                    <a href="{{ route('tenant.ecommerce.shipping-methods.create', ['tenant' => $tenant->slug]) }}"
                       class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200">
                        Create Shipping Method
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
