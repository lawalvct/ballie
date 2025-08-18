<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Database\Seeders\AccountGroupSeeder;
use Database\Seeders\VoucherTypeSeeder;

class SetupTenantAccounting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:setup-accounting {tenant_slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup accounting structure for a tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantSlug = $this->argument('tenant_slug');
        $tenant = Tenant::where('slug', $tenantSlug)->first();

        if (!$tenant) {
            $this->error("Tenant with slug '{$tenantSlug}' not found.");
            return 1;
        }

        $this->info("Setting up accounting structure for tenant: {$tenant->name}");

        // Seed account groups
        AccountGroupSeeder::seedForTenant($tenant->id);
        $this->info("✓ Account groups created");

        // Seed voucher types
        VoucherTypeSeeder::seedForTenant($tenant->id);
        $this->info("✓ Voucher types created");

        $this->info("✓ Accounting setup completed successfully!");

        return 0;
    }
}
