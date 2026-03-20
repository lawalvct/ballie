@extends('layouts.tenant')

@section('title', 'Tax Settings - ' . $tenant->name)
@section('page-title', 'Tax Settings')
@section('page-description')
    <span class="hidden md:inline">Configure tax rates, VAT registration, and statutory compliance settings</span>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('tenant.statutory.index', $tenant->slug) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to Tax
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('tenant.statutory.settings.update', $tenant->slug) }}">
        @csrf
        @method('PUT')

        <!-- VAT Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">VAT Settings</h3>
                <p class="text-sm text-gray-500 mt-1">Configure your Value Added Tax rate and registration details</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="vat_rate" class="block text-sm font-medium text-gray-700 mb-1">VAT Rate (%)</label>
                        <input type="number" name="vat_rate" id="vat_rate" step="0.01" min="0" max="100"
                            value="{{ old('vat_rate', $tenant->vat_rate ?? 7.50) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('vat_rate') border-red-500 @enderror">
                        @error('vat_rate')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Standard Nigerian VAT rate is 7.5%</p>
                    </div>
                    <div>
                        <label for="vat_registration_number" class="block text-sm font-medium text-gray-700 mb-1">VAT Registration Number</label>
                        <input type="text" name="vat_registration_number" id="vat_registration_number"
                            value="{{ old('vat_registration_number', $tenant->vat_registration_number) }}"
                            placeholder="e.g. VAT-12345678"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('vat_registration_number') border-red-500 @enderror">
                        @error('vat_registration_number')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="tax_identification_number" class="block text-sm font-medium text-gray-700 mb-1">Tax Identification Number (TIN)</label>
                    <input type="text" name="tax_identification_number" id="tax_identification_number"
                        value="{{ old('tax_identification_number', $tenant->tax_identification_number) }}"
                        placeholder="e.g. 12345678-0001"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('tax_identification_number') border-red-500 @enderror">
                    @error('tax_identification_number')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Tax Rates -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Configured Tax Rates</h3>
                <p class="text-sm text-gray-500 mt-1">Active tax rates used across your system</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Default</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($taxRates as $rate)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $rate->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $rate->type === 'percentage' ? $rate->rate . '%' : '₦' . number_format($rate->rate, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $rate->type }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($rate->is_default)
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Default</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($rate->is_active)
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                No tax rates configured. The system uses the default VAT rate above.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pension Settings (Read-Only Info) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Pension & Statutory Rates</h3>
                <p class="text-sm text-gray-500 mt-1">Standard statutory rates (managed by payroll module)</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600">Employee Pension</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">8%</p>
                        <p class="text-xs text-gray-500 mt-1">Contributory Pension Scheme</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600">Employer Pension</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">10%</p>
                        <p class="text-xs text-gray-500 mt-1">Contributory Pension Scheme</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600">NSITF</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">1%</p>
                        <p class="text-xs text-gray-500 mt-1">National Social Insurance Trust Fund</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-4">These rates are defined by Nigerian law. To manage employee exemptions, visit the Payroll module.</p>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 font-medium">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
