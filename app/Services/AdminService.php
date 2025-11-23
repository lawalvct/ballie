<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tenant\Role;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Exception;

class AdminService
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $tenantId = tenant()->id;

        return [
            'total_users' => User::where('tenant_id', $tenantId)->count(),
            'active_users' => User::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'total_roles' => Role::where('tenant_id', $tenantId)->count(),
            'total_permissions' => Permission::count(),
            'recent_users' => User::where('tenant_id', $tenantId)
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'recent_logins' => $this->getRecentLoginsCount(),
            'failed_logins_today' => $this->getFailedLoginsToday(),
            'active_sessions' => $this->getActiveSessionsCount(),
            'user_growth' => $this->getUserGrowthData(),
            'role_distribution' => $this->getRoleDistribution(),
            'permission_usage' => $this->getPermissionUsage(),
            'activity_summary' => $this->getActivitySummary(),
        ];
    }

    /**
     * Get recent logins count
     */
    private function getRecentLoginsCount()
    {
        return User::where('tenant_id', tenant()->id)
            ->where('last_login_at', '>=', now()->subHours(24))
            ->count();
    }

    /**
     * Get failed logins today
     */
    private function getFailedLoginsToday()
    {
        // Placeholder until failed_login_attempts table is implemented
        return 0;
    }

    /**
     * Get active sessions count
     */
    private function getActiveSessionsCount()
    {
        // Check if using database sessions
        if (config('session.driver') === 'database') {
            return DB::table('sessions')
                ->where('user_id', '!=', null)
                ->where('last_activity', '>=', now()->subMinutes(30)->timestamp)
                ->count();
        }

        // For file-based sessions, count users active in last 30 minutes
        return User::where('tenant_id', tenant()->id)
            ->where('last_login_at', '>=', now()->subMinutes(30))
            ->count();
    }

    /**
     * Get user growth data for charts
     */
    private function getUserGrowthData()
    {
        $tenantId = tenant()->id;
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::where('tenant_id', $tenantId)
                ->whereDate('created_at', $date)
                ->count();

            $data[] = [
                'date' => $date->format('M d'),
                'count' => $count
            ];
        }

        return $data;
    }

    /**
     * Get role distribution
     */
    private function getRoleDistribution()
    {
        $tenantId = tenant()->id;

        return Role::where('tenant_id', $tenantId)
            ->withCount('users')
            ->get()
            ->map(function ($role) {
                return [
                    'name' => $role->name,
                    'count' => $role->users_count,
                    'color' => $role->color,
                ];
            })
            ->toArray();
    }

    /**
     * Get permission usage statistics
     */
    private function getPermissionUsage()
    {
        $totalPermissions = Permission::count();
        $assignedPermissions = Permission::whereHas('roles')->distinct()->count();

        return [
            'total' => $totalPermissions,
            'assigned' => $assignedPermissions,
            'unassigned' => $totalPermissions - $assignedPermissions,
            'usage_percentage' => $totalPermissions > 0 ? round(($assignedPermissions / $totalPermissions) * 100, 1) : 0,
        ];
    }

    /**
     * Get activity summary
     */
    private function getActivitySummary()
    {
        $tenantId = tenant()->id;
        
        return [
            'user_registrations' => User::where('tenant_id', $tenantId)
                ->whereDate('created_at', today())
                ->count(),
            'role_assignments' => DB::table('role_user')
                ->join('users', 'role_user.user_id', '=', 'users.id')
                ->where('users.tenant_id', $tenantId)
                ->whereDate('role_user.created_at', today())
                ->count(),
            'permission_changes' => 0, // Placeholder until activity logging is implemented
            'login_attempts' => User::where('tenant_id', $tenantId)
                ->whereDate('last_login_at', today())
                ->count(),
        ];
    }

    /**
     * Send user invitation
     */
    public function sendUserInvitation(User $user, $password = null)
    {
        try {
            $invitationToken = Str::random(60);

            // Store invitation token (you might want to create an invitations table)
            Cache::put("user_invitation_{$user->id}", $invitationToken, now()->addDays(7));

            // Send invitation email
            Mail::send('emails.user-invitation', [
                'user' => $user,
                'password' => $password,
                'invitation_url' => route('invitation.accept', ['token' => $invitationToken]),
                'tenant' => tenant(),
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Welcome to ' . tenant('name'));
            });

            Log::info('User invitation sent', ['user_id' => $user->id, 'email' => $user->email]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send user invitation', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send password reset notification
     */
    public function sendPasswordReset(User $user, $newPassword)
    {
        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'password' => $newPassword,
                'tenant' => tenant(),
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Password Reset - ' . tenant('name'));
            });

            Log::info('Password reset email sent', ['user_id' => $user->id]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send password reset email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get user activity logs
     */
    public function getUserActivityLogs($userId, $limit = 50)
    {
        // This would typically come from an activity_logs table
        // For now, return placeholder data as a collection
        $activityData = [
            (object) [
                'id' => 1,
                'action' => 'login',
                'description' => 'User logged in',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0...',
                'created_at' => now()->subHours(1),
            ],
            (object) [
                'id' => 2,
                'action' => 'profile_update',
                'description' => 'Updated profile information',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0...',
                'created_at' => now()->subHours(2),
            ],
            (object) [
                'id' => 3,
                'action' => 'settings_update',
                'description' => 'Updated account settings',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0...',
                'created_at' => now()->subHours(4),
            ],
        ];

        return collect($activityData)->take($limit);
    }

    /**
     * Export users to CSV
     */
    public function exportUsers($filters = [])
    {
        $query = User::with(['roles'])
            ->where('tenant_id', tenant()->id);

        // Apply filters
        if (!empty($filters['role'])) {
            $query->whereHas('roles', function($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        $users = $query->get();

        $filename = 'users_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $path = 'exports/' . $filename;

        $csvData = "Name,Email,Roles,Status,Created At\n";

        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->implode(', ');
            $status = $user->is_active ? 'Active' : 'Inactive';

            $csvData .= '"' . $user->name . '","' . $user->email . '","' . $roles . '","' . $status . '","' . $user->created_at->format('Y-m-d H:i:s') . '"' . "\n";
        }

        Storage::put($path, $csvData);

        return Storage::download($path, $filename);
    }

    /**
     * Bulk user actions
     */
    public function bulkUserAction($action, $userIds)
    {
        $tenantId = tenant()->id;
        $currentUserId = auth()->id();

        // Remove current user from bulk actions
        $userIds = array_filter($userIds, function($id) use ($currentUserId) {
            return $id != $currentUserId;
        });

        $users = User::where('tenant_id', $tenantId)
            ->whereIn('id', $userIds)
            ->get();

        $processed = 0;

        try {
            DB::beginTransaction();

            foreach ($users as $user) {
                switch ($action) {
                    case 'activate':
                        $user->update(['is_active' => true]);
                        $processed++;
                        break;

                    case 'deactivate':
                        $user->update(['is_active' => false]);
                        $processed++;
                        break;

                    case 'delete':
                        $user->delete();
                        $processed++;
                        break;
                }
            }

            DB::commit();

            $actionText = [
                'activate' => 'activated',
                'deactivate' => 'deactivated',
                'delete' => 'deleted'
            ];

            return [
                'processed' => $processed,
                'message' => "Successfully {$actionText[$action]} {$processed} user(s)."
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Sync default permissions
     */
    public function syncDefaultPermissions()
    {
        $defaultPermissions = $this->getDefaultPermissions();
        $created = 0;

        foreach ($defaultPermissions as $permissionData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );

            if ($permission->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }

    /**
     * Get default permissions array
     */
    private function getDefaultPermissions()
    {
        return [
            // Dashboard
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'module' => 'dashboard', 'description' => 'Access to main dashboard'],

            // Users Management
            ['name' => 'view_users', 'display_name' => 'View Users', 'module' => 'users', 'description' => 'View users list'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'module' => 'users', 'description' => 'Create new users'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'module' => 'users', 'description' => 'Edit user information'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'module' => 'users', 'description' => 'Delete users'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'module' => 'users', 'description' => 'Full user management access'],

            // Roles Management
            ['name' => 'view_roles', 'display_name' => 'View Roles', 'module' => 'roles', 'description' => 'View roles list'],
            ['name' => 'create_roles', 'display_name' => 'Create Roles', 'module' => 'roles', 'description' => 'Create new roles'],
            ['name' => 'edit_roles', 'display_name' => 'Edit Roles', 'module' => 'roles', 'description' => 'Edit role information'],
            ['name' => 'delete_roles', 'display_name' => 'Delete Roles', 'module' => 'roles', 'description' => 'Delete roles'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'module' => 'roles', 'description' => 'Full role management access'],

            // Permissions Management
            ['name' => 'view_permissions', 'display_name' => 'View Permissions', 'module' => 'permissions', 'description' => 'View permissions list'],
            ['name' => 'create_permissions', 'display_name' => 'Create Permissions', 'module' => 'permissions', 'description' => 'Create new permissions'],
            ['name' => 'edit_permissions', 'display_name' => 'Edit Permissions', 'module' => 'permissions', 'description' => 'Edit permission information'],
            ['name' => 'delete_permissions', 'display_name' => 'Delete Permissions', 'module' => 'permissions', 'description' => 'Delete permissions'],
            ['name' => 'manage_permissions', 'display_name' => 'Manage Permissions', 'module' => 'permissions', 'description' => 'Full permission management access'],

            // Admin Dashboard
            ['name' => 'view_admin_dashboard', 'display_name' => 'View Admin Dashboard', 'module' => 'admin', 'description' => 'Access to admin dashboard'],
            ['name' => 'view_system_info', 'display_name' => 'View System Info', 'module' => 'system', 'description' => 'View system information'],
            ['name' => 'view_activity_logs', 'display_name' => 'View Activity Logs', 'module' => 'audit', 'description' => 'View system activity logs'],

            // Security
            ['name' => 'view_security_logs', 'display_name' => 'View Security Logs', 'module' => 'security', 'description' => 'View security and login logs'],
            ['name' => 'manage_sessions', 'display_name' => 'Manage Sessions', 'module' => 'security', 'description' => 'Manage user sessions'],

            // General
            ['name' => 'manage_own_profile', 'display_name' => 'Manage Own Profile', 'module' => 'users', 'description' => 'Edit own profile information'],
            ['name' => 'view_notifications', 'display_name' => 'View Notifications', 'module' => 'dashboard', 'description' => 'View notifications'],
        ];
    }

    /**
     * Get security statistics
     */
    public function getSecurityStats()
    {
        return [
            'total_logins_today' => rand(10, 100),
            'failed_logins_today' => rand(0, 5),
            'active_sessions' => $this->getActiveSessionsCount(),
            'unique_ips_today' => rand(5, 30),
            'locked_accounts' => rand(0, 2),
            'recent_login_attempts' => $this->getRecentLoginAttempts(),
            'security_alerts' => $this->getSecurityAlerts(),
        ];
    }

    /**
     * Get active sessions
     */
    public function getActiveSessions()
    {
        if (config('session.driver') === 'database') {
            // Get from sessions table with user information
            return DB::table('sessions')
                ->join('users', 'sessions.user_id', '=', 'users.id')
                ->where('users.tenant_id', tenant()->id)
                ->where('sessions.last_activity', '>=', now()->subMinutes(30)->timestamp)
                ->select([
                    'sessions.id',
                    'sessions.user_id',
                    'users.name as user_name',
                    'sessions.ip_address',
                    'sessions.user_agent',
                    'sessions.last_activity'
                ])
                ->orderBy('sessions.last_activity', 'desc')
                ->get()
                ->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'user_id' => $session->user_id,
                        'user_name' => $session->user_name,
                        'ip_address' => $session->ip_address,
                        'user_agent' => $session->user_agent,
                        'last_activity' => Carbon::createFromTimestamp($session->last_activity),
                        'location' => 'Unknown', // Could integrate with IP geolocation service
                    ];
                });
        }

        // For file-based sessions, return placeholder data or recent users
        return User::where('tenant_id', tenant()->id)
            ->where('updated_at', '>=', now()->subMinutes(30))
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => 'file_session_' . $user->id,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'ip_address' => request()->ip() ?? 'Unknown',
                    'user_agent' => 'File-based session',
                    'last_activity' => $user->updated_at,
                    'location' => 'Unknown',
                ];
            });
    }

    /**
     * Terminate session
     */
    public function terminateSession($sessionId)
    {
        // Only delete from sessions table if using database sessions
        if (config('session.driver') === 'database') {
            DB::table('sessions')->where('id', $sessionId)->delete();
        }

        Log::info('Session terminated', ['session_id' => $sessionId]);
    }

    /**
     * Get login logs
     */
    public function getLoginLogs($filters = [])
    {
        // This would typically come from login_logs table
        return collect([
            [
                'user_name' => 'John Doe',
                'email' => 'john@example.com',
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0...',
                'status' => 'success',
                'created_at' => now()->subHours(1),
            ],
        ])->paginate(15);
    }

    /**
     * Get failed logins
     */
    public function getFailedLogins($filters = [])
    {
        // This would typically come from failed_login_attempts table
        return collect([
            [
                'email' => 'invalid@example.com',
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0...',
                'reason' => 'Invalid credentials',
                'created_at' => now()->subHours(2),
            ],
        ])->paginate(15);
    }

    /**
     * Unlock user account
     */
    public function unlockUser(User $user)
    {
        // This would typically reset failed login attempts
        $user->update(['is_locked' => false, 'locked_until' => null]);
        Log::info('User account unlocked', ['user_id' => $user->id]);
    }

    /**
     * Get activity logs
     */
    public function getActivityLogs($filters = [])
    {
        // This would typically come from activity_logs table
        return collect([
            [
                'user_name' => 'John Doe',
                'action' => 'user.created',
                'description' => 'Created new user: Jane Smith',
                'ip_address' => '192.168.1.1',
                'created_at' => now()->subHours(1),
            ],
        ])->paginate(15);
    }

    /**
     * Get system information
     */
    public function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_type' => DB::connection()->getDriverName(),
            'server_os' => PHP_OS,
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'timezone' => config('app.timezone'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

    /**
     * Get recent login attempts
     */
    private function getRecentLoginAttempts()
    {
        return collect([
            ['time' => now()->subMinutes(5), 'status' => 'success', 'user' => 'john@example.com'],
            ['time' => now()->subMinutes(10), 'status' => 'failed', 'user' => 'invalid@example.com'],
        ]);
    }

    /**
     * Get security alerts
     */
    private function getSecurityAlerts()
    {
        return collect([
            [
                'type' => 'multiple_failed_logins',
                'message' => 'Multiple failed login attempts detected',
                'severity' => 'medium',
                'created_at' => now()->subHours(1),
            ],
        ]);
    }
}
