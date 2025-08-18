@extends('layouts.tenant')

@section('title', 'Edit ' . $voucherType->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Voucher Type</h1>
            <p class="mt-2 text-gray-600">Update the settings for {{ $voucherType->name }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.accounting.voucher-types.show',['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View
            </a>
            <a href="{{ route('tenant.accounting.voucher-types.index', ['tenant' => $tenant->slug]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    @if($voucherType->is_system_defined)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">System Defined Voucher Type</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <p>This is a system-defined voucher type. Some fields may be restricted from editing to maintain system integrity.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('tenant.accounting.voucher-types.update', ['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
          method="POST"
          x-data="voucherTypeEditForm()"
          class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Voucher Type Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               x-model="form.name"
                               value="{{ old('name', $voucherType->name) }}"
                               {{ $voucherType->is_system_defined ? 'readonly' : '' }}
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-300 @enderror {{ $voucherType->is_system_defined ? 'bg-gray-50' : '' }}"
                               placeholder="e.g., Sales Invoice">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($voucherType->is_system_defined)
                            <p class="mt-1 text-xs text-gray-500">System-defined voucher type names cannot be changed</p>
                        @endif
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Voucher Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="code"
                               id="code"
                               x-model="form.code"
                               value="{{ old('code', $voucherType->code) }}"
                               {{ $voucherType->is_system_defined ? 'readonly' : '' }}
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('code') border-red-300 @enderror {{ $voucherType->is_system_defined ? 'bg-gray-50' : '' }}"
                               placeholder="e.g., SI"
                               maxlength="30">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if($voucherType->is_system_defined)
                            <p class="mt-1 text-xs text-gray-500">System-defined voucher codes cannot be changed</p>
                        @else
                            <p class="mt-1 text-xs text-gray-500">Unique code for this voucher type (letters, numbers, hyphens, underscores only)</p>
                        @endif
                    </div>

                    <!-- Abbreviation -->
                    <div>
                        <label for="abbreviation" class="block text-sm font-medium text-gray-700 mb-2">
                            Abbreviation <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="abbreviation"
                               id="abbreviation"
                               x-model="form.abbreviation"
                               value="{{ old('abbreviation', $voucherType->abbreviation) }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('abbreviation') border-red-300 @enderror"
                               placeholder="e.g., SI"
                               maxlength="5">
                        @error('abbreviation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Short abbreviation (letters only, max 5 characters)</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  x-model="form.description"
                                  rows="3"
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('description') border-red-300 @enderror"
                                  placeholder="Brief description of this voucher type">{{ old('description', $voucherType->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Numbering Configuration -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Numbering Configuration</h3>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Numbering Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Numbering Method <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio"
                                       name="numbering_method"
                                       value="auto"
                                       x-model="form.numbering_method"
                                       {{ old('numbering_method', $voucherType->numbering_method) === 'auto' ? 'checked' : '' }}
                                       class="form-radio text-primary-600">
                                <span class="ml-2 text-sm text-gray-700">Automatic - System generates numbers</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio"
                                       name="numbering_method"
                                       value="manual"
                                       x-model="form.numbering_method"
                                       {{ old('numbering_method', $voucherType->numbering_method) === 'manual' ? 'checked' : '' }}
                                       class="form-radio text-primary-600">
                                <span class="ml-2 text-sm text-gray-700">Manual - User enters numbers</span>
                            </label>
                        </div>
                        @error('numbering_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Starting Number -->
                    <div>
                        <label for="starting_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Starting Number <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="starting_number"
                               id="starting_number"
                               x-model="form.starting_number"
                               value="{{ old('starting_number', $voucherType->starting_number) }}"
                               min="1"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('starting_number') border-red-300 @enderror">
                        @error('starting_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">The first number to use for this voucher type</p>
                    </div>

                    <!-- Current Number (Read-only) -->
                    <div>
                        <label for="current_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Current Number
                        </label>
                        <input type="number"
                               id="current_number"
                               value="{{ $voucherType->current_number }}"
                               readonly
                               class="block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm">
                        <p class="mt-1 text-xs text-gray-500">Current sequence number (use reset function to change)</p>
                    </div>

                    <!-- Prefix -->
                    <div>
                        <label for="prefix" class="block text-sm font-medium text-gray-700 mb-2">
                            Number Prefix
                        </label>
                        <input type="text"
                               name="prefix"
                               id="prefix"
                               x-model="form.prefix"
                               value="{{ old('prefix', $voucherType->prefix) }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('prefix') border-red-300 @enderror"
                               placeholder="e.g., SI-"
                               maxlength="10">
                        @error('prefix')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Optional prefix for voucher numbers. Example: "SI-" will generate SI-0001, SI-0002, etc.
                        </p>

                        <!-- Preview -->
                        <div class="mt-2 p-3 bg-gray-50 rounded-lg" x-show="form.prefix || form.starting_number">
                            <p class="text-sm text-gray-600">
                                <strong>Next Number Preview:</strong>
                                <span x-text="(form.prefix || '') + String({{ $voucherType->current_number + 1 }}).padStart(4, '0')"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Voucher Features -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Voucher Features</h3>
                <p class="mt-1 text-sm text-gray-500">Configure what this voucher type affects</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Has Reference -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="has_reference"
                                   id="has_reference"
                                   x-model="form.has_reference"
                                   value="1"
                                   {{ old('has_reference', $voucherType->has_reference) ? 'checked' : '' }}
                                   class="form-checkbox text-primary-600 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="has_reference" class="text-sm font-medium text-gray-700">
                                Requires Reference Number
                            </label>
                            <p class="text-xs text-gray-500">Check if this voucher type requires a reference number</p>
                        </div>
                    </div>

                    <!-- Affects Inventory -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="affects_inventory"
                                   id="affects_inventory"
                                   x-model="form.affects_inventory"
                                   value="1"
                                   {{ old('affects_inventory', $voucherType->affects_inventory) ? 'checked' : '' }}
                                   {{ $voucherType->is_system_defined ? 'disabled' : '' }}
                                   class="form-checkbox text-primary-600 rounded {{ $voucherType->is_system_defined ? 'opacity-50' : '' }}">
                        </div>
                        <div class="ml-3">
                            <label for="affects_inventory" class="text-sm font-medium text-gray-700">
                                Affects Inventory
                            </label>
                            <p class="text-xs text-gray-500">Check if this voucher type affects stock levels</p>
                            @if($voucherType->is_system_defined)
                                <p class="text-xs text-yellow-600">System-defined setting cannot be changed</p>
                            @endif
                        </div>
                    </div>

                    <!-- Affects Cash/Bank -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox"
                                   name="affects_cashbank"
                                   id="affects_cashbank"
                                   x-model="form.affects_cashbank"
                                   value="1"
                                   {{ old('affects_cashbank', $voucherType->affects_cashbank) ? 'checked' : '' }}
                                   {{ $voucherType->is_system_defined ? 'disabled' : '' }}
                                   class="form-checkbox text-primary-600 rounded {{ $voucherType->is_system_defined ? 'opacity-50' : '' }}">
                        </div>
                        <div class="ml-3">
                            <label for="affects_cashbank" class="text-sm font-medium text-gray-700">
                                Affects Cash/Bank
                            </label>
                            <p class="text-xs text-gray-500">Check if this voucher type affects cash or bank accounts</p>
                            @if($voucherType->is_system_defined)
                                <p class="text-xs text-yellow-600">System-defined setting cannot be changed</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Status</h3>
            </div>
            <div class="p-6">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox"
                               name="is_active"
                               id="is_active"
                               x-model="form.is_active"
                               value="1"
                               {{ old('is_active', $voucherType->is_active) ? 'checked' : '' }}
                               class="form-checkbox text-primary-600 rounded">
                    </div>
                            <div class="ml-3">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active
                        </label>
                        <p class="text-xs text-gray-500">Uncheck to deactivate this voucher type</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6">
            <a href="{{ route('tenant.accounting.voucher-types.show', ['tenant' => $tenant->slug, 'voucherType' => $voucherType->id]) }}"
               class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Voucher Type
            </button>
        </div>
    </form>
</div>

<script>
function voucherTypeEditForm() {
    return {
        form: {
            name: '{{ old('name', $voucherType->name) }}',
            code: '{{ old('code', $voucherType->code) }}',
            abbreviation: '{{ old('abbreviation', $voucherType->abbreviation) }}',
            description: '{{ old('description', $voucherType->description) }}',
            numbering_method: '{{ old('numbering_method', $voucherType->numbering_method) }}',
            prefix: '{{ old('prefix', $voucherType->prefix) }}',
            starting_number: {{ old('starting_number', $voucherType->starting_number) }},
            has_reference: {{ old('has_reference', $voucherType->has_reference) ? 'true' : 'false' }},
            affects_inventory: {{ old('affects_inventory', $voucherType->affects_inventory) ? 'true' : 'false' }},
            affects_cashbank: {{ old('affects_cashbank', $voucherType->affects_cashbank) ? 'true' : 'false' }},
            is_active: {{ old('is_active', $voucherType->is_active) ? 'true' : 'false' }}
        }
    }
}
</script>
@endsection