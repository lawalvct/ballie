@extends('layouts.tenant')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-description')
    <span class="hidden md:inline">
     Update user information and permissions.
    </span>
@endsection
@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
           
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tenant.admin.users.show', [tenant('slug'), $user->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Preview -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col items-center">
                        <div class="h-24 w-24 rounded-full bg-purple-100 flex items-center justify-center mb-4">
                            <span class="text-3xl font-bold text-purple-700" id="avatar-preview">
                                {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                            </span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">User ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">#{{ $user->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <form action="{{ route('tenant.admin.users.update', [tenant('slug'), $user->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Personal Information -->
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                        <p class="text-sm text-gray-500">Update the user's basic information</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Name -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(isset($user->first_name))
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('first_name') border-red-500 @enderror">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">
                                    Last Name
                                </label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('last_name') border-red-500 @enderror">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @else
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password (Optional) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    New Password <span class="text-xs text-gray-500">(Leave blank to keep current)</span>
                                </label>
                                <input type="password" name="password" id="password"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    Confirm Password
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Active User
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Inactive users cannot log in to the system</p>
                        </div>
                    </div>

                    <!-- Roles & Permissions -->
                    <div class="p-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Roles & Permissions</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Assign Roles <span class="text-red-500">*</span>
                            </label>
                            @if(isset($roles) && $roles->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($roles as $role)
                                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                            <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}"
                                                   {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                            <label for="role_{{ $role->id }}" class="ml-3 flex-1 cursor-pointer">
                                                <span class="block text-sm font-medium text-gray-900">{{ $role->name }}</span>
                                                @if($role->description)
                                                    <span class="block text-xs text-gray-500">{{ $role->description }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No roles available. Please create roles first.</p>
                            @endif
                            @error('roles')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Teams (Optional) -->
                    @if(isset($teams) && $teams->count() > 0)
                    <div class="p-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Team Membership</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Assign to Teams
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($teams as $team)
                                    <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                                        <input type="checkbox" name="teams[]" id="team_{{ $team->id }}" value="{{ $team->id }}"
                                               {{ in_array($team->id, old('teams', $user->teams->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                        <label for="team_{{ $team->id }}" class="ml-3 flex-1 cursor-pointer">
                                            <span class="block text-sm font-medium text-gray-900">{{ $team->name }}</span>
                                            @if($team->description)
                                                <span class="block text-xs text-gray-500">{{ $team->description }}</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Form Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                        <button type="button" onclick="deleteUser()" class="text-red-600 hover:text-red-800 text-sm font-medium">
                            <i class="fas fa-trash-alt mr-1"></i>
                            Delete User
                        </button>
                        <div class="flex space-x-3">
                            <a href="{{ route('tenant.admin.users.show', [tenant('slug'), $user->id]) }}"
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                                <i class="fas fa-save mr-2"></i>
                                Update User
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="delete-form" action="{{ route('tenant.admin.users.destroy', [tenant('slug'), $user->id]) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
// Update avatar preview when name changes
document.addEventListener('DOMContentLoaded', function() {
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');
    const nameInput = document.getElementById('name');
    const avatarPreview = document.getElementById('avatar-preview');

    function updateAvatar() {
        let initials = '';
        if (firstNameInput && lastNameInput) {
            const firstName = firstNameInput.value.trim();
            const lastName = lastNameInput.value.trim();
            initials = (firstName ? firstName.charAt(0) : '') + (lastName ? lastName.charAt(0) : '');
        } else if (nameInput) {
            const nameParts = nameInput.value.trim().split(' ');
            initials = nameParts[0] ? nameParts[0].charAt(0) : '';
            if (nameParts.length > 1) {
                initials += nameParts[nameParts.length - 1].charAt(0);
            }
        }
        avatarPreview.textContent = initials.toUpperCase() || 'U';
    }

    if (firstNameInput) firstNameInput.addEventListener('input', updateAvatar);
    if (lastNameInput) lastNameInput.addEventListener('input', updateAvatar);
    if (nameInput) nameInput.addEventListener('input', updateAvatar);
});

function deleteUser() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection
