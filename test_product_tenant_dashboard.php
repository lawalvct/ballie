<?php

/**
 * Test script to verify specific product and its tenant dashboard
 * Run this with: php test_product_tenant_dashboard.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

echo "=== Product & Tenant Dashboard Test ===\n\n";

// Test specific product
$productId = 48;
$product = Product::find($productId);

if (!$product) {
    echo "❌ Product ID {$productId} not found!\n";
    exit(1);
}

echo "Product: {$product->name} (ID: {$productId})\n";
echo "Tenant ID: {$product->tenant_id}\n";

// Get the tenant
$tenant = Tenant::find($product->tenant_id);
if (!$tenant) {
    echo "❌ Tenant not found!\n";
    exit(1);
}

echo "Tenant: {$tenant->name}\n";
echo str_repeat("=", 70) . "\n\n";

// Test product stock calculation
echo "PRODUCT STOCK DETAILS:\n";
echo str_repeat("-", 70) . "\n";

$currentStock = $product->current_stock;
$stockValue = $product->stock_value;
$reorderLevel = $product->reorder_level ?? 0;

echo "Current Stock:     {$currentStock} pcs\n";
echo "Stock Value:       ₦" . number_format($stockValue, 2) . "\n";
echo "Reorder Level:     {$reorderLevel} pcs\n";
echo "Maintain Stock:    " . ($product->maintain_stock ? 'Yes' : 'No') . "\n";
echo "Is Active:         " . ($product->is_active ? 'Yes' : 'No') . "\n";

if ($currentStock <= 0) {
    echo "Status:            ❌ OUT OF STOCK\n";
} elseif ($reorderLevel && $currentStock <= $reorderLevel) {
    echo "Status:            ⚠️  LOW STOCK\n";
} else {
    echo "Status:            ✅ IN STOCK\n";
}

echo "\n" . str_repeat("=", 70) . "\n\n";

// Test dashboard calculations for this tenant
echo "TENANT DASHBOARD STATISTICS:\n";
echo str_repeat("-", 70) . "\n";

$products = Product::where('tenant_id', $tenant->id)
    ->where('maintain_stock', true)
    ->where('is_active', true)
    ->get();

echo "Total Products (tracked): {$products->count()}\n\n";

$totalStockValue = 0;
$lowStockItems = 0;
$outOfStockItems = 0;
$inStockItems = 0;

echo "Product List:\n";
printf("%-5s %-35s %12s %12s %10s\n", "ID", "Name", "Stock", "Value", "Status");
echo str_repeat("-", 70) . "\n";

foreach ($products as $prod) {
    $stock = $prod->current_stock;
    $value = $prod->stock_value;
    $reorder = $prod->reorder_level ?? 0;

    $totalStockValue += $value;

    // Determine status
    $status = '';
    if ($stock <= 0) {
        $outOfStockItems++;
        $status = '❌ OUT';
    } elseif ($reorder && $stock <= $reorder) {
        $lowStockItems++;
        $status = '⚠️  LOW';
    } else {
        $inStockItems++;
        $status = '✅ OK';
    }

    printf("%-5d %-35s %12.2f ₦%11s %10s\n",
        $prod->id,
        substr($prod->name, 0, 35),
        $stock,
        number_format($value, 2),
        $status
    );
}

echo str_repeat("-", 70) . "\n\n";

// Summary
echo "DASHBOARD CARD STATISTICS:\n";
echo str_repeat("-", 70) . "\n";
echo "✅ In Stock Items:        {$inStockItems}\n";
echo "⚠️  Low Stock Items:       {$lowStockItems}\n";
echo "❌ Out of Stock Items:    {$outOfStockItems}\n";
echo "💰 Total Stock Value:     ₦" . number_format($totalStockValue, 2) . "\n";

echo "\n" . str_repeat("=", 70) . "\n";
echo "✓ Test Complete!\n";
echo "✓ These values should match what you see on the dashboard\n";
echo "✓ Product {$productId} ({$product->name}) with 200 pcs should show as IN STOCK\n";
