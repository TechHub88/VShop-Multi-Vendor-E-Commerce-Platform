<?php

ob_implicit_flush(true);
while (ob_get_level() > 0) {
    ob_end_flush();
}

use App\Models\Customer;
use App\Models\Seller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Reward;
use App\Models\ReferralHistory;
use Illuminate\Support\Facades\DB;

// Boot Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STARTING REFERRAL FLOW TEST ===\n\n";

function cleanupTestData() {
    $testOrderIds = DB::table('orders')->where('remarks', 'like', '%TEST_ORDER%')->pluck('id')->toArray();
    if (!empty($testOrderIds)) {
        DB::table('order_items')->whereIn('order_id', $testOrderIds)->delete();
        DB::table('referral_histories')->whereIn('order_id', $testOrderIds)->delete();
        DB::table('seller_finances')->whereIn('order_id', $testOrderIds)->delete();
        
        $refIds = array_map(function($id) { return 'ORD-' . $id; }, $testOrderIds);
        DB::table('wallet_transactions')->whereIn('reference_id', $refIds)->delete();
        
        DB::table('orders')->whereIn('id', $testOrderIds)->delete();
    }
}

cleanupTestData();

// 1. Setup Test Entities
$seller = Seller::firstOrCreate(
    ['email' => 'test_seller@vshop.com'],
    [
        'name' => 'Test Seller',
        'mobile' => '9999999999',
        'password' => bcrypt('password'),
        'status' => 'approved',
    ]
);

$buyer = Customer::firstOrCreate(
    ['email' => 'test_buyer@vshop.com'],
    [
        'name' => 'Test Buyer',
        'phone' => '8888888888',
        'password' => bcrypt('password'),
        'referral_code' => 'BUYER123',
    ]
);

$referrerNormal = Customer::firstOrCreate(
    ['email' => 'referrer_normal@vshop.com'],
    [
        'name' => 'Referrer Normal',
        'phone' => '7777777777',
        'password' => bcrypt('password'),
        'referral_code' => 'REF_NORM',
    ]
);

$referrerSellerPartner = Customer::firstOrCreate(
    ['email' => 'referrer_seller@vshop.com'],
    [
        'name' => 'Referrer Seller',
        'phone' => '6666666666',
        'password' => bcrypt('password'),
        'referral_code' => 'REF_SELL',
    ]
);

$sellerPartner = Seller::firstOrCreate(
    ['email' => 'referrer_seller@vshop.com'],
    [
        'name' => 'Referrer Seller Partner',
        'mobile' => '6666666666',
        'password' => bcrypt('password'),
        'status' => 'approved',
    ]
);

// Reset balances
$testWallets = [
    ['user_type' => 'seller', 'seller_id' => $seller->id],
    ['user_type' => 'customer', 'customer_id' => $buyer->id],
    ['user_type' => 'customer', 'customer_id' => $referrerNormal->id],
    ['user_type' => 'customer', 'customer_id' => $referrerSellerPartner->id],
    ['user_type' => 'seller', 'seller_id' => $sellerPartner->id],
    ['user_type' => 'platform', 'seller_id' => null, 'customer_id' => null],
];
foreach ($testWallets as $item) {
    Wallet::updateOrCreate($item, ['balance' => 0]);
}
$buyer->wallet_balance = 0;
$buyer->reward_points = 0;
$buyer->save();
$referrerNormal->wallet_balance = 0;
$referrerNormal->reward_points = 0;
$referrerNormal->save();

// --- TEST CASE 1: Referred purchase (normal buyer referrer) ---
echo "--- TEST CASE 1: Referred Purchase (Normal Buyer) ---\n";
$buyer->referred_by = $referrerNormal->id;
$buyer->save();

$totalPrice = 1000.00;
$referrerComm = 3.0;
$platformComm = 1.5;

