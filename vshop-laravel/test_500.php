<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $txn = \App\Models\SellerFinance::where('transaction_type', 'earning')->first();
    $req = Illuminate\Http\Request::create('/admin/finance/update-earnings-settings', 'POST', [
        'transaction_ids' => [$txn->id],
        'updates' => ['hold_days' => 2]
    ]);
    $res = app(App\Http\Controllers\admin\AdminFinanceController::class)->updateEarningsSettings($req);
    echo $res->getContent();
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine();
}
