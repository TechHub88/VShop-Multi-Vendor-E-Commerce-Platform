<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
DB::statement("ALTER TABLE chat_messages MODIFY COLUMN sender_type ENUM('seller', 'customer', 'admin', 'bot') NOT NULL DEFAULT 'customer'");
echo 'done';
