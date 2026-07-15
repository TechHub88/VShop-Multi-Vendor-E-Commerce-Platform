<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== BOOST RULES IN DATABASE ===\n";
$rules = \App\Models\BoostedListing::get();
foreach ($rules as $r) {
    echo "ID: {$r->id}, Product ID: " . ($r->product_id ?? 'NULL') . ", Category ID: " . ($r->category_id ?? 'NULL') . ", Seller ID: " . ($r->seller_id ?? 'NULL') . ", Per Day: {$r->per_day}, Status: {$r->status}, Start: " . ($r->start_date ?? 'NULL') . ", End: " . ($r->end_date ?? 'NULL') . "\n";
}

echo "\n=== PRODUCT CATEGORIES IN PRODUCTS TABLE ===\n";
$prods = \App\Models\Product::take(10)->get();
foreach ($prods as $p) {
    echo "ID: {$p->id}, Name: {$p->name}, Category ID: " . ($p->category_id ?? 'NULL') . ", Created By: " . ($p->created_by_id ?? 'NULL') . "\n";
}

echo "\n=== MARKETPLACE CATEGORIES ===\n";
$cats = \Illuminate\Support\Facades\DB::table('marketplace_categories')->take(10)->get();
foreach ($cats as $c) {
    echo "ID: {$c->id}, Name: {$c->name}\n";
}
