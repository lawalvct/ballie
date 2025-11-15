@extends('layouts.tenant')

@section('title', 'Update Salary - ' . $employee->full_name)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <i class="fas fa-calculator text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">Update Salary</h1>
                        <p class="text-blue-100 text-lg">{{ $employee->full_name }} - {{ $employee->employee_number }}</p>
                    </div>
                </div>
                <a href="{{ route('tenant.payroll.employees.show', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                   class="bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-white/20">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Profile
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('tenant.payroll.employees.update-salary', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
              method="POST"
              class="space-y-8">
            @csrf

            <!-- Current Salary Overview -->
            @if($employee->currentSalary)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Current Salary Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Basic Salary</p>
                        <p class="text-2xl font-bold text-blue-600">₦{{ number_format($employee->currentSalary->basic_salary, 2) }}</p>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Total Allowances</p>
                        <p class="text-2xl font-bold text-green-600">₦{{ number_format($employee->currentSalary->total_allowances, 2) }}</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Gross Salary</p>
                        <p class="text-2xl font-bold text-purple-600">₦{{ number_format($employee->currentSalary->gross_salary, 2) }}</p>
                    </div>
                </div>

                <div class="text-sm text-gray-600">
                    <i class="fas fa-calendar mr-2"></i>
                    Effective from: {{ $employee->currentSalary->effective_date->format('M d, Y') }}
                </div>
            </div>
            @endif

            <!-- New Salary Details -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-edit mr-2 text-purple-500"></i>
                    New Salary Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Salary -->
                    <div>
                        <label for="basic_salary" class="block text-sm font-medium text-gray-700 mb-2">
                            New Basic Salary <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                            <input type="number"
                                   name="basic_salary"
                                   id="basic_salary"
                                   step="0.01"
                                   min="0"
                                   value="{{ old('basic_salary', $employee->currentSalary?->basic_salary) }}"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('basic_salary') border-red-500 @enderror"
                                   required>
                        </div>
                        @error('basic_salary')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Effective Date -->
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Effective Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date"
                               name="effective_date"
                               id="effective_date"
                               value="{{ old('effective_date', now()->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('effective_date') border-red-500 @enderror"
                               required>
                        @error('effective_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Salary Update
                        </label>
                        <textarea name="notes"
                                  id="notes"
                                  rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                                  placeholder="E.g., Annual increment, Promotion, Performance bonus, etc.">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Salary Components -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-list mr-2 text-green-500"></i>
                    Salary Components
                </h3>

                @if($salaryComponents->count() > 0)
                    <div class="space-y-4">
                        @foreach($salaryComponents as $component)
                            <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <input type="checkbox"
                                       name="components[{{ $component->id }}][enabled]"
                                       id="component_{{ $component->id }}"
                                       value="1"
                                       {{ $employee->currentSalary && $employee->currentSalary->salaryComponents->contains('salary_component_id', $component->id) ? 'checked' : '' }}
                                       class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                       onchange="toggleComponentInputs({{ $component->id }})">

                                <div class="ml-4 flex-1">
                                    <label for="component_{{ $component->id }}" class="font-medium text-gray-900 cursor-pointer">
                                        {{ $component->name }}
                                        <span class="inline-flex items-center px-2 py-1 ml-2 rounded-full text-xs font-medium
                                            {{ $component->type === 'earning' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($component->type) }}
                                        </span>
                                    </label>
                                    @if($component->description)
                                        <p class="text-sm text-gray-500 mt-1">{{ $component->description }}</p>
                                    @endif
                                </div>

                                <!-- Amount/Percentage Input -->
                                <div class="ml-4 w-48" id="component_input_{{ $component->id }}" style="display: none;">
                                    @if($component->calculation_type === 'percentage')
                                        <div class="relative">
                                            <input type="number"
                                                   name="components[{{ $component->id }}][percentage]"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   value="{{ old('components.'.$component->id.'.percentage',
                                                             $employee->currentSalary?->salaryComponents->where('salary_component_id', $component->id)->first()?->percentage ?? 0) }}"
                                                   class="w-full pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                   placeholder="0.00">
                                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                                        </div>
                                    @elseif($component->calculation_type === 'fixed')
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₦</span>
                                            <input type="number"
                                                   name="components[{{ $component->id }}][amount]"
                                                   step="0.01"
                                                   min="0"
                                                   value="{{ old('components.'.$component->id.'.amount',
                                                             $employee->currentSalary?->salaryComponents->where('salary_component_id', $component->id)->first()?->amount ?? 0) }}"
                                                   class="w-full pl-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                                   placeholder="0.00">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-info-circle text-4xl mb-4"></i>
                        <p>No salary components available. You can add them in the <a href="{{ route('tenant.payroll.components.index', $tenant->slug) }}" class="text-purple-600 hover:underline">Components</a> section.</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('tenant.payroll.employees.show', ['tenant' => $tenant->slug, 'employee' => $employee->id]) }}"
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-300">
                    <i class="fas fa-save mr-2"></i>Update Salary
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Initialize component inputs visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    @foreach($salaryComponents as $component)
        toggleComponentInputs({{ $component->id }});
    @endforeach
});

function toggleComponentInputs(componentId) {
    const checkbox = document.getElementById('component_' + componentId);
    const inputDiv = document.getElementById('component_input_' + componentId);

    if (checkbox && inputDiv) {
        if (checkbox.checked) {
            inputDiv.style.display = 'block';
        } else {
            inputDiv.style.display = 'none';
        }
    }
}
</script>
@endsection
