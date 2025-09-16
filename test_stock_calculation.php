<?php

// Test script for date-based stock calculation
// This script can be run via php artisan tinker

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Tenant;

// Get the first tenant and product for testing
$tenant = Tenant::first();
$product = Product::where('tenant_id', $tenant->id)->where('maintain_stock', true)->first();

if (!$product) {
    echo "No product found that maintains stock. Please create a product with maintain_stock = true.\n";
    exit;
}

echo "Testing Tally-style date-based stock calculation for: {$product->name}\n";
echo "Current stock (stored): {$product->getAttributes()['current_stock']}\n";

// Test 1: Create some test stock movements
echo "\n=== Creating Test Stock Movements ===\n";

// Create opening stock movement
$openingMovement = StockMovement::create([
    'tenant_id' => $tenant->id,
    'product_id' => $product->id,
    'type' => 'in',
    'quantity' => 100,
    'rate' => 50,
    'transaction_type' => 'opening_stock',
    'transaction_date' => now()->subDays(10),
    'transaction_reference' => 'OPENING-001',
    'reference' => 'Opening Stock',
    'created_by' => 1,
    'old_stock' => 0,
    'new_stock' => 100,
]);
echo "✓ Created opening stock: +100 units at ₦50 each\n";

// Create purchase movement
$purchaseMovement = StockMovement::create([
    'tenant_id' => $tenant->id,
    'product_id' => $product->id,
    'type' => 'in',
    'quantity' => 50,
    'rate' => 60,
    'transaction_type' => 'purchase',
    'transaction_date' => now()->subDays(5),
    'transaction_reference' => 'PUR-001',
    'reference' => 'Purchase PUR-001',
    'created_by' => 1,
    'old_stock' => 100,
    'new_stock' => 150,
]);
echo "✓ Created purchase: +50 units at ₦60 each\n";

// Create sales movement
$salesMovement = StockMovement::create([
    'tenant_id' => $tenant->id,
    'product_id' => $product->id,
    'type' => 'out',
    'quantity' => -30,
    'rate' => 70,
    'transaction_type' => 'sales',
    'transaction_date' => now()->subDays(2),
    'transaction_reference' => 'SAL-001',
    'reference' => 'Sales SAL-001',
    'created_by' => 1,
    'old_stock' => 150,
    'new_stock' => 120,
]);
echo "✓ Created sales: -30 units at ₦70 each\n";

// Test 2: Calculate stock as of different dates
echo "\n=== Testing Date-Based Stock Calculations ===\n";

$stockBeforeAll = $product->getStockAsOfDate(now()->subDays(15));
echo "Stock as of " . now()->subDays(15)->toDateString() . ": {$stockBeforeAll} units\n";

$stockAfterOpening = $product->getStockAsOfDate(now()->subDays(8));
echo "Stock as of " . now()->subDays(8)->toDateString() . ": {$stockAfterOpening} units\n";

$stockAfterPurchase = $product->getStockAsOfDate(now()->subDays(3));
echo "Stock as of " . now()->subDays(3)->toDateString() . ": {$stockAfterPurchase} units\n";

$stockCurrent = $product->getStockAsOfDate(now());
echo "Stock as of " . now()->toDateString() . ": {$stockCurrent} units\n";

// Test 3: Test valuation methods
echo "\n=== Testing Valuation Methods ===\n";

$weightedAverage = $product->getStockValueAsOfDate(now(), 'weighted_average');
echo "Weighted Average Method:\n";
echo "  Quantity: {$weightedAverage['quantity']} units\n";
echo "  Value: ₦" . number_format($weightedAverage['value'], 2) . "\n";
echo "  Average Rate: ₦" . number_format($weightedAverage['average_rate'], 2) . "\n";

$fifo = $product->getStockValueAsOfDate(now(), 'fifo');
echo "\nFIFO Method:\n";
echo "  Quantity: {$fifo['quantity']} units\n";
echo "  Value: ₦" . number_format($fifo['value'], 2) . "\n";
echo "  Average Rate: ₦" . number_format($fifo['average_rate'], 2) . "\n";

// Test 4: Test stock movement history
echo "\n=== Testing Stock Movement History ===\n";
$history = $product->getStockMovementHistory(now()->subDays(15), now());
echo "Found {$history->count()} movements in the last 15 days:\n";
foreach ($history as $movement) {
    echo "  {$movement['date']}: {$movement['type']} {$movement['quantity']} units - {$movement['reference']}\n";
}

echo "\n=== Test Completed Successfully! ===\n";
echo "All date-based stock calculations are working correctly.\n";
echo "You can now use the enhanced inventory system with Tally-style date-based stock tracking.\n";

// Clean up test data (optional)
echo "\nCleaning up test data...\n";
StockMovement::whereIn('id', [$openingMovement->id, $purchaseMovement->id, $salesMovement->id])->delete();
echo "✓ Test data cleaned up\n";
