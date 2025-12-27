@extends('layouts.super-admin')

@section('title', 'Change Email Password')
@section('page-title', 'Change Email Password')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Change Email Password</h1>
                    <p class="text-sm text-gray-600 mt-1">Update the password for this email account</p>
                </div>
                <a href="{{ route('super-admin.emails.index', ['domain' => request('domain')]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Emails
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('super-admin.emails.update-password') }}" class="p-6 space-y-6">
            @csrf

            <!-- Email Account Info (Read-only) -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Email Account</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Domain</label>
                        <input type="text" value="{{ request('domain') }}" disabled
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700">
                        <input type="hidden" name="domain" value="{{ request('domain') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Username</label>
                        <input type="text" value="{{ request('username') }}" disabled
                               class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700">
                        <input type="hidden" name="username" value="{{ request('username') }}">
                    </div>
                </div>
                <div class="mt-3 flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium">{{ request('username') }}@{{ request('domain') }}</span>
                </div>
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                    New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="new_password" id="new_password" required
                           class="w-full px-4 py-2 pr-24 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('new_password') border-red-500 @enderror">
                    <button type="button" id="togglePassword"
                            class="absolute inset-y-0 right-16 flex items-center px-2 text-gray-600 hover:text-gray-900"
                            title="Show/Hide Password">
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button type="button" id="generatePassword"
                            class="absolute inset-y-0 right-2 flex items-center px-2 text-blue-600 hover:text-blue-900"
                            title="Generate Password">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
                @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Minimum 8 characters. Click the refresh icon to generate a strong password.</p>
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm New Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Warning Notice -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800">Important Notice</h3>
                        <p class="mt-1 text-sm text-amber-700">
                            Changing the password will immediately update the email account.
                            Make sure to notify the user if needed.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('super-admin.emails.index', ['domain' => request('domain')]) }}"
                   class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg text-sm font-medium hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('new_password');
    const passwordConfirmation = document.getElementById('new_password_confirmation');
    const togglePassword = document.getElementById('togglePassword');
    const generatePasswordBtn = document.getElementById('generatePassword');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        passwordConfirmation.setAttribute('type', type);

        // Toggle eye icon
        const eyeIcon = document.getElementById('eyeIcon');
        if (type === 'text') {
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        } else {
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    });

    // Generate random password
    generatePasswordBtn.addEventListener('click', async function() {
        try {
            const response = await fetch('{{ route('super-admin.emails.generate-password') }}');
            const data = await response.json();

            if (data.password) {
                passwordInput.value = data.password;
                passwordConfirmation.value = data.password;
                passwordInput.setAttribute('type', 'text');
                passwordConfirmation.setAttribute('type', 'text');

                // Show notification
                showNotification('Password generated successfully!', 'success');
            }
        } catch (error) {
            showNotification('Failed to generate password', 'error');
        }
    });

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg text-white text-sm font-medium shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => notification.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    }
});
</script>
@endpush
@endsection
