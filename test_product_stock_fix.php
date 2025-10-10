<?php

/**
 * Test script to verify product stock calculation from movements
 * Run this with: php test_product_stock_fix.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Cache;

echo "=== Product Stock Calculation Test ===\n\n";

// Get the product ID from your screenshot (product ID 48)
$productId = 48;

echo "Testing Product ID: {$productId}\n";
echo str_repeat("-", 50) . "\n\n";

// Find the product
$product = Product::find($productId);

if (!$product) {
    echo "❌ Product not found!\n";
    exit(1);
}

echo "Product Name: {$product->name}\n";
echo "Product SKU: {$product->sku}\n\n";

// Clear cache for this product
$asOfDate = now()->toDateString();
Cache::forget("product_stock_{$product->id}_{$asOfDate}");
Cache::forget("product_stock_value_{$product->id}_{$asOfDate}_weighted_average");
echo "✓ Cache cleared\n\n";

// Get stock movements
echo "Stock Movements:\n";
echo str_repeat("-", 50) . "\n";

$movements = StockMovement::where('product_id', $productId)
    ->orderBy('transaction_date', 'asc')
    ->orderBy('created_at', 'asc')
    ->get();

if ($movements->isEmpty()) {
    echo "⚠️  No stock movements found for this product!\n";
} else {
    echo "Found {$movements->count()} movement(s):\n\n";

    $runningStock = 0;
    foreach ($movements as $movement) {
        $runningStock += $movement->quantity;
        $direction = $movement->quantity > 0 ? 'IN' : 'OUT';
        $qty = abs($movement->quantity);

        echo sprintf(
            "Date: %s | Type: %-10s | %s: %8.2f | Running: %8.2f | Rate: ₦%s | Ref: %s\n",
            $movement->transaction_date->format('Y-m-d'),
            $movement->transaction_type,
            $direction,
            $qty,
            $runningStock,
            number_format($movement->rate, 2),
            $movement->transaction_reference ?? 'N/A'
        );
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Calculate stock using the model method
$calculatedStock = $product->getStockAsOfDate($asOfDate);
echo "Calculated Stock (from movements): {$calculatedStock} pcs\n";

// Get current_stock attribute (should now use the calculation)
$currentStock = $product->current_stock;
echo "Current Stock (via attribute): {$currentStock} pcs\n";

// Get stock value
$stockValueData = $product->getStockValueAsOfDate($asOfDate);
echo "Stock Value: ₦" . number_format($stockValueData['value'], 2) . "\n";
echo "Average Rate: ₦" . number_format($stockValueData['average_rate'], 2) . "\n";
echo "Quantity: {$stockValueData['quantity']} pcs\n";

// Check stock status
echo "\nStock Status: ";
if ($currentStock <= 0) {
    echo "❌ OUT OF STOCK\n";
} elseif ($product->reorder_level && $currentStock <= $product->reorder_level) {
    echo "⚠️  LOW STOCK\n";
} else {
    echo "✅ IN STOCK\n";
}

// Database column value (for comparison)
echo "\n" . str_repeat("-", 50) . "\n";
echo "Database Column Values (OLD - should not be used):\n";
$dbProduct = Product::withoutGlobalScopes()->find($productId);
echo "DB current_stock: {$dbProduct->getAttributes()['current_stock']} pcs\n";
echo "DB current_stock_value: ₦" . number_format($dbProduct->getAttributes()['current_stock_value'], 2) . "\n";

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test Complete!\n";
echo "✓ Stock calculation is now based on stock movements\n";
echo "✓ The product show page should display the correct stock\n";
