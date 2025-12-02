<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking shift_schedules table indexes...\n\n";

$indexes = DB::select('SHOW INDEX FROM shift_schedules');

echo "All indexes on shift_schedules table:\n";
echo str_repeat('-', 80) . "\n";

foreach ($indexes as $index) {
    if (strpos(strtolower($index->Column_name), 'code') !== false ||
        strpos(strtolower($index->Key_name), 'code') !== false) {
        echo "Key Name: {$index->Key_name}\n";
        echo "Column: {$index->Column_name}\n";
        echo "Non Unique: {$index->Non_unique}\n";
        echo "Index Type: {$index->Index_type}\n";
        echo str_repeat('-', 80) . "\n";
    }
}
