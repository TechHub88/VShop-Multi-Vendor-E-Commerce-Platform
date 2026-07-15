<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$f = App\Models\SellerFinance::where('order_id', 107)->where('transaction_type', 'earning')->first();
if($f) {
    echo 'Status: ' . $f->status . "\n";
    $f->status = 'hold';
    $f->save();
    echo "Updated order 107 seller_finance status to hold\n";
}

$w = App\Models\WalletTransaction::where('reference_id', 'ORD-107')->where('transaction_type', 'payout')->first();
if($w) {
    echo 'Wallet Type: ' . $w->transaction_type . "\n";
    $w->transaction_type = 'hold';
    $w->status = 'pending';
    $w->save();
    echo "Updated order 107 wallet_transaction to hold\n";
}

$w2 = App\Models\WalletTransaction::where('reference_id', 'ORD-108')->where('transaction_type', 'payout')->first();
if($w2) {
    echo 'Wallet 2 Type: ' . $w2->transaction_type . "\n";
    $w2->transaction_type = 'hold';
    $w2->status = 'pending';
    $w2->save();
    echo "Updated order 108 wallet_transaction to hold\n";
}
