<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check sellers table columns
$cols = \Schema::getColumnListing('sellers');
echo "=== SELLERS TABLE COLUMNS ===\n";
foreach ($cols as $col) {
    echo "  - $col\n";
}
echo "\nTotal columns: " . count($cols) . "\n";

// Check if specific columns exist
$needed = ['id', 'name', 'email', 'mobile', 'type', 'status', 'created_at', 'updated_at'];
echo "\n=== CHECKING NEEDED COLUMNS ===\n";
foreach ($needed as $col) {
    $exists = in_array($col, $cols) ? 'YES' : 'NO';
    echo "  $col: $exists\n";
}

// Also check boosted_listings table
$blExists = \Schema::hasTable('boosted_listings');
echo "\n=== boosted_listings table exists: " . ($blExists ? 'YES' : 'NO') . " ===\n";
if ($blExists) {
    $blCols = \Schema::getColumnListing('boosted_listings');
    echo "boosted_listings columns: " . implode(', ', $blCols) . "\n";
    echo "Has boost_amount: " . (in_array('boost_amount', $blCols) ? 'YES' : 'NO') . "\n";
    echo "Has seller_id: " . (in_array('seller_id', $blCols) ? 'YES' : 'NO') . "\n";
}

// Check product_reviews table
$prExists = \Schema::hasTable('product_reviews');
echo "\n=== product_reviews table exists: " . ($prExists ? 'YES' : 'NO') . " ===\n";
if ($prExists) {
    $prCols = \Schema::getColumnListing('product_reviews');
    echo "product_reviews columns: " . implode(', ', $prCols) . "\n";
    echo "Has seller_id: " . (in_array('seller_id', $prCols) ? 'YES' : 'NO') . "\n";
}

// Check orders table
$orderCols = \Schema::getColumnListing('orders');
echo "\norders has return_status: " . (in_array('return_status', $orderCols) ? 'YES' : 'NO') . "\n";
echo "orders has deleted_at: " . (in_array('deleted_at', $orderCols) ? 'YES' : 'NO') . "\n";

// Check customers table for is_blocked column
$custCols = \Schema::getColumnListing('customers');
echo "\n=== CUSTOMERS TABLE ===\n";
echo "Has is_blocked: " . (in_array('is_blocked', $custCols) ? 'YES' : 'NO') . "\n";
