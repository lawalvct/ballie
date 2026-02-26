<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Try law-firm tenant (id=28) since law-venture doesn't exist
$tenant = App\Models\Tenant::where('slug', 'law-firm')->first();
echo "Tenant: id={$tenant->id} slug={$tenant->slug} name={$tenant->name}\n\n";
$tenant->makeCurrent();

// Find product 107 in the whole system - check its actual tenant
$globalProduct107 = App\Models\Product::withTrashed()->where('id', 107)->first();
echo "Global product id=107: tenant_id=" . ($globalProduct107->tenant_id ?? 'N/A') . " name=" . ($globalProduct107->name ?? 'N/A') . "\n";

// Find all products belonging to law-firm
$products = App\Models\Product::where('tenant_id', $tenant->id)
    ->get(['id', 'name', 'image_path', 'barcode', 'type', 'is_visible_online']);
echo "Law-firm products (" . $products->count() . "):\n";
foreach ($products as $p) {
    $ip = $p->getRawOriginal('image_path');
    echo "  id={$p->id} name={$p->name} image_path=" . ($ip ?? 'null') . "\n";
}

// Also check each tenant's product 107
echo "\n\nChecking product 107 across all tenants:\n";
foreach (App\Models\Tenant::all() as $t) {
    $p = App\Models\Product::withTrashed()->where('tenant_id', $t->id)->where('id', 107)->first();
    if ($p) {
        $ip = $p->getRawOriginal('image_path');
        echo "  tenant={$t->slug} (id={$t->id}): product id=107 name={$p->name} image_path=" . ($ip ?? 'null') . "\n";
    }
}
