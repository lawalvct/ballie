@extends('layouts.tenant')

@section('title', 'Edit Account Group - ' . $accountGroup->name . ' - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Account Group</h1>
            <p class="mt-1 text-sm text-gray-500">
                Update the details of <strong>{{ $accountGroup->name }}</strong>
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.account-groups.show', ['tenant' => $tenant->slug, 'account_group' => $accountGroup->id]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Details
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('tenant.accounting.account-groups.update', ['tenant' => $tenant->slug, 'account_group' => $accountGroup->id]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Account Group Information</h3>
            </div>

            <div class="px-6 pb-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Account Group Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Group Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $accountGroup->name) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-300 @enderror"
                               placeholder="e.g., Current Assets"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Account Group Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Account Group Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="code"
                               id="code"
                               value="{{ old('code', $accountGroup->code) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('code') border-red-300 @enderror"
                               placeholder="e.g., CA"
                               required>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Use uppercase letters, numbers, hyphens, or underscores only</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nature -->
                    <div>
                        <label for="nature" class="block text-sm font-medium text-gray-700 mb-2">
                            Nature <span class="text-red-500">*</span>
                        </label>
                        <select name="nature"
                                id="nature"
                                x-data="{ nature: '{{ old('nature', $accountGroup->nature) }}' }"
                                x-model="nature"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('nature') border-red-300 @enderror"
                                {{ $accountGroup->children()->count() > 0 ? 'disabled' : '' }}
                                required>
                            <option value="">Select Nature</option>
                            @foreach($natures as $key => $label)
                                <option value="{{ $key }}" {{ old('nature', $accountGroup->nature) === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('nature')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($accountGroup->children()->count() > 0)
                            <p class="mt-1 text-sm text-orange-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Cannot change nature - this group has child groups
                            </p>
                        @endif
                    </div>

                    <!-- Parent Group -->
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Parent Group (Optional)
                        </label>
                        <select name="parent_id"
                                id="parent_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('parent_id') border-red-300 @enderror"
                                {{ $accountGroup->children()->count() > 0 ? 'disabled' : '' }}>
                            <option value="">No Parent (Top Level Group)</option>
                            @foreach($parentGroups as $parentGroup)
                                <option value="{{ $parentGroup->id }}"
                                        data-nature="{{ $parentGroup->nature }}"
                                        {{ old('parent_id', $accountGroup->parent_id) == $parentGroup->id ? 'selected' : '' }}>
                                    {{ $parentGroup->name }} ({{ $parentGroup->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($accountGroup->children()->count() > 0)
                            <p class="mt-1 text-sm text-orange-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Cannot change parent - this group has child groups
                            </p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Child groups must have the same nature as their parent</p>
                        @endif
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <input type="checkbox"
                           name="is_active"
                           id="is_active"
                           value="1"
                           {{ old('is_active', $accountGroup->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active (Group can be used for creating ledger accounts)
                    </label>
                </div>

                <!-- System Defined Warning -->
                @if($accountGroup->is_system_defined)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">System-Defined Account Group</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>This is a system-defined account group. Some restrictions apply:</p>
                                    <ul class="list-disc ml-5 mt-1">
                                        <li>The nature cannot be changed</li>
                                        <li>The group cannot be deleted</li>
                                        <li>Some core properties are protected</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Validation Warnings -->
                @if($accountGroup->ledgerAccounts()->count() > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Account Group in Use</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>This account group has {{ $accountGroup->ledgerAccounts()->count() }} ledger account(s). Changes to the nature or parent may affect existing accounts.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Help Text -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-800">Editing Guidelines</h3>
                            <div class="mt-2 text-sm text-gray-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li><strong>Name & Code:</strong> Can be changed anytime, but ensure consistency</li>
                                    <li><strong>Nature:</strong> Cannot be changed if there are child groups</li>
                                    <li><strong>Parent:</strong> Cannot be changed if there are child groups</li>
                                    <li><strong>Status:</strong> Deactivating will prevent new ledger account creation</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <span class="text-red-500">*</span> Required fields
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('tenant.accounting.account-groups.show', ['tenant' => $tenant->slug, 'account_group' => $accountGroup->id]) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Account Group
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter parent groups based on selected nature
    const natureSelect = document.getElementById('nature');
    const parentSelect = document.getElementById('parent_id');

    function filterParentGroups() {
        const selectedNature = natureSelect.value;
        const options = parentSelect.querySelectorAll('option[data-nature]');

        options.forEach(option => {
            if (selectedNature && option.dataset.nature !== selectedNature) {
                option.style.display = 'none';
                option.disabled = true;
            } else {
                option.style.display = 'block';
                option.disabled = false;
            }
        });

        // Reset parent selection if current selection is not compatible
        const currentParent = parentSelect.querySelector('option:checked');
        if (currentParent && currentParent.dataset.nature &&
            currentParent.dataset.nature !== selectedNature) {
            parentSelect.value = '';
        }
    }

    // Only add event listener if nature can be changed
    if (!natureSelect.disabled) {
        natureSelect.addEventListener('change', filterParentGroups);

        // Trigger filter on page load if nature is already selected
        if (natureSelect.value) {
            filterParentGroups();
        }
    }

    // Form validation before submit
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const code = document.getElementById('code').value.trim();
        const nature = document.getElementById('nature').value;

        if (!name || !code || !nature) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }

        // Validate code format
        const codePattern = /^[A-Z0-9_-]+$/;
        if (!codePattern.test(code)) {
            e.preventDefault();
            alert('Account Group Code can only contain uppercase letters, numbers, hyphens, and underscores.');
            return false;
        }

        return true;
    });
});
</script>
@endsection