<?php

/**
 * Setup Product Ledger Accounts
 *
 * This script ensures all products have the correct ledger accounts assigned:
 * - stock_asset_account_id: Inventory (Asset) - for purchases
 * - sales_account_id: Sales Revenue (Income) - for sales
 * - purchase_account_id: Can be used for purchase-specific tracking (optional)
 *
 * This follows Tally/Zoho/QuickBooks standard accounting logic.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\LedgerAccount;
use App\Models\Tenant;

echo "=== SETTING UP PRODUCT LEDGER ACCOUNTS ===\n\n";

// Get all tenants
$tenants = Tenant::all();

foreach ($tenants as $tenant) {
    echo "Processing Tenant: {$tenant->name} (ID: {$tenant->id})\n";
    echo str_repeat('-', 60) . "\n";

    // Find or create Inventory account (Asset)
    $inventoryAccount = LedgerAccount::where('tenant_id', $tenant->id)
        ->where(function($q) {
            $q->where('name', 'Inventory')
              ->orWhere('name', 'Stock')
              ->orWhere('code', 'INV');
        })
        ->where('account_type', 'asset')
        ->first();

    if (!$inventoryAccount) {
        echo "⚠️  Inventory account not found for tenant {$tenant->name}\n";
        echo "   Please create an 'Inventory' ledger account with account_type='asset'\n\n";
        continue;
    }

    echo "✓ Found Inventory Account: {$inventoryAccount->name} (ID: {$inventoryAccount->id})\n";

    // Find or create Sales Revenue account (Income)
    $salesAccount = LedgerAccount::where('tenant_id', $tenant->id)
        ->where(function($q) {
            $q->where('name', 'Sales Revenue')
              ->orWhere('name', 'Sales')
              ->orWhere('code', 'SAL');
        })
        ->where('account_type', 'income')
        ->first();

    if (!$salesAccount) {
        echo "⚠️  Sales Revenue account not found for tenant {$tenant->name}\n";
        echo "   Please create a 'Sales Revenue' ledger account with account_type='income'\n\n";
        continue;
    }

    echo "✓ Found Sales Account: {$salesAccount->name} (ID: {$salesAccount->id})\n\n";

    // Get all products for this tenant
    $products = Product::where('tenant_id', $tenant->id)->get();

    echo "Processing {$products->count()} products...\n";

    $updatedCount = 0;
    foreach ($products as $product) {
        $needsUpdate = false;
        $updates = [];

        // Check stock_asset_account_id
        if (!$product->stock_asset_account_id) {
            $updates['stock_asset_account_id'] = $inventoryAccount->id;
            $needsUpdate = true;
        }

        // Check sales_account_id
        if (!$product->sales_account_id) {
            $updates['sales_account_id'] = $salesAccount->id;
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $product->update($updates);
            $updatedCount++;

            $updatedFields = implode(', ', array_keys($updates));
            echo "  ✓ Updated: {$product->name} ({$product->sku}) - Set: {$updatedFields}\n";
        }
    }

    echo "\nTenant Summary:\n";
    echo "  Total Products: {$products->count()}\n";
    echo "  Updated: {$updatedCount}\n";
    echo "  Already Configured: " . ($products->count() - $updatedCount) . "\n";
    echo "\n" . str_repeat('=', 60) . "\n\n";
}

echo "=== SETUP COMPLETE ===\n\n";

echo "IMPORTANT: Understanding the Accounting Flow\n";
echo str_repeat('=', 60) . "\n";
echo "1. PURCHASE (₦300,000 inventory):\n";
echo "   DR: Inventory (Asset)           ₦300,000\n";
echo "   CR: Accounts Payable (Liability) ₦300,000\n";
echo "   → Balance Sheet changes only\n";
echo "   → P&L NOT affected\n\n";

echo "2. SALE (₦600,000, cost ₦500,000):\n";
echo "   DR: Accounts Receivable (Asset)  ₦600,000\n";
echo "   CR: Sales Revenue (Income)        ₦600,000\n";
echo "   DR: COGS (Expense)                ₦500,000\n";
echo "   CR: Inventory (Asset)             ₦500,000\n";
echo "   → P&L shows: Income ₦600,000 - COGS ₦500,000 = Profit ₦100,000\n\n";

echo "This matches Tally, Zoho, and QuickBooks behavior!\n";
