<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountGroup;

class AccountGroupSeeder extends Seeder
{
    public static function seedForTenant($tenantId)
    {
        // Check if account groups already exist for this tenant
        $existingGroups = AccountGroup::where('tenant_id', $tenantId)->count();
        if ($existingGroups > 0) {
            return; // Skip seeding if groups already exist
        }

        $defaultGroups = [
            // Assets
            [
                'name' => 'Current Assets',
                'code' => 'CA',
                'nature' => 'assets',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Fixed Assets',
                'code' => 'FA',
                'nature' => 'assets',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],

            // Liabilities
            [
                'name' => 'Current Liabilities',
                'code' => 'CL',
                'nature' => 'liabilities',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Long Term Liabilities',
                'code' => 'LTL',
                'nature' => 'liabilities',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],

            // Income
            [
                'name' => 'Direct Income',
                'code' => 'DI',
                'nature' => 'income',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Indirect Income',
                'code' => 'II',
                'nature' => 'income',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],

            // Expenses
            [
                'name' => 'Direct Expenses',
                'code' => 'DE',
                'nature' => 'expenses',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Indirect Expenses',
                'code' => 'IE',
                'nature' => 'expenses',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],

            // Capital/Equity
            [
                'name' => 'Capital Account',
                'code' => 'CAP',
                'nature' => 'equity',
                'parent_id' => null,
                'is_system_defined' => true,
                'is_active' => true,
            ],
        ];

        foreach ($defaultGroups as $group) {
            $group['tenant_id'] = $tenantId;
            $group['created_at'] = now();
            $group['updated_at'] = now();

            AccountGroup::create($group);
        }
    }

    public function run()
    {
        // This method can be used for standalone seeding if needed
        $tenantId = $this->command->option('tenant-id');

        if ($tenantId) {
            self::seedForTenant($tenantId);
            $this->command->info("Account groups seeded for tenant ID: {$tenantId}");
        } else {
            $this->command->error('Please provide --tenant-id option');
        }
    }
}
