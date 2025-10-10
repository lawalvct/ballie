<?php

/**
 * Test script to verify POS products are loading correctly with calculated stock
 * Run this with: php test_pos_products.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;

echo "=== POS Products Loading Test ===\n\n";

// Test with product 48's tenant
$product48 = Product::find(48);
if (!$product48) {
    echo "❌ Product ID 48 not found!\n";
    exit(1);
}

$tenant = Tenant::find($product48->tenant_id);
if (!$tenant) {
    echo "❌ Tenant not found!\n";
    exit(1);
}

echo "Testing for Tenant: {$tenant->name} (ID: {$tenant->id})\n";
echo str_repeat("=", 70) . "\n\n";

// Simulate POS controller logic
echo "SIMULATING POS CONTROLLER LOGIC:\n";
echo str_repeat("-", 70) . "\n\n";

// Step 1: Load all active products
echo "Step 1: Loading all active products...\n";
$allProducts = Product::where('tenant_id', $tenant->id)
    ->where('is_active', true)
    ->with(['category', 'primaryUnit'])
    ->orderBy('name')
    ->get();

echo "Total active products found: {$allProducts->count()}\n\n";

// Step 2: Filter by calculated stock
echo "Step 2: Filtering products with stock > 0 (calculated from movements)...\n";
echo str_repeat("-", 70) . "\n";

$productsWithStock = $allProducts->filter(function ($product) {
    $stock = $product->current_stock; // Calculated from movements
    return $stock > 0;
})->values();

echo "Products available for POS: {$productsWithStock->count()}\n\n";

// Display products
if ($productsWithStock->count() > 0) {
    echo "AVAILABLE PRODUCTS FOR POS:\n";
    echo str_repeat("-", 70) . "\n";
    printf("%-5s %-35s %12s %12s %12s\n", "ID", "Name", "Stock", "Price", "Category");
    echo str_repeat("-", 70) . "\n";

    foreach ($productsWithStock as $product) {
        $stock = $product->current_stock;
        $price = $product->sales_rate ?? 0;
        $category = $product->category->name ?? 'Uncategorized';

        printf("%-5d %-35s %12.2f ₦%11s %-20s\n",
            $product->id,
            substr($product->name, 0, 35),
            $stock,
            number_format($price, 2),
            substr($category, 0, 20)
        );
    }
    echo str_repeat("-", 70) . "\n";
} else {
    echo "⚠️  No products with stock available for POS!\n";
}

echo "\n" . str_repeat("=", 70) . "\n";

// Check specific product (ID 48 - Milk)
echo "\nCHECKING PRODUCT ID 48 (MILK):\n";
echo str_repeat("-", 70) . "\n";

$product = Product::find(48);
if ($product) {
    $stock = $product->current_stock;
    $price = $product->sales_rate ?? 0;
    $isActive = $product->is_active;

    echo "Product Name:      {$product->name}\n";
    echo "Current Stock:     {$stock} pcs\n";
    echo "Sales Price:       ₦" . number_format($price, 2) . "\n";
    echo "Is Active:         " . ($isActive ? 'Yes' : 'No') . "\n";
    echo "Should Show in POS: ";

    if ($isActive && $stock > 0) {
        echo "✅ YES\n";
    } else {
        if (!$isActive) {
            echo "❌ NO (Product is inactive)\n";
        } elseif ($stock <= 0) {
            echo "❌ NO (No stock available)\n";
        }
    }
} else {
    echo "❌ Product not found!\n";
}

echo "\n" . str_repeat("=", 70) . "\n";

// Comparison with old method (database column)
echo "\nCOMPARISON: Old Method vs New Method:\n";
echo str_repeat("-", 70) . "\n\n";

echo "OLD METHOD (Direct DB Column Query):\n";
$oldMethod = Product::where('tenant_id', $tenant->id)
    ->where('is_active', true)
    ->where('current_stock', '>', 0)  // Direct database column
    ->count();
echo "Products count: {$oldMethod}\n\n";

echo "NEW METHOD (Calculated from Movements):\n";
echo "Products count: {$productsWithStock->count()}\n\n";

if ($oldMethod === $productsWithStock->count()) {
    echo "✅ Both methods return same count\n";
} else {
    echo "⚠️  Different counts detected!\n";
    echo "   Old method: {$oldMethod} (reading stale database column)\n";
    echo "   New method: {$productsWithStock->count()} (calculating from movements)\n";
    echo "   Difference: " . abs($oldMethod - $productsWithStock->count()) . " products\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "✓ Test Complete!\n";
echo "✓ Products with calculated stock > 0 will now show in POS\n";
echo "✓ Refresh your POS page to see the products\n";
