<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReleaseSellerHoldFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hold:release';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Releases seller earning funds from hold status once the hold period has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting release of held seller funds...');

        // We find all seller_finances records that are currently on hold and their release_date is today or earlier.
        // We only fetch 'earning' rows, since 'commission_deduction' rows don't add to the wallet anyway.
        $heldEarnings = \Illuminate\Support\Facades\DB::table('seller_finances')
            ->where('status', 'hold')
            ->where('transaction_type', 'earning')
            ->whereDate('release_date', '<=', now()->toDateString())
            ->get();

        if ($heldEarnings->isEmpty()) {
            $this->info('No held funds to release today.');
            return;
        }

        $releasedCount = 0;
        $totalReleased = 0;

        foreach ($heldEarnings as $earning) {
            // Find the associated deductions to calculate net earning
            $deductions = \Illuminate\Support\Facades\DB::table('seller_finances')
                ->where('status', 'hold')
                ->where('order_id', $earning->order_id)
                ->where('seller_id', $earning->seller_id)
                ->where('transaction_type', 'commission_deduction')
                ->get();

            $totalDeduction = $deductions->sum('debit');
            $netSellerEarning = $earning->credit - $totalDeduction;

            if ($netSellerEarning > 0) {
                $sellerWallet = \App\Models\Wallet::firstOrCreate(
                    ['user_type' => 'seller', 'seller_id' => $earning->seller_id, 'customer_id' => null],
                    ['balance' => 0]
                );

                $sellerWallet->increment('balance', $netSellerEarning);

                // Re-create the wallet transactions here since we skipped them during checkout
                // 1. Credit 100% of order sale
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $sellerWallet->id,
                    'seller_id' => $earning->seller_id,
                    'user_type' => 'seller',
                    'transaction_type' => 'payout',
                    'credit' => $earning->credit,
                    'debit' => 0,
                    'balance_after' => $sellerWallet->balance + $totalDeduction, // Approximate state
                    'description' => "Hold released: Order sale earning for Order #{$earning->order_id}",
                    'reference_id' => $earning->reference_no,
                    'status' => 'completed'
                ]);

                // 2. Debit platform & referral fees
                foreach ($deductions as $deduction) {
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $sellerWallet->id,
                        'seller_id' => $earning->seller_id,
                        'user_type' => 'seller',
                        'transaction_type' => str_contains(strtolower($deduction->note), 'referral') ? 'referral_deduction' : 'commission_deduction',
                        'credit' => 0,
                        'debit' => $deduction->debit,
                        'balance_after' => $sellerWallet->balance, // Approximate state
                        'description' => "Hold released: {$deduction->note} for Order #{$earning->order_id}",
                        'reference_id' => $earning->reference_no,
                        'status' => 'completed'
                    ]);
                }
            }

            // Mark all related records for this order/seller as success
            \Illuminate\Support\Facades\DB::table('seller_finances')
                ->where('status', 'hold')
                ->where('order_id', $earning->order_id)
                ->where('seller_id', $earning->seller_id)
                ->update([
                    'status' => 'released',
                    'updated_at' => now(),
                    'note' => \Illuminate\Support\Facades\DB::raw("REPLACE(note, '(On Hold', '(Released')") // Just a simple string update if it contains (On Hold...)
                ]);
                
            // --- ADMIN ESCROW WALLET PAYOUT ---
            $escrowWallet = \App\Models\Wallet::firstOrCreate(
                ['user_type' => 'platform', 'seller_id' => null, 'customer_id' => null],
                ['balance' => 0]
            );
            
            // Debit seller payout from escrow
            $escrowWallet->decrement('balance', $netSellerEarning);
            \App\Models\WalletTransaction::create([
                'wallet_id' => $escrowWallet->id,
                'user_type' => 'platform',
                'transaction_type' => 'payout',
                'credit' => 0,
                'debit' => $netSellerEarning,
                'balance_after' => $escrowWallet->balance,
                'description' => "Auto-release: Payout to Seller #{$earning->seller_id} for Order #{$earning->order_id}",
                'reference_id' => $earning->reference_no,
                'status' => 'completed'
            ]);



            $releasedCount++;
            $totalReleased += $netSellerEarning;
        }

        $this->info("Successfully released {$releasedCount} held transactions totaling {$totalReleased}.");
    }
}
