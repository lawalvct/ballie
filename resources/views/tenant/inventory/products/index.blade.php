@extends('layouts.tenant')

@section('title', 'Products & Services')

@section('page-title', 'Products & Services')
@section('page-description')
    <span class="hidden md:inline">
  Manage your inventory items and services.
    </span>
@endsection
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="mt-4 lg:mt-0 flex flex-wrap gap-3">
            <button onclick="openImportProductsModal()"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Upload Products
            </button>

            <a href="{{ route('tenant.inventory.products.create', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Product
            </a>

            <a href="{{ route('tenant.inventory.stock-journal.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Stock Vouchers
            </a>

            <a href="{{ route('tenant.inventory.stock-journal.create.type', ['tenant' => $tenant->slug, 'type' => 'production']) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Stock Transfer
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $products->total() ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Products</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $products->where('is_active', true)->count() ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Low Stock</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $products->filter(function($p) { return $p->stock_status === 'low_stock'; })->count() ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Out of Stock</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $products->filter(function($p) { return $p->stock_status === 'out_of_stock'; })->count() ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Date and Valuation Method Filter -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Stock Date Filter</h3>
            @if(request('as_of_date') !== now()->toDateString())
                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    Viewing historical data
                </span>
            @endif
        </div>

        <form method="GET" class="flex flex-wrap items-end gap-4">
            <!-- Preserve existing filters -->
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            @if(request('category_id'))
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('stock_status'))
                <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
            @endif

            <div class="flex-1 min-w-0">
                <label for="as_of_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Stock as of Date
                </label>
                <input type="date"
                       id="as_of_date"
                       name="as_of_date"
                       value="{{ $asOfDate ?? now()->toDateString() }}"
                       max="{{ now()->toDateString() }}"
                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="flex-1 min-w-0">
                <label for="valuation_method" class="block text-sm font-medium text-gray-700 mb-1">
                    Valuation Method
                </label>
                <select id="valuation_method"
                        name="valuation_method"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500">
                    <option value="weighted_average" {{ ($valuationMethod ?? 'weighted_average') === 'weighted_average' ? 'selected' : '' }}>
                        Weighted Average
                    </option>
                    <option value="fifo" {{ ($valuationMethod ?? '') === 'fifo' ? 'selected' : '' }}>
                        FIFO (First In, First Out)
                    </option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Update View
                </button>

                @if(request()->hasAny(['as_of_date', 'valuation_method']) && (request('as_of_date') !== now()->toDateString() || request('valuation_method') !== 'weighted_average'))
                    <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}"
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Reset to Current
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="{ filtersExpanded: false }">
        <div class="p-6">
            <!-- Header with Toggle Button -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Search & Filters</h3>
                <button @click="filtersExpanded = !filtersExpanded"
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <span x-text="filtersExpanded ? 'Hide Filters' : 'Show Filters'"></span>
                    <svg class="ml-2 h-4 w-4 transition-transform duration-200"
                         :class="{ 'rotate-180': filtersExpanded }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            <!-- Always Visible Search Bar -->
            <form method="GET" class="space-y-4">
                <div class="flex-1">
                    <label for="search" class="sr-only">Search products</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500"
                               placeholder="Search by name, SKU, or barcode...">
                    </div>
                </div>

                <!-- Collapsible Filters Section -->
                <div x-show="filtersExpanded"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Product Type</label>
                        <select name="type"
                                id="type"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg">
                            <option value="">All Types</option>
                            <option value="item" {{ request('type') === 'item' ? 'selected' : '' }}>Items</option>
                            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Services</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id"
                                id="category_id"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg">
                            <option value="">All Categories</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                                id="status"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Stock Status Filter -->
                    <div>
                        <label for="stock_status" class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                        <select name="stock_status"
                                id="stock_status"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-lg">
                            <option value="">All Stock Status</option>
                            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                            </svg>
                            Apply Filters
                        </button>

                        @if(request()->hasAny(['search', 'type', 'category_id', 'status', 'stock_status']))
                            <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear All
                            </a>
                        @endif
                    </div>

                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['search', 'type', 'category_id', 'status', 'stock_status']))
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm text-gray-500">Active filters:</span>

                            @if(request('search'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            @if(request('type'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Type: {{ ucfirst(request('type')) }}
                                    <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            @if(request('status'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Status: {{ ucfirst(request('status')) }}
                                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        @if($products->count() > 0)
            <!-- Bulk Actions -->
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50" x-data="{ selectedItems: [], showBulkActions: false }" x-init="$watch('selectedItems', value => showBulkActions = value.length > 0)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox"
                               @change="selectedItems = $event.target.checked ? Array.from(document.querySelectorAll('input[name=\'products[]\']')).map(cb => cb.value) : []"
                               class="form-checkbox h-4 w-4 text-green-600 rounded">
                        <span class="text-sm text-gray-700">Select All</span>
                    </div>

                    <div x-show="showBulkActions" x-transition class="flex items-center space-x-2">
                        <span class="text-sm text-gray-700" x-text="`${selectedItems.length} selected`"></span>

                        <button type="button"
                                onclick="bulkAction('activate')"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200">
                            Activate
                        </button>

                        <button type="button"
                                onclick="bulkAction('deactivate')"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200">
                            Deactivate
                        </button>

                        <button type="button"
                                onclick="bulkAction('delete')"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="form-checkbox h-4 w-4 text-green-600 rounded">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type/Category
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pricing
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox"
                                           name="products[]"
                                           value="{{ $product->id }}"
                                           @change="$event.target.checked ? selectedItems.push('{{ $product->id }}') : selectedItems = selectedItems.filter(id => id !== '{{ $product->id }}')"
                                           class="form-checkbox h-4 w-4 text-green-600 rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($product->image_path)
                                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-green-600">
                                                        {{ substr($product->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id]) }}">
                                                    {{ $product->name }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                @if($product->sku)
                                                    <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $product->sku }}</span>
                                                @endif
                                                @if($product->hsn_code)
                                                    HSN: {{ $product->hsn_code }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->type === 'item' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($product->type) }}
                                        </span>
                                    </div>
                                    @if($product->category)
                                        <div class="text-sm text-gray-500">{{ $product->category->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->type === 'item' && $product->maintain_stock)
                                        <div class="text-sm text-gray-900">
                                            {{ number_format($product->calculated_stock ?? $product->current_stock, 2) }}
                                            {{ $product->primaryUnit->symbol ?? $product->primaryUnit->name ?? '' }}
                                        </div>

                                        @if(isset($product->calculated_stock_value))
                                            <div class="text-xs text-gray-500">
                                                Value: ₦{{ number_format($product->calculated_stock_value, 2) }}
                                                @if(isset($product->calculated_average_rate))
                                                    <br>Avg Rate: ₦{{ number_format($product->calculated_average_rate, 2) }}
                                                @endif
                                            </div>
                                        @endif

                                        @php
                                            $stockStatus = $product->calculated_stock_status ?? $product->stock_status;
                                            $stockColor = match($stockStatus) {
                                                'out_of_stock' => 'red',
                                                'low_stock' => 'orange',
                                                'in_stock' => 'green',
                                                default => 'gray'
                                            };
                                        @endphp

                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $stockColor }}-100 text-{{ $stockColor }}-800">
                                            {{ ucwords(str_replace('_', ' ', $stockStatus)) }}
                                        </span>

                                        @if(($asOfDate ?? now()->toDateString()) !== now()->toDateString())
                                            <div class="text-xs text-blue-600 mt-1">
                                                As of {{ \Carbon\Carbon::parse($asOfDate ?? now())->format('M d, Y') }}
                                            </div>
                                        @endif

                                        @if(isset($product->calculated_stock))
                                            <div class="text-xs text-purple-600 mt-1">
                                                Method: {{ ucwords(str_replace('_', ' ', $valuationMethod ?? 'weighted_average')) }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-500 text-sm">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div>Purchase: ₦{{ number_format($product->purchase_rate, 2) }}</div>
                                        <div>Sales: ₦{{ number_format($product->sales_rate, 2) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
                                           class="text-green-600 hover:text-green-900">
                                            View
                                        </a>
                                        @if($product->type === 'item' && $product->maintain_stock)
                                            <a href="{{ route('tenant.inventory.products.stock-movements', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
                                               class="text-purple-600 hover:text-purple-900" title="Stock Movements">
                                                Movements
                                            </a>
                                        @endif
                                        <a href="{{ route('tenant.inventory.products.edit', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                        <button onclick="deleteProduct({{ $product->id }})"
                                                class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'type', 'category_id', 'status', 'stock_status']))
                        No products match your current filters.
                    @else
                        Get started by creating your first product or service.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'type', 'category_id', 'status', 'stock_status']))
                        <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700">
                            Clear Filters
                        </a>
                    @else
                        <a href="{{ route('tenant.inventory.products.create', ['tenant' => $tenant->slug]) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Your First Product
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Delete Product</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete this product? This action cannot be undone.
                </p>
            </div>
            <div class="flex items-center justify-center space-x-4 mt-4">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-lg shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let productToDelete = null;
    const deleteModal = document.getElementById('deleteModal');
    const cancelDelete = document.getElementById('cancelDelete');
    const confirmDelete = document.getElementById('confirmDelete');

    // Handle select all checkbox
    const selectAllCheckbox = document.querySelector('thead input[type="checkbox"]');
    const itemCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });
    }

    // Update select all checkbox state when individual checkboxes change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('tbody input[type="checkbox"]:checked').length;
            const totalCount = itemCheckboxes.length;

            if (selectAllCheckbox) {
                selectAllCheckbox.checked = checkedCount === totalCount;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
            }
        });
    });

    // Delete product function
    window.deleteProduct = function(productId) {
        productToDelete = productId;
        deleteModal.classList.remove('hidden');
    };

    // Cancel delete
    cancelDelete.addEventListener('click', function() {
        deleteModal.classList.add('hidden');
        productToDelete = null;
    });

    // Confirm delete
    confirmDelete.addEventListener('click', function() {
        if (productToDelete) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug]) }}/${productToDelete}`;

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            const tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_token';
            tokenField.value = '{{ csrf_token() }}';

            form.appendChild(methodField);
            form.appendChild(tokenField);
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Close modal when clicking outside
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
            productToDelete = null;
        }
    });

    // Bulk actions
    window.bulkAction = function(action) {
        const selectedProducts = Array.from(document.querySelectorAll('input[name="products[]"]:checked')).map(cb => cb.value);

        if (selectedProducts.length === 0) {
            alert('Please select at least one product.');
            return;
        }

        let confirmMessage = '';
        switch(action) {
            case 'activate':
                confirmMessage = `Are you sure you want to activate ${selectedProducts.length} product(s)?`;
                break;
            case 'deactivate':
                confirmMessage = `Are you sure you want to deactivate ${selectedProducts.length} product(s)?`;
                break;
            case 'delete':
                confirmMessage = `Are you sure you want to delete ${selectedProducts.length} product(s)? This action cannot be undone.`;
                break;
        }

        if (confirm(confirmMessage)) {
            // Create form for bulk action
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("tenant.inventory.products.bulk-action", ["tenant" => $tenant->slug]) }}';

            const tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_token';
            tokenField.value = '{{ csrf_token() }}';
            form.appendChild(tokenField);

            const actionField = document.createElement('input');
            actionField.type = 'hidden';
            actionField.name = 'action';
            actionField.value = action;
            form.appendChild(actionField);

            selectedProducts.forEach(productId => {
                const productField = document.createElement('input');
                productField.type = 'hidden';
                productField.name = 'products[]';
                productField.value = productId;
                form.appendChild(productField);
            });

            document.body.appendChild(form);
            form.submit();
        }
    };
});
</script>
@endsection
