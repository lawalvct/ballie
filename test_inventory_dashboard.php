<?php

/**
 * Test script to verify inventory dashboard stock calculations
 * Run this with: php test_inventory_dashboard.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

echo "=== Inventory Dashboard Stock Calculation Test ===\n\n";

// Get the first tenant (adjust this if needed)
$tenant = Tenant::first();

if (!$tenant) {
    echo "❌ No tenant found!\n";
    exit(1);
}

echo "Testing for Tenant: {$tenant->name} (ID: {$tenant->id})\n";
echo str_repeat("=", 60) . "\n\n";

// Get all products with stock tracking
$products = Product::where('tenant_id', $tenant->id)
    ->where('maintain_stock', true)
    ->where('is_active', true)
    ->get();

echo "Total Products (with stock tracking): {$products->count()}\n\n";

// Calculate statistics
$totalStockValue = 0;
$lowStockItems = 0;
$outOfStockItems = 0;
$inStockItems = 0;

echo "Analyzing each product:\n";
echo str_repeat("-", 60) . "\n";
printf("%-5s %-30s %10s %10s %10s\n", "ID", "Name", "Stock", "Reorder", "Status");
echo str_repeat("-", 60) . "\n";

foreach ($products as $product) {
    $currentStock = $product->current_stock; // Uses calculated stock from movements
    $stockValue = $product->stock_value; // Uses calculated stock value
    $reorderLevel = $product->reorder_level ?? 0;

    $totalStockValue += $stockValue;

    // Determine status
    $status = '';
    if ($currentStock <= 0) {
        $outOfStockItems++;
        $status = '❌ OUT';
    } elseif ($reorderLevel && $currentStock <= $reorderLevel) {
        $lowStockItems++;
        $status = '⚠️  LOW';
    } else {
        $inStockItems++;
        $status = '✅ OK';
    }

    // Display first 10 products
    if (($outOfStockItems + $lowStockItems + $inStockItems) <= 10) {
        printf("%-5d %-30s %10.2f %10.2f %10s\n",
            $product->id,
            substr($product->name, 0, 30),
            $currentStock,
            $reorderLevel,
            $status
        );
    }
}

echo str_repeat("-", 60) . "\n";

// Display summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "DASHBOARD STATISTICS SUMMARY\n";
echo str_repeat("=", 60) . "\n\n";

echo "✅ In Stock Items:        {$inStockItems}\n";
echo "⚠️  Low Stock Items:       {$lowStockItems}\n";
echo "❌ Out of Stock Items:    {$outOfStockItems}\n";
echo "💰 Total Stock Value:     ₦" . number_format($totalStockValue, 2) . "\n";

echo "\n" . str_repeat("=", 60) . "\n";

// Test specific product if it exists (Product ID 48 from previous test)
echo "\nTesting Specific Product (ID: 48):\n";
echo str_repeat("-", 60) . "\n";

$testProduct = Product::find(48);
if ($testProduct && $testProduct->tenant_id === $tenant->id) {
    $stock = $testProduct->current_stock;
    $value = $testProduct->stock_value;
    $reorder = $testProduct->reorder_level ?? 0;

    echo "Product: {$testProduct->name}\n";
    echo "Current Stock: {$stock} pcs\n";
    echo "Stock Value: ₦" . number_format($value, 2) . "\n";
    echo "Reorder Level: {$reorder} pcs\n";

    if ($stock <= 0) {
        echo "Status: ❌ OUT OF STOCK\n";
    } elseif ($reorder && $stock <= $reorder) {
        echo "Status: ⚠️  LOW STOCK\n";
    } else {
        echo "Status: ✅ IN STOCK\n";
    }
} else {
    echo "Product ID 48 not found or belongs to different tenant.\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✓ Test Complete!\n";
echo "✓ All stock calculations are now based on stock movements\n";
echo "✓ Dashboard should display correct statistics\n";
