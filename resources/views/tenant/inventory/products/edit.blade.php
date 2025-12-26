@extends('layouts.tenant')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')
@section('page-description', 'Update product information and settings.')

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Product Details
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Editing: {{ $product->name }}</span>
            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
        </div>
    </div>

    <!-- Display validation errors -->
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Display success message if available -->
    @if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-sm font-medium text-gray-500">Update product information</h3>
            <span class="text-sm font-medium text-blue-600" id="progress-indicator">Ready to update</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 100%"></div>
        </div>
    </div>

    <form action="{{ route('tenant.inventory.products.update', ['tenant' => $tenant->slug, 'product' => $product->id]) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        @method('PUT')

        <!-- Section 1: Product Type Selection (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">1</span>
                Product Type
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <input type="radio" id="type_item" name="type" value="item" class="hidden peer" {{ old('type', $product->type) === 'item' ? 'checked' : '' }}>
                    <label for="type_item" class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 {{ $errors->has('type') ? 'border-red-300' : 'border-gray-300' }}">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Item</p>
                            <p class="text-sm text-gray-500">Physical products with inventory</p>
                        </div>
                    </label>
                </div>

                <div class="relative">
                    <input type="radio" id="type_service" name="type" value="service" class="hidden peer" {{ old('type', $product->type) === 'service' ? 'checked' : '' }}>
                    <label for="type_service" class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 {{ $errors->has('type') ? 'border-red-300' : 'border-gray-300' }}">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Service</p>
                            <p class="text-sm text-gray-500">Non-physical services</p>
                        </div>
                    </label>
                </div>
                @error('type')
                    <div class="md:col-span-2 mt-1">
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    </div>
                @enderror
            </div>
        </div>

        <!-- Section 2: Basic Information (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">2</span>
                Basic Information
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Enter product name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                        SKU (Stock Keeping Unit)
                    </label>
                    <div class="flex">
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('sku') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="Enter SKU">
                        <button type="button" onclick="generateSKU()" class="ml-2 mt-1 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Generate
                        </button>
                    </div>
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Category
                    </label>
                    <select name="category_id" id="category_id"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('category_id') ? 'border-red-300' : 'border-gray-300' }}">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">
                        Brand
                    </label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand) }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('brand') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Enter brand name">
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('description') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Pricing Information (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">3</span>
                Pricing Information
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="form-group">
                    <label for="purchase_rate" class="block text-sm font-medium text-gray-700 mb-1">
                        Purchase Rate <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₦</span>
                        <input type="number" name="purchase_rate" id="purchase_rate" step="0.01" min="0" value="{{ old('purchase_rate', $product->purchase_rate) }}" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 shadow-sm sm:text-sm rounded-md {{ $errors->has('purchase_rate') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="0.00">
                    </div>
                    @error('purchase_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <span class="text-xs text-gray-500" id="purchase_rate_formatted"></span>
                </div>

                <div class="form-group">
                    <label for="sales_rate" class="block text-sm font-medium text-gray-700 mb-1">
                        Sales Rate <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₦</span>
                        <input type="number" name="sales_rate" id="sales_rate" step="0.01" min="0" value="{{ old('sales_rate', $product->sales_rate) }}" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 shadow-sm sm:text-sm rounded-md {{ $errors->has('sales_rate') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="0.00">
                    </div>
                    @error('sales_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <span class="text-xs text-gray-500" id="sales_rate_formatted"></span>
                </div>

                <div class="form-group">
                    <label for="mrp" class="block text-sm font-medium text-gray-700 mb-1">
                        MRP (Maximum Retail Price)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₦</span>
                        <input type="number" name="mrp" id="mrp" step="0.01" min="0" value="{{ old('mrp', $product->mrp) }}"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full pl-8 shadow-sm sm:text-sm rounded-md {{ $errors->has('mrp') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="0.00">
                    </div>
                    @error('mrp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <span class="text-xs text-gray-500" id="mrp_formatted"></span>
                </div>
            </div>

            <!-- Tax Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="form-group">
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-1">
                        Tax Rate (%)
                    </label>
                    <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" value="{{ old('tax_rate', $product->tax_rate) }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('tax_rate') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="0.00">
                    @error('tax_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="hsn_code" class="block text-sm font-medium text-gray-700 mb-1">
                        HSN Code
                    </label>
                    <input type="text" name="hsn_code" id="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('hsn_code') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Enter HSN code">
                    @error('hsn_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tax Inclusive Checkbox -->
            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox" name="tax_inclusive" id="tax_inclusive" value="1" {{ old('tax_inclusive', $product->tax_inclusive) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="tax_inclusive" class="ml-2 block text-sm text-gray-900">
                        Tax Inclusive Pricing
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Check if the sales rate includes tax</p>
            </div>
        </div>

        <!-- Section 4: Unit & Stock Information (Conditional) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl" id="stock-section">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">4</span>
                Unit & Stock Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="primary_unit_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Primary Unit <span class="text-red-500">*</span>
                    </label>
                    <select name="primary_unit_id" id="primary_unit_id" required
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('primary_unit_id') ? 'border-red-300' : 'border-gray-300' }}">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('primary_unit_id', $product->primary_unit_id) == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} ({{ $unit->symbol }})
                            </option>
                        @endforeach
                    </select>
                    @error('primary_unit_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">
                        Barcode
                    </label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('barcode') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Enter barcode">
                    @error('barcode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stock Tracking Toggle -->
            <div class="mt-6">
                <div class="flex items-center">
                    <input type="checkbox" name="maintain_stock" id="maintain_stock" value="1" {{ old('maintain_stock', $product->maintain_stock) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        onchange="toggleStockFields()">
                    <label for="maintain_stock" class="ml-2 block text-sm text-gray-900">
                        Track Stock for this Product
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Enable inventory tracking for this product</p>
            </div>

            <!-- Stock Fields (Conditional) -->
            <div id="stock-fields" class="mt-6 {{ old('maintain_stock', $product->maintain_stock) ? '' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="form-group">
                        <label for="opening_stock" class="block text-sm font-medium text-gray-700 mb-1">
                            Opening Stock
                        </label>
                        <input type="number" name="opening_stock" id="opening_stock" step="0.01" min="0" value="{{ old('opening_stock', $product->opening_stock) }}"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('opening_stock') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="0.00">
                        @error('opening_stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-1">
                            Current Stock
                        </label>
                        <input type="number" name="current_stock" id="current_stock" step="0.01" min="0" value="{{ old('current_stock', $product->current_stock) }}"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('current_stock') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="0.00">
                        @error('current_stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="reorder_level" class="block text-sm font-medium text-gray-700 mb-1">
                            Reorder Level
                        </label>
                        <input type="number" name="reorder_level" id="reorder_level" step="0.01" min="0" value="{{ old('reorder_level', $product->reorder_level) }}"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('reorder_level') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="0.00">
                        @error('reorder_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 5: Product Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">5</span>
                Product Settings
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active Product
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_saleable" id="is_saleable" value="1" {{ old('is_saleable', $product->is_saleable) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_saleable" class="ml-2 block text-sm text-gray-900">
                        Can be Sold
                    </label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_purchasable" id="is_purchasable" value="1" {{ old('is_purchasable', $product->is_purchasable) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_purchasable" class="ml-2 block text-sm text-gray-900">
                        Can be Purchased
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 6: Product Images -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">6</span>
                Product Images
            </h3>

            <!-- Primary Image Section -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Primary Image</h4>
                @if($product->image_url)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Primary Image</label>
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg border border-gray-300 shadow-sm">
                </div>
                @endif

                <div class="form-group">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $product->image_url ? 'Replace Primary Image' : 'Upload Primary Image' }}
                        <span class="text-gray-500 font-normal text-xs">(Leave empty to keep current)</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*" onchange="previewImage(this)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Primary Image Preview -->
                <div id="image-preview" class="mt-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Image Preview</label>
                    <img id="preview-img" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                </div>
            </div>

            <!-- Product Gallery Section -->
            <div class="border-t pt-6">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Product Gallery</h4>

                @if($product->images && $product->images->count() > 0)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Current Gallery Images</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="existing-gallery">
                        @foreach($product->images as $image)
                        <div class="relative group" data-image-id="{{ $image->id }}">
                            <img src="{{ Storage::disk('public')->url($image->image_path) }}"
                                 alt="Gallery Image"
                                 class="w-full h-32 object-cover rounded-lg border border-gray-300 shadow-sm">
                            <button type="button"
                                    onclick="deleteGalleryImage({{ $image->id }})"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            @if($image->is_primary)
                            <span class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Primary</span>
                            @endif
                            <span class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">Order: {{ $image->sort_order }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label for="gallery_images" class="block text-sm font-medium text-gray-700 mb-1">
                        Add More Gallery Images
                        <span class="text-gray-500 font-normal text-xs">- Show different views of your product</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-purple-300 border-dashed rounded-md hover:border-purple-400 transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="gallery_images" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                    <span>Upload gallery images</span>
                                    <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple onchange="previewGalleryImages(this)">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">Select multiple images (PNG, JPG, GIF up to 2MB each)</p>
                        </div>
                    </div>
                    @error('gallery_images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Gallery Images Preview -->
                <div id="gallery-preview" class="mt-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-3">New Images Preview</label>
                    <div id="gallery-preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                </div>
            </div>
        </div>

        <!-- Section 7: E-commerce Settings -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 mr-2 text-sm font-semibold">7</span>
                E-commerce Settings
                <span class="ml-2 px-2 py-1 text-xs font-semibold text-blue-600 bg-blue-100 rounded-full">Online Store</span>
            </h3>

            <div class="space-y-6">
                <!-- Slug -->
                <div class="form-group">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                        Product URL Slug
                    </label>
                    <div class="flex">
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('slug') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="product-name-slug">
                        <button type="button" onclick="generateSlug()" class="ml-2 mt-1 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Generate
                        </button>
                    </div>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">URL-friendly name for product page. Leave empty to auto-generate from product name.</p>
                </div>

                <!-- Short Description -->
                <div class="form-group">
                    <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">
                        Short Description
                    </label>
                    <textarea name="short_description" id="short_description" rows="2"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('short_description') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Brief product description for listings">{{ old('short_description', $product->short_description) }}</textarea>
                    @error('short_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Brief description shown in product listings (recommended: 100-150 characters)</p>
                </div>

                <!-- Long Description -->
                <div class="form-group">
                    <label for="long_description" class="block text-sm font-medium text-gray-700 mb-1">
                        Long Description
                    </label>
                    <textarea name="long_description" id="long_description" rows="5"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('long_description') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Detailed product description for product page">{{ old('long_description', $product->long_description) }}</textarea>
                    @error('long_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Detailed description shown on product detail page</p>
                </div>

                <!-- Online Store Options -->
                <div class="border-t pt-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4">Online Store Options</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_visible_online" id="is_visible_online" value="1" {{ old('is_visible_online', $product->is_visible_online) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_visible_online" class="font-medium text-gray-700">Visible on Store</label>
                                <p class="text-gray-500">Show this product on the online store</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_featured" class="font-medium text-gray-700">Featured Product</label>
                                <p class="text-gray-500">Display on homepage featured section</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-2xl p-6 shadow-lg">
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
                   class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>

                <button type="submit" name="action" value="update"
                        class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Update Product
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Generate SKU function
function generateSKU() {
    const name = document.getElementById('name').value;
    const category = document.getElementById('category_id').selectedOptions[0]?.text || '';

    if (name) {
        const namePrefix = name.substring(0, 3).toUpperCase();
        const categoryPrefix = category ? category.substring(0, 2).toUpperCase() : 'GN';
        const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        const sku = `${namePrefix}${categoryPrefix}${randomNum}`;
        document.getElementById('sku').value = sku;
    } else {
        alert('Please enter a product name first');
    }
}

// Generate Slug function
function generateSlug() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (!nameInput.value.trim()) {
        alert('Please enter a product name first');
        nameInput.focus();
        return;
    }

    // Generate slug from product name
    const slug = nameInput.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    slugInput.value = slug;
}

// Toggle stock fields based on maintain_stock checkbox
function toggleStockFields() {
    const maintainStock = document.getElementById('maintain_stock').checked;
    const stockFields = document.getElementById('stock-fields');

    if (maintainStock) {
        stockFields.classList.remove('hidden');
    } else {
        stockFields.classList.add('hidden');
    }
}

// Preview uploaded image
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');

            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// Preview gallery images
function previewGalleryImages(input) {
    const files = input.files;
    const galleryPreview = document.getElementById('gallery-preview');
    const galleryContainer = document.getElementById('gallery-preview-container');

    if (files.length > 0) {
        galleryContainer.innerHTML = '';
        galleryPreview.classList.remove('hidden');

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Gallery ${index + 1}" class="w-full h-32 object-cover rounded-lg border border-gray-300">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity rounded-lg flex items-center justify-center">
                        <span class="text-white text-sm opacity-0 group-hover:opacity-100">New Image ${index + 1}</span>
                    </div>
                `;
                galleryContainer.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else {
        galleryPreview.classList.add('hidden');
        galleryContainer.innerHTML = '';
    }
}

// Delete gallery image
function deleteGalleryImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    fetch(`/{{ $tenant->slug }}/inventory/products/{{ $product->id }}/images/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the image from DOM
            const imageElement = document.querySelector(`[data-image-id="${imageId}"]`);
            if (imageElement) {
                imageElement.remove();
            }

            // Show success message
            alert('Image deleted successfully');

            // Check if gallery is now empty
            const gallery = document.getElementById('existing-gallery');
            if (gallery && gallery.children.length === 0) {
                gallery.parentElement.remove();
            }
        } else {
            alert('Failed to delete image: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the image');
    });
}

// Form validation and progress tracking
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productForm');
    const progressBar = document.getElementById('progress-bar');
    const progressIndicator = document.getElementById('progress-indicator');

    // Initialize stock fields visibility
    toggleStockFields();

    // Real-time thousand separator display for all price fields
    const purchaseRateInput = document.getElementById('purchase_rate');
    const salesRateInput = document.getElementById('sales_rate');
    const mrpInput = document.getElementById('mrp');
    const purchaseRateFormatted = document.getElementById('purchase_rate_formatted');
    const salesRateFormatted = document.getElementById('sales_rate_formatted');
    const mrpFormatted = document.getElementById('mrp_formatted');

    function formatWithThousands(value) {
        if (!value || value === '') return '';
        const number = parseFloat(value);
        if (isNaN(number)) return '';
        return '₦' + number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    if (purchaseRateInput) {
        purchaseRateInput.addEventListener('input', function() {
            purchaseRateFormatted.textContent = formatWithThousands(this.value);
        });
        // Display initial value
        if (purchaseRateInput.value) {
            purchaseRateFormatted.textContent = formatWithThousands(purchaseRateInput.value);
        }
    }

    if (salesRateInput) {
        salesRateInput.addEventListener('input', function() {
            salesRateFormatted.textContent = formatWithThousands(this.value);
        });
        // Display initial value
        if (salesRateInput.value) {
            salesRateFormatted.textContent = formatWithThousands(salesRateInput.value);
        }
    }

    if (mrpInput) {
        mrpInput.addEventListener('input', function() {
            mrpFormatted.textContent = formatWithThousands(this.value);
        });
        // Display initial value
        if (mrpInput.value) {
            mrpFormatted.textContent = formatWithThousands(mrpInput.value);
        }
    }

    // Track form completion
    function updateProgress() {
        const requiredFields = form.querySelectorAll('input[required], select[required]');
        let filledFields = 0;

        requiredFields.forEach(field => {
            if (field.value.trim() !== '') {
                filledFields++;
            }
        });

        const progress = (filledFields / requiredFields.length) * 100;
        progressBar.style.width = progress + '%';

        if (progress === 100) {
            progressIndicator.textContent = 'Ready to update';
            progressBar.classList.remove('bg-blue-600');
            progressBar.classList.add('bg-green-600');
        } else {
            progressIndicator.textContent = `${Math.round(progress)}% complete`;
            progressBar.classList.remove('bg-green-600');
            progressBar.classList.add('bg-blue-600');
        }
    }

    // Add event listeners to required fields
    const requiredFields = form.querySelectorAll('input[required], select[required]');
    requiredFields.forEach(field => {
        field.addEventListener('input', updateProgress);
        field.addEventListener('change', updateProgress);
    });

    // Initial progress calculation
    updateProgress();

    // Form submission handling
    form.addEventListener('submit', function(e) {
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Updating Product...
        `;
    });

    // Auto-save draft functionality (optional)
    let autoSaveTimer;
    function autoSave() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // You can implement auto-save functionality here
            console.log('Auto-saving draft...');
        }, 30000); // Auto-save every 30 seconds
    }

    // Add auto-save listeners
    form.addEventListener('input', autoSave);
    form.addEventListener('change', autoSave);
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+S to save
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        document.getElementById('productForm').submit();
    }

    // Escape to cancel
    if (e.key === 'Escape') {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            window.location.href = "{{ route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id]) }}";
        }
    }
});
</script>

<style>
.form-group {
    transition: all 0.3s ease;
}

.form-group:focus-within {
    transform: translateY(-2px);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Custom checkbox styles */
input[type="checkbox"]:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

/* Progress bar animation */
#progress-bar {
    transition: width 0.5s ease-in-out, background-color 0.3s ease;
}

/* Hover effects for cards */
.bg-white.rounded-2xl:hover {
    transform: translateY(-1px);
}

/* Loading animation for submit button */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
@endsection
