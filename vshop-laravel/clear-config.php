<?php
// Quick config clear + cache clear for Hostinger
// Access via: https://vshop.vridhee.com/clear-config.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<pre>";
echo "=== Clearing Config Cache ===\n";
Artisan::call('config:clear');
echo Artisan::output();

echo "\n=== Clearing Application Cache ===\n";
Artisan::call('cache:clear');
echo Artisan::output();

echo "\n=== Clearing Route Cache ===\n";
Artisan::call('route:clear');
echo Artisan::output();

echo "\n=== Clearing View Cache ===\n";
Artisan::call('view:clear');
echo Artisan::output();

echo "\n=== Current Config ===\n";
echo "CACHE_STORE: " . config('cache.default') . "\n";
echo "SESSION_DRIVER: " . config('session.driver') . "\n";
echo "\n✅ DONE! All caches cleared.\n";
echo "</pre>";
