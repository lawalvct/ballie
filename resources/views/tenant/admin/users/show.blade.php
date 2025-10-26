@extends('layouts.tenant')

@section('title', 'User Details')

@section('page-title', 'User Details')
@section('page-description')
    <span class="hidden md:inline">
     View and manage user information.
    </span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('tenant.admin.users.edit', [tenant('slug'), $user->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                <i class="fas fa-edit mr-2"></i>
                Edit User
            </a>
            <a href="{{ route('tenant.admin.users.index', tenant('slug')) }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col items-center">
                        <div class="h-24 w-24 rounded-full bg-purple-100 flex items-center justify-center mb-4">
                            <span class="text-3xl font-bold text-purple-700">
                                @php
                                    $firstName = is_string($user->first_name ?? $user->name) ? ($user->first_name ?? $user->name) : 'U';
                                    $lastName = is_string($user->last_name ?? '') ? ($user->last_name ?? '') : '';
                                    $initials = strtoupper(substr($firstName, 0, 1)) . strtoupper(substr($lastName, 0, 1));
                                @endphp
                                {{ $initials ?: 'U' }}
                            </span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ is_array($user->name) ? '' : ($user->first_name ?? $user->name) }}
                            {{ is_array($user->last_name) ? '' : $user->last_name }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ is_array($user->email) ? '' : $user->email }}</p>

                        <div class="mt-4">
                            @if($user->is_active ?? true)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </div>

                        @if($user->roles && $user->roles->count() > 0)
                        <div class="mt-4 w-full">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Roles</h4>
                            <div class="space-y-2">
                                @foreach($user->roles as $role)
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-shield mr-1"></i>
                                        {{ $role->name }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
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
                            <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}
                            </dd>
                        </div>
                        @if($user->email_verified_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email Verified</dt>
                            <dd class="mt-1 text-sm text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ $user->email_verified_at->format('M d, Y') }}
                            </dd>
                        </div>
                        @else
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email Status</dt>
                            <dd class="mt-1 text-sm text-yellow-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Not Verified
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-4">Quick Actions</h4>
                    <div class="space-y-2">
                        <button onclick="resetPassword({{ $user->id }})"
                                class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-key text-blue-600 mr-3"></i>
                            Reset Password
                        </button>
                        @if($user->is_active ?? true)
                        <button onclick="deactivateUser({{ $user->id }})"
                                class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-ban text-orange-600 mr-3"></i>
                            Deactivate User
                        </button>
                        @else
                        <button onclick="activateUser({{ $user->id }})"
                                class="w-full flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            Activate User
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details & Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Permissions -->
            @if($user->roles && $user->roles->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Permissions</h3>
                    <p class="text-sm text-gray-500">Permissions granted through roles</p>
                </div>
                <div class="p-6">
                    @php
                        $allPermissions = collect();
                        if ($user->roles && $user->roles->count() > 0) {
                            $allPermissions = $user->roles->flatMap(function($role) {
                                return $role->permissions ?? collect();
                            })->unique('id');
                        }
                    @endphp

                    @if($allPermissions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($allPermissions as $permission)
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $permission->name }}</p>
                                        @if($permission->description)
                                            <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No permissions assigned</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Teams -->
            @if(isset($user->teams) && $user->teams->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Teams</h3>
                    <p class="text-sm text-gray-500">Teams this user belongs to</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($user->teams as $team)
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-users text-purple-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $team->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $team->members_count ?? 0 }} members</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Activity Log -->
            @if(isset($activityLogs) && $activityLogs->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                    <p class="text-sm text-gray-500">Latest actions by this user</p>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($activityLogs->take(10) as $log)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center ring-8 ring-white">
                                                    <i class="fas fa-circle text-purple-600" style="font-size: 8px;"></i>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-900">{{ $log->description ?? 'Activity' }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time>{{ $log->created_at->diffForHumans() }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetPassword(userId) {
    if (confirm('Are you sure you want to reset this user\'s password? They will receive an email with reset instructions.')) {
        fetch(`{{ route('tenant.admin.users.reset-password', [tenant('slug'), ':userId']) }}`.replace(':userId', userId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resetting the password.');
        });
    }
}

function activateUser(userId) {
    if (confirm('Are you sure you want to activate this user?')) {
        fetch(`{{ route('tenant.admin.users.activate', [tenant('slug'), ':userId']) }}`.replace(':userId', userId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while activating the user.');
        });
    }
}

function deactivateUser(userId) {
    if (confirm('Are you sure you want to deactivate this user? They will not be able to access the system.')) {
        fetch(`{{ route('tenant.admin.users.deactivate', [tenant('slug'), ':userId']) }}`.replace(':userId', userId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deactivating the user.');
        });
    }
}
</script>
@endpush
@endsection
