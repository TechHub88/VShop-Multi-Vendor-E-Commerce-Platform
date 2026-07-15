<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check customers table columns
$cols = \Schema::getColumnListing('customers');
echo "=== CUSTOMERS TABLE COLUMNS ===\n";
foreach ($cols as $col) {
    echo "  - $col\n";
}

// Check if is_blocked exists
$hasBlocked = in_array('is_blocked', $cols);
echo "\nis_blocked exists: " . ($hasBlocked ? 'YES' : 'NO') . "\n";

// Check if status exists
$hasStatus = in_array('status', $cols);
echo "status exists: " . ($hasStatus ? 'YES' : 'NO') . "\n";
