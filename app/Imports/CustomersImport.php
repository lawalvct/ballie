<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\AccountGroup;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class CustomersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected $tenant;
    protected $errors = [];
    protected $successCount = 0;
    protected $failedCount = 0;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Process the imported collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                DB::beginTransaction();

                // Validate required fields
                if (empty($row['email'])) {
                    throw new \Exception('Email is required');
                }

                // Check if customer already exists
                $existingCustomer = Customer::where('tenant_id', $this->tenant->id)
                    ->where('email', $row['email'])
                    ->first();

                if ($existingCustomer) {
                    throw new \Exception('Customer with email ' . $row['email'] . ' already exists');
                }

                // Determine customer type
                $customerType = strtolower($row['customer_type'] ?? 'individual');
                if (!in_array($customerType, ['individual', 'business'])) {
                    $customerType = 'individual';
                }

                // Validate based on customer type
                if ($customerType === 'individual' && (empty($row['first_name']) || empty($row['last_name']))) {
                    throw new \Exception('First name and last name are required for individual customers');
                }

                if ($customerType === 'business' && empty($row['company_name'])) {
                    throw new \Exception('Company name is required for business customers');
                }

                // Create customer
                $customer = Customer::create([
                    'tenant_id' => $this->tenant->id,
                    'customer_type' => $customerType,
                    'first_name' => $row['first_name'] ?? null,
                    'last_name' => $row['last_name'] ?? null,
                    'company_name' => $row['company_name'] ?? null,
                    'email' => $row['email'],
                    'phone' => $row['phone'] ?? null,
                    'mobile' => $row['mobile'] ?? null,
                    'address_line1' => $row['address_line1'] ?? null,
                    'address_line2' => $row['address_line2'] ?? null,
                    'city' => $row['city'] ?? null,
                    'state' => $row['state'] ?? null,
                    'postal_code' => $row['postal_code'] ?? null,
                    'country' => $row['country'] ?? 'Nigeria',
                    'currency' => $row['currency'] ?? 'NGN',
                    'payment_terms' => $row['payment_terms'] ?? null,
                    'tax_id' => $row['tax_id'] ?? null,
                    'notes' => $row['notes'] ?? null,
                    'status' => 'active',
                ]);

                // Refresh to get ledger account created by boot method
                $customer->refresh();

                // Ensure ledger account exists
                if (!$customer->ledgerAccount) {
                    $customer->createLedgerAccount();
                    $customer->refresh();
                }

                // Handle opening balance if provided
                $openingBalanceAmount = floatval($row['opening_balance_amount'] ?? 0);
                $openingBalanceType = strtolower($row['opening_balance_type'] ?? 'none');
                $openingBalanceDate = $row['opening_balance_date'] ?? now()->format('Y-m-d');

                if ($openingBalanceAmount > 0 && in_array($openingBalanceType, ['debit', 'credit'])) {
                    $this->createOpeningBalanceVoucher(
                        $customer,
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

                // Get customer identifier for error message
                $identifier = $row['email'] ?? $row['first_name'] ?? $row['company_name'] ?? 'Unknown';

                $this->errors[] = [
                    'row' => $index + 2, // +2 because of 0-index and header row
                    'identifier' => $identifier,
                    'error' => $e->getMessage()
                ];

                Log::error('Customer import error', [
                    'row' => $index + 2,
                    'data' => $row->toArray(),
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Create opening balance voucher for customer
     */
    private function createOpeningBalanceVoucher(Customer $customer, $amount, $type, $date)
    {
        // Get or create Journal Voucher type
        $journalVoucherType = VoucherType::where('tenant_id', $customer->tenant_id)
            ->where('code', 'JV')
            ->first();

        if (!$journalVoucherType) {
            throw new \Exception('Journal Voucher type not found. Please ensure system voucher types are initialized.');
        }

        // Get Opening Balance Equity account
        $openingBalanceEquity = LedgerAccount::where('tenant_id', $customer->tenant_id)
            ->where('is_opening_balance_account', true)
            ->first();

        if (!$openingBalanceEquity) {
            // Get or create Equity account group
            $equityGroup = AccountGroup::where('tenant_id', $customer->tenant_id)
                ->where('nature', 'equity')
                ->first();

            if (!$equityGroup) {
                $equityGroup = AccountGroup::create([
                    'tenant_id' => $customer->tenant_id,
                    'name' => 'Equity',
                    'code' => 'EQ',
                    'nature' => 'equity',
                ]);
            }

            // Check if code already exists and generate a unique one
            $code = 'OBE-001';
            $counter = 1;
            while (LedgerAccount::where('tenant_id', $customer->tenant_id)->where('code', $code)->exists()) {
                $counter++;
                $code = 'OBE-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            }

            // Create Opening Balance Equity account
            $openingBalanceEquity = LedgerAccount::create([
                'tenant_id' => $customer->tenant_id,
                'name' => 'Opening Balance Equity',
                'code' => $code,
                'account_group_id' => $equityGroup->id,
                'opening_balance' => 0,
                'balance_type' => 'cr',
                'is_opening_balance_account' => true,
                'is_system_account' => true,
                'is_active' => true,
            ]);
        }

        // Create voucher
        $voucher = Voucher::create([
            'tenant_id' => $customer->tenant_id,
            'voucher_type_id' => $journalVoucherType->id,
            'voucher_number' => $journalVoucherType->getNextVoucherNumber(),
            'voucher_date' => $date,
            'narration' => 'Opening Balance for ' . $customer->getFullNameAttribute(),
            'total_amount' => $amount,
            'status' => 'posted',
            'created_by' => Auth::id(),
            'posted_at' => now(),
            'posted_by' => Auth::id(),
        ]);

        // Create voucher entries based on balance type
        if ($type === 'debit') {
            // Customer owes money (Debit Customer, Credit Opening Balance Equity)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $customer->ledgerAccount->id,
                'debit_amount' => $amount,
                'credit_amount' => 0,
                'narration' => 'Opening Balance - Customer Receivable',
            ]);

            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $openingBalanceEquity->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'narration' => 'Opening Balance Equity',
            ]);
        } else {
            // Credit balance - We owe customer (Credit Customer, Debit Opening Balance Equity)
            VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $customer->ledgerAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $amount,
                'narration' => 'Opening Balance - Customer Credit',
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
        $customer->ledgerAccount->update([
            'opening_balance_voucher_id' => $voucher->id,
        ]);

        // Update customer's ledger account balance
        $customer->ledgerAccount->updateCurrentBalance();

        return $voucher;
    }

    /**
     * Get import errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get success count
     */
    public function getSuccessCount()
    {
        return $this->successCount;
    }

    /**
     * Get failed count
     */
    public function getFailedCount()
    {
        return $this->failedCount;
    }

    /**
     * Check if import has errors
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }
}
