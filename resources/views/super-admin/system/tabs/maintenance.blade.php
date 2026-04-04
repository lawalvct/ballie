{{-- Maintenance Settings Tab --}}
<div class="space-y-8">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">Maintenance Mode</h3>
        <p class="mt-1 text-sm text-gray-500">Put the application into maintenance mode to prevent user access during updates.</p>
    </div>

    {{-- Maintenance Toggle --}}
    <div class="flex items-center justify-between p-5 rounded-xl border-2 {{ ($settings['maintenance_mode'] ?? false) ? 'bg-red-50 border-red-300' : 'bg-gray-50 border-gray-200' }}">
        <div>
            <h4 class="text-sm font-semibold {{ ($settings['maintenance_mode'] ?? false) ? 'text-red-900' : 'text-gray-900' }}">Maintenance Mode</h4>
            <p class="text-sm {{ ($settings['maintenance_mode'] ?? false) ? 'text-red-600' : 'text-gray-500' }} mt-0.5">
                {{ ($settings['maintenance_mode'] ?? false) ? '⚠️ Application is currently in maintenance mode' : 'Application is running normally' }}
            </p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="maintenance_mode" value="1"
                   {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                   class="sr-only peer"
                   onchange="document.getElementById('maintenance-warning').classList.toggle('hidden', !this.checked)">
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
        </label>
    </div>

    {{-- Warning Banner --}}
    <div id="maintenance-warning" class="{{ ($settings['maintenance_mode'] ?? false) ? '' : 'hidden' }}">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start space-x-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <h4 class="text-sm font-medium text-amber-800">Caution</h4>
                <p class="text-sm text-amber-700 mt-1">Enabling maintenance mode will prevent all users from accessing the application except those whose IPs are in the allowed list below. Super admin panel will remain accessible.</p>
            </div>
        </div>
    </div>

    {{-- Maintenance Message --}}
    <div>
        <label for="maintenance_message" class="block text-sm font-medium text-gray-700 mb-2">Maintenance Message</label>
        <textarea name="maintenance_message" id="maintenance_message" rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                  placeholder="We are currently performing scheduled maintenance...">{{ old('maintenance_message', $settings['maintenance_message'] ?? '') }}</textarea>
        <p class="mt-1 text-xs text-gray-500">This message will be displayed to users when maintenance mode is active.</p>
        @error('maintenance_message') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Allowed IPs --}}
    <div>
        <label for="maintenance_allowed_ips" class="block text-sm font-medium text-gray-700 mb-2">Allowed IP Addresses</label>
        <textarea name="maintenance_allowed_ips" id="maintenance_allowed_ips" rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all font-mono text-sm"
                  placeholder="127.0.0.1&#10;192.168.1.100">{{ old('maintenance_allowed_ips', $settings['maintenance_allowed_ips'] ?? '') }}</textarea>
        <p class="mt-1 text-xs text-gray-500">One IP address per line. These IPs will be able to access the site during maintenance.</p>
        @error('maintenance_allowed_ips') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