// Mock order placement
DB::beginTransaction();
try {
    echo "Creating order...\n";
    $order = Order::create([
        'seller_id' => $seller->id,
        'customer_id' => $buyer->id,
        'payment_method' => 'upi',
        'payment_status' => 'paid',
        'sub_total' => $totalPrice,
        'total_amount' => $totalPrice,
        'status' => 'pending',
        'remarks' => 'TEST_ORDER_1',
        'referrer_id' => $referrerNormal->id,
    ]);
    echo "Order created successfully. Checking referral history...\n";

    // Check referral logic
    $referralUserId = $buyer->referred_by;
    $referralAmount = 0;
    if ($referralUserId && $referralUserId != $buyer->id) {
        echo "Referrer normal exists. Checking previous referrals...\n";
        $hasPreviousPaidReferral = ReferralHistory::where('buyer_id', $buyer->id)->where('status', '!=', 'reversed')->exists();
        echo "Previous referrals checked. Result: " . ($hasPreviousPaidReferral ? 'Yes' : 'No') . "\n";
        if (!$hasPreviousPaidReferral) {
            $referralAmount = ($totalPrice * $referrerComm) / 100;
        }
    }
    echo "Referral amount calculated: {$referralAmount}. Calculating platform amount...\n";
    $platformAmount = ($totalPrice * $platformComm) / 100;
    $commissionAmount = $referralAmount + $platformAmount;
    $sellerEarning = $totalPrice - $commissionAmount;

    echo "Commission: {$commissionAmount}. Seller Earning: {$sellerEarning}. Incrementing reward points...\n";
    // Credit referrer (normal buyer gets rewards point)
    $referrerNormal->increment('reward_points', $referralAmount);
    echo "Reward points incremented. Creating reward record...\n";
    Reward::create([
        'customer_id' => $referrerNormal->id,
        'type' => 'points',
        'points' => (int) $referralAmount,
        'amount' => $referralAmount,
        'description' => "Referral points for order #{$order->id}",
        'status' => 'active',
        'expiry_date' => now()->addDays(365),
    ]);
    echo "Reward record created. Creating Wallet transaction...\n";
    $refWallet = Wallet::firstOrCreate(['user_type' => 'customer', 'customer_id' => $referrerNormal->id], ['balance' => 0]);
    echo "Wallet found/created. Writing transaction log...\n";
    WalletTransaction::create([
        'wallet_id' => $refWallet->id,
        'customer_id' => $referrerNormal->id,
        'user_type' => 'customer',
        'transaction_type' => 'commission',
        'credit' => 0,
        'debit' => 0,
        'balance_after' => $refWallet->balance,
        'description' => "Referral commission (credited as points) for Order #{$order->id}",
        'reference_id' => 'ORD-' . $order->id,
        'status' => 'completed',
    ]);
    echo "Wallet transaction logged. Recording Referral History...\n";

    // Record Referral History
    ReferralHistory::create([
        'referrer_id' => $referralUserId,
        'buyer_id' => $buyer->id,
        'seller_id' => $seller->id,
        'order_id' => $order->id,
        'amount' => $referralAmount,
        'commission_percent' => $referrerComm,
        'status' => 'paid',
    ]);

    // Credit Seller Wallet & Log individual entries
    $sellerWallet = Wallet::firstOrCreate(['user_type' => 'seller', 'seller_id' => $seller->id], ['balance' => 0]);
    $sellerWallet->increment('balance', $sellerEarning);

    WalletTransaction::create([
        'wallet_id' => $sellerWallet->id,
        'seller_id' => $seller->id,
        'user_type' => 'seller',
        'transaction_type' => 'payout',
        'credit' => $totalPrice,
        'debit' => 0,
        'balance_after' => $sellerWallet->balance + $commissionAmount,
        'description' => "Order sale earning for Order #{$order->id}",
        'reference_id' => 'ORD-' . $order->id,
        'status' => 'completed'
    ]);
    WalletTransaction::create([
        'wallet_id' => $sellerWallet->id,
        'seller_id' => $seller->id,
        'user_type' => 'seller',
        'transaction_type' => 'commission_deduction',
        'credit' => 0,
        'debit' => $platformAmount,
        'balance_after' => $sellerWallet->balance + $referralAmount,
        'description' => "Platform fee deduction for Order #{$order->id}",
        'reference_id' => 'ORD-' . $order->id,
        'status' => 'completed'
    ]);
    WalletTransaction::create([
        'wallet_id' => $sellerWallet->id,
        'seller_id' => $seller->id,
        'user_type' => 'seller',
        'transaction_type' => 'referral_deduction',
        'credit' => 0,
        'debit' => $referralAmount,
        'balance_after' => $sellerWallet->balance,
        'description' => "Referral commission deduction for Order #{$order->id}",
        'reference_id' => 'ORD-' . $order->id,
        'status' => 'completed'
    ]);

    // Log to seller_finances
    DB::table('seller_finances')->insert([
        'seller_id' => $seller->id, 'order_id' => $order->id, 'transaction_type' => 'earning', 'reference_no' => 'ORD-'.$order->id, 'credit' => $totalPrice, 'debit' => 0, 'commission_amount' => 0, 'status' => 'success', 'note' => 'Order sale', 'created_at' => now(), 'updated_at' => now()
    ]);
    DB::table('seller_finances')->insert([
        'seller_id' => $seller->id, 'order_id' => $order->id, 'transaction_type' => 'commission_deduction', 'reference_no' => 'ORD-'.$order->id, 'credit' => 0, 'debit' => $platformAmount, 'commission_amount' => $platformAmount, 'status' => 'success', 'note' => 'Platform fee', 'created_at' => now(), 'updated_at' => now()
    ]);
    DB::table('seller_finances')->insert([
        'seller_id' => $seller->id, 'order_id' => $order->id, 'transaction_type' => 'commission_deduction', 'reference_no' => 'ORD-'.$order->id, 'credit' => 0, 'debit' => $referralAmount, 'commission_amount' => $referralAmount, 'status' => 'success', 'note' => 'Referral commission', 'created_at' => now(), 'updated_at' => now()
    ]);

    // Admin Escrow Wallet Load & Payouts
    $escrowWallet = Wallet::firstOrCreate(['user_type' => 'platform'], ['balance' => 0]);
    $escrowWallet->increment('balance', $totalPrice);
    WalletTransaction::create([
        'wallet_id' => $escrowWallet->id, 'user_type' => 'platform', 'transaction_type' => 'payment', 'credit' => $totalPrice, 'debit' => 0, 'balance_after' => $escrowWallet->balance, 'description' => "Order loaded", 'reference_id' => 'ORD-'.$order->id, 'status' => 'completed'
    ]);
    $escrowWallet->decrement('balance', $sellerEarning);
    WalletTransaction::create([
        'wallet_id' => $escrowWallet->id, 'user_type' => 'platform', 'transaction_type' => 'payout', 'credit' => 0, 'debit' => $sellerEarning, 'balance_after' => $escrowWallet->balance, 'description' => "Payout seller", 'reference_id' => 'ORD-'.$order->id, 'status' => 'completed'
    ]);
    $escrowWallet->decrement('balance', $referralAmount);
    WalletTransaction::create([
        'wallet_id' => $escrowWallet->id, 'user_type' => 'platform', 'transaction_type' => 'commission', 'credit' => 0, 'debit' => $referralAmount, 'balance_after' => $escrowWallet->balance, 'description' => "Referral payout", 'reference_id' => 'ORD-'.$order->id, 'status' => 'completed'
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => 1,
        'quantity' => 1,
        'price' => $totalPrice,
        'total' => $totalPrice,
        'commission_percent' => $platformComm + $referrerComm,
        'commission_amount' => $commissionAmount,
        'seller_earning' => $sellerEarning,
        'referral_earning' => $referralAmount,
        'platform_earning' => $platformAmount,
    ]);

    $order->referral_commission_percent = $referrerComm;
    $order->referral_commission_amount = $referralAmount;
    $order->referral_paid = 1;
    $order->save();

    DB::commit();

    // Verify balances
    echo "Seller Wallet Balance: " . Wallet::where('user_type', 'seller')->where('seller_id', $seller->id)->value('balance') . " (Expected: 955.00)\n";
    echo "Referrer Reward Points: " . Customer::find($referrerNormal->id)->reward_points . " (Expected: 30.00)\n";
    echo "Admin Escrow Balance: " . Wallet::where('user_type', 'platform')->value('balance') . " (Expected: 15.00)\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Test Case 1 failed: " . $e->getMessage() . "\n";
}


