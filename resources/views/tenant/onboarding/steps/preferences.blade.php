@extends('layouts.tenant-onboarding')

@section('title', 'Business Preferences - Ballie Setup')

@section('content')
<!-- Progress Steps -->
<div class="mb-10">
    <div class="flex items-center justify-center">
        <div class="flex items-center space-x-3 md:space-x-6 overflow-x-auto pb-2">
            @foreach (['Company Info', 'Preferences', 'Accounts', 'Complete'] as $i => $label)
            @if ($i > 0)
            <div class="w-6 md:w-12 h-0.5 {{ $i == 0 ? 'bg-brand-blue' : ($i == 1 ? 'bg-brand-blue' : 'bg-gray-200') }} rounded hidden sm:block"></div>
            @endif
            <div class="flex items-center flex-shrink-0">
                @if ($i < 1)
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                @elseif ($i == 1)
                <div class="w-8 h-8 bg-brand-blue text-white rounded-full flex items-center justify-center font-semibold shadow-sm">
                    2
                </div>
                @else
                <div class="w-8 h-8 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center font-semibold">
                    {{ $i + 1 }}
                </div>
                @endif
                <span class="ml-2 text-xs font-medium {{ $i < 1 ? 'text-green-600' : ($i == 1 ? 'text-brand-blue' : 'text-gray-400') }} whitespace-nowrap hidden sm:inline">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Main Form Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-br from-brand-teal to-brand-blue text-white p-6 md:p-8">
        <div class="text-center">
            <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Configure your preferences</h2>
            <p class="text-blue-100 max-w-lg mx-auto">Set up your currency, financial year, and business modules.</p>
        </div>
    </div>

    <!-- Form Content -->
    <div class="p-6 md:p-8">
        <form method="POST" action="{{ route('tenant.onboarding.save-step', ['tenant' => $currentTenant->slug, 'step' => 'preferences']) }}" class="space-y-8" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <!-- Currency & Localization -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    Currency & Regional Settings
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Primary Currency <span class="text-red-500">*</span>
                        </label>
                        <select id="currency" name="currency"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors" required>
                            <option value="NGN" {{ old('currency', $currentTenant->settings['currency'] ?? 'NGN') == 'NGN' ? 'selected' : '' }}>Nigerian Naira (₦)</option>
                            <option value="USD" {{ old('currency', $currentTenant->settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>US Dollar ($)</option>
                            <option value="GBP" {{ old('currency', $currentTenant->settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>British Pound (£)</option>
                            <option value="EUR" {{ old('currency', $currentTenant->settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                        </select>
                    </div>

                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Timezone <span class="text-red-500">*</span>
                        </label>
                        <select id="timezone" name="timezone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors" required>
                            <option value="Africa/Lagos" {{ old('timezone', $currentTenant->settings['timezone'] ?? 'Africa/Lagos') == 'Africa/Lagos' ? 'selected' : '' }}>West Africa Time (WAT)</option>
                            <option value="UTC" {{ old('timezone', $currentTenant->settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">
                            Date Format <span class="text-red-500">*</span>
                        </label>
                        <select id="date_format" name="date_format"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors" required>
                            <option value="d/m/Y" {{ old('date_format', $currentTenant->settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/{{ date('Y') }})</option>
                            <option value="m/d/Y" {{ old('date_format', $currentTenant->settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/{{ date('Y') }})</option>
                            <option value="Y-m-d" {{ old('date_format', $currentTenant->settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD ({{ date('Y') }}-12-31)</option>
                        </select>
                    </div>

                    <div>
                        <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">
                            Time Format <span class="text-red-500">*</span>
                        </label>
                        <select id="time_format" name="time_format"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors" required>
                            <option value="12" {{ old('time_format', $currentTenant->settings['time_format'] ?? '12') == '12' ? 'selected' : '' }}>12 Hour (2:30 PM)</option>
                            <option value="24" {{ old('time_format', $currentTenant->settings['time_format'] ?? '') == '24' ? 'selected' : '' }}>24 Hour (14:30)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Business Settings -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                    </svg>
                    Business Operations
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fiscal_year_start" class="block text-sm font-medium text-gray-700 mb-2">
                            Financial Year Start <span class="text-red-500">*</span>
                        </label>
                        <select id="fiscal_year_start" name="fiscal_year_start"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors" required>
                            <option value="01-01" {{ old('fiscal_year_start', $currentTenant->settings['fiscal_year_start'] ?? '01-01') == '01-01' ? 'selected' : '' }}>January 1st</option>
                            <option value="04-01" {{ old('fiscal_year_start', $currentTenant->settings['fiscal_year_start'] ?? '') == '04-01' ? 'selected' : '' }}>April 1st</option>
                            <option value="07-01" {{ old('fiscal_year_start', $currentTenant->settings['fiscal_year_start'] ?? '') == '07-01' ? 'selected' : '' }}>July 1st</option>
                            <option value="10-01" {{ old('fiscal_year_start', $currentTenant->settings['fiscal_year_start'] ?? '') == '10-01' ? 'selected' : '' }}>October 1st</option>
                        </select>
                    </div>

                    <div>
                        <label for="default_tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Default VAT Rate (%) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="default_tax_rate" name="default_tax_rate"
                               value="{{ old('default_tax_rate', $currentTenant->settings['default_tax_rate'] ?? '7.5') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors"
                               step="0.01" min="0" max="100" required>
                        <p class="text-xs text-gray-500 mt-1">Current Nigerian VAT rate is 7.5%</p>
                    </div>
                </div>

                <div class="mt-6">
                    <!-- Hidden field - always set to 1 (tax inclusive) -->
                    <input type="hidden" name="tax_inclusive" value="1">
                </div>
            </div>

            <!-- Module Selection -->
            @if(isset($allModules) && count($allModules) > 0)
            <div class="bg-indigo-50 rounded-lg p-6" x-data="{
                modules: @js($allModules),
                get enabledCount() {
                    return this.modules.filter(m => m.enabled).length;
                },
                get optionalCount() {
                    return this.modules.filter(m => m.enabled && !m.core).length;
                },
                get coreCount() {
                    return this.modules.filter(m => m.core).length;
                }
            }">
                <h3 class="text-lg font-semibold text-gray-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    Modules for Your Business
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Based on your business type
                    @if(isset($businessCategory))
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($businessCategory === 'trading') bg-blue-100 text-blue-800
                            @elseif($businessCategory === 'manufacturing') bg-purple-100 text-purple-800
                            @elseif($businessCategory === 'service') bg-green-100 text-green-800
                            @else bg-amber-100 text-amber-800
                            @endif
                        ">{{ ucfirst($businessCategory) }}</span>
                    @endif
                    , we've pre-selected the most relevant modules. You can customize these anytime in Settings.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <template x-for="(mod, index) in modules" :key="mod.key">
                        <div class="relative flex items-start p-3 rounded-lg border transition-colors"
                             :class="mod.enabled ? 'bg-white border-indigo-200' : 'bg-gray-50 border-gray-200'">
                            <div class="flex items-center h-5 mt-0.5">
                                <input type="checkbox"
                                       :name="'enabled_modules[]'"
                                       :value="mod.key"
                                       :checked="mod.enabled"
                                       :disabled="mod.core"
                                       x-model="mod.enabled"
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                       :class="mod.core ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'">
                                <!-- Hidden input for core modules to ensure they're always submitted -->
                                <template x-if="mod.core">
                                    <input type="hidden" :name="'enabled_modules[]'" :value="mod.key">
                                </template>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900" x-text="mod.name"></span>
                                    <template x-if="mod.core">
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                            🔒 Core
                                        </span>
                                    </template>
                                    <template x-if="!mod.core && mod.recommended">
                                        <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                            ★ Recommended
                                        </span>
                                    </template>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="mod.description"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-3 text-center">
                    <span class="text-xs text-gray-500">
                        <span x-text="enabledCount"></span> modules enabled
                        (<span x-text="coreCount"></span> core + <span x-text="optionalCount"></span> optional)
                    </span>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                <div class="text-sm text-gray-400">
                    Step 2 of 4
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('tenant.onboarding.step', ['tenant' => $currentTenant->slug, 'step' => 'company']) }}"
                       class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-50 transition-all font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit"
                            :disabled="loading"
                            :class="loading ? 'opacity-75 cursor-not-allowed' : 'hover:bg-brand-dark-purple hover:shadow-lg'"
                            class="inline-flex items-center px-8 py-3 bg-brand-blue text-white rounded-xl transition-all font-semibold shadow-md">
                        <svg x-show="loading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="loading ? 'Saving...' : 'Next: Chart of Accounts'"></span>
                        <svg x-show="!loading" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Validate tax rate
document.getElementById('default_tax_rate').addEventListener('input', function() {
    const value = parseFloat(this.value);
    if (value < 0) this.value = 0;
    if (value > 100) this.value = 100;
});
</script>
@endpush
