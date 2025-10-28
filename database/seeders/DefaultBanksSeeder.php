<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\Bank;
use App\Models\AccountGroup;

class DefaultBanksSeeder extends Seeder
{
    public static function seedForTenant($tenantId)
    {
        // Check if banks already exist for this tenant
        $existingBanks = Bank::where('tenant_id', $tenantId)->count();
        if ($existingBanks > 0) {
            Log::info("Banks already exist for tenant, skipping seeding", [
                'tenant_id' => $tenantId,
                'existing_count' => $existingBanks
            ]);
            return; // Skip seeding if banks already exist
        }

        Log::info("Starting bank seeding for tenant", [
            'tenant_id' => $tenantId
        ]);

        try {
            // Verify "Current Assets" account group exists
            $currentAssetsGroup = AccountGroup::where('tenant_id', $tenantId)
                ->where('name', 'Current Assets')
                ->first();

            if (!$currentAssetsGroup) {
                Log::error("Current Assets account group not found for tenant", [
                    'tenant_id' => $tenantId
                ]);
                throw new \Exception("Current Assets account group must be seeded before creating banks");
            }

            Log::info("Current Assets account group found", [
                'tenant_id' => $tenantId,
                'account_group_id' => $currentAssetsGroup->id
            ]);

            // Create a placeholder bank account
            // The Bank model's boot() method will automatically create
            // the linked ledger account in the "Current Assets" group
            $bank = Bank::create([
                'tenant_id' => $tenantId,
                'bank_name' => 'Primary Bank Account',
                'account_name' => 'Business Current Account',
                'account_number' => '1234567890',
                'account_type' => 'current',
                'currency' => 'NGN',
                'branch_name' => 'To be updated',
                'branch_code' => 'N/A',
                'swift_code' => null,
                'iban' => null,
                'routing_number' => null,
                'sort_code' => null,
                'current_balance' => 0.00,
                'opening_balance' => 0.00,
                'minimum_balance' => 0.00,
                'overdraft_limit' => 0.00,
                'account_opening_date' => now()->startOfMonth(),
                'last_reconciliation_date' => null,
                'last_reconciled_balance' => null,
                'is_primary' => true,
                'is_active' => true,
                'online_banking_url' => null,
                'monthly_maintenance_fee' => 0.00,
                'description' => 'Default bank account created during onboarding. Please update with your actual bank details in the Banking module.',
                'notes' => 'This is a placeholder account. Navigate to Banking > Banks to update with your real bank information.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Default bank created successfully", [
                'tenant_id' => $tenantId,
                'bank_id' => $bank->id,
                'bank_name' => $bank->bank_name,
                'ledger_account_id' => $bank->ledger_account_id,
                'ledger_account_created' => !is_null($bank->ledgerAccount)
            ]);

            // Verify that the ledger account was auto-created
            if ($bank->ledgerAccount) {
                Log::info("Ledger account auto-created for bank", [
                    'tenant_id' => $tenantId,
                    'bank_id' => $bank->id,
                    'ledger_account_id' => $bank->ledger_account_id,
                    'ledger_account_name' => $bank->ledgerAccount->name,
                    'ledger_account_code' => $bank->ledgerAccount->code,
                    'account_group_id' => $bank->ledgerAccount->account_group_id,
                    'account_group_name' => $bank->ledgerAccount->accountGroup?->name
                ]);
            } else {
                Log::warning("Ledger account was NOT auto-created for bank", [
                    'tenant_id' => $tenantId,
                    'bank_id' => $bank->id,
                    'current_assets_group_id' => $currentAssetsGroup->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to create default bank", [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function run()
    {
        // This method can be used for standalone seeding if needed
        $tenantId = $this->command->option('tenant-id');

        if ($tenantId) {
            self::seedForTenant($tenantId);
            $this->command->info("Default bank seeded for tenant ID: {$tenantId}");
        } else {
            $this->command->error('Please provide --tenant-id option');
        }
    }
}
