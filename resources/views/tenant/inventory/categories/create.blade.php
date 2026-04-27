@extends('layouts.tenant')

@section('title', 'Create Category')
@section('page-title', 'Create Category')
@section('page-description', 'Add a new product category to organize your inventory')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Actions -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('tenant.inventory.categories.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Categories
        </a>
        <button type="button" id="openBulkCreateModalBtn"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Bulk Create Categories
        </button>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Category Information</h3>
            <p class="text-sm text-gray-600 mt-1">Fill in the details for your new product category</p>
        </div>

        <form method="POST" action="{{ route('tenant.inventory.categories.store', ['tenant' => $tenant->slug]) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Enter category name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                            Slug
                        </label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('slug') border-red-500 @enderror"
                               placeholder="category-slug (auto-generated if empty)">
                        <p class="mt-1 text-xs text-gray-500">URL-friendly version of the name. Leave empty to auto-generate.</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Parent Category
                        </label>
                        <select name="parent_id" id="parent_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('parent_id') border-red-500 @enderror">
                            <option value="">Root Category</option>
                            @foreach($hierarchicalCategories as $category)
                                <option value="{{ $category->id }}"
                                        {{ (old('parent_id', $selectedParentId) == $category->id) ? 'selected' : '' }}>
                                    {{ str_repeat('— ', $category->level) }}{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Enter category description">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="space-y-6">
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                            Category Image
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload image</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                        <div id="image-preview" class="mt-3"></div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                            Sort Order
                        </label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order') }}" min="0" max="999999"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('sort_order') border-red-500 @enderror"
                               placeholder="0">
                        <p class="mt-1 text-xs text-gray-500">Lower numbers appear first. Leave empty to auto-assign.</p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Inactive categories won't be available for product assignment.</p>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            {{-- <div class="border-t border-gray-200 pt-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h4>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                            Meta Title
                        </label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="255"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('meta_title') border-red-500 @enderror"
                               placeholder="SEO title for this category">
                        @error('meta_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                            Meta Description
                        </label>
                        <textarea name="meta_description" id="meta_description" rows="3" maxlength="500"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('meta_description') border-red-500 @enderror"
                                  placeholder="SEO description for this category">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div> --}}

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('tenant.inventory.categories.index', ['tenant' => $tenant->slug]) }}"
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" name="submit_action" value="create_and_add_new"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create &amp; Add New
                </button>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Create Modal -->
<div id="bulkCreateModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" aria-hidden="true">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
        <form id="bulkCreateForm"
              action="{{ route('tenant.inventory.categories.bulk-create', ['tenant' => $tenant->slug]) }}"
              method="POST">
            @csrf
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Create Multiple Categories</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Add several categories at once — you'll stay on this page after creation.</p>
                </div>
                <button type="button" id="bulkCreateCloseBtn"
                        class="text-gray-400 hover:text-gray-600" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4 space-y-3">
                <label for="bulkCreateNames" class="block text-sm font-medium text-gray-700">
                    Category Names <span class="text-red-500">*</span>
                </label>
                <textarea id="bulkCreateNames" name="names" rows="8" required
                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                          placeholder="Bread Nylon, Shopping bags, Industrial bags&#10;&#10;— or one per line —&#10;&#10;Bread Nylon&#10;Shopping bags&#10;Industrial bags"></textarea>
                <p class="text-xs text-gray-500">
                    Separate names with commas <strong>or</strong> new lines. Duplicates and existing categories will be skipped automatically.
                </p>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-xl flex items-center justify-end space-x-2">
                <button type="button" id="bulkCreateCancelBtn"
                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="bulkCreateSubmitBtn"
                        class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span data-default-label>Create Categories</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- AJAX flash toast -->
<div id="bulkCreateToast"
     class="fixed top-5 right-5 z-50 hidden max-w-sm rounded-lg shadow-lg px-4 py-3 text-sm font-medium"
     role="status" aria-live="polite"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    // Auto-generate slug from name
    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.userModified) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugInput.value = slug;
        }
    });

    // Mark slug as user-modified if manually changed
    slugInput.addEventListener('input', function() {
        slugInput.dataset.userModified = 'true';
    });

    // Image preview
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="relative inline-block">
                        <img src="${e.target.result}" alt="Preview" class="h-24 w-24 object-cover rounded-lg border-2 border-gray-300">
                        <button type="button" onclick="removeImagePreview()"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });
});

function removeImagePreview() {
    document.getElementById('image').value = '';
    document.getElementById('image-preview').innerHTML = '';
}

// Bulk Create modal logic
(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const bulkModal = document.getElementById('bulkCreateModal');
    const bulkOpenBtn = document.getElementById('openBulkCreateModalBtn');
    const bulkCloseBtn = document.getElementById('bulkCreateCloseBtn');
    const bulkCancelBtn = document.getElementById('bulkCreateCancelBtn');
    const bulkForm = document.getElementById('bulkCreateForm');
    const bulkSubmitBtn = document.getElementById('bulkCreateSubmitBtn');
    const bulkTextarea = document.getElementById('bulkCreateNames');
    const toast = document.getElementById('bulkCreateToast');
    let toastTimer;

    function showToast(message, type) {
        if (!toast) return;
        type = type || 'success';
        toast.textContent = message;
        toast.className = 'fixed top-5 right-5 z-50 max-w-sm rounded-lg shadow-lg px-4 py-3 text-sm font-medium ' +
            (type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.add('hidden'), 4500);
    }

    function openBulkModal() {
        if (!bulkModal) return;
        bulkModal.classList.remove('hidden');
        bulkModal.classList.add('flex');
        bulkModal.setAttribute('aria-hidden', 'false');
        setTimeout(() => bulkTextarea?.focus(), 50);
    }
    function closeBulkModal() {
        if (!bulkModal) return;
        bulkModal.classList.add('hidden');
        bulkModal.classList.remove('flex');
        bulkModal.setAttribute('aria-hidden', 'true');
    }

    bulkOpenBtn?.addEventListener('click', openBulkModal);
    bulkCloseBtn?.addEventListener('click', closeBulkModal);
    bulkCancelBtn?.addEventListener('click', closeBulkModal);
    bulkModal?.addEventListener('click', function (e) {
        if (e.target === bulkModal) closeBulkModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && bulkModal && !bulkModal.classList.contains('hidden')) {
            closeBulkModal();
        }
    });

    bulkForm?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const value = (bulkTextarea?.value || '').trim();
        if (!value) {
            showToast('Please enter at least one category name.', 'error');
            return;
        }
        const labelEl = bulkSubmitBtn?.querySelector('[data-default-label]');
        const originalLabel = labelEl?.textContent;
        if (bulkSubmitBtn) bulkSubmitBtn.disabled = true;
        if (labelEl) labelEl.textContent = 'Creating...';

        try {
            const res = await fetch(bulkForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(bulkForm),
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data.success) {
                showToast(data.message || 'Categories created successfully.', 'success');
                if (bulkTextarea) bulkTextarea.value = '';
                closeBulkModal();
            } else {
                showToast(data.message || 'Failed to create categories.', 'error');
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
        } finally {
            if (bulkSubmitBtn) bulkSubmitBtn.disabled = false;
            if (labelEl && originalLabel) labelEl.textContent = originalLabel;
        }
    });
})();
</script>
@endpush
@endsection
