<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN user_type ENUM('seller', 'customer', 'platform') NOT NULL");
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN transaction_type ENUM('payout','refund','commission','add_money','hold','payment','manual_credit','manual_debit','refund_deduction') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN user_type ENUM('seller', 'customer') NOT NULL");
        DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN transaction_type ENUM('payout','refund','commission','add_money','hold','payment','manual_credit','manual_debit') NOT NULL");
    }
};
