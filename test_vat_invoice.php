<?php

/**
 * Test script to verify VAT functionality in invoice creation
 * This script simulates creating an invoice with VAT enabled
 */

require_once 'vendor/autoload.php';

use App\Models\Tenant\Voucher;
use App\Models\Tenant\VoucherEntry;
use App\Models\Tenant\LedgerAccount;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Inventory\Product;
use Illuminate\Support\Facades\DB;

echo "=== VAT Invoice Test ===\n\n";

// Test data for VAT invoice
$testData = [
    'customer_id' => 1, // Assuming customer exists
    'items' => [
        [
            'product_id' => 1,
            'quantity' => 2,
            'rate' => 1500.00,
            'amount' => 3000.00
        ],
        [
            'product_id' => 2,
            'quantity' => 1,
            'rate' => 2500.00,
            'amount' => 2500.00
        ]
    ],
    'subtotal' => 5500.00,
    'vat_enabled' => true,
    'vat_applies_to' => 'items_only',
    'vat_rate' => 7.5,
    'vat_amount' => 412.50, // 7.5% of 5500
    'total_amount' => 5912.50,
    'additional_charges' => [
        [
            'account' => 'Transportation',
            'amount' => 500.00
        ]
    ]
];

echo "Test Invoice Data:\n";
echo "Subtotal: ₦" . number_format($testData['subtotal'], 2) . "\n";
echo "VAT Enabled: " . ($testData['vat_enabled'] ? 'Yes' : 'No') . "\n";
echo "VAT Applies To: " . ucfirst(str_replace('_', ' ', $testData['vat_applies_to'])) . "\n";
echo "VAT Rate: " . $testData['vat_rate'] . "%\n";
echo "VAT Amount: ₦" . number_format($testData['vat_amount'], 2) . "\n";
echo "Total Amount: ₦" . number_format($testData['total_amount'], 2) . "\n";

echo "\n=== Expected Voucher Entries ===\n";

// Customer Account (Debit)
echo "1. Customer Account (Dr): ₦" . number_format($testData['total_amount'], 2) . "\n";

// Product Sales Accounts (Credit)
foreach ($testData['items'] as $index => $item) {
    echo "2." . ($index + 1) . " Product Sales Account (Cr): ₦" . number_format($item['amount'], 2) . "\n";
}

// VAT Output Account (Credit)
echo "3. VAT Output Account (Cr): ₦" . number_format($testData['vat_amount'], 2) . "\n";

// Additional charges if any
foreach ($testData['additional_charges'] as $index => $charge) {
    echo "4." . ($index + 1) . " " . $charge['account'] . " (Cr): ₦" . number_format($charge['amount'], 2) . "\n";
}

echo "\n=== VAT Calculation Logic ===\n";
echo "Base Amount for VAT: ₦" . number_format($testData['subtotal'], 2) . " (items only)\n";
echo "VAT Calculation: " . $testData['subtotal'] . " × " . $testData['vat_rate'] . "% = ₦" . number_format($testData['vat_amount'], 2) . "\n";

echo "\n=== Display Breakdown (Expected) ===\n";
echo "Subtotal: ₦" . number_format($testData['subtotal'], 2) . "\n";
if (!empty($testData['additional_charges'])) {
    foreach ($testData['additional_charges'] as $charge) {
        echo $charge['account'] . ": ₦" . number_format($charge['amount'], 2) . "\n";
    }
}
echo "VAT Output (VAT @ 7.5% on items): ₦" . number_format($testData['vat_amount'], 2) . "\n";
echo "-----------------------------------\n";
echo "TOTAL: ₦" . number_format($testData['total_amount'], 2) . "\n";

echo "\n=== Test Form Data ===\n";
echo "Hidden input values that should be submitted:\n";
echo "vat_enabled: 1\n";
echo "vat_applies_to: items_only\n";
echo "vat_amount: " . $testData['vat_amount'] . "\n";

echo "\n=== Test Instructions ===\n";
echo "1. Navigate to invoice creation page\n";
echo "2. Add products with total subtotal of ₦5,500.00\n";
echo "3. Check 'Add VAT' checkbox\n";
echo "4. Select 'Items Only' for VAT application\n";
echo "5. Verify VAT amount shows as ₦412.50\n";
echo "6. Submit the form\n";
echo "7. Check voucher entries include VAT Output account\n";
echo "8. View invoice to see breakdown display\n";
echo "9. Print invoice to verify breakdown appears there too\n";

echo "\n=== Debugging Tips ===\n";
echo "- Check browser console for Alpine.js events\n";
echo "- Verify VAT account codes: VAT-OUT-001 (sales), VAT-IN-001 (purchase)\n";
echo "- Look for VAT entries in voucher_entries table\n";
echo "- Check narration field for VAT calculation details\n";

echo "\nTest completed. Please run manual tests as outlined above.\n";

?>
