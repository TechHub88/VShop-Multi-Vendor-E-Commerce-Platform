<?php 
require __DIR__."/vendor/autoload.php"; 
$app = require_once __DIR__."/bootstrap/app.php"; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 
try { 
    $c = \App\Models\ChatConversation::firstOrCreate(["type" => "support", "socket_id" => "undefined"], ["admin_id" => null, "subject" => "Support Chat", "status" => "open"]); 
    print_r($c->toArray()); 
} catch (\Exception $e) { 
    echo $e->getMessage(); 
}
