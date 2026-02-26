<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Find the law-venture tenant
$tenant = App\Models\Tenant::where('slug', 'law-venture')->first();
if (!$tenant) {
    echo "Tenant 'law-venture' not found!\n";
    $tenants = App\Models\Tenant::all(['id', 'slug', 'name'])->toArray();
    echo "All tenants:\n";
    foreach ($tenants as $t) {
        echo "  id={$t['id']} slug={$t['slug']} name={$t['name']}\n";
    }
    exit;
}

echo "Found tenant: id={$tenant->id} slug={$tenant->slug} name={$tenant->name}\n";
$tenant->makeCurrent();

// Find product 107 for this tenant
$product = App\Models\Product::where('tenant_id', $tenant->id)->find(107);
if (!$product) {
    // Maybe the product belongs to this tenant but has a different id?
    echo "Product id=107 not found for this tenant!\n";
    $products = App\Models\Product::where('tenant_id', $tenant->id)->limit(5)->get(['id', 'name', 'image_path']);
    echo "First 5 products for this tenant:\n";
    foreach ($products as $p) {
        echo "  id={$p->id} name={$p->name} image_path=" . ($p->image_path ?? 'null') . "\n";
    }
    exit;
}

echo "Product: id={$product->id} name={$product->name} image_path=" . ($product->getRawOriginal('image_path') ?? 'null') . "\n";
echo "Product is_visible_online: " . ($product->getAttributes()['is_visible_online'] ?? 'null') . "\n";

// Check product images
$product->load('images');
echo "Gallery images: " . $product->images->count() . "\n";
foreach ($product->images as $img) {
    echo "  id={$img->id} path={$img->image_path}\n";
}

// Check if image files actually exist (or are problematic)
if ($product->getRawOriginal('image_path')) {
    $path = storage_path('app/public/' . $product->getRawOriginal('image_path'));
    echo "Primary image file exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";
    echo "Path: $path\n";
}

foreach ($product->images as $img) {
    $path = storage_path('app/public/' . $img->image_path);
    echo "Gallery image file exists: " . (file_exists($path) ? 'YES' : 'NO') . " - path: $path\n";
}
