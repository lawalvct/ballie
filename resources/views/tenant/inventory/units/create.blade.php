@extends('layouts.tenant')

@section('title', 'Create Unit')
@section('page-title', 'Create New Unit')
@section('page-description', 'Add a new measurement unit to your inventory system')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Unit</h1>
            <p class="text-gray-600 mt-1">Add a new measurement unit for your products</p>
        </div>
        <a href="{{ route('tenant.inventory.units.index', ['tenant' => $tenant->slug]) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Units
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Unit Information</h3>
            <p class="text-sm text-gray-600 mt-1">Enter the details for the new unit</p>
        </div>

        <form method="POST" action="{{ route('tenant.inventory.units.store', ['tenant' => $tenant->slug]) }}" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Unit Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror"
                           placeholder="e.g., Kilogram, Meter, Liter">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit Symbol -->
                <div>
                    <label for="symbol" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Symbol <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="symbol" id="symbol" value="{{ old('symbol') }}" required maxlength="10"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('symbol') border-red-300 @enderror"
                           placeholder="e.g., kg, m, L">
                    @error('symbol')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-300 @enderror"
                          placeholder="Optional description of the unit">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Unit Type -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Type <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="base_unit" name="is_base_unit" type="radio" value="1"
                                       {{ old('is_base_unit', '1') == '1' ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="base_unit" class="font-medium text-gray-700">Base Unit</label>
                                <p class="text-gray-500">This is a fundamental unit (e.g., Kilogram, Meter, Liter)</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="derived_unit" name="is_base_unit" type="radio" value="0"
                                       {{ old('is_base_unit') == '0' ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="derived_unit" class="font-medium text-gray-700">Derived Unit</label>
                                <p class="text-gray-500">This unit is derived from a base unit (e.g., Gram from Kilogram)</p>
                            </div>
                        </div>
                    </div>
                    @error('is_base_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Derived Unit Fields -->
                <div id="derived_fields" class="space-y-4" style="display: {{ old('is_base_unit', '1') == '0' ? 'block' : 'none' }};">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Base Unit Selection -->
                        <div>
                            <label for="base_unit_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Base Unit <span class="text-red-500">*</span>
                            </label>
                            <select name="base_unit_id" id="base_unit_id"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('base_unit_id') border-red-300 @enderror">
                                <option value="">Select a base unit</option>
                                @foreach($baseUnits as $baseUnit)
                                    <option value="{{ $baseUnit->id }}" {{ old('base_unit_id') == $baseUnit->id ? 'selected' : '' }}>
                                        {{ $baseUnit->name }} ({{ $baseUnit->symbol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('base_unit_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Conversion Factor -->
                        <div>
                            <label for="conversion_factor" class="block text-sm font-medium text-gray-700 mb-2">
                                Conversion Factor <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="conversion_factor" id="conversion_factor"
                                   value="{{ old('conversion_factor') }}" step="0.000001" min="0.000001" max="999999.999999"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('conversion_factor') border-red-300 @enderror"
                                   placeholder="e.g., 0.001 for gram to kilogram">
                            <p class="mt-1 text-xs text-gray-500">
                                How many base units equal 1 of this unit (e.g., 0.001 for gram to kilogram)
                            </p>
                            @error('conversion_factor')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Conversion Example -->
                    <div id="conversion_example" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Conversion Example</h4>
                        <p class="text-sm text-blue-700" id="example_text">
                            Select a base unit and enter a conversion factor to see an example.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div>
                <div class="flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                           {{ old('is_active', '1') ? 'checked' : '' }}
                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Inactive units won't be available for selection in products</p>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('tenant.inventory.units.index', ['tenant' => $tenant->slug]) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Unit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseUnitRadio = document.getElementById('base_unit');
        const derivedUnitRadio = document.getElementById('derived_unit');
        const derivedFields = document.getElementById('derived_fields');
        const baseUnitSelect = document.getElementById('base_unit_id');
        const conversionFactorInput = document.getElementById('conversion_factor');
        const exampleText = document.getElementById('example_text');

        // Toggle derived fields visibility
        function toggleDerivedFields() {
            if (derivedUnitRadio.checked) {
                derivedFields.style.display = 'block';
                baseUnitSelect.setAttribute('required', 'required');
                conversionFactorInput.setAttribute('required', 'required');
            } else {
                derivedFields.style.display = 'none';
                baseUnitSelect.removeAttribute('required');
                conversionFactorInput.removeAttribute('required');
                baseUnitSelect.value = '';
                conversionFactorInput.value = '';
            }
        }

        // Update conversion example
        function updateConversionExample() {
            const baseUnitOption = baseUnitSelect.options[baseUnitSelect.selectedIndex];
            const conversionFactor = parseFloat(conversionFactorInput.value);
            const unitName = document.getElementById('name').value;
            const unitSymbol = document.getElementById('symbol').value;

            if (baseUnitOption.value && conversionFactor && unitName) {
                const baseUnitName = baseUnitOption.text.split(' (')[0];
                const baseUnitSymbol = baseUnitOption.text.match(/\(([^)]+)\)/)[1];

                exampleText.innerHTML = `
                    <strong>Example:</strong><br>
                    1 ${unitName} (${unitSymbol || '?'}) = ${conversionFactor} ${baseUnitName} (${baseUnitSymbol})<br>
                    1 ${baseUnitName} (${baseUnitSymbol}) = ${(1/conversionFactor).toFixed(6)} ${unitName} (${unitSymbol || '?'})
                `;
            } else {
                exampleText.textContent = 'Select a base unit and enter a conversion factor to see an example.';
            }
        }

        // Event listeners
        baseUnitRadio.addEventListener('change', toggleDerivedFields);
        derivedUnitRadio.addEventListener('change', toggleDerivedFields);
        baseUnitSelect.addEventListener('change', updateConversionExample);
        conversionFactorInput.addEventListener('input', updateConversionExample);
        document.getElementById('name').addEventListener('input', updateConversionExample);
        document.getElementById('symbol').addEventListener('input', updateConversionExample);

        // Initialize
        toggleDerivedFields();
        updateConversionExample();

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (derivedUnitRadio.checked) {
                if (!baseUnitSelect.value) {
                    e.preventDefault();
                    alert('Please select a base unit for the derived unit.');
                    baseUnitSelect.focus();
                    return;
                }

                if (!conversionFactorInput.value || parseFloat(conversionFactorInput.value) <= 0) {
                    e.preventDefault();
                    alert('Please enter a valid conversion factor greater than 0.');
                    conversionFactorInput.focus();
                    return;
                }
            }
        });

        // Auto-generate symbol from name
        document.getElementById('name').addEventListener('input', function() {
            const symbolInput = document.getElementById('symbol');
            if (!symbolInput.value) {
                const name = this.value.toLowerCase();
                let symbol = '';

                // Common unit abbreviations
                const abbreviations = {
                    'kilogram': 'kg',
                    'gram': 'g',
                    'pound': 'lb',
                    'ounce': 'oz',
                    'meter': 'm',
                    'centimeter': 'cm',
                    'millimeter': 'mm',
                    'kilometer': 'km',
                    'foot': 'ft',
                    'inch': 'in',
                    'liter': 'L',
                    'milliliter': 'mL',
                    'gallon': 'gal',
                    'quart': 'qt',
                    'pint': 'pt',
                    'piece': 'pcs',
                    'dozen': 'dz',
                    'gross': 'gr',
                    'square meter': 'm²',
                    'square foot': 'ft²',
                    'hour': 'hr',
                    'day': 'day',
                    'week': 'wk',
                    'month': 'mo',
                    'year': 'yr'
                };

                if (abbreviations[name]) {
                    symbol = abbreviations[name];
                } else {
                    // Generate abbreviation from first few characters
                    symbol = name.substring(0, 3);
                }

                symbolInput.value = symbol;
            }
        });
    });
</script>
@endpush
@endsection
