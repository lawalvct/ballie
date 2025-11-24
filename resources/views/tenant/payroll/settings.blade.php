@extends('layouts.tenant')

@section('title', 'Payroll Settings')
@section('page-title', 'Payroll Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('tenant.payroll.settings.update', $tenant) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Number Format</h3>
                
                <div class="mb-4">
                    <label for="employee_number_format" class="block text-sm font-medium text-gray-700 mb-2">
                        Format Pattern
                    </label>
                    <input type="text" 
                           name="employee_number_format" 
                           id="employee_number_format"
                           value="{{ old('employee_number_format', $tenant->employee_number_format ?? 'EMP-{YYYY}-{####}') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="EMP-{YYYY}-{####}">
                    @error('employee_number_format')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-blue-900 mb-2">Available Placeholders:</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li><code class="bg-blue-100 px-2 py-0.5 rounded">{YYYY}</code> - Full year (e.g., 2025)</li>
                        <li><code class="bg-blue-100 px-2 py-0.5 rounded">{YY}</code> - Short year (e.g., 25)</li>
                        <li><code class="bg-blue-100 px-2 py-0.5 rounded">{MM}</code> - Month (e.g., 01-12)</li>
                        <li><code class="bg-blue-100 px-2 py-0.5 rounded">{####}</code> - Sequential number (padded to 4 digits)</li>
                        <li><code class="bg-blue-100 px-2 py-0.5 rounded">{###}</code> - Sequential number (padded to 3 digits)</li>
                    </ul>
                    <p class="text-sm text-blue-700 mt-3">
                        <strong>Example:</strong> Format <code class="bg-blue-100 px-2 py-0.5 rounded">EMP-{YYYY}-{####}</code> 
                        generates <code class="bg-blue-100 px-2 py-0.5 rounded">EMP-2025-0001</code>
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('tenant.payroll.index', $tenant) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
