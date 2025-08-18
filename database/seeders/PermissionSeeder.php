<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            [
                'name' => 'view_dashboard',
                'display_name' => 'View Dashboard',
                'description' => 'Access to main dashboard',
                'module' => 'dashboard',
            ],
            [
                'name' => 'view_analytics',
                'display_name' => 'View Analytics',
                'description' => 'Access to analytics and insights',
                'module' => 'dashboard',
            ],

            // Users Management
            [
                'name' => 'view_users',
                'display_name' => 'View Users',
                'description' => 'View users list and profiles',
                'module' => 'users',
            ],
            [
                'name' => 'create_users',
                'display_name' => 'Create Users',
                'description' => 'Create new user accounts',
                'module' => 'users',
            ],
            [
                'name' => 'edit_users',
                'display_name' => 'Edit Users',
                'description' => 'Edit user information and settings',
                'module' => 'users',
            ],
            [
                'name' => 'delete_users',
                'display_name' => 'Delete Users',
                'description' => 'Delete user accounts',
                'module' => 'users',
            ],
            [
                'name' => 'manage_users',
                'display_name' => 'Manage Users',
                'description' => 'Full user management access',
                'module' => 'users',
            ],

            // Roles Management
            [
                'name' => 'view_roles',
                'display_name' => 'View Roles',
                'description' => 'View roles and their permissions',
                'module' => 'roles',
            ],
            [
                'name' => 'create_roles',
                'display_name' => 'Create Roles',
                'description' => 'Create new roles',
                'module' => 'roles',
            ],
            [
                'name' => 'edit_roles',
                'display_name' => 'Edit Roles',
                'description' => 'Edit role information and permissions',
                'module' => 'roles',
            ],
            [
                'name' => 'delete_roles',
                'display_name' => 'Delete Roles',
                'description' => 'Delete roles',
                'module' => 'roles',
            ],
            [
                'name' => 'manage_roles',
                'display_name' => 'Manage Roles',
                'description' => 'Full role management access',
                'module' => 'roles',
            ],

            // Permissions Management
            [
                'name' => 'view_permissions',
                'display_name' => 'View Permissions',
                'description' => 'View permissions list',
                'module' => 'permissions',
            ],
            [
                'name' => 'create_permissions',
                'display_name' => 'Create Permissions',
                'description' => 'Create new permissions',
                'module' => 'permissions',
            ],
            [
                'name' => 'edit_permissions',
                'display_name' => 'Edit Permissions',
                'description' => 'Edit permission information',
                'module' => 'permissions',
            ],
            [
                'name' => 'delete_permissions',
                'display_name' => 'Delete Permissions',
                'description' => 'Delete permissions',
                'module' => 'permissions',
            ],
            [
                'name' => 'manage_permissions',
                'display_name' => 'Manage Permissions',
                'description' => 'Full permission management access',
                'module' => 'permissions',
            ],

            // Admin Dashboard
            [
                'name' => 'view_admin_dashboard',
                'display_name' => 'View Admin Dashboard',
                'description' => 'Access to admin dashboard and statistics',
                'module' => 'admin',
            ],
            [
                'name' => 'view_system_info',
                'display_name' => 'View System Info',
                'description' => 'View system information and status',
                'module' => 'system',
            ],
            [
                'name' => 'view_activity_logs',
                'display_name' => 'View Activity Logs',
                'description' => 'View system activity and audit logs',
                'module' => 'audit',
            ],

            // Security
            [
                'name' => 'view_security_logs',
                'display_name' => 'View Security Logs',
                'description' => 'View security and login logs',
                'module' => 'security',
            ],
            [
                'name' => 'manage_sessions',
                'display_name' => 'Manage Sessions',
                'description' => 'Manage user sessions and access',
                'module' => 'security',
            ],
            [
                'name' => 'manage_security_settings',
                'display_name' => 'Manage Security Settings',
                'description' => 'Configure security policies and settings',
                'module' => 'security',
            ],

            // Teams Management
            [
                'name' => 'view_teams',
                'display_name' => 'View Teams',
                'description' => 'View teams and their members',
                'module' => 'teams',
            ],
            [
                'name' => 'create_teams',
                'display_name' => 'Create Teams',
                'description' => 'Create new teams',
                'module' => 'teams',
            ],
            [
                'name' => 'edit_teams',
                'display_name' => 'Edit Teams',
                'description' => 'Edit team information and members',
                'module' => 'teams',
            ],
            [
                'name' => 'delete_teams',
                'display_name' => 'Delete Teams',
                'description' => 'Delete teams',
                'module' => 'teams',
            ],
            [
                'name' => 'manage_teams',
                'display_name' => 'Manage Teams',
                'description' => 'Full team management access',
                'module' => 'teams',
            ],

            // General Permissions
            [
                'name' => 'manage_own_profile',
                'display_name' => 'Manage Own Profile',
                'description' => 'Edit own profile information',
                'module' => 'users',
            ],
            [
                'name' => 'view_notifications',
                'display_name' => 'View Notifications',
                'description' => 'View system notifications',
                'module' => 'dashboard',
            ],
            [
                'name' => 'export_data',
                'display_name' => 'Export Data',
                'description' => 'Export data to various formats',
                'module' => 'system',
            ],
            [
                'name' => 'import_data',
                'display_name' => 'Import Data',
                'description' => 'Import data from external sources',
                'module' => 'system',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                array_merge($permission, [
                    'slug' => \Illuminate\Support\Str::slug($permission['name']),
                    'guard_name' => 'web',
                    'is_active' => true,
                    'priority' => 0,
                ])
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
