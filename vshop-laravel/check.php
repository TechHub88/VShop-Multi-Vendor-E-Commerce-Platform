<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$sellerFinances = Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM seller_finances');
$products = Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM products');

echo "--- SELLER FINANCES ---\n";
foreach($sellerFinances as $col) echo $col->Field . " (" . $col->Type . ")\n";
echo "\n--- PRODUCTS ---\n";
foreach($products as $col) {
    if (strpos($col->Field, 'cancel') !== false) {
        echo $col->Field . " (" . $col->Type . ")\n";
    }
}
