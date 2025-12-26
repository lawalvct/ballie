@extends('layouts.tenant')

@section('title', $product->name . ' - Product Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-full {{ $product->is_active ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-full object-cover">
                    @else
                        <span class="text-lg font-bold {{ $product->is_active ? 'text-green-800' : 'text-red-800' }}">
                            {{ strtoupper(substr($product->name, 0, 2)) }}
                        </span>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="text-gray-600">SKU: <span class="font-mono">{{ $product->sku ?? 'N/A' }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <div class="flex items-left">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <dd class="text-lg font-medium text-gray-900">
                        @if($product->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Product Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">SKU</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $product->sku ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? 'No Category' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($product->type ?? 'Product') }}
                                </span>
                            </dd>
                        </div>
                        @if($product->brand)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Brand</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->brand }}</dd>
                        </div>
                        @endif
                        @if($product->hsn_code)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">HSN Code</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $product->hsn_code }}</dd>
                        </div>
                        @endif
                        @if($product->description)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Pricing Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Purchase Rate</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">₦{{ number_format($product->purchase_rate ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sales Rate</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">₦{{ number_format($product->sales_rate ?? 0, 2) }}</dd>
                        </div>
                        @if($product->mrp)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">MRP</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">₦{{ number_format($product->mrp, 2) }}</dd>
                        </div>
                        @endif
                        @if($product->tax_rate)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tax Rate</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $product->tax_rate }}%</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Stock Information -->
            @if($product->maintain_stock)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Stock Information</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Current Stock</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">
                                {{ number_format($product->current_stock ?? 0, 2) }} {{ $product->primaryUnit->name ?? 'units' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Opening Stock</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">
                                {{ number_format($product->opening_stock ?? 0, 2) }} {{ $product->primaryUnit->name ?? 'units' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reorder Level</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">
                                {{ number_format($product->reorder_level ?? 0, 2) }} {{ $product->primaryUnit->name ?? 'units' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Stock Status</dt>
                            <dd class="mt-1">
                                @php
                                    $stockStatus = $product->stock_status;
                                    $statusColors = [
                                        'in_stock' => 'bg-green-100 text-green-800',
                                        'low_stock' => 'bg-yellow-100 text-yellow-800',
                                        'out_of_stock' => 'bg-red-100 text-red-800',
                                        'not_tracked' => 'bg-gray-100 text-gray-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$stockStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucwords(str_replace('_', ' ', $stockStatus)) }}
                                </span>
                            </dd>
                        </div>
                        @if($product->current_stock_value)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Current Stock Value</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">₦{{ number_format($product->current_stock_value, 2) }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif

            <!-- Features -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Product Features</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-center">
                            @if($product->is_saleable)
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700">Saleable</span>
                        </div>

                        <div class="flex items-center">
                            @if($product->is_purchasable)
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700">Purchasable</span>
                        </div>

                        <div class="flex items-center">
                            @if($product->maintain_stock)
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700">Track Stock</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- E-commerce Information -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        Online Store
                        @if($product->is_visible_online)
                            <span class="ml-2 px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Live</span>
                        @else
                            <span class="ml-2 px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Hidden</span>
                        @endif
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Visibility Status</dt>
                            <dd class="mt-1">
                                @if($product->is_visible_online)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Visible on Storefront
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                                            <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                                        </svg>
                                        Hidden from Storefront
                                    </span>
                                @endif
                            </dd>
                        </div>

                        @if($product->is_visible_online)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Featured Product</dt>
                            <dd class="mt-1">
                                @if($product->is_featured)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Displayed on Homepage
                                    </span>
                                @else
                                    <span class="text-sm text-gray-600">Not featured</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Product URL</dt>
                            <dd class="mt-1">
                                @php
                                    $storeUrl = url('/' . $tenant->slug . '/store/products/' . ($product->slug ?? $product->id));
                                @endphp
                                <a href="{{ $storeUrl }}" target="_blank"
                                   class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    View on Store
                                </a>
                            </dd>
                        </div>

                        @if($product->slug)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">URL Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded border border-gray-200">
                                {{ $product->slug }}
                            </dd>
                        </div>
                        @endif

                        @if($product->short_description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Short Description</dt>
                            <dd class="mt-1 text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded border border-gray-200">
                                {{ $product->short_description }}
                            </dd>
                        </div>
                        @endif

                        @if($product->long_description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Long Description</dt>
                            <dd class="mt-1 text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded border border-gray-200 max-h-32 overflow-y-auto">
                                {{ $product->long_description }}
                            </dd>
                        </div>
                        @endif

                        @if($product->view_count > 0)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">Views</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ number_format($product->view_count) }} {{ Str::plural('view', $product->view_count) }}
                                </span>
                            </dd>
                        </div>
                        @endif
                        @endif
                    </dl>

                    @if(!$product->is_visible_online)
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-3 flex items-start">
                            <svg class="w-5 h-5 mr-2 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            This product is currently hidden from your online store. Update the e-commerce settings to make it visible to customers.
                        </p>
                        <a href="{{ route('tenant.inventory.products.edit', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Update E-commerce Settings
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('tenant.inventory.products.edit', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Product
                    </a>

                    @if($product->is_active)
                        <form action="{{ route('tenant.inventory.products.toggle-status', ['tenant' => $tenant->slug, 'product' => $product->id]) }}" method="POST" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>
                                Deactivate
                            </button>
                        </form>
                    @else
                        <form action="{{ route('tenant.inventory.products.toggle-status', ['tenant' => $tenant->slug, 'product' => $product->id]) }}" method="POST" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Activate
                            </button>
                        </form>
                    @endif

                    <button type="button"
                            onclick="if(confirm('Are you sure you want to delete this product?')) { document.getElementById('delete-form').submit(); }"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Product
                    </button>

                    <form id="delete-form" action="{{ route('tenant.inventory.products.destroy', ['tenant' => $tenant->slug, 'product' => $product->id]) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($product->maintain_stock)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Stock Value</span>
                        <span class="text-sm font-medium text-gray-900">₦{{ number_format($product->current_stock_value ?? 0, 2) }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Unit</span>
                        <span class="text-sm font-medium text-gray-900">{{ $product->primaryUnit->name ?? 'N/A' }}</span>
                    </div>

                    @if($product->barcode)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Barcode</span>
                        <span class="text-sm font-medium text-gray-900 font-mono">{{ $product->barcode }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Created</span>
                        <span class="text-sm font-medium text-gray-900">{{ $product->created_at->format('M d, Y') }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Updated</span>
                        <span class="text-sm font-medium text-gray-900">{{ $product->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Ledger Accounts -->
            @if($product->stockAssetAccount || $product->salesAccount || $product->purchaseAccount)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Linked Accounts</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($product->stockAssetAccount)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stock Asset Account</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->stockAssetAccount->name }}</dd>
                    </div>
                    @endif

                    @if($product->salesAccount)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sales Account</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->salesAccount->name }}</dd>
                    </div>
                    @endif

                    @if($product->purchaseAccount)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Purchase Account</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->purchaseAccount->name }}</dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Product Images -->
            @if($product->image_url || $product->images->count() > 0)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Product Images</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Primary Image -->
                    @if($product->image_url)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">Primary Image</label>
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-lg border-2 border-blue-200">
                    </div>
                    @endif

                    <!-- Gallery Images -->
                    @if($product->images->count() > 0)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wider">
                            Gallery ({{ $product->images->count() }} {{ Str::plural('image', $product->images->count()) }})
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($product->images->sortBy('sort_order') as $image)
                            <div class="relative group">
                                <img src="{{ Storage::disk('public')->url($image->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-24 object-cover rounded-lg border border-gray-200 group-hover:border-blue-400 transition-colors">
                                @if($image->is_primary)
                                <span class="absolute top-1 left-1 bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">Primary</span>
                                @endif
                                <span class="absolute bottom-1 right-1 bg-black bg-opacity-60 text-white text-xs px-2 py-0.5 rounded">#{{ $image->sort_order }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex justify-start">
        <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Products
        </a>
    </div>
</div>
@endsection
