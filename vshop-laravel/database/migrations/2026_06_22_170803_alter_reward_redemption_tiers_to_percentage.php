<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reward_redemption_tiers', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
            $table->decimal('discount_percentage', 5, 2)->after('points_required')->default(0);
            $table->integer('expiry_days')->after('discount_percentage')->default(180);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_redemption_tiers', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
            $table->dropColumn('expiry_days');
            $table->decimal('discount_amount', 8, 2)->after('points_required')->default(0);
        });
    }
};
