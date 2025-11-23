@extends('layouts.tenant')

@section('title', 'Create Role')
@section('page-title', 'Create New Role')
@section('page-description')
    <span class="hidden md:inline">Define a new role with specific permissions for your organization</span>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('tenant.admin.roles.index', tenant('slug')) }}"
               class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Roles
            </a>
        </div>

        {{-- Role Creation Form --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('tenant.admin.roles.store', tenant('slug')) }}">
                @csrf

                {{-- Form Header with Gradient --}}
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-8">
                    <h2 class="text-2xl font-bold text-white">Create New Role</h2>
                    <p class="text-purple-100 mt-2">Configure role details and assign permissions</p>
                </div>

                {{-- Form Content --}}
                <div class="px-6 py-8">
                    {{-- Basic Information Section --}}
                    <div class="mb-8">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {{-- Role Name --}}
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-800 mb-2">
                                    Role Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                       class="w-full px-4 py-3 rounded-lg border-2 transition {{ $errors->has('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-purple-500 focus:ring-purple-500' }} focus:outline-none focus:ring-2"
                                       placeholder="Manager">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Unique identifier for this role</p>
                            </div>

                            {{-- Display Name --}}
                            <div>
                                <label for="display_name" class="block text-sm font-semibold text-gray-800 mb-2">
                                    Display Name
                                </label>
                                <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}"
                                       class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-purple-500 focus:outline-none focus:ring-2 transition"
                                       placeholder="Account Manager">
                                <p class="mt-2 text-sm text-gray-500">Human-readable name</p>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-semibold text-gray-800 mb-2">
                                Description
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-purple-500 focus:outline-none focus:ring-2 transition"
                                      placeholder="Describe what this role can do...">{{ old('description') }}</textarea>
                            <p class="mt-2 text-sm text-gray-500">Clear description of role responsibilities</p>
                        </div>
                    </div>

                    {{-- Permissions Section --}}
                    <div class="mt-8 pt-6 border-t-2 border-gray-100">
                        <div class="flex items-center mb-6">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.414-5.414l-4-4L8 3l4 4 5.414-5.414a2 2 0 012.828 2.828L15.828 9l4 4-1.414 1.414L14 10.414l-4 4-1.414-1.414L13 8.586 8.586 4.172a2 2 0 00-2.828 2.828z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Permissions</h3>
                        </div>

                        @if(isset($permissions) && count($permissions) > 0)
                            <div class="space-y-4">
                                @foreach($permissions as $module => $modulePermissions)
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-5 border border-gray-200">
                                        <div class="flex items-center justify-between mb-4">
                                            <h5 class="text-sm font-semibold text-gray-900 flex items-center">
                                                <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                                                {{ ucfirst(str_replace('_', ' ', $module)) }}
                                            </h5>
                                            <button type="button" onclick="toggleModulePermissions('{{ $module }}')"
                                                    class="text-xs font-medium text-purple-600 hover:text-purple-700 px-3 py-1 rounded-md hover:bg-purple-50 transition">
                                                Select All
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach($modulePermissions as $permission)
                                                <label class="flex items-center p-3 rounded-lg border-2 border-gray-200 hover:border-purple-300 cursor-pointer transition bg-white">
                                                    <input id="permission_{{ $permission->id }}"
                                                           name="permissions[]"
                                                           type="checkbox"
                                                           value="{{ $permission->id }}"
                                                           data-module="{{ $module }}"
                                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                                    <span class="ml-3 text-sm text-gray-900">
                                                        {{ $permission->display_name ?? $permission->name }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No permissions available</h3>
                                <p class="mt-1 text-sm text-gray-500">Please create some permissions first</p>
                            </div>
                        @endif
                    </div>

                    {{-- Role Settings --}}
                    <div class="mt-8 pt-6 border-t-2 border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Settings</h3>

                        <div class="space-y-4">
                            {{-- Is Active --}}
                            <label class="flex items-center p-4 rounded-lg border-2 border-gray-200 hover:border-purple-300 cursor-pointer transition">
                                <input id="is_active" name="is_active" type="checkbox" value="1" checked
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Active Role</span>
                                    <span class="block text-xs text-gray-500">Role can be assigned to users</span>
                                </span>
                            </label>

                            {{-- Is Default --}}
                            <label class="flex items-center p-4 rounded-lg border-2 border-gray-200 hover:border-purple-300 cursor-pointer transition">
                                <input id="is_default" name="is_default" type="checkbox" value="1"
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <span class="ml-3">
                                    <span class="block text-sm font-medium text-gray-900">Default Role</span>
                                    <span class="block text-xs text-gray-500">Automatically assigned to new users</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-6 py-4 bg-gray-50 border-t-2 border-gray-100 flex justify-end space-x-3">
                    <button type="button" onclick="window.history.back()"
                            class="px-6 py-2.5 rounded-lg border-2 border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-purple-600 to-purple-700 text-white font-medium hover:from-purple-700 hover:to-purple-800 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleModulePermissions(module) {
        const checkboxes = document.querySelectorAll(`input[data-module="${module}"]`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }
</script>
@endpush
