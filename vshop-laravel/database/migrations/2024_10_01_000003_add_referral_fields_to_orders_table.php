<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('referrer_id')->nullable();
            $table->decimal('referral_commission_percent', 5, 2)->default(0);
            $table->decimal('referral_commission_amount', 10, 2)->default(0);
            $table->boolean('referral_paid')->default(false);
            
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
