{{-- Payment Gateways Settings Tab --}}
<div class="space-y-8">
    <div>
        <h3 class="text-lg font-semibold text-gray-900">Payment Gateways</h3>
        <p class="mt-1 text-sm text-gray-500">Enable or disable payment gateways available to tenants. API keys are managed via environment variables.</p>
    </div>

    <div class="space-y-6">
        {{-- Paystack --}}
        <div class="p-5 bg-gray-50 rounded-xl border border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Paystack</h4>
                        <p class="text-sm text-gray-500">Accept payments via Paystack payment gateway</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="paystack_enabled" value="1"
                           {{ ($settings['paystack_enabled'] ?? false) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            <div class="mt-3 flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($settings['paystack_enabled'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ ($settings['paystack_enabled'] ?? false) ? 'Active' : 'Disabled' }}
                </span>
                <span class="text-xs text-gray-400">API keys configured in .env</span>
            </div>
        </div>

        {{-- Nomba --}}
        <div class="p-5 bg-gray-50 rounded-xl border border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Nomba</h4>
                        <p class="text-sm text-gray-500">Accept payments via Nomba payment gateway</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="nomba_enabled" value="1"
                           {{ ($settings['nomba_enabled'] ?? false) ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>
            <div class="mt-3 flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($settings['nomba_enabled'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ ($settings['nomba_enabled'] ?? false) ? 'Active' : 'Disabled' }}
                </span>
                <span class="text-xs text-gray-400">API keys configured in .env</span>
            </div>
        </div>
    </div>
</div>
