@extends('layouts.tenant')

@section('title', 'Create Account Group - ' . $tenant->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Account Group</h1>
            <p class="mt-1 text-sm text-gray-500">
                Add a new account group to organize your chart of accounts
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.account-groups.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Account Groups
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('tenant.accounting.account-groups.store', ['tenant' => $tenant->slug]) }}" class="space-y-6">
            @csrf

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
                               value="{{ old('name') }}"
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
                               value="{{ old('code') }}"
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
                                x-data="{ nature: '{{ old('nature') }}' }"
                                x-model="nature"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('nature') border-red-300 @enderror"
                                required>
                            <option value="">Select Nature</option>
                            @foreach($natures as $key => $label)
                                <option value="{{ $key }}" {{ old('nature') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('nature')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Parent Group -->
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Parent Group (Optional)
                        </label>
                        <select name="parent_id"
                                id="parent_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-primary-500 focus:border-primary-500 @error('parent_id') border-red-300 @enderror">
                            <option value="">No Parent (Top Level Group)</option>
                            @foreach($parentGroups as $parentGroup)
                                <option value="{{ $parentGroup->id }}"
                                        data-nature="{{ $parentGroup->nature }}"
                                        {{ old('parent_id') == $parentGroup->id ? 'selected' : '' }}>
                                    {{ $parentGroup->name }} ({{ $parentGroup->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Child groups must have the same nature as their parent</p>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <input type="checkbox"
                           name="is_active"
                           id="is_active"
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Active (Group can be used for creating ledger accounts)
                    </label>
                </div>

                <!-- Help Text -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Account Group Guidelines</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li><strong>Assets:</strong> Resources owned by the business (Cash, Inventory, Equipment, etc.)</li>
                                    <li><strong>Liabilities:</strong> Debts and obligations (Accounts Payable, Loans, etc.)</li>
                                    <li><strong>Equity:</strong> Owner's stake in the business (Capital, Retained Earnings, etc.)</li>
                                    <li><strong>Income:</strong> Revenue and earnings (Sales, Service Income, etc.)</li>
                                    <li><strong>Expenses:</strong> Costs and expenditures (Office Expenses, Travel, etc.)</li>
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
                    <a href="{{ route('tenant.accounting.account-groups.index', ['tenant' => $tenant->slug]) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Account Group
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate code from name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');

    nameInput.addEventListener('input', function() {
        if (!codeInput.value || codeInput.dataset.autoGenerated === 'true') {
            // Generate code from first letters of each word
            const code = this.value
                .split(' ')
                .map(word => word.charAt(0))
                .join('')
                .toUpperCase()
                .substring(0, 10);

            codeInput.value = code;
            codeInput.dataset.autoGenerated = 'true';
        }
    });

    // Reset auto-generation flag when user manually edits code
    codeInput.addEventListener('input', function() {
        this.dataset.autoGenerated = 'false';
    });

    // Filter parent groups based on selected nature
    const natureSelect = document.getElementById('nature');
    const parentSelect = document.getElementById('parent_id');

    natureSelect.addEventListener('change', function() {
        const selectedNature = this.value;
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
    });

    // Trigger filter on page load if nature is already selected
    if (natureSelect.value) {
        natureSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection