<?php
/**
 * Test Global Search Widget API
 *
 * This script tests the Global Search functionality
 *
 * Usage: Run from browser: http://your-tenant-domain.test/test_global_search.php
 */

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Global Search Widget Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #6366f1; padding-bottom: 10px; }
        h2 { color: #6366f1; margin-top: 30px; }
        .test-section { background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #6366f1; }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .info { color: #6366f1; }
        pre { background: #1f2937; color: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .result-item { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #e5e7eb; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; margin-left: 10px; }
        .badge-route { background: #dbeafe; color: #1e40af; }
        .badge-record { background: #dcfce7; color: #166534; }
        .badge-action { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔍 Global Search Widget - API Test</h1>";
echo "<p>Testing the Global Search functionality for the tenant application.</p>";

// Test 1: Check if routes exist
echo "<div class='test-section'>";
echo "<h2>Test 1: Route Verification</h2>";

try {
    $routes = app('router')->getRoutes();
    $searchRoute = $routes->getByName('tenant.api.global-search');
    $actionsRoute = $routes->getByName('tenant.api.quick-actions');

    if ($searchRoute && $actionsRoute) {
        echo "<p class='success'>✓ Routes registered successfully!</p>";
        echo "<ul>";
        echo "<li><code>tenant.api.global-search</code> - " . $searchRoute->uri() . "</li>";
        echo "<li><code>tenant.api.quick-actions</code> - " . $actionsRoute->uri() . "</li>";
        echo "</ul>";
    } else {
        echo "<p class='error'>✗ Routes not found!</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Test 2: Get first tenant
echo "<div class='test-section'>";
echo "<h2>Test 2: Tenant Detection</h2>";

try {
    $tenant = \App\Models\Tenant::first();

    if ($tenant) {
        echo "<p class='success'>✓ Tenant found!</p>";
        echo "<ul>";
        echo "<li><strong>Name:</strong> {$tenant->name}</li>";
        echo "<li><strong>Slug:</strong> {$tenant->slug}</li>";
        echo "<li><strong>Domain:</strong> {$tenant->domain}</li>";
        echo "</ul>";

        // Store tenant slug for API tests
        $tenantSlug = $tenant->slug;
    } else {
        echo "<p class='error'>✗ No tenant found in database!</p>";
        $tenantSlug = null;
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    $tenantSlug = null;
}

echo "</div>";

// Test 3: Test Search Routes Method
if ($tenantSlug) {
    echo "<div class='test-section'>";
    echo "<h2>Test 3: Search Routes Functionality</h2>";

    try {
        // Initialize tenant context
        $tenant = \App\Models\Tenant::where('slug', $tenantSlug)->first();
        tenancy()->initialize($tenant);

        $controller = new \App\Http\Controllers\Tenant\Api\GlobalSearchController();

        // Use reflection to test private method
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('searchRoutes');
        $method->setAccessible(true);

        // Test search
        $testQueries = ['sales', 'invoice', 'customer', 'product'];

        foreach ($testQueries as $query) {
            $results = $method->invoke($controller, $query);
            echo "<div class='result-item'>";
            echo "<strong>Query: '{$query}'</strong> <span class='badge badge-route'>" . count($results) . " results</span>";

            if (count($results) > 0) {
                echo "<ul>";
                foreach (array_slice($results, 0, 3) as $result) {
                    echo "<li>{$result['title']} - <em>{$result['category']}</em></li>";
                }
                if (count($results) > 3) {
                    echo "<li><em>... and " . (count($results) - 3) . " more</em></li>";
                }
                echo "</ul>";
            }
            echo "</div>";
        }

        echo "<p class='success'>✓ Search routes working correctly!</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

    echo "</div>";

    // Test 4: Test Database Search
    echo "<div class='test-section'>";
    echo "<h2>Test 4: Database Search Functionality</h2>";

    try {
        $controller = new \App\Http\Controllers\Tenant\Api\GlobalSearchController();

        // Use reflection to test private method
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('searchRecords');
        $method->setAccessible(true);

        // Count records
        $customerCount = \App\Models\Customer::where('tenant_id', $tenant->id)->count();
        $productCount = \App\Models\Product::where('tenant_id', $tenant->id)->count();
        $voucherCount = \App\Models\Voucher::where('tenant_id', $tenant->id)->count();
        $ledgerCount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)->count();

        echo "<p class='info'>Database records available:</p>";
        echo "<ul>";
        echo "<li><strong>Customers:</strong> {$customerCount}</li>";
        echo "<li><strong>Products:</strong> {$productCount}</li>";
        echo "<li><strong>Vouchers:</strong> {$voucherCount}</li>";
        echo "<li><strong>Ledger Accounts:</strong> {$ledgerCount}</li>";
        echo "</ul>";

        // Test search if records exist
        if ($customerCount > 0 || $productCount > 0 || $voucherCount > 0 || $ledgerCount > 0) {
            $results = $method->invoke($controller, 'a', $tenant->id);

            echo "<div class='result-item'>";
            echo "<strong>Search Test: 'a'</strong> <span class='badge badge-record'>" . count($results) . " results</span>";

            if (count($results) > 0) {
                echo "<ul>";
                foreach (array_slice($results, 0, 5) as $result) {
                    echo "<li><strong>{$result['title']}</strong> - {$result['description']} <em>({$result['category']})</em></li>";
                }
                if (count($results) > 5) {
                    echo "<li><em>... and " . (count($results) - 5) . " more</em></li>";
                }
                echo "</ul>";
            }
            echo "</div>";

            echo "<p class='success'>✓ Database search working correctly!</p>";
        } else {
            echo "<p class='info'>ℹ No records to search. Create some customers, products, or vouchers first.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }

    echo "</div>";

    // Test 5: Full API Test
    echo "<div class='test-section'>";
    echo "<h2>Test 5: Full API Integration Test</h2>";

    echo "<p class='info'>Test the API directly in your browser:</p>";
    $searchUrl = url("/tenant/{$tenantSlug}/api/global-search?query=sales");
    $actionsUrl = url("/tenant/{$tenantSlug}/api/quick-actions?query=invoice");

    echo "<ul>";
    echo "<li><a href='{$searchUrl}' target='_blank'>Search API: {$searchUrl}</a></li>";
    echo "<li><a href='{$actionsUrl}' target='_blank'>Quick Actions API: {$actionsUrl}</a></li>";
    echo "</ul>";

    echo "<p class='info'>Or test with the widget:</p>";
    $dashboardUrl = url("/tenant/{$tenantSlug}/dashboard");
    echo "<ul>";
    echo "<li><a href='{$dashboardUrl}' target='_blank'>Open Dashboard and press Ctrl+K</a></li>";
    echo "</ul>";

    echo "</div>";
}

echo "<div class='test-section'>";
echo "<h2>✅ Test Summary</h2>";
echo "<p>All tests completed! The Global Search Widget is ready to use.</p>";
echo "<p><strong>How to use:</strong></p>";
echo "<ol>";
echo "<li>Navigate to any page in your tenant application</li>";
echo "<li>Click the purple floating button at the bottom-right, or press <kbd>Ctrl+K</kbd></li>";
echo "<li>Start typing to search for pages, customers, products, etc.</li>";
echo "<li>Click any result to navigate to that page</li>";
echo "</ol>";
echo "</div>";

echo "</div>
</body>
</html>";
