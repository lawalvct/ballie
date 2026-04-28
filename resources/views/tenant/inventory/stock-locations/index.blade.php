@extends('layouts.tenant')

@section('title', 'Stock Locations')
@section('page-title', 'Stock Locations')
@section('page-description', 'Manage warehouses, stores, departments and WIP locations.')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="text-sm text-gray-600">
            All stock movements are tied to a location. The <span class="font-semibold text-gray-900">main</span> location is the
            default for sales and purchase invoices.
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('tenant.inventory.stock-locations.create', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <i class="fas fa-plus mr-2"></i> New Location
            </a>
            <a href="{{ route('tenant.inventory.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Flags</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($locations as $location)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <div class="font-semibold text-gray-900">{{ $location->name }}</div>
                            @if ($location->description)
                                <div class="text-xs text-gray-500">{{ $location->description }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-700">{{ $location->code }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $location->type_label }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex flex-wrap gap-1">
                                @if ($location->is_main)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800">Main</span>
                                @endif
                                @if ($location->is_wip)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">WIP</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if ($location->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right space-x-2">
                            @unless ($location->is_main)
                                <form action="{{ route('tenant.inventory.stock-locations.set-main', ['tenant' => $tenant->slug, 'stockLocation' => $location->id]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs text-blue-600 hover:underline">Set as Main</button>
                                </form>
                            @endunless
                            <a href="{{ route('tenant.inventory.stock-locations.edit', ['tenant' => $tenant->slug, 'stockLocation' => $location->id]) }}" class="text-xs text-primary-600 hover:underline">Edit</a>
                            @unless ($location->is_main)
                                <form action="{{ route('tenant.inventory.stock-locations.destroy', ['tenant' => $tenant->slug, 'stockLocation' => $location->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this location?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No locations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
