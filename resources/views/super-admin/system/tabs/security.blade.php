{{-- Security Settings Tab --}}
<div class="space-y-8">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">Security Settings</h3>
        <p class="mt-1 text-sm text-gray-500">Configure security policies for the application.</p>
    </div>

    {{-- Two Factor Enforcement --}}
    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
        <div>
            <h4 class="text-sm font-medium text-gray-900">Enforce Two-Factor Authentication</h4>
            <p class="text-sm text-gray-500 mt-0.5">Require all users to set up 2FA on their accounts</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="two_factor_enforcement" value="1"
                   {{ ($settings['two_factor_enforcement'] ?? false) ? 'checked' : '' }}
                   class="sr-only peer">
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
        </label>
    </div>

    {{-- Numeric Security Settings --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Max Login Attempts --}}
        <div>
            <label for="max_login_attempts" class="block text-sm font-medium text-gray-700 mb-2">Max Login Attempts</label>
            <input type="number" name="max_login_attempts" id="max_login_attempts"
                   value="{{ old('max_login_attempts', $settings['max_login_attempts'] ?? 5) }}"
                   min="1" max="20"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-gray-500">Failed attempts before lockout</p>
            @error('max_login_attempts') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Lockout Duration --}}
        <div>
            <label for="lockout_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Lockout Duration (minutes)</label>
            <input type="number" name="lockout_duration_minutes" id="lockout_duration_minutes"
                   value="{{ old('lockout_duration_minutes', $settings['lockout_duration_minutes'] ?? 15) }}"
                   min="1" max="1440"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-gray-500">Lockout time after max attempts</p>
            @error('lockout_duration_minutes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Force Password Change --}}
        <div>
            <label for="force_password_change_days" class="block text-sm font-medium text-gray-700 mb-2">Force Password Change (days)</label>
            <input type="number" name="force_password_change_days" id="force_password_change_days"
                   value="{{ old('force_password_change_days', $settings['force_password_change_days'] ?? 0) }}"
                   min="0" max="365"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            <p class="mt-1 text-xs text-gray-500">Days before password change is required. 0 = disabled.</p>
            @error('force_password_change_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
