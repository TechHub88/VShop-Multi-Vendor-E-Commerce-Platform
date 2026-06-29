<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap(); // Boot the framework completely

$req = Illuminate\Http\Request::create('/seller/products/boost/products', 'GET');
$user = App\Models\Seller::first();
if ($user) {
    auth('seller')->login($user);
}

$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$res = $httpKernel->handle($req);
echo $res->getContent();
