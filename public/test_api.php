<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Force login as Seller ID 1 (Seller One) or first seller
$user = \App\Models\Seller::where('id', 1)->first() ?: \App\Models\Seller::first();
if ($user) {
    auth('seller')->login($user);
}

// Request the boost products route
$req = Illuminate\Http\Request::create('/seller/products/boost-products', 'GET');
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$res = $httpKernel->handle($req);

header('Content-Type: application/json');
echo $res->getContent();
