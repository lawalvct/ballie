@extends('layouts.tenant')

@section('title', 'Create User')

@section('content')
    {{-- Page Header --}}
    <div class="bg-white shadow">
        <div class="px-4 sm:px-6 lg:max-w-6xl lg:mx-auto lg:px-8">
            <div class="py-6 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                        <a href="{{ route('tenant.admin.index', tenant('slug')) }}" class="flex items-center text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.350 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Admin
                        </a>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-900 font-medium">Users</span>
                    </div>

                    <h1 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Create New User
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Add a new user to your organization with appropriate roles and permissions.
                    </p>
                </div>

                <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('tenant.admin.users.index', tenant('slug')) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- User Creation Form --}}
        <div class="bg-white shadow sm:rounded-lg">
            <form method="POST" action="{{ route('tenant.admin.users.store', tenant('slug')) }}">
                @csrf

                {{-- Form Header --}}
                <div class="px-4 py-5 sm:p-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        User Information
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Fill in the details below to create a new user account.
                    </p>
                </div>

                {{-- Form Content --}}
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        {{-- Personal Information Section --}}
                        <div class="sm:col-span-2">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h4>
                        </div>

                        {{-- First Name --}}
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                       class="block w-full rounded-md shadow-sm {{ $errors->has('first_name') ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500' }}"
                                       placeholder="Enter first name">
                            </div>
                            @error('first_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                       class="block w-full rounded-md shadow-sm {{ $errors->has('last_name') ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500' }}"
                                       placeholder="Enter last name">
                            </div>
                            @error('last_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                       class="block w-full rounded-md shadow-sm {{ $errors->has('email') ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500' }}"
                                       placeholder="Enter email address">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">This will be used for login and notifications.</p>
                        </div>

                        {{-- Account Settings Section --}}
                        <div class="sm:col-span-2 mt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Account Settings</h4>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="password" name="password" id="password" required
                                       class="block w-full rounded-md shadow-sm {{ $errors->has('password') ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500' }}"
                                       placeholder="Enter password">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Minimum 8 characters required.</p>
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="block w-full rounded-md shadow-sm border-gray-300 focus:ring-purple-500 focus:border-purple-500"
                                       placeholder="Confirm password">
                            </div>
                        </div>

                        {{-- Role Selection --}}
                        <div class="sm:col-span-2">
                            <label for="role_id" class="block text-sm font-medium text-gray-700">
                                Role <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select name="role_id" id="role_id" required
                                        class="block w-full rounded-md shadow-sm {{ $errors->has('role_id') ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-purple-500 focus:border-purple-500' }}">
                                    <option value="">Select a role</option>
                                    @if(isset($roles))
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            @error('role_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">Choose the role that determines user permissions.</p>
                        </div>

                        {{-- Status --}}
                        <div class="sm:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Account Status <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <select name="status" id="status" required
                                        class="block w-full rounded-md shadow-sm border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending Activation</option>
                                </select>
                            </div>
                        </div>

                        {{-- Additional Options --}}
                        <div class="sm:col-span-2 mt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Additional Options</h4>

                            <div class="space-y-4">
                                {{-- Send Welcome Email --}}
                                <div class="flex items-center">
                                    <input id="send_welcome_email" name="send_welcome_email" type="checkbox" value="1"
                                           {{ old('send_welcome_email', '1') ? 'checked' : '' }}
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <label for="send_welcome_email" class="ml-2 block text-sm text-gray-900">
                                        Send welcome email with login instructions
                                    </label>
                                </div>

                                {{-- Force Password Change --}}
                                <div class="flex items-center">
                                    <input id="force_password_change" name="force_password_change" type="checkbox" value="1"
                                           {{ old('force_password_change') ? 'checked' : '' }}
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                    <label for="force_password_change" class="ml-2 block text-sm text-gray-900">
                                        Force password change on first login
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                    <button type="button" onclick="window.history.back()"
                            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Cancel
                    </button>
                    <button type="submit"
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
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
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const passwordField = document.querySelector('[name="password"]');
        const confirmPasswordField = document.querySelector('[name="password_confirmation"]');

        // Password confirmation validation
        confirmPasswordField.addEventListener('blur', function() {
            if (this.value && this.value !== passwordField.value) {
                this.classList.add('border-red-300', 'text-red-900', 'placeholder-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                this.classList.remove('border-gray-300', 'focus:ring-purple-500', 'focus:border-purple-500');

                let errorDiv = this.parentNode.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('p');
                    errorDiv.className = 'field-error mt-2 text-sm text-red-600';
                    this.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Passwords do not match.';
            } else {
                this.classList.remove('border-red-300', 'text-red-900', 'placeholder-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                this.classList.add('border-gray-300', 'focus:ring-purple-500', 'focus:border-purple-500');

                const errorDiv = this.parentNode.querySelector('.field-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });

        // Email validation
        const emailField = document.querySelector('[name="email"]');
        emailField.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('border-red-300', 'text-red-900', 'placeholder-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                this.classList.remove('border-gray-300', 'focus:ring-purple-500', 'focus:border-purple-500');

                let errorDiv = this.parentNode.querySelector('.field-error');
                if (!errorDiv) {
                    errorDiv = document.createElement('p');
                    errorDiv.className = 'field-error mt-2 text-sm text-red-600';
                    this.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = 'Please enter a valid email address.';
            } else {
                this.classList.remove('border-red-300', 'text-red-900', 'placeholder-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                this.classList.add('border-gray-300', 'focus:ring-purple-500', 'focus:border-purple-500');

                const errorDiv = this.parentNode.querySelector('.field-error');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });
    });
</script>
@endpush