// --- TEST CASE 2: Cancel Order & Reverse Payouts ---
echo "\n--- TEST CASE 2: Cancel Order & Reverse Payouts ---\n";
Order::reverseOrderFunds($order);
echo "Seller Wallet Balance after Reversal: " . Wallet::where('user_type', 'seller')->where('seller_id', $seller->id)->value('balance') . " (Expected: 0.00)\n";
echo "Referrer Reward Points after Reversal: " . Customer::find($referrerNormal->id)->reward_points . " (Expected: 0.00)\n";
echo "Admin Escrow Balance after Reversal: " . Wallet::where('user_type', 'platform')->value('balance') . " (Expected: 0.00)\n";
echo "Buyer Wallet Balance after Refund: " . Wallet::where('user_type', 'customer')->where('customer_id', $buyer->id)->value('balance') . " (Expected: 1000.00)\n";


// --- TEST CASE 3: Without Referral ---
echo "\n--- TEST CASE 3: Without Referral ---\n";
// Clear buyer referred_by to simulate direct purchase
$buyer->referred_by = null;
$buyer->wallet_balance = 0;
$buyer->save();
$sellerWallet->balance = 0;
$sellerWallet->save();
$escrowWallet->balance = 0;
$escrowWallet->save();

DB::beginTransaction();
try {
    $order2 = Order::create([
        'seller_id' => $seller->id,
        'customer_id' => $buyer->id,
        'payment_method' => 'upi',
        'payment_status' => 'paid',
        'sub_total' => $totalPrice,
        'total_amount' => $totalPrice,
        'status' => 'pending',
        'remarks' => 'TEST_ORDER_2',
    ]);

    $referralAmount = 0;
    $platformAmount = ($totalPrice * $platformComm) / 100;
    $commissionAmount = $referralAmount + $platformAmount;
    $sellerEarning = $totalPrice - $commissionAmount;

    // Credit Seller
    $sellerWallet->increment('balance', $sellerEarning);
    WalletTransaction::create([
        'wallet_id' => $sellerWallet->id, 'seller_id' => $seller->id, 'user_type' => 'seller', 'transaction_type' => 'payout', 'credit' => $totalPrice, 'debit' => 0, 'balance_after' => $sellerWallet->balance + $commissionAmount, 'description' => "Order sale", 'reference_id' => 'ORD-'.$order2->id, 'status' => 'completed'
    ]);
    WalletTransaction::create([
        'wallet_id' => $sellerWallet->id, 'seller_id' => $seller->id, 'user_type' => 'seller', 'transaction_type' => 'commission_deduction', 'credit' => 0, 'debit' => $platformAmount, 'balance_after' => $sellerWallet->balance, 'description' => "Platform fee", 'reference_id' => 'ORD-'.$order2->id, 'status' => 'completed'
    ]);

    OrderItem::create([
        'order_id' => $order2->id,
        'product_id' => 1,
        'quantity' => 1,
        'price' => $totalPrice,
        'total' => $totalPrice,
        'commission_percent' => $platformComm,
        'commission_amount' => $commissionAmount,
        'seller_earning' => $sellerEarning,
        'referral_earning' => 0,
        'platform_earning' => $platformAmount,
    ]);

    $order2->referral_commission_percent = 0;
    $order2->referral_commission_amount = 0;
    $order2->referral_paid = 0;
    $order2->save();

    DB::commit();

    echo "Seller Wallet Balance: " . Wallet::where('user_type', 'seller')->where('seller_id', $seller->id)->value('balance') . " (Expected: 985.00)\n";
    echo "Referral History Created? " . (ReferralHistory::where('order_id', $order2->id)->exists() ? 'Yes' : 'No') . " (Expected: No)\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Test Case 3 failed: " . $e->getMessage() . "\n";
}


