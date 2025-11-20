<?php

/**
 * Fix Vendor and Customer Ledger Account Types
 *
 * This script updates all vendor ledger accounts to have account_type = 'liability'
 * and all customer ledger accounts to have account_type = 'asset'.
 *
 * Run this script once to fix existing data.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Vendor;
use App\Models\Customer;
use App\Models\LedgerAccount;

echo "Fixing Vendor and Customer Ledger Account Types...\n\n";

// Fix Vendor Accounts
echo "Processing Vendors...\n";
$vendorsUpdated = 0;
$vendors = Vendor::whereNotNull('ledger_account_id')->get();

foreach ($vendors as $vendor) {
    if ($vendor->ledgerAccount) {
        $currentType = $vendor->ledgerAccount->account_type;

        if ($currentType !== 'liability') {
            $vendor->ledgerAccount->update(['account_type' => 'liability']);
            echo "  ✓ Updated vendor '{$vendor->getFullNameAttribute()}' (ID: {$vendor->id})\n";
            echo "    Changed account_type from '{$currentType}' to 'liability'\n";
            $vendorsUpdated++;
        }
    }
}

echo "Total vendors updated: {$vendorsUpdated}\n\n";

// Fix Customer Accounts
echo "Processing Customers...\n";
$customersUpdated = 0;
$customers = Customer::whereNotNull('ledger_account_id')->get();

foreach ($customers as $customer) {
    if ($customer->ledgerAccount) {
        $currentType = $customer->ledgerAccount->account_type;

        if ($currentType !== 'asset') {
            $customer->ledgerAccount->update(['account_type' => 'asset']);
            echo "  ✓ Updated customer '{$customer->getFullNameAttribute()}' (ID: {$customer->id})\n";
            echo "    Changed account_type from '{$currentType}' to 'asset'\n";
            $customersUpdated++;
        }
    }
}

echo "Total customers updated: {$customersUpdated}\n\n";

echo "Done! All vendor and customer ledger accounts have been corrected.\n";
echo "\nSummary:\n";
echo "- Vendors: {$vendorsUpdated} updated\n";
echo "- Customers: {$customersUpdated} updated\n";
