<?php

namespace Database\Seeders;

use App\Models\Tenant\Permission;
use Illuminate\Database\Seeder;

/**
 * Seeds the `mobile.sync.*` permissions used by the offline sync
 * registry. Idempotent: re-runs only insert what's missing.
 */
class MobileSyncPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'Mobile Sync — Read',
                'display_name' => 'Mobile Sync — Read',
                'slug' => 'mobile.sync.read',
                'module' => 'Mobile Sync',
                'description' => 'Pull master data (customers, vendors, products, etc.) to the mobile device for offline use.',
            ],
            [
                'name' => 'Mobile Sync — Read Invoices',
                'display_name' => 'Mobile Sync — Read Invoices',
                'slug' => 'mobile.sync.read.invoices',
                'module' => 'Mobile Sync',
                'description' => 'Pull invoice and voucher data to the mobile device for offline view.',
            ],
            [
                'name' => 'Mobile Sync — Write CRM',
                'display_name' => 'Mobile Sync — Write CRM',
                'slug' => 'mobile.sync.write.crm',
                'module' => 'Mobile Sync',
                'description' => 'Push offline create/update/delete for customers and vendors.',
            ],
            [
                'name' => 'Mobile Sync — Write Inventory',
                'display_name' => 'Mobile Sync — Write Inventory',
                'slug' => 'mobile.sync.write.inventory',
                'module' => 'Mobile Sync',
                'description' => 'Push offline create/update for products, units, and product categories (descriptive fields only).',
            ],
            [
                'name' => 'Mobile Sync — Write Invoices',
                'display_name' => 'Mobile Sync — Write Invoices',
                'slug' => 'mobile.sync.write.invoices',
                'module' => 'Mobile Sync',
                'description' => 'Push offline pending sales/purchase invoices (Phase 2).',
            ],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                array_merge($perm, [
                    'guard_name' => 'web',
                    'is_active' => true,
                ])
            );
        }
    }
}
