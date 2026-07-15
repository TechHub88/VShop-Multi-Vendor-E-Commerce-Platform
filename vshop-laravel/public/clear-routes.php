<?php
/**
 * Emergency Cache Clear Script
 * Access this directly: https://vshop.vridhee.com/clear-routes.php
 * This bypasses Laravel routing entirely to fix cached route issues.
 */

// Delete the cached routes file directly
$cachePaths = [
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
    __DIR__ . '/../bootstrap/cache/routes.php',
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
    __DIR__ . '/../bootstrap/cache/services.php',
];

$results = [];

foreach ($cachePaths as $path) {
    if (file_exists($path)) {
        if (@unlink($path)) {
            $results[] = "✅ Deleted: " . basename($path);
        } else {
            $results[] = "❌ Failed to delete: " . basename($path);
        }
    } else {
        $results[] = "⏭️ Not found (OK): " . basename($path);
    }
}

// Also try running artisan commands
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    Illuminate\Support\Facades\Artisan::call('route:clear');
    $results[] = "✅ Artisan route:clear executed";
    
    Illuminate\Support\Facades\Artisan::call('config:clear');
    $results[] = "✅ Artisan config:clear executed";
    
    Illuminate\Support\Facades\Artisan::call('cache:clear');
    $results[] = "✅ Artisan cache:clear executed";
    
    Illuminate\Support\Facades\Artisan::call('view:clear');
    $results[] = "✅ Artisan view:clear executed";

    // Run pending migrations
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $results[] = "✅ Migrations executed";
    $results[] = Illuminate\Support\Facades\Artisan::output();
    
} catch (Exception $e) {
    $results[] = "⚠️ Artisan error (non-fatal): " . $e->getMessage();
}

echo "<html><head><title>Cache Clear</title></head><body style='font-family:monospace;padding:40px;'>";
echo "<h2>🔧 Emergency Cache Clear Results</h2><hr>";
foreach ($results as $r) {
    echo "<p>" . $r . "</p>";
}
echo "<hr><p><strong>Now try:</strong> <a href='/admin/customers/block-control'>/admin/customers/block-control</a></p>";
echo "<p><a href='/admin/settings/master-control'>Master Control</a> | <a href='/admin/finance-management'>Finance Management</a></p>";
echo "</body></html>";
