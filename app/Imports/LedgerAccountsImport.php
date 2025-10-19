<?php

namespace App\Imports;

use App\Models\LedgerAccount;
use App\Models\AccountGroup;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class LedgerAccountsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected $errors = [];
    protected $successCount = 0;
    protected $failedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index

            try {
                DB::beginTransaction();

                // Validate required fields
                $name = trim($row['name'] ?? '');
                if (empty($name)) {
                    throw new \Exception("Account name is required.");
                }

                $code = trim($row['code'] ?? '');
                if (empty($code)) {
                    throw new \Exception("Account code is required.");
                }

                // Check for duplicate code within tenant
                $existingAccount = LedgerAccount::where('tenant_id', tenant()->id)
                    ->where('code', $code)
                    ->first();

                if ($existingAccount) {
                    throw new \Exception("A ledger account with code '{$code}' already exists.");
                }

                // Validate account type
                $accountType = strtolower(trim($row['account_type'] ?? ''));
                if (!in_array($accountType, ['asset', 'liability', 'income', 'expense', 'equity'])) {
                    throw new \Exception("Invalid account type. Must be one of: asset, liability, income, expense, equity.");
                }

                // Find account group
                $accountGroupName = trim($row['account_group'] ?? '');
                if (empty($accountGroupName)) {
                    throw new \Exception("Account group is required.");
                }

                $accountGroup = AccountGroup::where('tenant_id', tenant()->id)
                    ->where('name', $accountGroupName)
                    ->first();

                if (!$accountGroup) {
                    throw new \Exception("Account group '{$accountGroupName}' not found.");
                }

                // Validate balance type
                $balanceType = strtolower(trim($row['balance_type'] ?? 'dr'));
                if (!in_array($balanceType, ['dr', 'cr'])) {
                    $balanceType = 'dr';
                }

                // Handle parent account if specified
                $parentId = null;
                $parentCode = trim($row['parent_code'] ?? '');
                if (!empty($parentCode)) {
                    $parentAccount = LedgerAccount::where('tenant_id', tenant()->id)
                        ->where('code', $parentCode)
                        ->first();

                    if (!$parentAccount) {
                        throw new \Exception("Parent account with code '{$parentCode}' not found.");
                    }
                    $parentId = $parentAccount->id;
                }

                // Parse opening balance
                $openingBalance = floatval($row['opening_balance'] ?? 0);

                // Create ledger account
                $ledgerAccount = LedgerAccount::create([
                    'tenant_id' => tenant()->id,
                    'name' => $name,
                    'code' => $code,
                    'account_group_id' => $accountGroup->id,
                    'account_type' => $accountType,
                    'description' => trim($row['description'] ?? ''),
                    'parent_id' => $parentId,
                    'opening_balance' => $openingBalance,
                    'current_balance' => $openingBalance,
                    'balance_type' => $balanceType,
                    'address' => trim($row['address'] ?? ''),
                    'phone' => trim($row['phone'] ?? ''),
                    'email' => trim($row['email'] ?? ''),
                    'is_active' => strtolower(trim($row['is_active'] ?? 'yes')) === 'yes',
                    'is_system_account' => false,
                ]);

                // Create opening balance voucher if amount is specified
                if ($openingBalance != 0) {
                    $openingBalanceDate = trim($row['opening_balance_date'] ?? now()->format('Y-m-d'));

                    // Validate and format date
                    try {
                        $openingBalanceDate = \Carbon\Carbon::parse($openingBalanceDate)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $openingBalanceDate = now()->format('Y-m-d');
                    }

                    $this->createOpeningBalanceVoucher(
                        $ledgerAccount,
                        abs($openingBalance),
                        $openingBalance >= 0 ? $balanceType : ($balanceType === 'dr' ? 'cr' : 'dr'),
                        $openingBalanceDate
                    );
                }

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->failedCount++;
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                Log::error("Ledger account import error on row {$rowNumber}: " . $e->getMessage());
            }
        }
    }

    /**
     * Create opening balance voucher for ledger account
     */
    private function createOpeningBalanceVoucher(LedgerAccount $ledgerAccount, $amount, $type, $date)
    {
        // Get or create Journal Voucher type
        $journalVoucherType = VoucherType::where('tenant_id', $ledgerAccount->tenant_id)
            ->where('code', 'JV')
            ->first();

        if (!$journalVoucherType) {
            throw new \Exception('Journal Voucher type not found. Please ensure system voucher types are initialized.');
        }

        // Get Opening Balance Equity account
        $openingBalanceEquity = LedgerAccount::where('tenant_id', $ledgerAccount->tenant_id)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$openingBalanceEquity) {
            // Get or create Equity account group
            $equityGroup = AccountGroup::where('tenant_id', $ledgerAccount->tenant_id)
                ->where('nature', 'equity')
                ->first();

            if (!$equityGroup) {
                // Create equity account group if it doesn't exist
                $equityGroup = AccountGroup::create([
                    'tenant_id' => $ledgerAccount->tenant_id,
                    'name' => 'Equity',
                    'nature' => 'equity',
                    'code' => 'EQ',
                    'description' => 'Equity accounts',
                    'parent_id' => null,
                    'is_active' => true,
                ]);
            }

            // Check if code already exists and generate a unique one
            $code = 'OBE-001';
            $counter = 1;
            while (LedgerAccount::where('tenant_id', $ledgerAccount->tenant_id)->where('code', $code)->exists()) {
                $counter++;
                $code = 'OBE-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }

            // Create Opening Balance Equity account if it doesn't exist
            $openingBalanceEquity = LedgerAccount::create([
                'tenant_id' => $ledgerAccount->tenant_id,
                'name' => 'Opening Balance Equity',
                'code' => $code,
                'account_group_id' => $equityGroup->id,
                'account_type' => 'equity',
                'description' => 'Opening balance equity account',
                'opening_balance' => 0,
                'current_balance' => 0,
                'balance_type' => 'cr',
                'is_opening_balance_account' => true,
                'is_system_account' => true,
                'is_active' => true,
            ]);
        }

        // Create voucher
        $voucher = Voucher::create([
            'tenant_id' => $ledgerAccount->tenant_id,
            'voucher_type_id' => $journalVoucherType->id,
            'voucher_number' => $journalVoucherType->getNextVoucherNumber(),
            'voucher_date' => $date,
            'narration' => 'Opening Balance for ' . $ledgerAccount->name,
            'total_amount' => $amount,
            'status' => 'posted',
            'created_by' => Auth::id(),
            'posted_at' => now(),
            'posted_by' => Auth::id(),
        ]);

        // Create voucher entries based on balance type
        if ($type === 'dr') {
            // Debit the account, Credit Opening Balance Equity
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $ledgerAccount->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance - ' . $ledgerAccount->name,
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'narration' => 'Opening Balance Equity',
            ]);
        } else {
            // Credit the account, Debit Opening Balance Equity
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $ledgerAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'narration' => 'Opening Balance - ' . $ledgerAccount->name,
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance Equity',
            ]);
        }

        // Update ledger account's opening balance voucher reference
        $ledgerAccount->update([
            'opening_balance_voucher_id' => $voucher->id,
        ]);

        // Update ledger account balance
        if (method_exists($ledgerAccount, 'updateCurrentBalance')) {
            $ledgerAccount->updateCurrentBalance();
        }

        return $voucher;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }
}
