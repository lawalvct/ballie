@extends('layouts.super-admin')

@section('title', 'Payout Settings')

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('super-admin.payouts.index') }}"
           class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Payouts
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-800">Payout Settings</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('super-admin.payouts.settings.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Enable/Disable Payouts -->
                        <div class="p-4 rounded-lg border {{ $settings->payouts_enabled ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="payouts_enabled" class="text-sm font-semibold text-gray-800">Enable Payouts</label>
                                    <p class="text-sm text-gray-600">When disabled, tenants cannot request new payouts.</p>
                                </div>
                                <div class="relative">
                                    <input type="checkbox"
                                           id="payouts_enabled"
                                           name="payouts_enabled"
                                           value="1"
                                           class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                           {{ $settings->payouts_enabled ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Deduction Settings -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Deduction Settings</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="deduction_type" class="block text-sm font-medium text-gray-700 mb-2">Deduction Type <span class="text-red-500">*</span></label>
                                    <select id="deduction_type"
                                            name="deduction_type"
                                            required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deduction_type') border-red-500 @enderror">
                                        <option value="percentage" {{ $settings->deduction_type === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                        <option value="fixed" {{ $settings->deduction_type === 'fixed' ? 'selected' : '' }}>Fixed Amount (₦)</option>
                                    </select>
                                    @error('deduction_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="deduction_value" class="block text-sm font-medium text-gray-700 mb-2">Deduction Value <span class="text-red-500">*</span></label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 rounded-l-lg bg-gray-50 text-gray-500" id="deductionPrefix">%</span>
                                        <input type="number"
                                               id="deduction_value"
                                               name="deduction_value"
                                               value="{{ $settings->deduction_value }}"
                                               step="0.01"
                                               min="0"
                                               required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deduction_value') border-red-500 @enderror">
                                    </div>
                                    @error('deduction_value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="deduction_name" class="block text-sm font-medium text-gray-700 mb-2">Deduction Name <span class="text-red-500">*</span></label>
                                <input type="text"
                                       id="deduction_name"
                                       name="deduction_name"
                                       value="{{ $settings->deduction_name }}"
                                       placeholder="e.g., Service Fee, Processing Fee"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('deduction_name') border-red-500 @enderror">
                                @error('deduction_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">This name will be shown to tenants on their payout requests.</p>
                            </div>
                        </div>

                        <!-- Payout Limits -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Payout Limits</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="minimum_payout" class="block text-sm font-medium text-gray-700 mb-2">Minimum Payout <span class="text-red-500">*</span></label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 rounded-l-lg bg-gray-50 text-gray-500">₦</span>
                                        <input type="number"
                                               id="minimum_payout"
                                               name="minimum_payout"
                                               value="{{ $settings->minimum_payout }}"
                                               step="0.01"
                                               min="0"
                                               required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('minimum_payout') border-red-500 @enderror">
                                    </div>
                                    @error('minimum_payout')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="maximum_payout" class="block text-sm font-medium text-gray-700 mb-2">Maximum Payout</label>
                                    <div class="flex">
                                        <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 rounded-l-lg bg-gray-50 text-gray-500">₦</span>
                                        <input type="number"
                                               id="maximum_payout"
                                               name="maximum_payout"
                                               value="{{ $settings->maximum_payout }}"
                                               step="0.01"
                                               min="0"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('maximum_payout') border-red-500 @enderror">
                                    </div>
                                    @error('maximum_payout')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-sm text-gray-500">Leave empty for no maximum limit.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Additional Information</h4>
                            <div class="mb-4">
                                <label for="processing_time" class="block text-sm font-medium text-gray-700 mb-2">Processing Time <span class="text-red-500">*</span></label>
                                <input type="text"
                                       id="processing_time"
                                       name="processing_time"
                                       value="{{ $settings->processing_time }}"
                                       placeholder="e.g., 3-5 business days"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('processing_time') border-red-500 @enderror">
                                @error('processing_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payout_terms" class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                                <textarea id="payout_terms"
                                          name="payout_terms"
                                          rows="5"
                                          placeholder="Enter payout terms and conditions..."
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('payout_terms') border-red-500 @enderror">{{ $settings->payout_terms }}</textarea>
                                @error('payout_terms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">These terms will be shown to tenants before they submit a payout request.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <a href="{{ route('super-admin.payouts.index') }}"
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                Back
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800">Current Settings Preview</h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Deduction</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $settings->deduction_description }}</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p class="font-medium text-gray-800">Example</p>
                        <p class="mt-1">
                            If a tenant requests ₦100,000, they will receive
                            <span class="font-semibold text-green-600">₦{{ number_format(100000 - ($settings->deduction_type === 'percentage' ? 100000 * ($settings->deduction_value / 100) : $settings->deduction_value), 2) }}</span>
                            after deduction of
                            <span class="font-semibold text-red-600">₦{{ number_format($settings->deduction_type === 'percentage' ? 100000 * ($settings->deduction_value / 100) : $settings->deduction_value, 2) }}</span>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deductionType = document.getElementById('deduction_type');
    const deductionPrefix = document.getElementById('deductionPrefix');

    function updatePrefix() {
        if (deductionType.value === 'percentage') {
            deductionPrefix.textContent = '%';
        } else {
            deductionPrefix.textContent = '₦';
        }
    }

    deductionType.addEventListener('change', updatePrefix);
    updatePrefix();
});
</script>
@endpush
@endsection
