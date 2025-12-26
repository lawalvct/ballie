@extends('layouts.storefront')

@section('title', 'My Account - ' . ($storeSettings->store_name ?? $tenant->name))

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">My Account</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Account Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Account Information</h2>
                        <a href="{{ route('storefront.account.edit', ['tenant' => $tenant->slug]) }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Edit Profile
                        </a>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Name</label>
                            <p class="text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Email</label>
                            <p class="text-gray-900">{{ auth('customer')->user()->email }}</p>
                        </div>

                        @if($customer->phone)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Phone</label>
                                <p class="text-gray-900">{{ $customer->phone }}</p>
                            </div>
                        @endif

                        <div>
                            <label class="text-sm font-medium text-gray-600">Customer Type</label>
                            <p class="text-gray-900 capitalize">{{ $customer->customer_type }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Member Since</label>
                            <p class="text-gray-900">{{ $customer->created_at->format('F Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Saved Addresses -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Saved Addresses</h2>

                    @if($customer->addresses && $customer->addresses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($customer->addresses as $address)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="font-semibold text-gray-900 mb-2">{{ $address->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $address->address_line1 }}</p>
                                    @if($address->address_line2)
                                        <p class="text-sm text-gray-600">{{ $address->address_line2 }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->state }} {{ $address->zip_code }}</p>
                                    @if($address->phone)
                                        <p class="text-sm text-gray-600 mt-2">Phone: {{ $address->phone }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No saved addresses yet.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>

                    <div class="space-y-3">
                        <a href="{{ route('storefront.orders', ['tenant' => $tenant->slug]) }}"
                           class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span class="font-medium text-gray-900">My Orders</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a href="{{ route('storefront.account.edit', ['tenant' => $tenant->slug]) }}"
                           class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span class="font-medium text-gray-900">Edit Profile</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a href="{{ route('storefront.products', ['tenant' => $tenant->slug]) }}"
                           class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="font-medium text-gray-900">Continue Shopping</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
