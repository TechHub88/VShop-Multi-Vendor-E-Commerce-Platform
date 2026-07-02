<?php
// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Product;

header('Content-Type: text/plain');

echo "=== CART ITEMS ===\n";
$cartItems = DB::table('cart_items')->get();
$cartProductIds = [];
foreach ($cartItems as $item) {
    echo "Cart Item ID: {$item->id} | Product ID: {$item->product_id}\n";
    $cartProductIds[] = (string) $item->product_id;
}
$cartProductIds = array_unique($cartProductIds);
echo "Unique Cart Product IDs: " . implode(', ', $cartProductIds) . "\n\n";

echo "=== ALL RULES ===\n";
$allRules = DB::table('recommendation_rules')->get();
foreach ($allRules as $rule) {
    echo "Rule ID: {$rule->id} | Type: {$rule->type} | Source: {$rule->source} | Source Type: {$rule->source_type} | Targets: {$rule->targets} | Active: {$rule->is_active}\n";
}
echo "\n";

echo "=== MATCHING RULES ===\n";
$rules = DB::table('recommendation_rules')
    ->where('is_active', 1)
    ->where('type', 'product')
    ->where(function($q) use ($cartProductIds) {
        if (!empty($cartProductIds)) {
            $q->whereIn('source', $cartProductIds)
              ->orWhere('source', 'global');
        } else {
            $q->where('source', 'global');
        }
    })
    ->get();
echo "Rules Found: " . $rules->count() . "\n";
$recommendedProductIds = [];
foreach ($rules as $rule) {
    echo "Matched Rule ID: {$rule->id} | Source: {$rule->source} | Targets: {$rule->targets}\n";
    $targets = $rule->targets;
    if (is_string($targets)) {
        $targets = json_decode($targets, true);
    }
    if (is_array($targets)) {
        foreach ($targets as $target) {
            $recommendedProductIds[] = (string) $target;
        }
    }
}
$recommendedProductIds = array_unique($recommendedProductIds);
echo "Decoded Targets: " . implode(', ', $recommendedProductIds) . "\n\n";

echo "=== TARGET PRODUCTS DETAILS ===\n";
$products = Product::whereIn('id', $recommendedProductIds)->get();
echo "Total target products in DB: " . $products->count() . "\n";
foreach ($products as $p) {
    echo "Product ID: {$p->id} | Name: {$p->name} | Status: {$p->status} | Created By: {$p->created_by} | Type: {$p->type} | Price: {$p->price}\n";
}
echo "\n";

echo "=== FILTERED TARGET PRODUCTS ===\n";
$filteredProducts = Product::whereIn('id', $recommendedProductIds)
    ->where(function($q) {
        $q->whereNull('created_by')
          ->orWhere(function($sub) {
              $sub->where('created_by', 'seller')->where('status', 'approved');
          });
    })
    ->get();
echo "Filtered approved target products: " . $filteredProducts->count() . "\n";
foreach ($filteredProducts as $p) {
    echo "Approved ID: {$p->id} | Name: {$p->name}\n";
}
