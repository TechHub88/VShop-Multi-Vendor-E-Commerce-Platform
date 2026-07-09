<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrderReturn;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AutoRefundExpiredReturns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-refund-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically refunds return/cancel requests if the seller takes no action within 72 hours (SLA breach)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Checking for expired return/cancel requests...");

        $expiredReturns = OrderReturn::whereIn('status', ['requested', 'pickup_initiated'])
            ->whereNotNull('seller_action_deadline')
            ->where('seller_action_deadline', '<=', now())
            ->get();

        if ($expiredReturns->isEmpty()) {
            $this->info("No expired requests found.");
            return;
        }

        $this->info("Found {$expiredReturns->count()} expired requests. Processing auto-refunds...");

        foreach ($expiredReturns as $returnReq) {
            DB::beginTransaction();
            try {
                $order = Order::find($returnReq->order_id);
                if (!$order) {
                    throw new \Exception("Order not found for return ID {$returnReq->id}");
                }

                $customer = Customer::find($returnReq->customer_id);
                if (!$customer) {
                    throw new \Exception("Customer not found for return ID {$returnReq->id}");
                }

                $refundAmount = $order->total_amount;

                // 1. Mark as auto-refunded due to SLA breach
                $returnReq->status = 'refunded';
                $returnReq->admin_refund_status = 'completed';
                $returnReq->note = trim($returnReq->note . " [Auto-refunded due to 72HR SLA Breach by Seller]");
                $returnReq->save();

                $order->status = ($returnReq->return_type == 'cancel' || str_contains(strtolower($returnReq->reason), 'cancel')) 
                    ? 'cancelled' 
                    : 'returned';
                $order->refund_status = 'completed';
                $order->return_status_workflow = 'refund_completed';
                $order->save();

                // 2. Refund to Customer Wallet
                $customer->wallet_balance = ($customer->wallet_balance ?? 0) + $refundAmount;
                $customer->save();

                $wallet = Wallet::firstOrCreate(
                    ['user_type' => 'customer', 'customer_id' => $customer->id],
                    ['balance' => 0]
                );
                
                $wallet->balance += $refundAmount;
                $wallet->save();
                
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'seller_id' => null,
                    'customer_id' => $customer->id,
                    'user_type' => 'customer',
                    'transaction_type' => 'refund',
                    'credit' => $refundAmount,
                    'debit' => 0,
                    'balance_after' => $customer->wallet_balance,
                    'description' => "Auto-Refund for Order #{$order->id} (Seller 72HR SLA Breach)",
                    'reference_id' => 'AUTOREF_' . $returnReq->id . '_' . time(),
                    'status' => 'completed'
                ]);

                // 3. Deduct from Seller Wallet (Reversing Earning)
                $sellerId = $order->seller_id;
                $sellerWallet = Wallet::firstOrCreate(
                    ['user_type' => 'seller', 'seller_id' => $sellerId],
                    ['balance' => 0]
                );
                
                $sellerWallet->balance -= $refundAmount;
                $sellerWallet->save();

                WalletTransaction::create([
                    'wallet_id' => $sellerWallet->id,
                    'seller_id' => $sellerId,
                    'customer_id' => null,
                    'user_type' => 'seller',
                    'transaction_type' => 'refund_deduction',
                    'credit' => 0,
                    'debit' => $refundAmount,
                    'balance_after' => $sellerWallet->balance,
                    'description' => "Auto-Refund Deduction for Order #{$order->id} (72HR SLA Breach)",
                    'reference_id' => 'AUTOREFDED_' . $returnReq->id . '_' . time(),
                    'status' => 'completed'
                ]);

                // 4. Notify Customer & Seller
                NotificationService::notify(
                    $customer->id,
                    'customer',
                    'wallet',
                    "₹{$refundAmount} has been refunded to your wallet for Order #{$order->id}.",
                    route('vshop.order.details', $order->id)
                );

                NotificationService::notify(
                    $sellerId,
                    'seller',
                    'alert',
                    "72HR SLA Breached for Order #{$order->id}. Amount ₹{$refundAmount} was auto-refunded to customer.",
                    route('seller.paymenthandle')
                );

                DB::commit();
                $this->info("Successfully auto-refunded Return ID {$returnReq->id}");

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Failed to auto-refund Return ID {$returnReq->id}: " . $e->getMessage());
            }
        }

        $this->info("Auto-refund process completed.");
    }
}
