<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\VoucherType;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use Illuminate\Support\Facades\DB;

// Get tenant ID from command line
$tenantId = $argv[1] ?? null;

if (!$tenantId) {
    echo "Usage: php generate-test-entries.php <tenant_id>\n";
    exit(1);
}

$tenant = Tenant::find($tenantId);
if (!$tenant) {
    echo "Tenant not found!\n";
    exit(1);
}

echo "Generating test entries for tenant: {$tenant->name} (ID: {$tenantId})\n";

// Get accounts
$cash = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'CASH-001')->first();
$sales = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'SALES-001')->first();
$cogs = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'COGS-001')->first();
$inventory = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'INV-001')->first();
$rent = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'RENT-001')->first();
$salary = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'SAL-001')->first();
$utilities = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'UTIL-001')->first();
$ar = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'AR-001')->first();
$ap = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'AP-001')->first();

// Get voucher type
$voucherType = VoucherType::where('tenant_id', $tenantId)->where('code', 'JV')->first();

if (!$voucherType) {
    echo "Creating Journal Voucher type...\n";
    $voucherType = VoucherType::create([
        'tenant_id' => $tenantId,
        'name' => 'Journal Voucher',
        'code' => 'JV',
        'prefix' => 'JV',
        'next_number' => 1,
        'is_active' => true,
    ]);
}

$entries = [
    ['date' => '2025-01-05', 'desc' => 'Cash sales', 'debit' => $cash, 'credit' => $sales, 'amount' => 50000],
    ['date' => '2025-01-05', 'desc' => 'Cost of goods sold', 'debit' => $cogs, 'credit' => $inventory, 'amount' => 30000],
    ['date' => '2025-01-10', 'desc' => 'Credit sales', 'debit' => $ar, 'credit' => $sales, 'amount' => 75000],
    ['date' => '2025-01-10', 'desc' => 'Cost of goods sold', 'debit' => $cogs, 'credit' => $inventory, 'amount' => 45000],
    ['date' => '2025-01-15', 'desc' => 'Rent payment', 'debit' => $rent, 'credit' => $cash, 'amount' => 20000],
    ['date' => '2025-01-20', 'desc' => 'Salary payment', 'debit' => $salary, 'credit' => $cash, 'amount' => 35000],
    ['date' => '2025-01-25', 'desc' => 'Utilities payment', 'debit' => $utilities, 'credit' => $cash, 'amount' => 5000],
    ['date' => '2025-01-28', 'desc' => 'Customer payment received', 'debit' => $cash, 'credit' => $ar, 'amount' => 40000],
    ['date' => '2025-02-05', 'desc' => 'Cash sales', 'debit' => $cash, 'credit' => $sales, 'amount' => 60000],
    ['date' => '2025-02-05', 'desc' => 'Cost of goods sold', 'debit' => $cogs, 'credit' => $inventory, 'amount' => 36000],
    ['date' => '2025-02-10', 'desc' => 'Credit sales', 'debit' => $ar, 'credit' => $sales, 'amount' => 85000],
    ['date' => '2025-02-10', 'desc' => 'Cost of goods sold', 'debit' => $cogs, 'credit' => $inventory, 'amount' => 51000],
    ['date' => '2025-02-15', 'desc' => 'Rent payment', 'debit' => $rent, 'credit' => $cash, 'amount' => 20000],
    ['date' => '2025-02-20', 'desc' => 'Salary payment', 'debit' => $salary, 'credit' => $cash, 'amount' => 35000],
    ['date' => '2025-02-25', 'desc' => 'Utilities payment', 'debit' => $utilities, 'credit' => $cash, 'amount' => 6000],
    ['date' => '2025-02-28', 'desc' => 'Customer payment received', 'debit' => $cash, 'credit' => $ar, 'amount' => 50000],
    ['date' => '2025-03-05', 'desc' => 'Cash sales', 'debit' => $cash, 'credit' => $sales, 'amount' => 70000],
    ['date' => '2025-03-05', 'desc' => 'Cost of goods sold', 'debit' => $cogs, 'credit' => $inventory, 'amount' => 42000],
    ['date' => '2025-03-10', 'desc' => 'Credit sales', 'debit' => $ar, 'credit' => $sales, 'amount' => 95000],
    ['date' => '2025-03-10', 'desc' => 'Cost of goods sold', 'debit' => $cogs, 'credit' => $inventory, 'amount' => 57000],
];

DB::beginTransaction();
try {
    foreach ($entries as $entry) {
        if (!$entry['debit'] || !$entry['credit']) {
            echo "Skipping entry: {$entry['desc']} - missing accounts\n";
            continue;
        }

        $voucher = Voucher::create([
            'tenant_id' => $tenantId,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $voucherType->prefix . '-' . str_pad($voucherType->next_number++, 4, '0', STR_PAD_LEFT),
            'voucher_date' => $entry['date'],
            'description' => $entry['desc'],
            'status' => 'posted',
            'total_amount' => $entry['amount'],
            'total_debit' => $entry['amount'],
            'total_credit' => $entry['amount'],
            'created_by' => 1,
        ]);

        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $entry['debit']->id,
            'debit_amount' => $entry['amount'],
            'credit_amount' => 0,
            'particulars' => $entry['desc'],
        ]);

        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $entry['credit']->id,
            'debit_amount' => 0,
            'credit_amount' => $entry['amount'],
            'particulars' => $entry['desc'],
        ]);

        // Update account balances
        $entry['debit']->increment('current_balance', $entry['amount']);
        $entry['credit']->decrement('current_balance', $entry['amount']);

        echo "Created: {$voucher->voucher_number} - {$entry['desc']}\n";
    }

    DB::commit();
    echo "\nSuccessfully created 20 test entries!\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
