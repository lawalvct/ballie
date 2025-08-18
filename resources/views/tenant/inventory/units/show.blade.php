@extends('layouts.tenant')

@section('title', $unit->name . ' - Unit Details')
@section('page-title', 'Unit Details')
@section('page-description', 'View detailed information about ' . $unit->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-{{ $unit->type_color }}-100 flex items-center justify-center">
                <span class="text-{{ $unit->type_color }}-600 font-bold text-lg">{{ strtoupper(substr($unit->symbol, 0, 2)) }}</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $unit->name }}</h1>
                <p class="text-gray-600">{{ $unit->symbol }} • {{ $unit->type }}</p>
            </div>
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $unit->status_color }}-100 text-{{ $unit->status_color }}-800">
                    <span class="w-1.5 h-1.5 mr-1.5 bg-{{ $unit->status_color }}-400 rounded-full"></span>
                    {{ $unit->status }}
                </span>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.inventory.units.edit', ['tenant' => $tenant->slug, 'unit' => $unit->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Unit
            </a>
            <a href="{{ route('tenant.inventory.units.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Units
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Unit Name</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $unit->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Symbol</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $unit->symbol }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Type</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-{{ $unit->type_color }}-100 text-{{ $unit->type_color }}-800">
                                {{ $unit->type }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-{{ $unit->status_color }}-100 text-{{ $unit->status_color }}-800">
                                <span class="w-1.5 h-1.5 mr-1.5 bg-{{ $unit->status_color }}-400 rounded-full"></span>
                                {{ $unit->status }}
                            </span>
                        </div>
                    </div>

                    @if($unit->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                            <p class="text-gray-900">{{ $unit->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Created</label>
                            <p class="text-gray-900">{{ $unit->created_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                            <p class="text-gray-900">{{ $unit->updated_at->format('M j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversion Information -->
            @if(!$unit->is_base_unit && $unit->baseUnit)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Conversion Information</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Base Unit</label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-semibold text-gray-900">{{ $unit->baseUnit->name }}</span>
                                    <span class="text-gray-500">({{ $unit->baseUnit->symbol }})</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Conversion Factor</label>
                                <p class="text-lg font-semibold text-gray-900 font-mono">{{ $unit->conversion_factor }}</p>
                            </div>
                        </div>

                        <!-- Conversion Examples -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Conversion Examples</h4>
                            <div class="text-sm text-blue-700 space-y-1">
                                <p><strong>1 {{ $unit->name }} ({{ $unit->symbol }}) = {{ $unit->conversion_factor }} {{ $unit->baseUnit->name }} ({{ $unit->baseUnit->symbol }})</strong></p>
                                <p><strong>1 {{ $unit->baseUnit->name }} ({{ $unit->baseUnit->symbol }}) = {{ number_format(1 / $unit->conversion_factor, 6) }} {{ $unit->name }} ({{ $unit->symbol }})</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Derived Units -->
            @if($unit->is_base_unit && $unit->derivedUnits->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Derived Units</h3>
                        <p class="text-sm text-gray-600 mt-1">Units that are based on this unit</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($unit->derivedUnits as $derivedUnit)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-8 w-8 rounded-lg bg-{{ $derivedUnit->type_color }}-100 flex items-center justify-center">
                                            <span class="text-{{ $derivedUnit->type_color }}-600 font-semibold text-xs">{{ strtoupper(substr($derivedUnit->symbol, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $derivedUnit->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $derivedUnit->symbol }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">1:{{ $derivedUnit->conversion_factor }}</p>
                                        <p class="text-xs text-gray-500">conversion ratio</p>
                                    </div>
                                    <a href="{{ route('tenant.inventory.units.show', ['tenant' => $tenant->slug, 'unit' => $derivedUnit->id]) }}"
                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Products Using This Unit -->
            @if($unit->products && $unit->products->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Products Using This Unit</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $unit->products->count() }} products use this unit</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($unit->products->take(10) as $product)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        @if($product->image)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-8 w-8 rounded-lg object-cover">
                                        @else
                                            <div class="h-8 w-8 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $product->sku }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</p>
                                        <p class="text-xs text-gray-500">per {{ $unit->symbol }}</p>
                                    </div>
                                    <a href="{{ route('tenant.inventory.products.show', ['tenant' => $tenant->slug, 'product' => $product->id]) }}"
                                       class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach

                            @if($unit->products->count() > 10)
                                <div class="text-center pt-3">
                                    <p class="text-sm text-gray-500">And {{ $unit->products->count() - 10 }} more products...</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Products Using</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $unit->products_count ?? 0 }}</span>
                    </div>
                    @if($unit->is_base_unit)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Derived Units</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $unit->derivedUnits->count() }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Status</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $unit->status_color }}-100 text-{{ $unit->status_color }}-800">
                            {{ $unit->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('tenant.inventory.units.edit', ['tenant' => $tenant->slug, 'unit' => $unit->id]) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Unit
                    </a>

                    <form method="POST" action="{{ route('tenant.inventory.units.toggle-status', ['tenant' => $tenant->slug, 'unit' => $unit->id]) }}" class="w-full">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-{{ $unit->is_active ? 'orange' : 'green' }}-600 hover:bg-{{ $unit->is_active ? 'orange' : 'green' }}-700 text-white font-medium rounded-lg transition-colors duration-200"
                                onclick="return confirm('Are you sure you want to {{ $unit->is_active ? 'deactivate' : 'activate' }} this unit?')">
                            @if($unit->is_active)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                Deactivate Unit
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Activate Unit
                            @endif
                        </button>
                    </form>

                    @if($unit->products_count == 0 && $unit->derivedUnits->count() == 0)
                        <form method="POST" action="{{ route('tenant.inventory.units.destroy', ['tenant' => $tenant->slug, 'unit' => $unit->id]) }}" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200"
                                    onclick="return confirm('Are you sure you want to delete this unit? This action cannot be undone.')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Unit
                            </button>
                        </form>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex">
                                <svg class="w-5 h-5 text-yellow-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-yellow-800">Cannot Delete</p>
                                    <p class="text-xs text-yellow-700 mt-1">
                                        This unit cannot be deleted because it's being used by
                                        @if($unit->products_count > 0)
                                            {{ $unit->products_count }} product(s)
                                        @endif
                                        @if($unit->products_count > 0 && $unit->derivedUnits->count() > 0)
                                            and
                                        @endif
                                        @if($unit->derivedUnits->count() > 0)
                                            {{ $unit->derivedUnits->count() }} derived unit(s)
                                        @endif
                                        .
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Unit Hierarchy -->
            @if(!$unit->is_base_unit || $unit->derivedUnits->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Unit Hierarchy</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if(!$unit->is_base_unit && $unit->baseUnit)
                                <!-- Parent Unit -->
                                <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-blue-900">Base Unit</p>
                                        <p class="text-xs text-blue-700">{{ $unit->baseUnit->name }} ({{ $unit->baseUnit->symbol }})</p>
                                    </div>
                                    <a href="{{ route('tenant.inventory.units.show', ['tenant' => $tenant->slug, 'unit' => $unit->baseUnit->id]) }}"
                                       class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif

                            <!-- Current Unit -->
                            <div class="flex items-center space-x-3 p-3 bg-gray-100 rounded-lg border-2 border-gray-300">
                                <div class="h-8 w-8 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-600 font-semibold text-xs">{{ strtoupper(substr($unit->symbol, 0, 2)) }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Current Unit</p>
                                    <p class="text-xs text-gray-600">{{ $unit->name }} ({{ $unit->symbol }})</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>

                            @if($unit->derivedUnits->count() > 0)
                                <!-- Derived Units -->
                                @foreach($unit->derivedUnits as $derivedUnit)
                                    <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                        <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-green-900">Derived Unit</p>
                                            <p class="text-xs text-green-700">{{ $derivedUnit->name }} ({{ $derivedUnit->symbol }})</p>
                                        </div>
                                        <a href="{{ route('tenant.inventory.units.show', ['tenant' => $tenant->slug, 'unit' => $derivedUnit->id]) }}"
                                           class="text-green-600 hover:text-green-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
