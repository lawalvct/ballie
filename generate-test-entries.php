<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\VoucherType;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

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

echo "Generating test data for tenant: {$tenant->name} (ID: {$tenantId})\n\n";

// Create products
echo "Creating products...\n";
$products = [
    ['name' => 'Laptop Computer', 'sku' => 'PROD-001', 'price' => 150000, 'cost' => 100000],
    ['name' => 'Office Chair', 'sku' => 'PROD-002', 'price' => 25000, 'cost' => 15000],
    ['name' => 'Desk Lamp', 'sku' => 'PROD-003', 'price' => 5000, 'cost' => 3000],
    ['name' => 'Wireless Mouse', 'sku' => 'PROD-004', 'price' => 3500, 'cost' => 2000],
    ['name' => 'USB Cable', 'sku' => 'PROD-005', 'price' => 1500, 'cost' => 800],
];

$createdProducts = [];
foreach ($products as $prod) {
    $product = Product::where('tenant_id', $tenantId)->where('sku', $prod['sku'])->first();
    if (!$product) {
        $product = Product::create([
            'tenant_id' => $tenantId,
            'name' => $prod['name'],
            'sku' => $prod['sku'],
            'selling_price' => $prod['price'],
            'sales_rate' => $prod['price'],
            'purchase_rate' => $prod['cost'],
            'current_stock' => 100,
            'reorder_level' => 10,
            'is_active' => true,
        ]);
        echo "Created product: {$product->name}\n";
    } else {
        echo "Product exists: {$product->name}\n";
    }
    $createdProducts[] = $product;
}

// Create customers
echo "\nCreating customers...\n";
$customersData = [
    ['name' => 'ABC Company Ltd', 'email' => 'abc@example.com', 'phone' => '08012345678'],
    ['name' => 'XYZ Enterprises', 'email' => 'xyz@example.com', 'phone' => '08087654321'],
];

$createdCustomers = [];
foreach ($customersData as $cust) {
    $existing = Customer::where('tenant_id', $tenantId)->where('company_name', $cust['name'])->first();
    if (!$existing) {
        $customer = Customer::create([
            'tenant_id' => $tenantId,
            'customer_type' => 'business',
            'company_name' => $cust['name'],
            'email' => $cust['email'],
            'phone' => $cust['phone'],
            'status' => 'active',
        ]);
        echo "Created customer: {$customer->company_name}\n";
        $createdCustomers[] = $customer;
    } else {
        echo "Customer exists: {$existing->company_name}\n";
        $createdCustomers[] = $existing;
    }
}

// Get accounts
$cash = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'CASH-001')->first();
$sales = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'SALES-001')->first();
$cogs = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'COGS-001')->first();
$inventory = LedgerAccount::where('tenant_id', $tenantId)->where('code', 'INV-001')->first();

// Get voucher types
$salesVoucherType = VoucherType::where('tenant_id', $tenantId)->where('code', 'SALES')->first();

if (!$salesVoucherType) {
    echo "\nCreating SALES voucher type...\n";
    $salesVoucherType = VoucherType::create([
        'tenant_id' => $tenantId,
        'name' => 'Sales Invoice',
        'code' => 'SALES',
        'abbreviation' => 'SI',
        'prefix' => 'INV',
        'is_active' => true,
    ]);
}

// Create sales invoices
echo "\nCreating sales invoices...\n";
$invoices = [
    ['date' => '2025-01-15', 'customer' => $createdCustomers[0], 'items' => [
        ['product' => $createdProducts[0], 'qty' => 2],
        ['product' => $createdProducts[1], 'qty' => 5],
    ]],
    ['date' => '2025-02-10', 'customer' => $createdCustomers[1], 'items' => [
        ['product' => $createdProducts[2], 'qty' => 10],
        ['product' => $createdProducts[3], 'qty' => 15],
    ]],
    ['date' => '2025-03-05', 'customer' => $createdCustomers[0], 'items' => [
        ['product' => $createdProducts[4], 'qty' => 50],
        ['product' => $createdProducts[0], 'qty' => 1],
    ]],
];

DB::beginTransaction();
try {
    $invNum = 1;
    foreach ($invoices as $inv) {
        $subtotal = 0;
        $costTotal = 0;

        foreach ($inv['items'] as $item) {
            $subtotal += $item['product']->selling_price * $item['qty'];
            $costTotal += $item['product']->purchase_rate * $item['qty'];
        }

        $voucher = Voucher::create([
            'tenant_id' => $tenantId,
            'voucher_type_id' => $salesVoucherType->id,
            'voucher_number' => 'INV-' . str_pad($invNum++, 4, '0', STR_PAD_LEFT),
            'voucher_date' => $inv['date'],
            'narration' => 'Sales invoice to ' . $inv['customer']->company_name,
            'total_amount' => $subtotal,
            'status' => 'posted',
            'created_by' => 1,
        ]);

        // Create invoice items
        foreach ($inv['items'] as $item) {
            InvoiceItem::create([
                'voucher_id' => $voucher->id,
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'quantity' => $item['qty'],
                'rate' => $item['product']->selling_price,
                'amount' => $item['product']->selling_price * $item['qty'],
                'purchase_rate' => $item['product']->purchase_rate ?? 0,
            ]);
        }

        // Create voucher entries (double entry)
        // Debit: Customer (AR)
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $inv['customer']->ledger_account_id,
            'debit_amount' => $subtotal,
            'credit_amount' => 0,
            'particulars' => 'Sales to ' . $inv['customer']->company_name,
        ]);

        // Credit: Sales
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $sales->id,
            'debit_amount' => 0,
            'credit_amount' => $subtotal,
            'particulars' => 'Sales revenue',
        ]);

        // Debit: COGS
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $cogs->id,
            'debit_amount' => $costTotal,
            'credit_amount' => 0,
            'particulars' => 'Cost of goods sold',
        ]);

        // Credit: Inventory
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $inventory->id,
            'debit_amount' => 0,
            'credit_amount' => $costTotal,
            'particulars' => 'Inventory reduction',
        ]);

        echo "Created invoice: {$voucher->voucher_number} for {$inv['customer']->company_name} - ₦" . number_format($subtotal, 2) . "\n";
    }

    DB::commit();
    echo "\n=== Test Data Generation Complete ===\n";
    echo "Products: 5\n";
    echo "Customers: 2\n";
    echo "Sales Invoices: 3\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
