<?php

namespace App\Imports;

use App\Models\Vendor;
use App\Models\LedgerAccount;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use App\Models\AccountGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class VendorsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
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

                // Validate vendor type
                $vendorType = strtolower(trim($row['vendor_type'] ?? 'individual'));
                if (!in_array($vendorType, ['individual', 'business'])) {
                    throw new \Exception("Invalid vendor type. Must be 'individual' or 'business'.");
                }

                // Validate required fields based on vendor type
                if ($vendorType === 'individual') {
                    if (empty($row['first_name']) || empty($row['last_name'])) {
                        throw new \Exception("First name and last name are required for individual vendors.");
                    }
                } else {
                    if (empty($row['company_name'])) {
                        throw new \Exception("Company name is required for business vendors.");
                    }
                }

                // Validate email
                $email = trim($row['email'] ?? '');
                if (empty($email)) {
                    throw new \Exception("Email is required.");
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Invalid email format.");
                }

                // Check for duplicate email
                $existingVendor = Vendor::where('tenant_id', tenant()->id)
                    ->where('email', $email)
                    ->first();

                if ($existingVendor) {
                    throw new \Exception("A vendor with email '{$email}' already exists.");
                }

                // Create vendor
                $vendor = new Vendor();
                $vendor->tenant_id = tenant()->id;
                $vendor->vendor_type = $vendorType;
                $vendor->first_name = trim($row['first_name'] ?? '');
                $vendor->last_name = trim($row['last_name'] ?? '');
                $vendor->company_name = trim($row['company_name'] ?? '');
                $vendor->email = $email;
                $vendor->phone = trim($row['phone'] ?? '');
                $vendor->mobile = trim($row['mobile'] ?? '');
                $vendor->website = trim($row['website'] ?? '');
                $vendor->tax_id = trim($row['tax_id'] ?? '');
                $vendor->registration_number = trim($row['registration_number'] ?? '');
                $vendor->address_line1 = trim($row['address_line1'] ?? '');
                $vendor->address_line2 = trim($row['address_line2'] ?? '');
                $vendor->city = trim($row['city'] ?? '');
                $vendor->state = trim($row['state'] ?? '');
                $vendor->postal_code = trim($row['postal_code'] ?? '');
                $vendor->country = trim($row['country'] ?? 'Nigeria');
                $vendor->currency = strtoupper(trim($row['currency'] ?? 'NGN'));
                $vendor->payment_terms = trim($row['payment_terms'] ?? 'Net 30');
                $vendor->bank_name = trim($row['bank_name'] ?? '');
                $vendor->bank_account_number = trim($row['bank_account_number'] ?? '');
                $vendor->bank_account_name = trim($row['bank_account_name'] ?? '');
                $vendor->notes = trim($row['notes'] ?? '');
                $vendor->status = 'active';
                $vendor->save();

                // Ensure ledger account is created
                $vendor->refresh();
                if (!$vendor->ledgerAccount) {
                    $vendor->createLedgerAccount();
                    $vendor->refresh();
                }

                // Handle opening balance if provided
                $openingBalanceAmount = floatval($row['opening_balance_amount'] ?? 0);
                $openingBalanceType = strtolower(trim($row['opening_balance_type'] ?? 'none'));
                $openingBalanceDate = trim($row['opening_balance_date'] ?? now()->format('Y-m-d'));

                // Validate opening balance type
                if (!in_array($openingBalanceType, ['none', 'debit', 'credit'])) {
                    $openingBalanceType = 'none';
                }

                // Validate and format date
                try {
                    $openingBalanceDate = \Carbon\Carbon::parse($openingBalanceDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    $openingBalanceDate = now()->format('Y-m-d');
                }

                if ($openingBalanceAmount > 0 && $openingBalanceType !== 'none') {
                    $this->createOpeningBalanceVoucher(
                        $vendor,
                        $openingBalanceAmount,
                        $openingBalanceType,
                        $openingBalanceDate
                    );
                }

                DB::commit();
                $this->successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $this->failedCount++;
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                Log::error("Vendor import error on row {$rowNumber}: " . $e->getMessage());
            }
        }
    }

    /**
     * Create opening balance voucher for vendor
     */
    private function createOpeningBalanceVoucher(Vendor $vendor, $amount, $type, $date)
    {
        // Get or create Journal Voucher type
        $journalVoucherType = VoucherType::where('tenant_id', $vendor->tenant_id)
            ->where('code', 'JV')
            ->first();

        if (!$journalVoucherType) {
            throw new \Exception('Journal Voucher type not found. Please ensure system voucher types are initialized.');
        }

        // Get Opening Balance Equity account
        $openingBalanceEquity = LedgerAccount::where('tenant_id', $vendor->tenant_id)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$openingBalanceEquity) {
            // Get or create Equity account group
            $equityGroup = AccountGroup::where('tenant_id', $vendor->tenant_id)
                ->where('nature', 'equity')
                ->first();

            if (!$equityGroup) {
                // Create equity account group if it doesn't exist
                $equityGroup = AccountGroup::create([
                    'tenant_id' => $vendor->tenant_id,
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
            while (LedgerAccount::where('tenant_id', $vendor->tenant_id)->where('code', $code)->exists()) {
                $counter++;
                $code = 'OBE-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }

            // Create Opening Balance Equity account if it doesn't exist
            $openingBalanceEquity = LedgerAccount::create([
                'tenant_id' => $vendor->tenant_id,
                'name' => 'Opening Balance Equity',
                'code' => $code,
                'account_group_id' => $equityGroup->id,
                'description' => 'Opening balance equity account',
                'opening_balance' => 0,
                'current_balance' => 0,
                'nature' => 'equity',
                'is_opening_balance_account' => true,
                'is_active' => true,
            ]);
        }

        // Get vendor name for narration
        $vendorName = $vendor->company_name ?: trim($vendor->first_name . ' ' . $vendor->last_name);

        // Create voucher
        $voucher = Voucher::create([
            'tenant_id' => $vendor->tenant_id,
            'voucher_type_id' => $journalVoucherType->id,
            'voucher_number' => $journalVoucherType->getNextVoucherNumber(),
            'voucher_date' => $date,
            'narration' => 'Opening Balance for ' . $vendorName,
            'total_amount' => $amount,
            'status' => 'posted',
            'created_by' => Auth::id(),
            'posted_at' => now(),
            'posted_by' => Auth::id(),
        ]);

        // Create voucher entries based on balance type
        if ($type === 'credit') {
            // We owe vendor money (Credit Vendor, Debit Opening Balance Equity)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $vendor->ledgerAccount->id,
                'credit_amount' => $amount,
                'debit_amount' => 0,
                'narration' => 'Opening Balance - Vendor Payable',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance Equity',
            ]);
        } else {
            // Debit balance - Vendor owes us (Debit Vendor, Credit Opening Balance Equity)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $vendor->ledgerAccount->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance - Vendor Advance',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'credit_amount' => $amount,
                'debit_amount' => 0,
                'narration' => 'Opening Balance Equity',
            ]);
        }

        // Update ledger account's opening balance voucher reference
        $vendor->ledgerAccount->update([
            'opening_balance_voucher_id' => $voucher->id,
            'opening_balance' => $type === 'credit' ? $amount : -$amount,
        ]);

        // Update vendor's ledger account balance
        $vendor->ledgerAccount->updateCurrentBalance();

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
