@extends('layouts.tenant')

@section('title', 'Create Voucher Type')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Voucher Type</h1>
            <p class="mt-2 text-gray-600">Set up a new voucher type for your accounting transactions</p>
        </div>
        <a href="{{ route('tenant.accounting.voucher-types.index', ['tenant' => tenant()->slug]) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to List
        </a>
    </div>

    <form action="{{ route('tenant.accounting.voucher-types.store', ['tenant' => tenant()->slug]) }}"
          method="POST"
          x-data="voucherTypeForm()"
          class="space-y-6">
        @csrf

        <!-- Primary Voucher Type Selection -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Primary Voucher Type</h3>
                <p class="mt-1 text-sm text-gray-500">Select a primary voucher type to pre-fill settings, or create a custom one</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($primaryVoucherTypes as $key => $primaryType)
                    <div class="relative">
                        <input type="radio"
                               name="primary_voucher_type"
                               value="{{ $key }}"
                               id="primary_{{ $key }}"
                               x-model="selectedPrimary"
                               @change="applyPrimaryType('{{ $key }}')"
                               class="sr-only">
                        <label for="primary_{{ $key }}"
                               class="block p-4 border-2 rounded-lg cursor-pointer transition-all duration-200"
                               :class="selectedPrimary === '{{ $key }}' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                            <div class="text-center">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center"
                                     :class="selectedPrimary === '{{ $key }}' ? 'bg-primary-100' : 'bg-gray-100'">
                                    <span class="text-sm font-bold"
                                          :class="selectedPrimary === '{{ $key }}' ? 'text-primary-700' : 'text-gray-600'">
                                      {{ $primaryType['abbreviation'] ?? strtoupper(substr($primaryType['name'], 0, 2)) }}
                                    </span>
                                </div>
                                <h4 class="font-medium text-gray-900 text-sm">{{ $primaryType['name'] }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $primaryType['description'] }}</p>
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <label class="flex items-center">
                        <input type="radio"
                               name="primary_voucher_type"
                               value=""
                               x-model="selectedPrimary"
                               @change="clearPrimaryType()"
                               class="form-radio text-primary-600">
                        <span class="ml-2 text-sm text-gray-700">Create custom voucher type</span>
                    </label>
                </div>
            </div>
        </div>

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
                               @input="generateCode()"
                               value="{{ old('name') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('name') border-red-300 @enderror"
                               placeholder="e.g., Sales Invoice">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                               value="{{ old('code') }}"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('code') border-red-300 @enderror"
                               placeholder="e.g., SI"
                               maxlength="30">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Unique code for this voucher type (letters, numbers, hyphens, underscores only)</p>
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
                               value="{{ old('abbreviation') }}"
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
                                  placeholder="Brief description of this voucher type">{{ old('description') }}</textarea>
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
                                       {{ old('numbering_method', 'auto') === 'auto' ? 'checked' : '' }}
                                       class="form-radio text-primary-600">
                                <span class="ml-2 text-sm text-gray-700">Automatic - System generates numbers</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio"
                                       name="numbering_method"
                                       value="manual"
                                       x-model="form.numbering_method"
                                       {{ old('numbering_method') === 'manual' ? 'checked' : '' }}
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
                               value="{{ old('starting_number', 1) }}"
                               min="1"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 @error('starting_number') border-red-300 @enderror">
                        @error('starting_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">The first number to use for this voucher type</p>
                    </div>

                    <!-- Prefix -->
                    <div class="md:col-span-2">
                        <label for="prefix" class="block text-sm font-medium text-gray-700 mb-2">
                            Number Prefix
                        </label>
                        <input type="text"
                               name="prefix"
                               id="prefix"
                               x-model="form.prefix"
                               value="{{ old('prefix') }}"
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
                                <strong>Preview:</strong>
                                <span x-text="(form.prefix || '') + String(form.starting_number || 1).padStart(4, '0')"></span>
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
                                   {{ old('has_reference') ? 'checked' : '' }}
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
                                   {{ old('affects_inventory') ? 'checked' : '' }}
                                   class="form-checkbox text-primary-600 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="affects_inventory" class="text-sm font-medium text-gray-700">
                                Affects Inventory
                            </label>
                            <p class="text-xs text-gray-500">Check if this voucher type affects stock levels</p>
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
                                   {{ old('affects_cashbank') ? 'checked' : '' }}
                                   class="form-checkbox text-primary-600 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="affects_cashbank" class="text-sm font-medium text-gray-700">
                                Affects Cash/Bank
                            </label>
                            <p class="text-xs text-gray-500">Check if this voucher type affects cash or bank accounts</p>
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
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="form-checkbox text-primary-600 rounded">
                    </div>
                    <div class="ml-3">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active
                        </label>
                        <p class="text-xs text-gray-500">Uncheck to create this voucher type as inactive</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-4 pt-6">
            <a href="{{ route('tenant.accounting.voucher-types.index', ['tenant' => tenant()->slug]) }}"
               class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Create Voucher Type
            </button>
        </div>
    </form>
</div>

<script>
function voucherTypeForm() {
    return {
        selectedPrimary: '',
        form: {
            name: '',
            code: '',
            abbreviation: '',
            description: '',
            numbering_method: 'auto',
            prefix: '',
            starting_number: 1,
            has_reference: false,
            affects_inventory: false,
            affects_cashbank: false,
            is_active: true
        },

        primaryTypes: @json($primaryVoucherTypes),

        applyPrimaryType(type) {
            if (this.primaryTypes[type]) {
                const primaryType = this.primaryTypes[type];
                this.form.name = primaryType.name;
                this.form.code = primaryType.code || this.generateCodeFromName(primaryType.name);
                this.form.abbreviation = primaryType.abbreviation || primaryType.code?.substring(0, 5) || '';
                this.form.description = primaryType.description;
                this.form.prefix = primaryType.prefix || '';
                this.form.has_reference = primaryType.has_reference;
                this.form.affects_inventory = primaryType.affects_inventory;
                this.form.affects_cashbank = primaryType.affects_cashbank;
            }
        },

        clearPrimaryType() {
            this.form = {
                name: '',
                code: '',
                abbreviation: '',
                description: '',
                numbering_method: 'auto',
                prefix: '',
                starting_number: 1,
                has_reference: false,
                affects_inventory: false,
                affects_cashbank: false,
                is_active: true
            };
        },

        generateCode() {
            if (!this.selectedPrimary && this.form.name) {
                this.form.code = this.generateCodeFromName(this.form.name);
                this.form.abbreviation = this.form.code.substring(0, 5);
            }
        },

        generateCodeFromName(name) {
            const words = name.toUpperCase().split(' ');
            if (words.length === 1) {
                return words[0].substring(0, 3);
            }
            return words.map(word => word.charAt(0)).join('').substring(0, 5);
        }
    }
}
</script>
@endsection