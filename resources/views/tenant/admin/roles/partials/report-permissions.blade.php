@php
    $existingPermissionIds = isset($role) ? $role->permissions->pluck('id')->all() : [];
    $selectedPermissionIds = collect(old('permissions', $existingPermissionIds))
        ->map(fn($id) => (int) $id)
        ->all();
@endphp

@if(isset($reportPermissionGroups) && $reportPermissionGroups->isNotEmpty())
    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50/70 p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h4 class="text-sm font-semibold text-amber-900 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Business Report Types
                </h4>
                <p class="mt-1 text-xs text-amber-800">Select exactly which report sections this role can see.</p>
            </div>
            <button type="button" onclick="toggleModulePermissions('business_reports')"
                    class="self-start text-xs font-medium text-amber-800 hover:text-amber-900 px-3 py-1 rounded-md hover:bg-amber-100 transition">
                Select All
            </button>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($reportPermissionGroups as $group)
                @php
                    $isChecked = count(array_intersect($selectedPermissionIds, $group['permission_ids'])) > 0;
                @endphp
                <label class="flex items-start p-3 rounded-lg border-2 border-amber-200 hover:border-amber-400 cursor-pointer transition bg-white">
                    <input id="permission_{{ $group['permission']->id }}"
                           name="permissions[]"
                           type="checkbox"
                           value="{{ $group['permission']->id }}"
                           data-module="business_reports"
                           {{ $isChecked ? 'checked' : '' }}
                           class="mt-1 h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                    <span class="ml-3">
                        <span class="block text-sm font-semibold text-gray-900">{{ $group['label'] }}</span>
                        <span class="block mt-1 text-xs text-gray-500 leading-5">{{ $group['description'] }}</span>
                    </span>
                </label>
            @endforeach
        </div>
    </div>
@endif
