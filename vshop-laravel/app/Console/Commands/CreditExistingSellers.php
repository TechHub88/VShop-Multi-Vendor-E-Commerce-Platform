<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Seller;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class CreditExistingSellers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:credit-existing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credit 50,000 to the wallet of all existing sellers if they don\'t have it';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting wallet credit process for existing sellers...');
        
        $sellers = Seller::all();
        $count = 0;

        foreach ($sellers as $seller) {
            $wallet = Wallet::firstOrCreate(
                ['seller_id' => $seller->id, 'user_type' => 'seller'],
                ['balance' => 0, 'is_frozen' => 0]
            );

            // Check if they already received the Welcome Bonus by checking transactions
            $hasWelcomeBonus = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('description', 'Welcome Dummy Balance')
                ->exists();

            if (!$hasWelcomeBonus) {
                // Add 50,000
                $wallet->balance += 50000;
                $wallet->save();

                WalletTransaction::create([
                    'wallet_id'        => $wallet->id,
                    'seller_id'        => $seller->id,
                    'user_type'        => 'seller',
                    'transaction_type' => 'add_money',
                    'credit'           => 50000,
                    'debit'            => 0,
                    'balance_after'    => $wallet->balance,
                    'description'      => 'Welcome Dummy Balance',
                    'status'           => 'completed',
                ]);

                $count++;
                $this->info("Credited 50,000 to Seller ID: {$seller->id} ({$seller->name})");
            }
        }

        $this->info("Completed! Credited $count existing sellers.");
    }
}
