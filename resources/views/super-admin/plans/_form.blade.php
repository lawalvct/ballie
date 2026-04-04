@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Form -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-base font-semibold text-gray-900">Plan Details</h3>
                <p class="text-xs text-gray-500 mt-0.5">Name, description, and feature list</p>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Plan Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $plan->name ?? '') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                               placeholder="e.g. Starter, Professional, Enterprise" required>
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $plan->sort_order ?? 0) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500"
                               min="0" placeholder="0">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="2"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 @enderror"
                              placeholder="Brief plan description for customers">{{ old('description', $plan->description ?? '') }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="features" class="block text-sm font-medium text-gray-700 mb-1">Features <span class="text-gray-400 font-normal">(one per line)</span></label>
                    <textarea name="features" id="features" rows="5"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-mono focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Unlimited invoices&#10;Up to 5 users&#10;Basic reports&#10;Email support">{{ old('features', isset($plan) && is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">These appear on the pricing page as bullet points</p>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-base font-semibold text-gray-900">Pricing</h3>
                <p class="text-xs text-gray-500 mt-0.5">All prices in <strong>kobo</strong> (&#8358;1,000 = 100000 kobo)</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label for="monthly_price" class="block text-sm font-medium text-gray-700 mb-1">Monthly <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">kobo</div>
                            <input type="number" name="monthly_price" id="monthly_price"
                                   value="{{ old('monthly_price', $plan->monthly_price ?? 0) }}"
                                   class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('monthly_price') border-red-300 @enderror"
                                   min="0" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="monthly_display"></p>
                        @error('monthly_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="quarterly_price" class="block text-sm font-medium text-gray-700 mb-1">Quarterly <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">kobo</div>
                            <input type="number" name="quarterly_price" id="quarterly_price"
                                   value="{{ old('quarterly_price', $plan->quarterly_price ?? 0) }}"
                                   class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('quarterly_price') border-red-300 @enderror"
                                   min="0" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="quarterly_display"></p>
                        @error('quarterly_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="biannual_price" class="block text-sm font-medium text-gray-700 mb-1">Bi-Annual <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">kobo</div>
                            <input type="number" name="biannual_price" id="biannual_price"
                                   value="{{ old('biannual_price', $plan->biannual_price ?? 0) }}"
                                   class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('biannual_price') border-red-300 @enderror"
                                   min="0" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="biannual_display"></p>
                        @error('biannual_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="yearly_price" class="block text-sm font-medium text-gray-700 mb-1">Yearly <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">kobo</div>
                            <input type="number" name="yearly_price" id="yearly_price"
                                   value="{{ old('yearly_price', $plan->yearly_price ?? 0) }}"
                                   class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('yearly_price') border-red-300 @enderror"
                                   min="0" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500" id="yearly_display"></p>
                        @error('yearly_price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Limits -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-indigo-50">
                <h3 class="text-base font-semibold text-gray-900">Limits & Support</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="max_users" class="block text-sm font-medium text-gray-700 mb-1">Max Users <span class="text-red-500">*</span></label>
                        <input type="number" name="max_users" id="max_users" value="{{ old('max_users', $plan->max_users ?? 1) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('max_users') border-red-300 @enderror"
                               min="1" required>
                        @error('max_users') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="max_customers" class="block text-sm font-medium text-gray-700 mb-1">Max Customers <span class="text-red-500">*</span></label>
                        <input type="number" name="max_customers" id="max_customers" value="{{ old('max_customers', $plan->max_customers ?? 0) }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('max_customers') border-red-300 @enderror"
                               min="0" required>
                        <p class="mt-1 text-xs text-gray-500">0 = unlimited</p>
                        @error('max_customers') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="support_level" class="block text-sm font-medium text-gray-700 mb-1">Support Level <span class="text-red-500">*</span></label>
                        <select name="support_level" id="support_level"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 @error('support_level') border-red-300 @enderror" required>
                            @foreach(['basic', 'standard', 'priority', 'dedicated'] as $level)
                                <option value="{{ $level }}" {{ old('support_level', $plan->support_level ?? 'basic') === $level ? 'selected' : '' }}>
                                    {{ ucfirst($level) }}
                                </option>
                            @endforeach
                        </select>
                        @error('support_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">

        <!-- Feature Flags -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Feature Flags</h3>
                <p class="text-xs text-gray-500 mt-0.5">Toggle modules included in this plan</p>
            </div>
            <div class="p-6 space-y-4">
                @php
                    $flags = [
                        'has_pos' => ['POS (Point of Sale)', 'In-store sales terminal'],
                        'has_payroll' => ['Payroll', 'Employee salary management'],
                        'has_api_access' => ['API Access', 'Third-party integrations'],
                        'has_advanced_reports' => ['Advanced Reports', 'Detailed analytics & exports'],
                        'has_ecommerce' => ['E-commerce', 'Online store features'],
                        'has_audit_log' => ['Audit Log', 'Activity tracking & history'],
                        'has_multi_location' => ['Multi-Location', 'Multiple business locations'],
                        'has_multi_currency' => ['Multi-Currency', 'Foreign currency support'],
                    ];
                @endphp
                @foreach($flags as $field => [$label, $desc])
                    <label class="flex items-start cursor-pointer group">
                        <input type="checkbox" name="{{ $field }}" value="1"
                               {{ old($field, isset($plan) && $plan->$field ? '1' : '') ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div class="ml-3">
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ $label }}</span>
                            <p class="text-xs text-gray-400">{{ $desc }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Status & Options -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Options</h3>
            </div>
            <div class="p-6 space-y-4">
                <label class="flex items-start cursor-pointer group">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', isset($plan) ? $plan->is_active : true) ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Active</span>
                        <p class="text-xs text-gray-400">Plan is visible and available for subscription</p>
                    </div>
                </label>
                <label class="flex items-start cursor-pointer group">
                    <input type="checkbox" name="is_popular" value="1"
                           {{ old('is_popular', isset($plan) && $plan->is_popular ? '1' : '') ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 text-yellow-500 border-gray-300 rounded focus:ring-yellow-500">
                    <div class="ml-3">
                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Mark as Popular</span>
                        <p class="text-xs text-gray-400">Highlights this plan on the pricing page</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <button type="submit"
                    class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ isset($plan) ? 'Update Plan' : 'Create Plan' }}
            </button>
            <a href="{{ route('super-admin.plans.index') }}"
               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </div>
</div>
