@extends('layouts.super-admin')

@section('title', 'Support Categories')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Support Categories</h1>
            <p class="mt-1 text-sm text-gray-600">Organize support tickets and knowledge base articles</p>
        </div>
        <a href="{{ route('super-admin.support.categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Category
        </a>
    </div>

    <!-- Categories List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($categories->count() > 0)
            <div class="px-6 py-4 border-b border-gray-200">
                <p class="text-sm text-gray-600">Drag to reorder categories</p>
            </div>

            <ul id="categories-list" class="divide-y divide-gray-200">
                @foreach($categories as $category)
                    <li data-id="{{ $category->id }}" class="hover:bg-gray-50 cursor-move">
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 flex-1">
                                    <div class="text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                        </svg>
                                    </div>

                                    @if($category->icon)
                                        <div class="text-2xl">{{ $category->icon }}</div>
                                    @endif

                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-medium text-gray-900">{{ $category->name }}</h3>
                                            @if($category->is_active)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                            @endif
                                            <span class="text-sm text-gray-500">{{ $category->tickets_count }} tickets</span>
                                        </div>
                                        @if($category->description)
                                            <p class="mt-1 text-sm text-gray-500">{{ $category->description }}</p>
                                        @endif
                                        <p class="mt-1 text-xs text-gray-400">Slug: {{ $category->slug }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 ml-4">
                                    <a href="{{ route('super-admin.support.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('super-admin.support.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium" {{ $category->tickets_count > 0 ? 'disabled' : '' }}>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first support category.</p>
                <div class="mt-6">
                    <a href="{{ route('super-admin.support.categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Category
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const list = document.getElementById('categories-list');
    if (list) {
        new Sortable(list, {
            animation: 150,
            handle: 'li',
            onEnd: function(evt) {
                const order = Array.from(list.children).map(li => li.dataset.id);

                fetch('{{ route("super-admin.support.categories.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Categories reordered successfully');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }
});
</script>
@endsection
