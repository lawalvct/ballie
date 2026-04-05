{{-- Registration Settings Tab --}}
<div class="space-y-8">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">Registration Settings</h3>
        <p class="mt-1 text-sm text-gray-500">Control user and tenant registration options.</p>
    </div>

    {{-- Toggle Switches --}}
    <div class="space-y-6">
        {{-- Registration Enabled --}}
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
            <div>
                <h4 class="text-sm font-medium text-gray-900">User Registration</h4>
                <p class="text-sm text-gray-500 mt-0.5">Allow new users to register for accounts</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="registration_enabled" value="1"
                       {{ ($settings['registration_enabled'] ?? false) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
        </div>

        {{-- Affiliate Registration Enabled --}}
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
            <div>
                <h4 class="text-sm font-medium text-gray-900">Affiliate Registration</h4>
                <p class="text-sm text-gray-500 mt-0.5">Allow users to register as affiliates</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="affiliate_registration_enabled" value="1"
                       {{ ($settings['affiliate_registration_enabled'] ?? false) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
        </div>

        {{-- Email Verification Required --}}
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
            <div>
                <h4 class="text-sm font-medium text-gray-900">Require Email Verification</h4>
                <p class="text-sm text-gray-500 mt-0.5">Users must verify their email before accessing their account</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="require_email_verification" value="1"
                       {{ ($settings['require_email_verification'] ?? false) ? 'checked' : '' }}
                       class="sr-only peer">
                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
        </div>
    </div>

    {{-- Numeric Settings --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Default Trial Days --}}
        <div>
            <label for="default_trial_days" class="block text-sm font-medium text-gray-700 mb-2">Default Trial Days</label>
            <input type="number" name="default_trial_days" id="default_trial_days"
                   value="{{ old('default_trial_days', $settings['default_trial_days'] ?? 14) }}"
                   min="0" max="365"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-gray-500">Number of free trial days for new tenants. Set 0 to disable.</p>
            @error('default_trial_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Max Companies Per User reserved for future implementation. --}}
    </div>
</div>
