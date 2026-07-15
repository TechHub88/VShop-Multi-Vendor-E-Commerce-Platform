<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sellerId = 30; // Let's check for seller 30
echo "=== SIMULATING getBoostProducts FOR SELLER $sellerId ===\n";

$products = \App\Models\Product::where('created_by_id', $sellerId)
    ->select('id', 'name', 'sku', 'category_id', 'is_boosted', 'price', 'thumbnail')
    ->get();

$boostRules = \App\Models\BoostedListing::all();

foreach ($products as $p) {
    // getBoostRule logic
    $rule = $boostRules->first(function ($r) use ($p) {
        return $r->product_id && $r->product_id == $p->id;
    });

    $matchType = "1. Exact Product Match";

    if (!$rule) {
        $rule = $boostRules->first(function ($r) use ($p, $sellerId) {
            return empty($r->product_id) && $r->category_id && $r->category_id == $p->category_id && $r->seller_id && $r->seller_id == $sellerId;
        });
        $matchType = "2. Category + Seller Match";
    }

    if (!$rule) {
        $rule = $boostRules->first(function ($r) use ($p) {
            return empty($r->product_id) && $r->category_id && $r->category_id == $p->category_id && empty($r->seller_id);
        });
        $matchType = "3. Category + Global Match";
    }

    if (!$rule) {
        $rule = $boostRules->first(function ($r) use ($sellerId) {
            return empty($r->product_id) && empty($r->category_id) && $r->seller_id && $r->seller_id == $sellerId;
        });
        $matchType = "4. Global Seller Match";
    }

    if (!$rule) {
        $rule = $boostRules->first(function ($r) {
            return empty($r->product_id) && empty($r->category_id) && empty($r->seller_id);
        });
        $matchType = "5. Absolute Global Match";
    }

    echo "Product: {$p->name} (ID: {$p->id}, Cat: {$p->category_id})\n";
    if ($rule) {
        echo "  Matched Rule ID: {$rule->id}, Per Day: {$rule->per_day}, Status: {$rule->status}, Match Type: {$matchType}\n";
    } else {
        echo "  No Rule Matched!\n";
    }
}
