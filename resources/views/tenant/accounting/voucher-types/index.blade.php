@extends('layouts.tenant')

@section('title', 'Voucher Types')
@section('page-title', 'Voucher Types')
@section('page-description', 'Manage your accounting voucher types and numbering sequences')
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div>
            <a href="{{ route('tenant.accounting.voucher-types.create', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Voucher Type
            </a>
        </div>
        <div class="mt-4 lg:mt-0">
            <a href="{{ route('tenant.accounting.voucher-types.create', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Voucher Type
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Types</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $totalVoucherTypes }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Active Types</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $activeVoucherTypes }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">System Types</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $systemVoucherTypes }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Custom Types</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $customVoucherTypes }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200" x-data="{ filtersExpanded: false }">
        <div class="p-6">
            <!-- Header with Toggle Button -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Search & Filters</h3>
                <button @click="filtersExpanded = !filtersExpanded"
                        type="button"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
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
                    <label for="search" class="sr-only">Search voucher types</label>
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
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                               placeholder="Search by name, code, or abbreviation...">
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

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                                id="status"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type"
                                id="type"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="">All Types</option>
                            <option value="system" {{ request('type') === 'system' ? 'selected' : '' }}>System Defined</option>
                            <option value="custom" {{ request('type') === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort"
                                id="sort"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                            <option value="code" {{ request('sort') === 'code' ? 'selected' : '' }}>Code</option>
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                            <option value="is_active" {{ request('sort') === 'is_active' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>

                    <!-- Sort Direction -->
                    <div>
                        <label for="direction" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                        <select name="direction"
                                id="direction"
                                class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-lg">
                            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-200">
                    <div class="flex items-center space-x-2">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                            </svg>
                            Apply Filters
                        </button>

                        @if(request()->hasAny(['search', 'status', 'type', 'sort', 'direction']))
                            <a href="{{ route('tenant.accounting.voucher-types.index', ['tenant' => $tenant->slug]) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear All
                            </a>
                        @endif
                    </div>

                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['search', 'status', 'type', 'sort', 'direction']))
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm text-gray-500">Active filters:</span>

                            @if(request('search'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    Search: "{{ request('search') }}"
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-primary-600 hover:text-primary-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            @if(request('status'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Status: {{ ucfirst(request('status')) }}
                                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            @if(request('type'))
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Type: {{ request('type') === 'system' ? 'System Defined' : 'Custom' }}
                                    <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">
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

    <!-- Voucher Types Table -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        @if($voucherTypes->count() > 0)
            <!-- Bulk Actions -->
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50" x-data="{ selectedItems: [], showBulkActions: false }" x-init="$watch('selectedItems', value => showBulkActions = value.length > 0)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox"
                               @change="selectedItems = $event.target.checked ? Array.from(document.querySelectorAll('input[name=\'voucher_types[]\']')).map(cb => cb.value) : []"
                               class="form-checkbox h-4 w-4 text-primary-600 rounded">
                        <span class="text-sm text-gray-700">Select All</span>
                    </div>

                    <div x-show="showBulkActions" x-transition class="flex items-center space-x-2">
                        <span class="text-sm text-gray-700" x-text="`${selectedItems.length} selected`"></span>
                        <form method="POST" action="{{ route('tenant.accounting.voucher-types.bulk-action', ['tenant' => $tenant->slug]) }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="activate">
                            <template x-for="id in selectedItems" :key="id">
                                <input type="hidden" name="voucher_types[]" :value="id">
                            </template>
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200">
                                Activate
                            </button>
                        </form>

                        <form method="POST" action="{{ route('tenant.accounting.voucher-types.bulk-action', ['tenant' => $tenant->slug]) }}" class="inline">
                            @csrf
                            <input type="hidden" name="action" value="deactivate">
                            <template x-for="id in selectedItems" :key="id">
                                <input type="hidden" name="voucher_types[]" :value="id">
                            </template>
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200">
                                Deactivate
                            </button>
                        </form>

                        <form method="POST" action="{{ route('tenant.accounting.voucher-types.bulk-action', ['tenant' => $tenant->slug]) }}" class="inline"
                              onsubmit="return confirm('Are you sure you want to delete the selected voucher types?')">
                            @csrf
                            <input type="hidden" name="action" value="delete">
                            <template x-for="id in selectedItems" :key="id">
                                <input type="hidden" name="voucher_types[]" :value="id">
                            </template>
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" class="form-checkbox h-4 w-4 text-primary-600 rounded">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Voucher Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Code/Abbreviation
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Numbering
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Features
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vouchers
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
                        @foreach($voucherTypes as $voucherType)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox"
                                           name="voucher_types[]"
                                           value="{{ $voucherType->id }}"
                                           @change="$event.target.checked ? selectedItems.push('{{ $voucherType->id }}') : selectedItems = selectedItems.filter(id => id !== '{{ $voucherType->id }}')"
                                           class="form-checkbox h-4 w-4 text-primary-600 rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full {{ $voucherType->is_system_defined ? 'bg-purple-100' : 'bg-blue-100' }} flex items-center justify-center">
                                                <span class="text-sm font-medium {{ $voucherType->is_system_defined ? 'text-purple-600' : 'text-blue-600' }}">
                                                    {{ $voucherType->abbreviation }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                       <a href="{{ route('tenant.accounting.voucher-types.show', ['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}">
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($voucherType->description, 50) }}
                                            </div>
                                            @if($voucherType->is_system_defined)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    System Defined
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $voucherType->code }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $voucherType->abbreviation }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="capitalize">{{ $voucherType->numbering_method }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @if($voucherType->prefix)
                                            Next: {{ $voucherType->prefix }}{{ str_pad($voucherType->current_number + 1, 4, '0', STR_PAD_LEFT) }}
                                        @else
                                            Next: {{ $voucherType->current_number + 1 }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @if($voucherType->has_reference)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                Reference
                                            </span>
                                        @endif
                                        @if($voucherType->affects_inventory)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Inventory
                                            </span>
                                        @endif
                                        @if($voucherType->affects_cashbank)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Cash/Bank
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $voucherCounts[$voucherType->id] ?? 0 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($voucherType->is_active)
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
                                        <a href="{{ route('tenant.accounting.voucher-types.show', ['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
                                           class="text-primary-600 hover:text-primary-900">
                                            View
                                        </a>
                                        <a href="{{ route('tenant.accounting.voucher-types.edit', ['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                        @if(!$voucherType->is_system_defined && ($voucherCounts[$voucherType->id] ?? 0) === 0)
                                            <form method="POST"
                                                  action="{{ route('tenant.accounting.voucher-types.destroy', ['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this voucher type?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($voucherTypes->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $voucherTypes->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No voucher types found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'status', 'type']))
                        No voucher types match your current filters.
                    @else
                        Get started by creating your first voucher type.
                    @endif
                </p>
                <div class="mt-6">
                    @if(request()->hasAny(['search', 'status', 'type']))
                        <a href="{{ route('tenant.accounting.voucher-types.index', ['tenant' => $tenant->slug]) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                            Clear Filters
                        </a>
                    @else
                        <a href="{{ route('tenant.accounting.voucher-types.create', ['tenant' => $tenant->slug]) }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Voucher Type
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection
