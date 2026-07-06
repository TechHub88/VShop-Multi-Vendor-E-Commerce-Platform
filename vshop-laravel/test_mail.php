<?php 
require __DIR__."/vendor/autoload.php"; 
$app = require_once __DIR__."/bootstrap/app.php"; 
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); 
$kernel->bootstrap(); 

try { 
    \Illuminate\Support\Facades\Mail::raw('Test Email from VSHOP local', function($msg) {
        $msg->to('nisharoy3363@gmail.com')->subject('Test SMTP Connection');
    });
    echo "Mail sent successfully.\n"; 
} catch (\Exception $e) { 
    echo "Mail sending failed: " . $e->getMessage() . "\n"; 
}
