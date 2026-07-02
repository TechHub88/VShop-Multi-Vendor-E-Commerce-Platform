<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$enquiries = Enquiry::limit(5)->get();
foreach ($enquiries as $e) {
    echo "ID: {$e->id} | No: {$e->enquiry_no} | Name: {$e->user_name} | Product: {$e->product_service} | Source: {$e->source} | Status: {$e->status} | Msg: {$e->message} | Type: {$e->type}\n";
}
