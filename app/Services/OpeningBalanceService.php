<?php

namespace App\Services;

use App\Models\LedgerAccount;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use App\Models\AccountGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OpeningBalanceService
{
    /**
     * Create opening balance entry for a ledger account
     * This maintains double-entry bookkeeping by creating a counter-entry
     * to "Opening Balance Equity" account
     *
     * @param LedgerAccount $account The account being created with opening balance
     * @param float $amount The opening balance amount
     * @return Voucher|null The created voucher or null if amount is 0
     */
    public function createOpeningBalanceEntry(LedgerAccount $account, float $amount): ?Voucher
    {
        // Skip if no opening balance or zero balance
        if (!$amount || $amount <= 0) {
            return null;
        }

        DB::beginTransaction();
        try {
            // Get or create "Opening Balance Equity" account
            $openingBalanceEquity = $this->getOrCreateOpeningBalanceEquityAccount($account->tenant_id);

            // Get or create Opening Balance voucher type
            $voucherType = $this->getOrCreateOpeningBalanceVoucherType($account->tenant_id);

            // Determine debit and credit accounts based on account type
            [$debitAccount, $creditAccount] = $this->determineDebitCreditAccounts($account, $openingBalanceEquity);

            // Create the voucher
            $voucher = Voucher::create([
                'tenant_id' => $account->tenant_id,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => $this->generateVoucherNumber($voucherType, $account->tenant_id),
                'voucher_date' => now(),
                'reference_number' => 'OB-' . $account->code,
                'narration' => "Opening balance for {$account->name}",
                'total_amount' => $amount,
                'status' => Voucher::STATUS_POSTED, // Auto-post opening balance entries
                'posted_at' => now(),
                'posted_by' => auth()->id() ?? 1,
                'created_by' => auth()->id() ?? 1,
                'meta_data' => json_encode([
                    'is_opening_balance' => true,
                    'account_id' => $account->id,
                    'account_code' => $account->code,
                    'account_name' => $account->name,
                    'account_type' => $account->account_type,
                ]),
            ]);

            // Create voucher entries (double-entry)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $debitAccount->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'description' => "Opening balance - {$account->name}",
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $creditAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'description' => "Opening balance - {$account->name}",
            ]);

            // Update the ledger account with the voucher reference
            $account->update(['opening_balance_voucher_id' => $voucher->id]);

            // Update current balances for both accounts
            $account->updateCurrentBalance();
            $openingBalanceEquity->updateCurrentBalance();

            DB::commit();

            Log::info("Opening balance entry created", [
                'account' => $account->name,
                'account_code' => $account->code,
                'amount' => $amount,
                'voucher_id' => $voucher->id,
                'voucher_number' => $voucher->voucher_number,
                'debit_account' => $debitAccount->name,
                'credit_account' => $creditAccount->name,
            ]);

            return $voucher;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating opening balance entry: ' . $e->getMessage(), [
                'account' => $account->name,
                'amount' => $amount,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Determine which account gets debited and which gets credited
     * based on the account type
     *
     * @param LedgerAccount $account The account being created
     * @param LedgerAccount $openingBalanceEquity The opening balance equity account
     * @return array [debitAccount, creditAccount]
     */
    private function determineDebitCreditAccounts(LedgerAccount $account, LedgerAccount $openingBalanceEquity): array
    {
        switch ($account->account_type) {
            case 'asset':
            case 'expense':
                // Assets and Expenses have debit balance
                // Debit: The new account (increases asset/expense)
                // Credit: Opening Balance Equity (decreases equity)
                return [$account, $openingBalanceEquity];

            case 'liability':
            case 'equity':
            case 'income':
                // Liabilities, Equity, and Income have credit balance
                // Debit: Opening Balance Equity (decreases equity)
                // Credit: The new account (increases liability/equity/income)
                return [$openingBalanceEquity, $account];

            default:
                // Default to asset behavior
                Log::warning("Unknown account type: {$account->account_type}, defaulting to asset behavior");
                return [$account, $openingBalanceEquity];
        }
    }

    /**
     * Get or create the Opening Balance Equity account
     * This is a system account that serves as the counter-entry for all opening balances
     *
     * @param int $tenantId The tenant ID
     * @return LedgerAccount
     */
    public function getOrCreateOpeningBalanceEquityAccount(int $tenantId): LedgerAccount
    {
        $account = LedgerAccount::where('tenant_id', $tenantId)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$account) {
            // Get or create equity account group
            $equityGroup = AccountGroup::where('tenant_id', $tenantId)
                ->where('nature', 'equity')
                ->first();

            if (!$equityGroup) {
                // Create equity account group if it doesn't exist
                $equityGroup = AccountGroup::create([
                    'tenant_id' => $tenantId,
                    'name' => 'Capital Account',
                    'code' => 'CAP',
                    'nature' => 'equity',
                    'parent_id' => null,
                    'is_system_defined' => true,
                    'is_active' => true,
                ]);

                Log::info("Created equity account group", ['group_id' => $equityGroup->id]);
            }

            // Check if code already exists and generate a unique one
            $code = 'OBE-001';
            $counter = 1;
            while (LedgerAccount::where('tenant_id', $tenantId)->where('code', $code)->exists()) {
                $counter++;
                $code = 'OBE-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }

            $account = LedgerAccount::create([
                'tenant_id' => $tenantId,
                'code' => $code,
                'name' => 'Opening Balance Equity',
                'account_type' => 'equity',
                'account_group_id' => $equityGroup->id,
                'balance_type' => 'cr',
                'description' => 'System account for opening balance entries. This should be reclassified to appropriate equity accounts (Owner\'s Capital or Retained Earnings) after initial setup is complete.',
                'is_active' => true,
                'is_system_account' => true,
                'is_opening_balance_account' => true,
                'opening_balance' => 0,
                'current_balance' => 0,
            ]);

            Log::info("Created Opening Balance Equity account", [
                'account_id' => $account->id,
                'tenant_id' => $tenantId
            ]);
        }

        return $account;
    }

    /**
     * Get or create Opening Balance voucher type
     *
     * @param int $tenantId The tenant ID
     * @return VoucherType
     */
    private function getOrCreateOpeningBalanceVoucherType(int $tenantId): VoucherType
    {
        $voucherType = VoucherType::where('tenant_id', $tenantId)
            ->where('code', 'OB')
            ->first();

        if (!$voucherType) {
            $voucherType = VoucherType::create([
                'tenant_id' => $tenantId,
                'name' => 'Opening Balance',
                'code' => 'OB',
                'abbreviation' => 'OB',
                'description' => 'Opening balance entries for new accounts',
                'prefix' => 'OB',
                'is_active' => true,
                'is_system_type' => true,
                'numbering_method' => 'auto',
                'next_number' => 1,
            ]);

            Log::info("Created Opening Balance voucher type", [
                'voucher_type_id' => $voucherType->id,
                'tenant_id' => $tenantId
            ]);
        }

        return $voucherType;
    }

    /**
     * Generate unique voucher number for opening balance entry
     *
     * @param VoucherType $voucherType The voucher type
     * @param int $tenantId The tenant ID
     * @return string The generated voucher number
     */
    private function generateVoucherNumber(VoucherType $voucherType, int $tenantId): string
    {
        $lastVoucher = Voucher::where('tenant_id', $tenantId)
            ->where('voucher_type_id', $voucherType->id)
            ->latest('id')
            ->first();

        if ($lastVoucher) {
            // Extract number from last voucher and increment
            $lastNumber = (int) preg_replace('/[^0-9]/', '', substr($lastVoucher->voucher_number, -6));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $voucherType->prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Reclassify opening balance equity to proper equity accounts
     * (Owner's Capital, Retained Earnings, etc.)
     *
     * @param int $tenantId The tenant ID
     * @param int $targetAccountId The target equity account ID
     * @param float $amount The amount to reclassify
     * @param string|null $description Optional description
     * @return Voucher The reclassification voucher
     */
    public function reclassifyOpeningBalance(int $tenantId, int $targetAccountId, float $amount, ?string $description = null): Voucher
    {
        DB::beginTransaction();
        try {
            $openingBalanceEquity = LedgerAccount::where('tenant_id', $tenantId)
                ->where('is_opening_balance_account', true)
                ->firstOrFail();

            $targetAccount = LedgerAccount::findOrFail($targetAccountId);

            // Validate target account is equity type
            if ($targetAccount->account_type !== 'equity') {
                throw new \Exception("Target account must be an equity account");
            }

            // Validate amount doesn't exceed available balance
            $availableBalance = abs($openingBalanceEquity->getCurrentBalance());
            if ($amount > $availableBalance) {
                throw new \Exception("Amount exceeds available opening balance equity (Available: {$availableBalance})");
            }

            // Get Journal Voucher type
            $voucherType = VoucherType::where('tenant_id', $tenantId)
                ->where('code', 'JV')
                ->firstOrFail();

            $voucher = Voucher::create([
                'tenant_id' => $tenantId,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => $this->generateVoucherNumber($voucherType, $tenantId),
                'voucher_date' => now(),
                'reference_number' => 'OBR-' . time(),
                'narration' => $description ?? "Reclassification of opening balance equity to {$targetAccount->name}",
                'total_amount' => $amount,
                'status' => Voucher::STATUS_POSTED,
                'posted_at' => now(),
                'posted_by' => auth()->id() ?? 1,
                'created_by' => auth()->id() ?? 1,
                'meta_data' => json_encode([
                    'is_reclassification' => true,
                    'from_account' => 'Opening Balance Equity',
                    'to_account' => $targetAccount->name,
                ]),
            ]);

            // Debit Opening Balance Equity (reduce it)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'description' => "Reclassification to {$targetAccount->name}",
            ]);

            // Credit target account (Owner's Capital / Retained Earnings)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $targetAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'description' => "Reclassification from Opening Balance Equity",
            ]);

            // Update current balances
            $openingBalanceEquity->updateCurrentBalance();
            $targetAccount->updateCurrentBalance();

            DB::commit();

            Log::info("Opening balance equity reclassified", [
                'amount' => $amount,
                'from' => $openingBalanceEquity->name,
                'to' => $targetAccount->name,
                'voucher_id' => $voucher->id,
            ]);

            return $voucher;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reclassifying opening balance: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get the opening balance equity account balance for a tenant
     *
     * @param int $tenantId The tenant ID
     * @return float The current balance of opening balance equity account
     */
    public function getOpeningBalanceEquityBalance(int $tenantId): float
    {
        $account = LedgerAccount::where('tenant_id', $tenantId)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$account) {
            return 0.0;
        }

        return $account->getCurrentBalance();
    }
}
