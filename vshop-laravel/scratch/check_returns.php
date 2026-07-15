<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = \Schema::getColumnListing('order_returns');
echo "=== ORDER RETURNS COLUMNS ===\n";
foreach ($cols as $col) {
    echo "  - $col\n";
}

// Get first 3 rows
$rows = \DB::table('order_returns')->limit(3)->get();
echo "\n=== ORDER RETURNS SAMPLE DATA ===\n";
print_r($rows->toArray());
