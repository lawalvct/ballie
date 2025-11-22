<div class="space-y-6">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-900">Permission Selection</h4>
                <p class="text-sm text-blue-700 mt-1">Select the permissions this role should have. Permissions are grouped by module.</p>
            </div>
        </div>
    </div>

    @php
        $permissionsByModule = $permissions->groupBy('module');
        $selectedPermissions = old('permissions', $role->permissions->pluck('id')->toArray() ?? []);
    @endphp

    @foreach($permissionsByModule as $module => $modulePermissions)
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="module_{{ \Str::slug($module) }}" 
                           class="module-checkbox h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                           data-module="{{ \Str::slug($module) }}">
                    <label for="module_{{ \Str::slug($module) }}" class="ml-2 text-sm font-semibold text-gray-900">
                        {{ $module }} Module
                    </label>
                </div>
                <span class="text-xs text-gray-500">{{ $modulePermissions->count() }} permissions</span>
            </div>
        </div>
        <div class="bg-white px-4 py-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($modulePermissions as $permission)
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" 
                               name="permissions[]" 
                               value="{{ $permission->id }}"
                               id="permission_{{ $permission->id }}"
                               class="permission-checkbox h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                               data-module="{{ \Str::slug($module) }}"
                               {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="permission_{{ $permission->id }}" class="font-medium text-gray-700 cursor-pointer">
                            {{ $permission->display_name }}
                        </label>
                        @if($permission->description)
                        <p class="text-gray-500 text-xs mt-0.5">{{ $permission->description }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
        const module = moduleCheckbox.dataset.module;
        const permissionCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
        
        updateModuleCheckbox(moduleCheckbox, permissionCheckboxes);
        
        moduleCheckbox.addEventListener('change', function() {
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateModuleCheckbox(moduleCheckbox, permissionCheckboxes);
            });
        });
    });
    
    function updateModuleCheckbox(moduleCheckbox, permissionCheckboxes) {
        const checkedCount = Array.from(permissionCheckboxes).filter(cb => cb.checked).length;
        const totalCount = permissionCheckboxes.length;
        
        if (checkedCount === 0) {
            moduleCheckbox.checked = false;
            moduleCheckbox.indeterminate = false;
        } else if (checkedCount === totalCount) {
            moduleCheckbox.checked = true;
            moduleCheckbox.indeterminate = false;
        } else {
            moduleCheckbox.checked = false;
            moduleCheckbox.indeterminate = true;
        }
    }
});
</script>
@endpush
