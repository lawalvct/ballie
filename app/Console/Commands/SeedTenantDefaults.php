<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Database\Seeders\AccountGroupSeeder;
use Database\Seeders\VoucherTypeSeeder;
use Database\Seeders\DefaultLedgerAccountsSeeder;
use Illuminate\Support\Facades\DB;

class SeedTenantDefaults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed-defaults {tenant_id?} {--all} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed default account groups, voucher types, and ledger accounts for tenant(s)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->seedForAllTenants();
        } elseif ($tenantId = $this->argument('tenant_id')) {
            $this->seedForTenant($tenantId);
        } else {
            $this->error('Please provide a tenant ID or use --all flag');
            return 1;
        }

        return 0;
    }

    private function seedForAllTenants()
    {
        $tenants = Tenant::all();
        $this->info("Seeding default data for {$tenants->count()} tenants...");

        $bar = $this->output->createProgressBar($tenants->count());
        $bar->start();

        foreach ($tenants as $tenant) {
            try {
                $this->seedTenantData($tenant->id);
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nFailed to seed data for tenant {$tenant->id}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->info("\nDefault data seeding completed!");
    }

    private function seedForTenant($tenantId)
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found");
            return;
        }

        try {
            $this->seedTenantData($tenant->id);
            $this->info("Default data seeded successfully for tenant: {$tenant->name}");
        } catch (\Exception $e) {
            $this->error("Failed to seed default data: " . $e->getMessage());
        }
    }

    private function seedTenantData($tenantId)
    {
        DB::transaction(function () use ($tenantId) {
            // Check if we should force re-seed
            if (!$this->option('force')) {
                // Check if data already exists
                $hasAccountGroups = \App\Models\AccountGroup::where('tenant_id', $tenantId)->exists();
                $hasVoucherTypes = \App\Models\VoucherType::where('tenant_id', $tenantId)->exists();
                $hasLedgerAccounts = \App\Models\LedgerAccount::where('tenant_id', $tenantId)->exists();

                if ($hasAccountGroups && $hasVoucherTypes && $hasLedgerAccounts) {
                    $this->warn("Default data already exists for tenant {$tenantId}. Use --force to re-seed.");
                    return;
                }
            }

            // Seed in order: Account Groups -> Voucher Types -> Ledger Accounts
            AccountGroupSeeder::seedForTenant($tenantId);
            VoucherTypeSeeder::seedForTenant($tenantId);
            DefaultLedgerAccountsSeeder::seedForTenant($tenantId);
        });
    }
}
