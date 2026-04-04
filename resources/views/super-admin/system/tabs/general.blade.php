{{-- General Settings Tab --}}
<div class="space-y-8">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">General Settings</h3>
        <p class="mt-1 text-sm text-gray-500">Configure basic application information and defaults.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- App Name --}}
        <div>
            <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
            <input type="text" name="app_name" id="app_name"
                   value="{{ old('app_name', $settings['app_name'] ?? '') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   placeholder="Ballie">
            @error('app_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- App Tagline --}}
        <div>
            <label for="app_tagline" class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
            <input type="text" name="app_tagline" id="app_tagline"
                   value="{{ old('app_tagline', $settings['app_tagline'] ?? '') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   placeholder="Business Management Made Simple">
            @error('app_tagline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Support Email --}}
        <div>
            <label for="support_email" class="block text-sm font-medium text-gray-700 mb-2">Support Email</label>
            <input type="email" name="support_email" id="support_email"
                   value="{{ old('support_email', $settings['support_email'] ?? '') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   placeholder="support@ballie.app">
            @error('support_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Support Phone --}}
        <div>
            <label for="support_phone" class="block text-sm font-medium text-gray-700 mb-2">Support Phone</label>
            <input type="text" name="support_phone" id="support_phone"
                   value="{{ old('support_phone', $settings['support_phone'] ?? '') }}"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   placeholder="+234 xxx xxx xxxx">
            @error('support_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Default Currency --}}
        <div>
            <label for="default_currency" class="block text-sm font-medium text-gray-700 mb-2">Default Currency</label>
            <select name="default_currency" id="default_currency"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                @foreach(['NGN' => 'Nigerian Naira (NGN)', 'USD' => 'US Dollar (USD)', 'GBP' => 'British Pound (GBP)', 'EUR' => 'Euro (EUR)', 'GHS' => 'Ghanaian Cedi (GHS)', 'KES' => 'Kenyan Shilling (KES)', 'ZAR' => 'South African Rand (ZAR)'] as $code => $label)
                    <option value="{{ $code }}" {{ ($settings['default_currency'] ?? 'NGN') === $code ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('default_currency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Default Timezone --}}
        <div>
            <label for="default_timezone" class="block text-sm font-medium text-gray-700 mb-2">Default Timezone</label>
            <select name="default_timezone" id="default_timezone"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                @foreach(['Africa/Lagos' => 'Africa/Lagos (WAT)', 'Africa/Accra' => 'Africa/Accra (GMT)', 'Africa/Nairobi' => 'Africa/Nairobi (EAT)', 'Africa/Johannesburg' => 'Africa/Johannesburg (SAST)', 'Europe/London' => 'Europe/London (GMT/BST)', 'America/New_York' => 'America/New York (EST)', 'UTC' => 'UTC'] as $tz => $label)
                    <option value="{{ $tz }}" {{ ($settings['default_timezone'] ?? 'Africa/Lagos') === $tz ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('default_timezone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
