<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$p = App\Models\Product::with('images')->find(107);
if (!$p) {
    echo "Not found\n";
    exit;
}

echo "=== Product 107 ===\n";
echo "name: " . $p->getAttributes()['name'] . "\n";
echo "type: " . $p->getAttributes()['type'] . "\n";
echo "image_path: " . ($p->getAttributes()['image_path'] ?? 'null') . "\n";
echo "barcode: " . ($p->getAttributes()['barcode'] ?? 'null') . "\n";
echo "description: " . substr($p->getAttributes()['description'] ?? '', 0, 200) . "\n";
echo "tenant_id: " . $p->getAttributes()['tenant_id'] . "\n";

echo "\n=== Gallery Images ===\n";
echo "count: " . $p->images->count() . "\n";
foreach ($p->images as $img) {
    echo "  id=" . $img->id . " path=" . $img->image_path . "\n";
}

echo "\n=== Try image_url accessor ===\n";
try {
    foreach ($p->images as $img) {
        echo "  image_url: " . $img->image_url . "\n";
    }
    echo "image_url accessor OK\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Try getStockAsOfDate ===\n";
try {
    $stock = $p->getStockAsOfDate(now()->toDateString());
    echo "stock: " . $stock . "\n";
} catch (Throwable $e) {
    echo "ERROR in getStockAsOfDate: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Try getStockValueAsOfDate ===\n";
try {
    $val = $p->getStockValueAsOfDate(now()->toDateString());
    echo "value: " . json_encode($val) . "\n";
} catch (Throwable $e) {
    echo "ERROR in getStockValueAsOfDate: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Try primary_image storage URL ===\n";
try {
    $url = $p->getAttributes()['image_path'] ? Illuminate\Support\Facades\Storage::disk('public')->url($p->getAttributes()['image_path']) : 'null (no image)';
    echo "primary_image url: " . $url . "\n";
} catch (Throwable $e) {
    echo "ERROR in primary_image: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
