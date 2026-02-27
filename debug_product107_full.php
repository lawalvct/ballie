<?php
/**
 * Debug script to simulate the exact HTTP request for product 107
 * and capture the full error trace
 */

// Disable output buffering
ob_start();

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Get first active tenant with slug 'law-venture' (or any working tenant)
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// We need to run through the full HTTP stack to reproduce the error
// Let's directly call the controller method with the right context

$kernel->bootstrap();

// Find the tenant
$tenant = App\Models\Tenant::where('id', 30)->first();
if (!$tenant) {
    $tenants = App\Models\Tenant::all(['id', 'slug', 'name'])->toArray();
    echo "Tenant 30 not found. Available tenants:\n";
    print_r($tenants);
    exit;
}

echo "Tenant: {$tenant->name} (id={$tenant->id}, slug={$tenant->slug})\n";

// Make the tenant current
$tenant->makeCurrent();

// Find product 107
$product = App\Models\Product::find(107);
if (!$product) {
    echo "Product 107 not found!\n";
    exit;
}

echo "Product: {$product->name} (tenant_id={$product->tenant_id})\n\n";

// Now simulate the full show() method with detailed error catching
try {
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    });

    $product->load(['category', 'primaryUnit', 'stockAssetAccount', 'salesAccount', 'purchaseAccount', 'images']);

    $asOfDate = now()->toDateString();

    echo "Calling getStockAsOfDate...\n";
    $product->calculated_stock = $product->getStockAsOfDate($asOfDate);
    echo "calculated_stock = " . $product->calculated_stock . "\n";

    echo "Calling getStockValueAsOfDate...\n";
    $product->calculated_stock_value = $product->getStockValueAsOfDate($asOfDate);
    echo "calculated_stock_value type = " . gettype($product->calculated_stock_value) . "\n";
    echo "calculated_stock_value = " . json_encode($product->calculated_stock_value) . "\n";

    echo "\nCalling formatProductResponse...\n";

    // Simulate formatProductResponse
    $response = [
        'id' => $product->id,
        'type' => $product->type,
        'name' => $product->name,
        'sku' => $product->sku,
        'slug' => $product->slug,
        'description' => $product->description,
        'brand' => $product->brand,
        'hsn_code' => $product->hsn_code,
        'barcode' => $product->barcode,
        'purchase_rate' => (float) $product->purchase_rate,
        'sales_rate' => (float) $product->sales_rate,
        'mrp' => (float) $product->mrp,
        'tax_rate' => (float) $product->tax_rate,
        'current_stock' => (float) ($product->calculated_stock ?? $product->current_stock),
        'opening_stock' => (float) $product->opening_stock,
        'reorder_level' => (float) $product->reorder_level,
        'maintain_stock' => (bool) $product->maintain_stock,
        'is_active' => (bool) $product->is_active,
        'is_visible_online' => (bool) $product->is_visible_online,
        'is_featured' => (bool) $product->is_featured,
        'category' => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name] : null,
        'primary_unit' => $product->primaryUnit ? ['id' => $product->primaryUnit->id, 'name' => $product->primaryUnit->name, 'short_name' => $product->primaryUnit->symbol] : null,
    ];
    echo "Basic fields OK\n";

    // includeDetails = true section
    $meta_title = $product->meta_title;
    $meta_description = $product->meta_description;
    echo "meta_title: " . ($meta_title ?? 'null') . "\n";

    echo "stock_value cast: ";
    $stockValue = (float) ($product->calculated_stock_value ?? 0);
    echo "(float) of array = $stockValue\n";

    echo "stock_asset_account: ";
    $saa = $product->stockAssetAccount ? [
        'id' => $product->stockAssetAccount->id,
        'name' => $product->stockAssetAccount->name,
        'account_code' => $product->stockAssetAccount->code,
    ] : null;
    echo ($saa ? json_encode($saa) : 'null') . "\n";

    echo "primary_image: ";
    $pi = $product->image_path ? Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) : null;
    echo ($pi ?? 'null') . "\n";

    echo "gallery_images: ";
    $gi = $product->images->map(function ($image) {
        return [
            'id' => $image->id,
            'url' => $image->image_url,
            'sort_order' => $image->sort_order,
        ];
    });
    echo json_encode($gi) . "\n";

    echo "\nAll steps completed successfully!\n";
    echo "JSON result:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n";

} catch (Throwable $e) {
    echo "\n=== ERROR CAUGHT ===\n";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

// Clean up debug file
echo "\n--- done ---\n";
