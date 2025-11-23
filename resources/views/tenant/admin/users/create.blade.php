@extends('layouts.tenant')

@section('title', 'Create User')
@section('page-title', 'Create User')
@section('page-description')
    <span class="hidden md:inline">Add a new user to your organization with appropriate roles and permissions.</span>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('tenant.admin.users.index', tenant('slug')) }}"
               class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Users
            </a>
        </div>

        {{-- User Creation Form --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <form method="POST" action="{{ route('tenant.admin.users.store', tenant('slug')) }}">
                @csrf

                {{-- Form Header with Gradient --}}
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-8">
                    <h2 class="text-2xl font-bold text-white">Create New User</h2>
                    <p class="text-purple-100 mt-2">Add a new team member to your organization</p>
                </div>

                {{-- Form Content --}}
                <div class="px-6 py-8">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                        {{-- Personal Information Section --}}
                        <div class="sm:col-span-2">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                            </div>
                        </div>

                        {{-- First Name --}}
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-gray-800 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                   class="w-full px-4 py-3 rounded-lg border-2 transition {{ $errors->has('first_name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-purple-500 focus:ring-purple-500' }} focus:outline-none focus:ring-2"
                                   placeholder="John">
                            @error('first_name')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z" clip-rule="evenodd" /></svg>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label for="last_name" class="block text-sm font-semibold text-gray-800 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                   class="w-full px-4 py-3 rounded-lg border-2 transition {{ $errors->has('last_name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-purple-500 focus:ring-purple-500' }} focus:outline-none focus:ring-2"
                                   placeholder="Doe">
                            @error('last_name')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z" clip-rule="evenodd" /></svg>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 rounded-lg border-2 transition {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-purple-500 focus:ring-purple-500' }} focus:outline-none focus:ring-2"
                                   placeholder="john.doe@example.com">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z" clip-rule="evenodd" /></svg>{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Used for login and notifications</p>
                        </div>

                        {{-- Security Section --}}
                        <div class="sm:col-span-2 mt-8 pt-6 border-t-2 border-gray-100">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Security</h3>
                            </div>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="password" required
                                   class="w-full px-4 py-3 rounded-lg border-2 transition {{ $errors->has('password') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-purple-500 focus:ring-purple-500' }} focus:outline-none focus:ring-2"
                                   placeholder="••••••••">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z" clip-rule="evenodd" /></svg>{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Minimum 8 characters</p>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-purple-500 focus:outline-none focus:ring-2 transition"
                                   placeholder="••••••••">
                        </div>

                        {{-- Role Selection --}}
                        <div class="sm:col-span-2 mt-8 pt-6 border-t-2 border-gray-100">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">Permissions & Role</h3>
                            </div>
                        </div>

                        {{-- Role Selection --}}
                        <div class="sm:col-span-2">
                            <label for="role_id" class="block text-sm font-semibold text-gray-800 mb-2">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <select name="role_id" id="role_id" required
                                    class="w-full px-4 py-3 rounded-lg border-2 {{ $errors->has('role_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-200 focus:border-purple-500 focus:ring-purple-500' }} focus:outline-none focus:ring-2 transition">
                                <option value="">Select a role</option>
                                @if(isset($roles) && $roles->count() > 0)
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                                {{ old('role_id') == $role->id ? 'selected' : '' }}
                                                data-description="{{ $role->description }}"
                                                data-permissions-count="{{ $role->permissions->count() }}">
                                            {{ $role->name }}
                                            @if($role->is_default)
                                                (Default)
                                            @endif
                                            - {{ $role->permissions->count() }} permissions
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No roles available</option>
                                @endif
                            </select>
                            @error('role_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z" clip-rule="evenodd" /></svg>{{ $message }}</p>
                            @enderror
                            <p id="role-description" class="mt-2 text-sm text-gray-500">Choose the role that determines user permissions.</p>
                            
                            {{-- Role Permissions Preview --}}
                            <div id="role-permissions-preview" class="mt-4 hidden">
                                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                                    <h5 class="text-xs font-semibold text-green-900 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.756a1 1 0 01-.227.519l-5.519 6.921a1 1 0 01-1.588 0l-5.519-6.921a1 1 0 01-.227-.519v-6.756a3.066 3.066 0 012.812-3.062zM9 16a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" /></svg>
                                        Role Permissions
                                    </h5>
                                    <div id="permissions-list" class="text-xs text-green-700 space-y-1"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="sm:col-span-2">
                            <label for="status" class="block text-sm font-semibold text-gray-800 mb-2">
                                Account Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-purple-500 focus:ring-purple-500 focus:outline-none focus:ring-2 transition">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active - User can login immediately</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive - User cannot login</option>
                            </select>
                            <p class="mt-2 text-sm text-gray-500">Active users can login and access the system immediately.</p>
                        </div>

                        {{-- Additional Options --}}
                        <div class="sm:col-span-2 mt-8 pt-6 border-t-2 border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Options</h3>

                            <div class="space-y-4">
                                {{-- Send Welcome Email --}}
                                <label class="flex items-center p-4 rounded-lg border-2 border-gray-200 hover:border-purple-300 cursor-pointer transition">
                                    <input id="send_welcome_email" name="send_welcome_email" type="checkbox" value="1"
                                           {{ old('send_welcome_email', '1') ? 'checked' : '' }}
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Send welcome email</span>
                                        <span class="block text-xs text-gray-500">User will receive login instructions</span>
                                    </span>
                                </label>

                                {{-- Force Password Change --}}
                                <label class="flex items-center p-4 rounded-lg border-2 border-gray-200 hover:border-purple-300 cursor-pointer transition">
                                    <input id="force_password_change" name="force_password_change" type="checkbox" value="1"
                                           {{ old('force_password_change') ? 'checked' : '' }}
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <span class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Force password change</span>
                                        <span class="block text-xs text-gray-500">User must change password on first login</span>
                                    </span>
                                </label>
                            </div>
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
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const passwordField = document.querySelector('[name="password"]');
        const confirmPasswordField = document.querySelector('[name="password_confirmation"]');
        const emailField = document.querySelector('[name="email"]');
        const roleSelect = document.getElementById('role_id');
        const roleDescription = document.getElementById('role-description');
        const permissionsPreview = document.getElementById('role-permissions-preview');
        const permissionsList = document.getElementById('permissions-list');

        // Password confirmation validation
        confirmPasswordField.addEventListener('blur', function() {
            if (this.value && this.value !== passwordField.value) {
                this.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                this.classList.remove('border-gray-200', 'focus:border-purple-500', 'focus:ring-purple-500');
                let errorDiv = this.parentNode.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('p');
                    errorDiv.className = 'field-error mt-2 text-sm text-red-600 flex items-center';
                    errorDiv.innerHTML = '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z" clip-rule="evenodd" /></svg>Passwords do not match.';
                    this.parentNode.appendChild(errorDiv);
                }
            } else {
                this.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                this.classList.add('border-gray-200', 'focus:border-purple-500', 'focus:ring-purple-500');
                const errorDiv = this.parentNode.querySelector('.field-error');
                if (errorDiv) errorDiv.remove();
            }
        });

        // Email validation
        emailField.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                this.classList.remove('border-gray-200', 'focus:border-purple-500', 'focus:ring-purple-500');
                let errorDiv = this.parentNode.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('p');
                    errorDiv.className = 'field-error mt-2 text-sm text-red-600 flex items-center';
                    errorDiv.innerHTML = '<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8.1 8.1a1 1 0 001.414 0l8.1-8.1z" clip-rule="evenodd" /></svg>Please enter a valid email address.';
                    this.parentNode.appendChild(errorDiv);
                }
            } else {
                this.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                this.classList.add('border-gray-200', 'focus:border-purple-500', 'focus:ring-purple-500');
                const errorDiv = this.parentNode.querySelector('.field-error');
                if (errorDiv) errorDiv.remove();
            }
        });

        // Role selection - show description and permissions
        if (roleSelect && roleDescription) {
            roleSelect.addEventListener('change', async function() {
                const selectedOption = this.options[this.selectedIndex];
                const description = selectedOption.getAttribute('data-description');
                const roleId = this.value;

                if (description && description !== 'null') {
                    roleDescription.textContent = description;
                    roleDescription.classList.remove('text-gray-500');
                    roleDescription.classList.add('text-blue-600', 'font-medium');
                } else {
                    roleDescription.textContent = 'Choose the role that determines user permissions.';
                    roleDescription.classList.add('text-gray-500');
                    roleDescription.classList.remove('text-blue-600', 'font-medium');
                }

                if (roleId && permissionsPreview && permissionsList) {
                    try {
                        const response = await fetch(`/{{ tenant('slug') }}/admin/roles/${roleId}/permissions`);
                        const data = await response.json();
                        
                        if (data.permissions && data.permissions.length > 0) {
                            permissionsList.innerHTML = data.permissions
                                .slice(0, 10)
                                .map(p => `<div class="flex items-center"><svg class="w-3 h-3 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>${p.display_name}</div>`)
                                .join('');
                            
                            if (data.permissions.length > 10) {
                                permissionsList.innerHTML += `<div class="text-green-600 font-medium mt-2">+ ${data.permissions.length - 10} more permissions</div>`;
                            }
                            
                            permissionsPreview.classList.remove('hidden');
                        } else {
                            permissionsPreview.classList.add('hidden');
                        }
                    } catch (error) {
                        console.error('Error fetching permissions:', error);
                        permissionsPreview.classList.add('hidden');
                    }
                } else {
                    if (permissionsPreview) {
                        permissionsPreview.classList.add('hidden');
                    }
                }
            });
        }
    });
</script>
@endpush