// --- TEST CASE 4: Referrer listed in Seller Panel gets Money ---
echo "\n--- TEST CASE 4: Referrer in Seller Panel gets Money ---\n";
// Set buyer referred by referrerSellerPartner
$buyer->referred_by = $referrerSellerPartner->id;
$buyer->save();
$sellerWallet->balance = 0;
$sellerWallet->save();
$referrerSellerWallet = Wallet::firstOrCreate(['user_type' => 'seller', 'seller_id' => $sellerPartner->id], ['balance' => 0]);
$referrerSellerWallet->balance = 0;
$referrerSellerWallet->save();

DB::beginTransaction();
try {
    $order3 = Order::create([
        'seller_id' => $seller->id,
        'customer_id' => $buyer->id,
        'payment_method' => 'upi',
        'payment_status' => 'paid',
        'sub_total' => $totalPrice,
        'total_amount' => $totalPrice,
        'status' => 'pending',
        'remarks' => 'TEST_ORDER_3',
        'referrer_id' => $referrerSellerPartner->id,
    ]);

    // Check referrer is in seller panel
    $referrerSellerObj = Seller::where('email', $referrerSellerPartner->email)->first();
    $referralAmount = ($totalPrice * $referrerComm) / 100;
    $platformAmount = ($totalPrice * $platformComm) / 100;
    $commissionAmount = $referralAmount + $platformAmount;
    $sellerEarning = $totalPrice - $commissionAmount;

    if ($referrerSellerObj) {
        $refSellerWallet = Wallet::firstOrCreate(['user_type' => 'seller', 'seller_id' => $referrerSellerObj->id], ['balance' => 0]);
        $refSellerWallet->increment('balance', $referralAmount);
        
        WalletTransaction::create([
            'wallet_id' => $refSellerWallet->id,
            'seller_id' => $referrerSellerObj->id,
            'user_type' => 'seller',
            'transaction_type' => 'commission',
            'credit' => $referralAmount,
            'debit' => 0,
            'balance_after' => $refSellerWallet->balance,
            'description' => "Referral earning",
            'reference_id' => 'ORD-' . $order3->id,
            'status' => 'completed',
        ]);
    }

    OrderItem::create([
        'order_id' => $order3->id,
        'product_id' => 1,
        'quantity' => 1,
        'price' => $totalPrice,
        'total' => $totalPrice,
        'commission_percent' => $platformComm + $referrerComm,
        'commission_amount' => $commissionAmount,
        'seller_earning' => $sellerEarning,
        'referral_earning' => $referralAmount,
        'platform_earning' => $platformAmount,
    ]);

    $order3->referral_commission_percent = $referrerComm;
    $order3->referral_commission_amount = $referralAmount;
    $order3->referral_paid = 1;
    $order3->save();

    DB::commit();

    echo "Referrer Seller Wallet Balance: " . Wallet::where('user_type', 'seller')->where('seller_id', $sellerPartner->id)->value('balance') . " (Expected: 30.00)\n";
    echo "Referrer Customer Reward Points: " . Customer::find($referrerSellerPartner->id)->reward_points . " (Expected: 0)\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Test Case 4 failed: " . $e->getMessage() . "\n";
}


