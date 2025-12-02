<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing PFA unique constraint fix...\n\n";

// Check current PFAs
$totalPfas = App\Models\Pfa::count();
$distinctCodes = App\Models\Pfa::distinct('code')->count();
echo "Current PFAs in database: $totalPfas\n";
echo "Distinct codes: $distinctCodes\n\n";

// Try to create a PFA with duplicate code for a different tenant
try {
    $pfa = App\Models\Pfa::create([
        'tenant_id' => 999,
        'name' => 'Test PFA',
        'code' => 'SIBTC',
        'is_active' => true
    ]);

    echo "✓ SUCCESS! Created PFA with code 'SIBTC' for tenant 999\n";
    echo "  This means the unique constraint is now per-tenant!\n\n";

    // Clean up
    $pfa->delete();
    echo "✓ Test PFA deleted\n";

} catch (Exception $e) {
    echo "✗ FAILED: " . $e->getMessage() . "\n";
    echo "  The constraint is still global (not per-tenant)\n";
}
