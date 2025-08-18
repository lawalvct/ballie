<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Services\TenantSetupService;

class TenantSeeder extends Seeder
{
    public function run()
    {
        $tenantId = $this->command->option('tenant-id');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                $setupService = new TenantSetupService();
                $setupService->createDefaultLedgerAccounts($tenant);
                $this->command->info("Default ledger accounts created for tenant: {$tenant->name}");
            }
        }
    }
}
