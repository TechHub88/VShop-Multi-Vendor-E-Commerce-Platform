<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$videos = \App\Models\Advertisement::with('media')->where('type', 'video')->get();
foreach ($videos as $v) {
    echo "ID: {$v->id}, Category: {$v->category_id}, Active: {$v->is_active}, Media: " . $v->media->count() . "\n";
}
