@php
    $loc = $location ?? null;
@endphp
<form action="{{ $action }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $loc?->name) }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Code <span class="text-red-500">*</span></label>
            <input type="text" name="code" value="{{ old('code', $loc?->code) }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm uppercase font-mono focus:border-primary-500 focus:ring-primary-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Type <span class="text-red-500">*</span></label>
            <select name="type" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('type', $loc?->type ?? 'store') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Sort Order</label>
            <input type="number" min="0" max="9999" name="sort_order" value="{{ old('sort_order', $loc?->sort_order ?? 0) }}" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="2" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('description', $loc?->description) }}</textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_main" value="0">
            <input type="checkbox" name="is_main" value="1" @checked(old('is_main', $loc?->is_main)) class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            Main location (default for sales/purchase)
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_wip" value="0">
            <input type="checkbox" name="is_wip" value="1" @checked(old('is_wip', $loc?->is_wip)) class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            Work in Progress (WIP)
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $loc?->is_active ?? true)) class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            Active
        </label>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('tenant.inventory.stock-locations.index', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Cancel</a>
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-primary-700">Save Location</button>
    </div>
</form>
