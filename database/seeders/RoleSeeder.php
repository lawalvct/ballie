<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant\Role;
use App\Models\Tenant\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define default roles for tenant onboarding
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'color' => '#dc2626', // Red
                'priority' => 100,
                'is_default' => true,
                'permissions' => 'all', // Special case - all permissions
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrative access to most system features',
                'color' => '#7c3aed', // Purple
                'priority' => 90,
                'permissions' => [
                    'view_dashboard', 'view_analytics', 'view_admin_dashboard',
                    'view_users', 'create_users', 'edit_users', 'manage_users',
                    'view_roles', 'create_roles', 'edit_roles', 'manage_roles',
                    'view_permissions',
                    'view_teams', 'create_teams', 'edit_teams', 'manage_teams',
                    'view_reports', 'create_reports', 'export_reports',
                    'view_settings', 'manage_settings',
                    'view_activity_logs', 'view_security_logs',
                    'manage_own_profile', 'view_notifications',
                ],
            ],
            [
                'name' => 'Manager',
                'description' => 'Management access to business operations',
                'color' => '#059669', // Green
                'priority' => 80,
                'permissions' => [
                    'view_dashboard', 'view_analytics',
                    'view_users', 'create_users', 'edit_users',
                    'view_teams', 'create_teams', 'edit_teams',
                    'view_inventory', 'manage_inventory',
                    'view_products', 'manage_products',
                    'view_customers', 'manage_customers',
                    'view_vendors', 'manage_vendors',
                    'view_invoices', 'create_invoices', 'edit_invoices',
                    'view_payments', 'process_payments',
                    'view_reports', 'create_reports', 'export_reports',
                    'view_crm', 'manage_crm',
                    'manage_own_profile', 'view_notifications',
                ],
            ],
            [
                'name' => 'Accountant',
                'description' => 'Access to financial and accounting features',
                'color' => '#2563eb', // Blue
                'priority' => 70,
                'permissions' => [
                    'view_dashboard',
                    'view_accounting', 'manage_accounting',
                    'view_invoices', 'create_invoices', 'edit_invoices', 'manage_invoices',
                    'view_payments', 'process_payments', 'manage_payments',
                    'view_customers', 'edit_customers',
                    'view_vendors', 'edit_vendors',
                    'view_reports', 'create_reports', 'export_reports',
                    'view_payroll', 'manage_payroll',
                    'manage_own_profile', 'view_notifications',
                ],
            ],
            [
                'name' => 'Sales Representative',
                'description' => 'Access to sales and customer management features',
                'color' => '#ea580c', // Orange
                'priority' => 60,
                'permissions' => [
                    'view_dashboard',
                    'view_customers', 'create_customers', 'edit_customers', 'manage_customers',
                    'view_products',
                    'view_invoices', 'create_invoices',
                    'access_pos', 'manage_pos',
                    'view_crm', 'manage_crm',
                    'view_reports',
                    'manage_own_profile', 'view_notifications',
                ],
            ],
            [
                'name' => 'Inventory Manager',
                'description' => 'Access to inventory and product management',
                'color' => '#0891b2', // Cyan
                'priority' => 50,
                'permissions' => [
                    'view_dashboard',
                    'view_inventory', 'manage_inventory',
                    'view_products', 'manage_products',
                    'view_vendors', 'edit_vendors',
                    'view_reports',
                    'manage_own_profile', 'view_notifications',
                ],
            ],
            [
                'name' => 'Employee',
                'description' => 'Basic access for regular employees',
                'color' => '#64748b', // Gray
                'priority' => 30,
                'permissions' => [
                    'view_dashboard',
                    'view_customers',
                    'view_products',
                    'view_reports',
                    'manage_own_profile', 'view_notifications',
                ],
            ],
            [
                'name' => 'Guest',
                'description' => 'Limited read-only access for guests',
                'color' => '#94a3b8', // Light Gray
                'priority' => 10,
                'permissions' => [
                    'view_dashboard',
                    'manage_own_profile', 'view_notifications',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $permissions = $roleData['permissions'];
            unset($roleData['permissions']);

            // Create the role
            $role = Role::firstOrCreate(
                ['name' => $roleData['name'], 'tenant_id' => 'default'], // Use 'default' for seed data
                array_merge($roleData, [
                    'slug' => \Illuminate\Support\Str::slug($roleData['name']),
                    'tenant_id' => 'default',
                    'is_active' => true,
                ])
            );

            // Assign permissions
            if ($permissions === 'all') {
                // Assign all permissions
                $allPermissions = Permission::all();
                $role->permissions()->sync($allPermissions->pluck('id')->toArray());
            } else {
                // Assign specific permissions
                $permissionIds = Permission::whereIn('name', $permissions)->pluck('id')->toArray();
                $role->permissions()->sync($permissionIds);
            }
        }

        $this->command->info('Roles seeded successfully!');
    }
}
