@extends('layouts.tenant')

@section('title', $category->name)
@section('page-title', $category->name)
@section('page-description', 'View category details and manage subcategories')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('tenant.inventory.categories.index', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Categories
                </a>
            </li>
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        @if($index < count($breadcrumbs) - 1)
                            <a href="{{ route('tenant.inventory.categories.show', ['tenant' => $tenant->slug, 'category' => $breadcrumb->id]) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                                {{ $breadcrumb->name }}
                            </a>
                        @else
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $breadcrumb->name }}</span>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Category Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-12 w-12 rounded-lg object-cover">
                        @else
                            <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $category->slug }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $category->is_active ? 'green' : 'red' }}-100 text-{{ $category->is_active ? 'green' : 'red' }}-800">
                        <span class="w-1.5 h-1.5 mr-1.5 bg-{{ $category->is_active ? 'green' : 'red' }}-400 rounded-full"></span>
                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="p-6">
                    @if($category->description)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Description</h4>
                            <p class="text-gray-700">{{ $category->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Products</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->products_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Subcategories</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->children_count }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sort Order</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->sort_order }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('M j, Y') }}</dd>
                        </div>
                    </div>

                    @if($category->meta_title || $category->meta_description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">SEO Information</h4>
                            <div class="space-y-3">
                                @if($category->meta_title)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Meta Title</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $category->meta_title }}</dd>
                                    </div>
                                @endif
                                @if($category->meta_description)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $category->meta_description }}</dd>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Subcategories -->
            @if($category->children->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Subcategories</h3>
                        <a href="{{ route('tenant.inventory.categories.create', ['tenant' => $tenant->slug, 'parent_id' => $category->id]) }}"
                           class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Subcategory
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($category->children as $child)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center space-x-3">
                                        @if($child->image)
                                            <img src="{{ $child->image_url }}" alt="{{ $child->name }}" class="h-10 w-10 rounded-lg object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $child->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $child->products_count }} products</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $child->is_active ? 'green' : 'red' }}-100 text-{{ $child->is_active ? 'green' : 'red' }}-800">
                                            {{ $child->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <a href="{{ route('tenant.inventory.categories.show', ['tenant' => $tenant->slug, 'category' => $child->id]) }}"
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Products -->
            @if($category->products->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Recent Products</h3>
                        <a href="{{ route('tenant.inventory.products.index', ['tenant' => $tenant->slug, 'category' => $category->id]) }}"
                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View All Products
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($category->products->take(5) as $product)
                                <div class="flex items-center space-x-4 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                    @if($product->image)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $product->sku }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</p>
                                        <p class="text-xs text-gray-500">Stock: {{ $product->stock_quantity }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('tenant.inventory.categories.edit', ['tenant' => $tenant->slug, 'category' => $category->id]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Category
                    </a>

                    <a href="{{ route('tenant.inventory.categories.create', ['tenant' => $tenant->slug, 'parent_id' => $category->id]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Subcategory
                    </a>

                    <form method="POST" action="{{ route('tenant.inventory.categories.toggle-status', ['tenant' => $tenant->slug, 'category' => $category->id]) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-{{ $category->is_active ? 'orange' : 'green' }}-600 hover:bg-{{ $category->is_active ? 'orange' : 'green' }}-700 text-white font-medium rounded-lg transition-colors duration-200"
                                onclick="return confirm('Are you sure you want to {{ $category->is_active ? 'deactivate' : 'activate' }} this category?')">
                            @if($category->is_active)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                Deactivate
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Activate
                            @endif
                        </button>
                    </form>

                    @if($category->products_count == 0 && $category->children_count == 0)
                        <form method="POST" action="{{ route('tenant.inventory.categories.destroy', ['tenant' => $tenant->slug, 'category' => $category->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200"
                                    onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Category
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Category Hierarchy -->
            @if($category->parent || $category->children->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Category Hierarchy</h3>
                    </div>
                    <div class="p-6">
                        @if($category->parent)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Parent Category</h4>
                                <a href="{{ route('tenant.inventory.categories.show', ['tenant' => $tenant->slug, 'category' => $category->parent->id]) }}"
                                   class="flex items-center space-x-2 text-blue-600 hover:text-blue-900">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                    </svg>
                                    <span>{{ $category->parent->name }}</span>
                                </a>
                            </div>
                        @endif

                        @if($category->children->count() > 0)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Subcategories</h4>
                                <div class="space-y-2">
                                    @foreach($category->children as $child)
                                        <a href="{{ route('tenant.inventory.categories.show', ['tenant' => $tenant->slug, 'category' => $child->id]) }}"
                                           class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                            <span class="text-sm text-gray-900">{{ $child->name }}</span>
                                            <span class="text-xs text-gray-500">{{ $child->products_count }} products</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total Products</span>
                            <span class="text-sm font-medium text-gray-900">{{ $category->products_count }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Direct Subcategories</span>
                            <span class="text-sm font-medium text-gray-900">{{ $category->children_count }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total Subcategories</span>
                            <span class="text-sm font-medium text-gray-900">{{ $category->descendants_count }}</span>
                        </div>
                        @if($category->products->count() > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Avg. Product Price</span>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($category->products->avg('price'), 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Total Stock Value</span>
                                <span class="text-sm font-medium text-gray-900">${{ number_format($category->products->sum(function($product) { return $product->price * $product->stock_quantity; }), 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection