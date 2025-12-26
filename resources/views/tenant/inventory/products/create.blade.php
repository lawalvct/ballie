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
                    <div class="flex items-center justify-between mb-1">
                        <label for="category_id" class="block text-sm font-medium text-gray-700">
                            Category
                        </label>
                        <button type="button" onclick="openQuickCategoryModal()"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-600 hover:text-green-700 hover:bg-green-50 rounded-md transition-colors duration-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Quick Create
                        </button>
                    </div>
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
                    <span class="text-xs text-gray-500" id="purchase_rate_formatted"></span>
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
                    <span class="text-xs text-gray-500" id="sales_rate_formatted"></span>
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
                    <span class="text-xs text-gray-500" id="mrp_formatted"></span>
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
                        <p class="mt-1 text-xs text-gray-500">Initial stock quantity</p>
                    </div>

                    <div class="form-group">
                        <label for="opening_stock_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Opening Stock Date
                        </label>
                        <input type="date" name="opening_stock_date" id="opening_stock_date"
                               value="{{ old('opening_stock_date', now()->subDay()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}"
                               class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('opening_stock_date') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('opening_stock_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Date of stock count</p>
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
                </div>

                <!-- Maintain Stock Checkbox -->
                <div class="mt-4 flex items-center">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="maintain_stock" id="maintain_stock" value="1" {{ old('maintain_stock', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="maintain_stock" class="font-medium text-gray-700">Maintain Stock</label>
                        <p class="text-gray-500">Track inventory for this product</p>
                    </div>
                </div>

                <!-- Info Box -->

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
                            @php
                                $stockAccounts = $ledgerAccounts->where('account_type', 'asset')->filter(function($account) {
                                    return stripos($account->name, 'inventory') !== false ||
                                           stripos($account->name, 'stock') !== false ||
                                           stripos($account->code, 'INV') !== false ||
                                           stripos($account->code, 'STOCK') !== false;
                                });
                                $defaultStockId = $defaultStockAccount->id ?? null;
                            @endphp
                            @foreach($stockAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('stock_asset_account_id', $defaultStockId) == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} @if($account->code)({{ $account->code }})@endif
                                </option>
                            @endforeach
                            @if($stockAccounts->isEmpty())
                                @foreach($ledgerAccounts->where('account_type', 'asset') as $account)
                                    <option value="{{ $account->id }}" {{ old('stock_asset_account_id', $defaultStockId) == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} @if($account->code)({{ $account->code }})@endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('stock_asset_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">For stock inventory valuation (e.g., Inventory, Stock in Hand)</p>
                    </div>

                    <div class="form-group">
                        <label for="sales_account_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Sales Account
                        </label>
                        <select name="sales_account_id" id="sales_account_id"
                                class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('sales_account_id') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Select Account</option>
                            @php
                                $salesAccounts = $ledgerAccounts->where('account_type', 'income')->filter(function($account) {
                                    return stripos($account->name, 'sales') !== false ||
                                           stripos($account->name, 'service income') !== false ||
                                           stripos($account->name, 'revenue') !== false ||
                                           stripos($account->code, 'SALES') !== false ||
                                           stripos($account->code, 'SERV') !== false;
                                });
                                $defaultSalesId = $defaultSalesAccount->id ?? null;
                            @endphp
                            @foreach($salesAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('sales_account_id', $defaultSalesId) == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} @if($account->code)({{ $account->code }})@endif
                                </option>
                            @endforeach
                            @if($salesAccounts->isEmpty())
                                @foreach($ledgerAccounts->where('account_type', 'income') as $account)
                                    <option value="{{ $account->id }}" {{ old('sales_account_id', $defaultSalesId) == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} @if($account->code)({{ $account->code }})@endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('sales_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Revenue from product sales (e.g., Sales Revenue, Service Income)</p>
                    </div>

                    <div class="form-group">
                        <label for="purchase_account_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Purchase Account
                        </label>
                        <select name="purchase_account_id" id="purchase_account_id"
                                class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('purchase_account_id') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Select Account</option>
                            @php
                                $purchaseAccounts = $ledgerAccounts->where('account_type', 'expense')->filter(function($account) {
                                    return stripos($account->name, 'purchase') !== false ||
                                           stripos($account->name, 'cost of goods') !== false ||
                                           stripos($account->name, 'cogs') !== false ||
                                           stripos($account->name, 'direct expenses') !== false ||
                                           stripos($account->code, 'PURCH') !== false ||
                                           stripos($account->code, 'COGS') !== false;
                                });
                                $defaultPurchaseId = $defaultPurchaseAccount->id ?? null;
                            @endphp
                            @foreach($purchaseAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('purchase_account_id', $defaultPurchaseId) == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} @if($account->code)({{ $account->code }})@endif
                                </option>
                            @endforeach
                            @if($purchaseAccounts->isEmpty())
                                @foreach($ledgerAccounts->where('account_type', 'expense') as $account)
                                    <option value="{{ $account->id }}" {{ old('purchase_account_id', $defaultPurchaseId) == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} @if($account->code)({{ $account->code }})@endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('purchase_account_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Cost of goods purchased (e.g., Purchases, Cost of Goods Sold)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 9: Product Images (Collapsible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSection('product-image')">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">9</span>
                    Product Images
                </h3>
                <svg id="product-image-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="product-image-content" class="mt-4 hidden">
                <!-- Primary Product Image -->
                <div class="form-group mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                        Primary Product Image
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                    <span>Upload primary image</span>
                                    <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                PNG, JPG, GIF up to 2MB - This will be the main product image
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

                <!-- Additional Gallery Images -->
                <div class="form-group border-t pt-6">
                    <label for="gallery_images" class="block text-sm font-medium text-gray-700 mb-1">
                        Additional Images (Gallery)
                        <span class="text-gray-500 font-normal text-xs">- Show different views of your product</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-purple-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="gallery_images" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-purple-500">
                                    <span>Upload gallery images</span>
                                    <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" accept="image/*" multiple>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">
                                Select multiple images (PNG, JPG, GIF up to 2MB each)
                            </p>
                        </div>
                    </div>
                    <div id="gallery-preview" class="mt-4 hidden">
                        <p class="text-sm font-medium text-gray-700 mb-3">Gallery Preview:</p>
                        <div id="gallery-preview-container" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                    </div>
                    @error('gallery_images')
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

        <!-- Section 11: E-commerce Settings (Collapsible) -->
        <div class="bg-white rounded-2xl p-6 shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-between cursor-pointer" onclick="toggleSection('ecommerce-settings')">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 mr-2 text-sm font-semibold">11</span>
                    E-commerce Settings
                    <span class="ml-2 px-2 py-1 text-xs font-semibold text-blue-600 bg-blue-100 rounded-full">Online Store</span>
                </h3>
                <svg id="ecommerce-settings-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div id="ecommerce-settings-content" class="mt-4 hidden">
                <!-- Slug -->
                <div class="form-group mb-6">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                        Product URL Slug
                    </label>
                    <div class="flex">
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                            class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('slug') ? 'border-red-300' : 'border-gray-300' }}"
                            placeholder="product-name-slug">
                        <button type="button" onclick="generateSlug()" class="ml-2 mt-1 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Generate
                        </button>
                    </div>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">URL-friendly name for product page. Leave empty to auto-generate from product name.</p>
                </div>

                <!-- Short Description -->
                <div class="form-group mb-6">
                    <label for="short_description" class="block text-sm font-medium text-gray-700 mb-1">
                        Short Description
                    </label>
                    <textarea name="short_description" id="short_description" rows="2"
                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('short_description') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Brief product description for listings">{{ old('short_description') }}</textarea>
                    @error('short_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Brief description shown in product listings (recommended: 100-150 characters)</p>
                </div>

                <!-- Long Description -->
                <div class="form-group mb-6">
                    <label for="long_description" class="block text-sm font-medium text-gray-700 mb-1">
                        Long Description
                    </label>
                    <textarea name="long_description" id="long_description" rows="5"
                        class="mt-1 focus:ring-green-500 focus:border-green-500 block w-full shadow-sm sm:text-sm rounded-md {{ $errors->has('long_description') ? 'border-red-300' : 'border-gray-300' }}"
                        placeholder="Detailed product description for product page">{{ old('long_description') }}</textarea>
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
                                <input type="checkbox" name="is_visible_online" id="is_visible_online" value="1" {{ old('is_visible_online', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_visible_online" class="font-medium text-gray-700">Visible on Store</label>
                                <p class="text-gray-500">Show this product on the online store</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
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

<!-- Quick Create Category Modal -->
<div id="quickCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Quick Create Category</h3>
                <button type="button" onclick="closeQuickCategoryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="quickCategoryForm" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="quick_category_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="quick_category_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500"
                           placeholder="Enter category name">
                </div>

                <div>
                    <label for="quick_category_description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea name="description" id="quick_category_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500"
                              placeholder="Enter category description"></textarea>
                </div>

                <div>
                    <label for="quick_parent_category" class="block text-sm font-medium text-gray-700 mb-1">
                        Parent Category
                    </label>
                    <select name="parent_id" id="quick_parent_category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">None (Root Category)</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="quick_category_active" value="1" checked
                           class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200">
                    <label for="quick_category_active" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-4 border-t mt-4 space-x-3">
                <button type="button" onclick="closeQuickCategoryModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <button type="button" onclick="submitQuickCategory()"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <span id="quick-submit-text">Create Category</span>
                    <svg id="quick-submit-loading" class="hidden animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
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
    toggleSection('ecommerce-settings', true);

    // Declare all form elements at the top to avoid reference errors
    const productForm = document.getElementById('productForm');
    const nameInput = document.getElementById('name');
    const purchaseRateInput = document.getElementById('purchase_rate');
    const salesRateInput = document.getElementById('sales_rate');
    const mrpInput = document.getElementById('mrp');

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

    // Gallery images preview functionality
    const galleryInput = document.getElementById('gallery_images');
    const galleryPreview = document.getElementById('gallery-preview');
    const galleryContainer = document.getElementById('gallery-preview-container');

    galleryInput.addEventListener('change', function() {
        const files = this.files;
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
                            <span class="text-white text-sm opacity-0 group-hover:opacity-100">Image ${index + 1}</span>
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
    });

    // Form validation
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

    // Sync sales_rate to MRP in real-time
    let mrpManuallyEdited = false;

    // Sync sales rate to MRP unless MRP was manually edited
    salesRateInput.addEventListener('input', function() {
        if (!mrpManuallyEdited) {
            mrpInput.value = this.value;
        }
    });

    // Track if user manually edits MRP
    mrpInput.addEventListener('input', function() {
        // If user types in MRP, mark it as manually edited
        mrpManuallyEdited = true;
    });

    // If user clears MRP, resume auto-sync
    mrpInput.addEventListener('blur', function() {
        if (this.value === '' || this.value === '0') {
            mrpManuallyEdited = false;
            this.value = salesRateInput.value;
        }
    });

    // Initialize MRP with sales_rate value on page load if MRP is empty
    if (!mrpInput.value || mrpInput.value === '0') {
        mrpInput.value = salesRateInput.value;
    }

    // Real-time thousand separator display for all price fields
    const purchaseRateFormatted = document.getElementById('purchase_rate_formatted');
    const salesRateFormatted = document.getElementById('sales_rate_formatted');
    const mrpFormatted = document.getElementById('mrp_formatted');

    function formatWithThousands(value) {
        if (!value || value === '') return '';
        const number = parseFloat(value);
        if (isNaN(number)) return '';
        return '₦' + number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    purchaseRateInput.addEventListener('input', function() {
        purchaseRateFormatted.textContent = formatWithThousands(this.value);
    });

    salesRateInput.addEventListener('input', function() {
        salesRateFormatted.textContent = formatWithThousands(this.value);
    });

    mrpInput.addEventListener('input', function() {
        mrpFormatted.textContent = formatWithThousands(this.value);
    });

    // Display initial values if exist
    if (purchaseRateInput.value) {
        purchaseRateFormatted.textContent = formatWithThousands(purchaseRateInput.value);
    }
    if (salesRateInput.value) {
        salesRateFormatted.textContent = formatWithThousands(salesRateInput.value);
    }
    if (mrpInput.value) {
        mrpFormatted.textContent = formatWithThousands(mrpInput.value);
    }
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

// Quick Category Modal Functions
function openQuickCategoryModal() {
    document.getElementById('quickCategoryModal').classList.remove('hidden');
    document.getElementById('quick_category_name').focus();
}

function closeQuickCategoryModal() {
    document.getElementById('quickCategoryModal').classList.add('hidden');
    // Reset form
    document.getElementById('quickCategoryForm').reset();
    // Reset button state
    document.getElementById('quick-submit-text').textContent = 'Create Category';
    document.getElementById('quick-submit-loading').classList.add('hidden');
}

function submitQuickCategory() {
    const form = document.getElementById('quickCategoryForm');
    const formData = new FormData(form);
    const submitButton = document.querySelector('#quickCategoryModal button[onclick="submitQuickCategory()"]');
    const submitText = document.getElementById('quick-submit-text');
    const submitLoading = document.getElementById('quick-submit-loading');

    // Validate required fields
    const name = document.getElementById('quick_category_name').value.trim();
    if (!name) {
        alert('Please enter a category name');
        document.getElementById('quick_category_name').focus();
        return;
    }

    // Show loading state
    submitButton.disabled = true;
    submitText.textContent = 'Creating...';
    submitLoading.classList.remove('hidden');

    // Make AJAX request
    fetch(`{{ route('tenant.inventory.categories.quick-store', ['tenant' => $tenant->slug]) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new category to select dropdown
            const categorySelect = document.getElementById('category_id');
            const newOption = document.createElement('option');
            newOption.value = data.category.id;
            newOption.textContent = data.category.name;
            newOption.selected = true;
            categorySelect.appendChild(newOption);

            // Close modal and show success message
            closeQuickCategoryModal();

            // Show success notification
            showSuccessNotification('Category created successfully!');
        } else {
            // Show error message
            alert(data.message || 'Error creating category');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating category. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitText.textContent = 'Create Category';
        submitLoading.classList.add('hidden');
    });
}

function showSuccessNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50 transform transition-transform duration-300 translate-x-full';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            ${message}
        </div>
    `;

    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Hide notification after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Function to generate Slug
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
