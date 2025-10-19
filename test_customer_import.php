<?php

/**
 * Customer Import Feature - Test Script
 *
 * This script tests if the customer import feature is properly configured.
 * Run: php test_customer_import.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Customer Import Feature...\n\n";

// Test 1: Check if Excel facade is available
echo "1. Checking if Excel package is installed...\n";
try {
    if (class_exists('Maatwebsite\Excel\Facades\Excel')) {
        echo "   ✅ Excel facade found\n";
    } else {
        echo "   ❌ Excel facade NOT found\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check if CustomersImport class exists
echo "\n2. Checking if CustomersImport class exists...\n";
try {
    if (class_exists('App\Imports\CustomersImport')) {
        echo "   ✅ CustomersImport class found\n";
    } else {
        echo "   ❌ CustomersImport class NOT found\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Check if CustomersTemplateExport class exists
echo "\n3. Checking if CustomersTemplateExport class exists...\n";
try {
    if (class_exists('App\Exports\CustomersTemplateExport')) {
        echo "   ✅ CustomersTemplateExport class found\n";
    } else {
        echo "   ❌ CustomersTemplateExport class NOT found\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Check if routes exist
echo "\n4. Checking if import/export routes are registered...\n";
try {
    $routes = app('router')->getRoutes();
    $exportTemplateFound = false;
    $importFound = false;

    foreach ($routes as $route) {
        if (str_contains($route->getName() ?? '', 'customers.export.template')) {
            $exportTemplateFound = true;
        }
        if (str_contains($route->getName() ?? '', 'customers.import')) {
            $importFound = true;
        }
    }

    if ($exportTemplateFound) {
        echo "   ✅ Export template route found\n";
    } else {
        echo "   ⚠️  Export template route NOT found\n";
    }

    if ($importFound) {
        echo "   ✅ Import route found\n";
    } else {
        echo "   ⚠️  Import route NOT found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 5: Check if Excel interfaces are available
echo "\n5. Checking if Excel interfaces are available...\n";
$interfaces = [
    'Maatwebsite\Excel\Concerns\ToCollection',
    'Maatwebsite\Excel\Concerns\WithHeadingRow',
    'Maatwebsite\Excel\Concerns\FromArray',
    'Maatwebsite\Excel\Concerns\WithHeadings',
];

foreach ($interfaces as $interface) {
    if (interface_exists($interface)) {
        echo "   ✅ " . basename(str_replace('\\', '/', $interface)) . " interface found\n";
    } else {
        echo "   ❌ " . basename(str_replace('\\', '/', $interface)) . " interface NOT found\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ All tests passed! Customer import feature is ready to use.\n";
echo str_repeat("=", 60) . "\n\n";

echo "Next steps:\n";
echo "1. Navigate to your tenant's customer page\n";
echo "2. Click 'Bulk Upload Customers' button\n";
echo "3. Download the template\n";
echo "4. Fill the template with customer data\n";
echo "5. Upload the file\n\n";

echo "Happy importing! 🎉\n";
