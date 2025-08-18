@extends('layouts.tenant')

@section('title', 'Add Product')
@section('page-title', 'Add New Product')
@section('page-description', 'Add a new product or service to your inventory.')

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Products
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-500">Creating new product</span>
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
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
            <h3 class="text-sm font-medium text-gray-500">Complete all required fields</h3>
            <span class="text-sm font-medium text-green-600" id="progress-indicator">0% Complete</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
        </div>
    </div>

    <form action="{{ route('tenant.inventory.products.store', ['tenant' => $tenant->slug]) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf

        <!-- Section 1: Product Type Selection (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">1</span>
                Product Type
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <input type="radio" id="type_item" name="type" value="item" class="hidden peer" {{ old('type', 'item') === 'item' ? 'checked' : '' }}>
                    <label for="type_item" class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 {{ $errors->has('type') ? 'border-red-300' : 'border-gray-300' }}">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <input type="radio" id="type_service" name="type" value="service" class="hidden peer" {{ old('type') === 'service' ? 'checked' : '' }}>
                    <label for="type_service" class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200 peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 {{ $errors->has('type') ? 'border-red-300' : 'border-gray-300' }}">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">2</span>
                Basic Information
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Enter product name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="hidden text-sm text-red-600 mt-1 field-error" id="name-error"></div>
                </div>

                <div class="form-group">
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">
                        SKU (Stock Keeping Unit)
                    </label>
                    <div class="flex">
                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                            class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('sku') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="Leave empty to auto-generate">
                        <button type="button" onclick="generateSKU()" class="ml-2 mt-1 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('category_id') ? 'border-red-300' : 'border-gray-300' }}">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}"
                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('brand') ? 'border-red-300' : 'border-gray-300' }}"
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
                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('description') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Enter product description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Pricing Information (Always Visible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">3</span>
                Pricing Information
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="form-group">
                    <label for="purchase_rate" class="block text-sm font-medium text-gray-700 mb-1">
                        Purchase Rate/ Net Cost  <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₦</span>
                        <input type="number" name="purchase_rate" id="purchase_rate" value="{{ old('purchase_rate') }}" step="0.01" min="0" required
                               class="mt-1 pl-8 pr-3 py-2 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('purchase_rate') ? 'border-red-300' : 'border-gray-300' }}">
                    </div>
                    @error('purchase_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="sales_rate" class="block text-sm font-medium text-gray-700 mb-1">
                        Sales Rate/Service Cost <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₦</span>
                        <input type="number" name="sales_rate" id="sales_rate" value="{{ old('sales_rate') }}" step="0.01" min="0" required
                               class="mt-1 pl-8 pr-3 py-2 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('sales_rate') ? 'border-red-300' : 'border-gray-300' }}">
                    </div>
                    @error('sales_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mrp" class="block text-sm font-medium text-gray-700 mb-1">
                        MRP (Maximum Retail Price)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">₦</span>
                        <input type="number" name="mrp" id="mrp" value="{{ old('mrp') }}" step="0.01" min="0"
                               class="mt-1 pl-8 pr-3 py-2 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('mrp') ? 'border-red-300' : 'border-gray-300' }}">
                    </div>
                    @error('mrp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 4: Units (Visible only for Items) -->
        <div id="units-section" class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">4</span>
                Units
                <span class="text-red-500 ml-1">*</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="primary_unit_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Primary Unit <span class="text-red-500">*</span>
                    </label>
                    <select name="primary_unit_id" id="primary_unit_id"
                            class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('primary_unit_id') ? 'border-red-300' : 'border-gray-300' }}">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('primary_unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->displayName }}
                            </option>
                        @endforeach
                    </select>
                    @error('primary_unit_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="unit_conversion_factor" class="block text-sm font-medium text-gray-700 mb-1">
                        Unit Conversion Factor
                    </label>
                    <input type="number" name="unit_conversion_factor" id="unit_conversion_factor" value="{{ old('unit_conversion_factor', 1) }}" step="0.000001" min="0.000001"
                           class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('unit_conversion_factor') ? 'border-red-300' : 'border-gray-300' }}">
                    <p class="mt-1 text-xs text-gray-500">For unit conversions (default: 1)</p>
                    @error('unit_conversion_factor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 5: Additional Information (Collapsible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSection('additional-info')">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">5</span>
                    Additional Information
                </h3>
                <svg id="additional-info-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="additional-info-content" class="mt-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="barcode" class="block text-sm font-medium text-gray-700 mb-1">
                            Barcode
                        </label>
                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                               class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('barcode') ? 'border-red-300' : 'border-gray-300' }}"
                               placeholder="Enter barcode">
                        @error('barcode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="hsn_code" class="block text-sm font-medium text-gray-700 mb-1">
                            HSN/SAC Code
                        </label>
                        <input type="text" name="hsn_code" id="hsn_code" value="{{ old('hsn_code') }}"
                               class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('hsn_code') ? 'border-red-300' : 'border-gray-300' }}"
                               placeholder="Enter HSN/SAC code">
                        @error('hsn_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 6: Tax Information (Collapsible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSection('tax-info')">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">6</span>
                    Tax Information
                </h3>
                <svg id="tax-info-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="tax-info-content" class="mt-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-1">
                            Tax Rate (%)
                        </label>
                        <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', 0) }}" step="0.01" min="0" max="100"
                               class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('tax_rate') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('tax_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group flex items-center">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="tax_inclusive" id="tax_inclusive" value="1" {{ old('tax_inclusive') ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="tax_inclusive" class="font-medium text-gray-700">Tax Inclusive</label>
                            <p class="text-gray-500">Check if the sales rate includes tax</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 7: Stock Information (Collapsible, for items only) -->
        <div id="stock-section" class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSection('stock-info')">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">7</span>
                    Stock Information
                </h3>
                <svg id="stock-info-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="stock-info-content" class="mt-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="form-group">
                        <label for="opening_stock" class="block text-sm font-medium text-gray-700 mb-1">
                            Opening Stock
                        </label>
                        <input type="number" name="opening_stock" id="opening_stock" value="{{ old('opening_stock', 0) }}" step="0.01" min="0"
                               class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('opening_stock') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('opening_stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="reorder_level" class="block text-sm font-medium text-gray-700 mb-1">
                            Reorder Level
                        </label>
                        <input type="number" name="reorder_level" id="reorder_level" value="{{ old('reorder_level') }}" step="0.01" min="0"
                               class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('reorder_level') ? 'border-red-300' : 'border-gray-300' }}">
                        <p class="mt-1 text-xs text-gray-500">Alert when stock falls below this level</p>
                        @error('reorder_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group flex items-center">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="maintain_stock" id="maintain_stock" value="1" {{ old('maintain_stock', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="maintain_stock" class="font-medium text-gray-700">Maintain Stock</label>
                            <p class="text-gray-500">Track inventory for this product</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 8: Ledger Accounts (Collapsible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSection('ledger-accounts')">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">8</span>
                    Ledger Accounts
                </h3>
                <svg id="ledger-accounts-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="ledger-accounts-content" class="mt-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="form-group">
                        <label for="stock_asset_account_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Stock Asset Account
                        </label>
                        <select name="stock_asset_account_id" id="stock_asset_account_id"
                                class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('stock_asset_account_id') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Select Account</option>
                            @foreach($ledgerAccounts->where('type', 'asset') as $account)
                                <option value="{{ $account->id }}" {{ old('stock_asset_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('stock_asset_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="sales_account_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Sales Account
                        </label>
                        <select name="sales_account_id" id="sales_account_id"
                                class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('sales_account_id') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Select Account</option>
                            @foreach($ledgerAccounts->where('type', 'income') as $account)
                                <option value="{{ $account->id }}" {{ old('sales_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('sales_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="purchase_account_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Purchase Account
                        </label>
                        <select name="purchase_account_id" id="purchase_account_id"
                                class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('purchase_account_id') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Select Account</option>
                            @foreach($ledgerAccounts->where('type', 'expense') as $account)
                                <option value="{{ $account->id }}" {{ old('purchase_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 9: Product Image (Collapsible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSection('product-image')">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">9</span>
                    Product Image
                </h3>
                <svg id="product-image-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="product-image-content" class="mt-4 hidden">
                <div class="form-group">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        Upload Image
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                    <span>Upload a file</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, GIF up to 2MB
                            </p>
                        </div>
                    </div>
                    <div id="image-preview" class="mt-3 hidden">
                        <div class="flex items-center">
                            <div class="w-16 h-16 border rounded-md overflow-hidden bg-gray-100">
                                <img id="preview-image" src="#" alt="Preview" class="w-full h-full object-cover">
                            </div>
                            <button type="button" id="remove-image" class="ml-3 text-sm text-red-600 hover:text-red-800">
                                Remove
                            </button>
                        </div>
                    </div>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 10: Product Options (Collapsible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSection('product-options')">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">10</span>
                    Product Options
                </h3>
                <svg id="product-options-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="product-options-content" class="mt-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">Active</label>
                            <p class="text-gray-500">Product is available for use</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="is_saleable" id="is_saleable" value="1" {{ old('is_saleable', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_saleable" class="font-medium text-gray-700">Saleable</label>
                            <p class="text-gray-500">Can be sold to customers</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="is_purchasable" id="is_purchasable" value="1" {{ old('is_purchasable', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_purchasable" class="font-medium text-gray-700">Purchasable</label>
                            <p class="text-gray-500">Can be purchased from vendors</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6">
            <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Cancel
            </a>
            <div class="flex items-center space-x-3">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Product
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form sections
    toggleSection('additional-info', true);
    toggleSection('tax-info', true);
    toggleSection('stock-info', true);
    toggleSection('ledger-accounts', true);
    toggleSection('product-image', true);
    toggleSection('product-options', true);

    // Toggle sections based on product type
    const typeRadios = document.querySelectorAll('input[name="type"]');
    const unitsSection = document.getElementById('units-section');
    const stockSection = document.getElementById('stock-section');
    const maintainStockCheckbox = document.getElementById('maintain_stock');
    const primaryUnitSelect = document.getElementById('primary_unit_id');

    // Function to toggle sections based on product type
    function toggleProductType() {
        const selectedType = document.querySelector('input[name="type"]:checked').value;

        if (selectedType === 'service') {
            unitsSection.classList.add('hidden');
            stockSection.classList.add('hidden');
            maintainStockCheckbox.checked = false;
            primaryUnitSelect.required = false;
        } else {
            unitsSection.classList.remove('hidden');
            stockSection.classList.remove('hidden');
            primaryUnitSelect.required = true;
        }

        updateProgressBar();
    }

    // Add event listeners to type radios
    typeRadios.forEach(radio => {
        radio.addEventListener('change', toggleProductType);
    });

    // Initialize based on default selection
    toggleProductType();

    // Image preview functionality
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const removeButton = document.getElementById('remove-image');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });

    removeButton.addEventListener('click', function() {
        imageInput.value = '';
        previewContainer.classList.add('hidden');
        previewImage.src = '#';
    });

    // Form validation
    const productForm = document.getElementById('productForm');
    const nameInput = document.getElementById('name');
    const purchaseRateInput = document.getElementById('purchase_rate');
    const salesRateInput = document.getElementById('sales_rate');

    productForm.addEventListener('submit', function(e) {
        let isValid = true;

        // Validate name
        if (!nameInput.value.trim()) {
            document.getElementById('name-error').textContent = 'Product name is required';
            document.getElementById('name-error').classList.remove('hidden');
            nameInput.classList.add('border-red-300');
            isValid = false;
        } else {
            document.getElementById('name-error').classList.add('hidden');
            nameInput.classList.remove('border-red-300');
        }

        // Validate purchase rate
        if (purchaseRateInput.value < 0) {
            isValid = false;
            purchaseRateInput.classList.add('border-red-300');
        } else {
            purchaseRateInput.classList.remove('border-red-300');
        }

        // Validate sales rate
        if (salesRateInput.value < 0) {
            isValid = false;
            salesRateInput.classList.add('border-red-300');
        } else {
            salesRateInput.classList.remove('border-red-300');
        }

        // Validate primary unit for items
        if (document.querySelector('input[name="type"]:checked').value === 'item' && !primaryUnitSelect.value) {
            isValid = false;
            primaryUnitSelect.classList.add('border-red-300');
        } else {
            primaryUnitSelect.classList.remove('border-red-300');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

    // Progress bar update
    function updateProgressBar() {
        const requiredFields = [
            document.querySelector('input[name="type"]:checked'),
            nameInput,
            purchaseRateInput,
            salesRateInput
        ];

        // Add primary unit if product is an item
        if (document.querySelector('input[name="type"]:checked').value === 'item') {
            requiredFields.push(primaryUnitSelect);
        }

        const filledFields = requiredFields.filter(field => {
            if (!field) return false;
            if (field.type === 'radio') return true; // Radio is always filled since we have a default
            return field.value.trim() !== '';
        });

        const progressPercentage = Math.round((filledFields.length / requiredFields.length) * 100);
        document.getElementById('progress-bar').style.width = `${progressPercentage}%`;
        document.getElementById('progress-indicator').textContent = `${progressPercentage}% Complete`;
    }

    // Add event listeners to update progress bar
    const formInputs = document.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('change', updateProgressBar);
        input.addEventListener('keyup', updateProgressBar);
    });

    // Initialize progress bar
    updateProgressBar();
});

// Function to toggle collapsible sections
function toggleSection(sectionId, forceHide = false) {
    const content = document.getElementById(`${sectionId}-content`);
    const icon = document.getElementById(`${sectionId}-icon`);

    if (forceHide) {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
        return;
    }

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }

    // Smooth scroll to the section
    if (!content.classList.contains('hidden')) {
        setTimeout(() => {
            document.getElementById(sectionId + '-content').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
    }
}

// Function to generate SKU
function generateSKU() {
    const nameInput = document.getElementById('name');
    const categorySelect = document.getElementById('category_id');

    if (!nameInput.value.trim()) {
        alert('Please enter a product name first');
        nameInput.focus();
        return;
    }

    // Get name prefix (first 3 letters)
    let namePrefix = nameInput.value.replace(/[^A-Za-z0-9]/g, '').substring(0, 3).toUpperCase();
    if (namePrefix.length < 3) {
        namePrefix = namePrefix.padEnd(3, 'X');
    }

    // Get category prefix
    let categoryPrefix = 'GN'; // Default: General
    if (categorySelect.value) {
        const categoryName = categorySelect.options[categorySelect.selectedIndex].text;
        categoryPrefix = categoryName.substring(0, 2).toUpperCase();
    }

    // Generate random suffix
    const randomSuffix = Math.floor(Math.random() * 900 + 100); // 100-999

    // Combine to create SKU
    const sku = namePrefix + categoryPrefix + randomSuffix;

    // Set the SKU field
    document.getElementById('sku').value = sku;
}
</script>
@endsection