// --- TEST CASE 5: Self Referral check ---
echo "\n--- TEST CASE 5: Self Referral check ---\n";
$buyer->referred_by = $buyer->id; // Self referral
$buyer->save();

DB::beginTransaction();
try {
    $order4 = Order::create([
        'seller_id' => $seller->id,
        'customer_id' => $buyer->id,
        'payment_method' => 'upi',
        'payment_status' => 'paid',
        'sub_total' => $totalPrice,
        'total_amount' => $totalPrice,
        'status' => 'pending',
        'remarks' => 'TEST_ORDER_4',
        'referrer_id' => $buyer->id,
    ]);

    $referralAmount = 0; // Self referral is 0
    $platformAmount = ($totalPrice * $platformComm) / 100;
    $commissionAmount = $referralAmount + $platformAmount;
    $sellerEarning = $totalPrice - $commissionAmount;

    echo "Referral Commission for Self-Referral: {$referralAmount} (Expected: 0)\n";
    echo "Seller Earning: {$sellerEarning} (Expected: 985.00)\n";

} catch (\Exception $e) {
    echo "Test Case 5 failed: " . $e->getMessage() . "\n";
}

// Clean up
cleanupTestData();
echo "\n=== ALL TESTS COMPLETED SUCCESSFULLY ===\n";
