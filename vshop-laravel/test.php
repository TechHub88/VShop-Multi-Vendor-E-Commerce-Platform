<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $seller = App\Models\Seller::where('status', 'approved')->first();
    Auth::guard('seller')->login($seller);
    $req = Request::create('/seller/products/campaign-participation/all', 'GET');
    $res = app()->handle($req);
    echo $res->getContent();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine();
}
