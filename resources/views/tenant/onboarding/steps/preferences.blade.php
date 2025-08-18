@extends('layouts.tenant-onboarding')

@section('title', 'Business Preferences - Ballie Setup')

@section('content')
<!-- Progress Steps -->
<div class="mb-8">
    <div class="flex items-center justify-center">
        <div class="flex items-center space-x-4 md:space-x-8 overflow-x-auto pb-2">
            <!-- Step 1 - Completed -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="ml-3 text-sm font-medium text-green-600 whitespace-nowrap">Company Info</span>
            </div>

            <!-- Connector -->
            <div class="w-8 md:w-16 h-1 bg-brand-blue rounded hidden sm:block"></div>

            <!-- Step 2 - Active -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-brand-blue text-white rounded-full flex items-center justify-center font-semibold shadow-lg">
                    2
                </div>
                <span class="ml-3 text-sm font-medium text-brand-blue whitespace-nowrap">Preferences</span>
            </div>

            <!-- Connector -->
            <div class="w-8 md:w-16 h-1 bg-gray-200 rounded hidden sm:block"></div>

            <!-- Step 3 -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                    3
                </div>
                <span class="ml-3 text-sm font-medium text-gray-500 whitespace-nowrap">Team Setup</span>
            </div>

            <!-- Connector -->
            <div class="w-8 md:w-16 h-1 bg-gray-200 rounded hidden sm:block"></div>

            <!-- Step 4 -->
            <div class="flex items-center flex-shrink-0">
                <div class="w-10 h-10 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-semibold">
                    4
                </div>
                <span class="ml-3 text-sm font-medium text-gray-500 whitespace-nowrap">Complete</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Form Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-brand-teal to-brand-blue text-white p-6 md:p-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold mb-2">Configure your preferences</h2>
            <p class="text-blue-100">Set up your business settings and preferences.</p>
        </div>
    </div>

    <!-- Form Content -->
    <div class="p-6 md:p-8">
        <form method="POST" action="{{ route('tenant.onboarding.save-step', ['tenant' => $currentTenant->slug, 'step' => 'preferences']) }}" class="space-y-8">
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
                            <option value="d/m/Y" {{ old('date_format', $currentTenant->settings['date_format'] ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2025)</option>
                            <option value="m/d/Y" {{ old('date_format', $currentTenant->settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2024)</option>
                            <option value="Y-m-d" {{ old('date_format', $currentTenant->settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2024-12-31)</option>
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
                        <label for="invoice_prefix" class="block text-sm font-medium text-gray-700 mb-2">
                            Invoice Number Prefix
                        </label>
                        <input type="text" id="invoice_prefix" name="invoice_prefix"
                        value="{{ old('invoice_prefix', $currentTenant->settings['invoice_prefix'] ?? 'INV') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors"
                        placeholder="INV" maxlength="10">
                 <p class="text-xs text-gray-500 mt-1">Example: INV-001, BILL-001</p>
             </div>
         </div>

         <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
             <div>
                 <label for="quote_prefix" class="block text-sm font-medium text-gray-700 mb-2">
                     Quote Number Prefix
                 </label>
                 <input type="text" id="quote_prefix" name="quote_prefix"
                        value="{{ old('quote_prefix', $currentTenant->settings['quote_prefix'] ?? 'QUO') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors"
                        placeholder="QUO" maxlength="10">
             </div>

             <div>
                 <label for="receipt_prefix" class="block text-sm font-medium text-gray-700 mb-2">
                     Receipt Number Prefix
                 </label>
                 <input type="text" id="receipt_prefix" name="receipt_prefix"
                        value="{{ old('receipt_prefix', $currentTenant->settings['receipt_prefix'] ?? 'REC') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors"
                        placeholder="REC" maxlength="10">
             </div>
         </div>
     </div>

     <!-- Tax Settings -->
     <div class="bg-green-50 rounded-lg p-6">
         <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
             <svg class="w-5 h-5 mr-2 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
             </svg>
             Tax Configuration
         </h3>

         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <div>
                 <label for="default_tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                     Default VAT Rate (%)
                 </label>
                 <input type="number" id="default_tax_rate" name="default_tax_rate"
                        value="{{ old('default_tax_rate', $currentTenant->settings['default_tax_rate'] ?? '7.5') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent transition-colors"
                        step="0.01" min="0" max="100">
                 <p class="text-xs text-gray-500 mt-1">Current Nigerian VAT rate is 7.5%</p>
             </div>

             <div>
                 <label class="block text-sm font-medium text-gray-700 mb-2">
                     Tax Inclusive Pricing
                 </label>
                 <div class="space-y-2">
                     <label class="flex items-center">
                         <input type="radio" name="tax_inclusive" value="1"
                                {{ old('tax_inclusive', $currentTenant->settings['tax_inclusive'] ?? '0') == '1' ? 'checked' : '' }}
                                class="text-brand-blue focus:ring-brand-blue">
                         <span class="ml-2 text-sm text-gray-700">Prices include tax</span>
                     </label>
                     <label class="flex items-center">
                         <input type="radio" name="tax_inclusive" value="0"
                                {{ old('tax_inclusive', $currentTenant->settings['tax_inclusive'] ?? '0') == '0' ? 'checked' : '' }}
                                class="text-brand-blue focus:ring-brand-blue">
                         <span class="ml-2 text-sm text-gray-700">Prices exclude tax</span>
                     </label>
                 </div>
             </div>
         </div>
     </div>

     <!-- Notification Settings -->
     <div class="bg-purple-50 rounded-lg p-6">
         <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
             <svg class="w-5 h-5 mr-2 text-brand-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h8v-2H4v2zM4 11h10V9H4v2zM4 7h12V5H4v2z"></path>
             </svg>
             Notifications & Reminders
         </h3>

         <div class="space-y-4">
             <label class="flex items-center">
                 <input type="checkbox" name="email_notifications" value="1"
                        {{ old('email_notifications', $currentTenant->settings['email_notifications'] ?? '1') == '1' ? 'checked' : '' }}
                        class="text-brand-blue focus:ring-brand-blue rounded">
                 <span class="ml-3 text-sm text-gray-700">Send email notifications for important events</span>
             </label>

             <label class="flex items-center">
                 <input type="checkbox" name="payment_reminders" value="1"
                        {{ old('payment_reminders', $currentTenant->settings['payment_reminders'] ?? '1') == '1' ? 'checked' : '' }}
                        class="text-brand-blue focus:ring-brand-blue rounded">
                 <span class="ml-3 text-sm text-gray-700">Automatic payment reminders to customers</span>
             </label>

             <label class="flex items-center">
                 <input type="checkbox" name="low_stock_alerts" value="1"
                        {{ old('low_stock_alerts', $currentTenant->settings['low_stock_alerts'] ?? '1') == '1' ? 'checked' : '' }}
                        class="text-brand-blue focus:ring-brand-blue rounded">
                 <span class="ml-3 text-sm text-gray-700">Low stock level alerts</span>
             </label>

             <label class="flex items-center">
                 <input type="checkbox" name="backup_reminders" value="1"
                        {{ old('backup_reminders', $currentTenant->settings['backup_reminders'] ?? '1') == '1' ? 'checked' : '' }}
                        class="text-brand-blue focus:ring-brand-blue rounded">
                 <span class="ml-3 text-sm text-gray-700">Weekly backup reminders</span>
             </label>
         </div>
     </div>

     <!-- Form Actions -->
     <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
         <div class="text-sm text-gray-500">
             Step 2 of 4 - Business Preferences
         </div>

         <div class="flex space-x-4">
             <a href="{{ route('tenant.onboarding.step', ['tenant' => $currentTenant->slug, 'step' => 'company']) }}"
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                 Back
             </a>
             <button type="submit"
                     class="px-8 py-3 bg-brand-blue text-white rounded-lg hover:bg-brand-dark-purple transition-colors font-medium flex items-center">
                 Continue
                 <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                 </svg>
             </button>
         </div>
     </div>
 </form>
</div>
</div>

<!-- Help Section -->
<div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
<div class="flex items-start">
 <div class="w-10 h-10 bg-brand-teal bg-opacity-10 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
     <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
     </svg>
 </div>
 <div>
     <h3 class="text-lg font-semibold text-gray-900 mb-2">Configuration Tips</h3>
     <ul class="text-gray-600 space-y-2 text-sm">
         <li>• <strong>Currency:</strong> Choose your primary business currency. You can add more currencies later.</li>
         <li>• <strong>Financial Year:</strong> Most Nigerian businesses use January 1st as their financial year start.</li>
         <li>• <strong>VAT Rate:</strong> The current Nigerian VAT rate is 7.5%. This can be updated if rates change.</li>
         <li>• <strong>Prefixes:</strong> Use short, meaningful prefixes for your document numbers (e.g., INV, QUO, REC).</li>
     </ul>
 </div>
</div>
</div>
@endsection

@push('scripts')
<script>
// Preview currency format
document.getElementById('currency').addEventListener('change', function() {
const currency = this.value;
const symbols = {
 'NGN': '₦',
 'USD': '$',
 'GBP': '£',
 'EUR': '€'
};

// You can add preview functionality here
console.log('Currency changed to:', currency, symbols[currency]);
});

// Validate tax rate
document.getElementById('default_tax_rate').addEventListener('input', function() {
const value = parseFloat(this.value);
if (value < 0) this.value = 0;
if (value > 100) this.value = 100;
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
const currency = document.getElementById('currency').value;
const timezone = document.getElementById('timezone').value;
const dateFormat = document.getElementById('date_format').value;
const timeFormat = document.getElementById('time_format').value;
const financialYear = document.getElementById('fiscal_year_start').value;

if (!currency || !timezone || !dateFormat || !timeFormat || !financialYear) {
 e.preventDefault();
 alert('Please fill in all required fields.');
 return false;
}
});
</script>
@endpush
