@extends('layouts.tenant')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Management')
@section('page-description')
    <span class="hidden md:inline">
        Oversee users, roles, permissions, and ensure secure administration of your organization’s platform.
    </span>
@endsection
@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div class="flex space-x-3">
            <a href="{{ route('tenant.admin.users.index', tenant('slug')) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-users mr-2"></i>
                Manage Users
            </a>
            <a href="{{ route('tenant.admin.roles.index', tenant('slug')) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-user-shield mr-2"></i>
                Manage Roles
            </a>
        </div>
        <div>
            <a href="{{ route('tenant.admin.users.create', tenant('slug')) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-plus mr-2"></i>
                Add User
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-600 font-medium">{{ $stats['active_users'] ?? 0 }}</span>
                    <span class="text-gray-500 ml-2">active users</span>
                </div>
            </div>
        </div>

        <!-- Roles -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-user-shield text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Roles</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_roles'] ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-purple-600 font-medium">{{ $stats['total_permissions'] ?? 0 }}</span>
                    <span class="text-gray-500 ml-2">permissions</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Recent Logins</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['recent_logins'] ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-green-600 font-medium">{{ $stats['recent_users'] ?? 0 }}</span>
                    <span class="text-gray-500 ml-2">new this week</span>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                        <i class="fas fa-shield-alt text-red-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Failed Logins</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['failed_logins_today'] ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center text-sm">
                    <span class="text-red-600 font-medium">{{ $stats['active_sessions'] ?? 0 }}</span>
                    <span class="text-gray-500 ml-2">active sessions</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Growth Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User Growth</h3>
                <p class="text-sm text-gray-500">User registrations over the last 7 days</p>
            </div>
            <div class="p-6">
                <canvas id="userGrowthChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Role Distribution -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Role Distribution</h3>
                <p class="text-sm text-gray-500">Users by role assignment</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @if(isset($stats['role_distribution']) && is_array($stats['role_distribution']))
                        @foreach($stats['role_distribution'] as $role)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $role['color'] ?? '#6366f1' }}"></div>
                                    <span class="text-sm font-medium text-gray-900">{{ $role['name'] }}</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $role['count'] }} users</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">No role data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                <p class="text-sm text-gray-500">Latest system activity and user actions</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @if(isset($stats['activity_summary']))
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                            <span class="text-sm text-gray-900">{{ $stats['activity_summary']['user_registrations'] ?? 0 }} new user registrations</span>
                            <span class="text-xs text-gray-500">Today</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                            <span class="text-sm text-gray-900">{{ $stats['activity_summary']['role_assignments'] ?? 0 }} role assignments</span>
                            <span class="text-xs text-gray-500">Today</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                            <span class="text-sm text-gray-900">{{ $stats['activity_summary']['permission_changes'] ?? 0 }} permission changes</span>
                            <span class="text-xs text-gray-500">Today</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-orange-400 rounded-full"></div>
                            <span class="text-sm text-gray-900">{{ $stats['activity_summary']['login_attempts'] ?? 0 }} login attempts</span>
                            <span class="text-xs text-gray-500">Today</span>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No recent activity</p>
                    @endif
                </div>
                <div class="mt-6">
                    <a href="{{ route('tenant.admin.activity.index', tenant('slug')) }}" class="text-sm font-medium text-purple-600 hover:text-purple-500">
                        View all activity
                        <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                <p class="text-sm text-gray-500">Common administrative tasks</p>
            </div>
            <div class="p-6 space-y-4">
                <a href="{{ route('tenant.admin.users.create', tenant('slug')) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                    Add New User
                </a>
                <a href="{{ route('tenant.admin.roles.create', tenant('slug')) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-plus-circle text-purple-600 mr-3"></i>
                    Create Role
                </a>
                <a href="{{ route('tenant.admin.security.index', tenant('slug')) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-shield-alt text-green-600 mr-3"></i>
                    Security Settings
                </a>
                <a href="{{ route('tenant.admin.system.info', tenant('slug')) }}" class="flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-cog text-gray-600 mr-3"></i>
                    System Info
                </a>
                <button onclick="syncPermissions()" class="w-full flex items-center p-3 text-sm font-medium text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-sync-alt text-orange-600 mr-3"></i>
                    Sync Permissions
                </button>
            </div>
        </div>
    </div>

    <!-- Users List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Users</h3>
                <p class="text-sm text-gray-500">Recent users in your organization</p>
            </div>
            <a href="{{ route('tenant.admin.users.index', tenant('slug')) }}" class="text-sm font-medium text-purple-600 hover:text-purple-500">
                View all users
                <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users ?? [] as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-purple-700">
                                                {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->roles && $user->roles->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $user->roles->first()->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        No Role
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active ?? true)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle text-green-400 mr-1" style="font-size: 6px;"></i>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-circle text-gray-400 mr-1" style="font-size: 6px;"></i>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('tenant.admin.users.show', [tenant('slug'), $user->id]) }}"
                                       class="text-gray-600 hover:text-gray-900"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tenant.admin.users.edit', [tenant('slug'), $user->id]) }}"
                                       class="text-purple-600 hover:text-purple-900"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-center">
                                    <i class="fas fa-users text-gray-400 text-4xl mb-3"></i>
                                    <h3 class="text-sm font-medium text-gray-900">No users found</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first user.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('tenant.admin.users.create', tenant('slug')) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                            <i class="fas fa-plus mr-2"></i>
                                            Create User
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Permission Usage Summary -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">System Overview</h3>
            <p class="text-sm text-gray-500">Current system status and usage metrics</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Permission Usage -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Permission Usage</h4>
                    @if(isset($stats['permission_usage']))
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Permissions</span>
                                <span class="font-medium">{{ $stats['permission_usage']['total'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Assigned</span>
                                <span class="font-medium text-green-600">{{ $stats['permission_usage']['assigned'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Unassigned</span>
                                <span class="font-medium text-gray-500">{{ $stats['permission_usage']['unassigned'] ?? 0 }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $stats['permission_usage']['usage_percentage'] ?? 0 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500">{{ $stats['permission_usage']['usage_percentage'] ?? 0 }}% utilized</p>
                        </div>
                    @endif
                </div>

                <!-- User Status -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">User Status</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Active Users</span>
                            <span class="font-medium text-green-600">{{ $stats['active_users'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Inactive Users</span>
                            <span class="font-medium text-gray-500">{{ ($stats['total_users'] ?? 0) - ($stats['active_users'] ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">New This Week</span>
                            <span class="font-medium text-blue-600">{{ $stats['recent_users'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Security Summary -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Security Status</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Active Sessions</span>
                            <span class="font-medium">{{ $stats['active_sessions'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Failed Logins</span>
                            <span class="font-medium text-red-600">{{ $stats['failed_logins_today'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Recent Logins</span>
                            <span class="font-medium text-green-600">{{ $stats['recent_logins'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    const userGrowthData = @json($stats['user_growth'] ?? []);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: userGrowthData.map(item => item.date),
            datasets: [{
                label: 'New Users',
                data: userGrowthData.map(item => item.count),
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});

// Sync Permissions Function
function syncPermissions() {
    if (confirm('This will sync all default permissions. Continue?')) {
        fetch('{{ route("tenant.admin.permissions.sync", tenant("slug")) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            alert('An error occurred while syncing permissions.');
        });
    }
}
</script>
@endpush
@endsection
