<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$res = DB::select('SHOW CREATE TABLE products');
echo $res[0]->{'Create Table'};
