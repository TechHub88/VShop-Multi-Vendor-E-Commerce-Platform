<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('referrer_id')->nullable()->after('wallet_balance');
            $table->decimal('referral_commission_percent', 5, 2)->default(0)->after('referrer_id');
            $table->decimal('referral_commission_amount', 10, 2)->default(0)->after('referral_commission_percent');
            $table->boolean('referral_paid')->default(false)->after('referral_commission_amount');
            
            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['referrer_id']);
            $table->dropColumn([
                'referrer_id', 
                'referral_commission_percent', 
                'referral_commission_amount', 
                'referral_paid'
            ]);
        });
    }
};
