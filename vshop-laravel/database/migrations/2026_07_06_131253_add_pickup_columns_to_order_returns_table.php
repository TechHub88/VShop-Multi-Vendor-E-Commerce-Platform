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
        Schema::table('order_returns', function (Blueprint $table) {
            $table->boolean('seller_pickup_required')->default(false)->after('status');
            $table->string('pickup_status')->default('pending')->after('seller_pickup_required');
            $table->timestamp('hold_date')->nullable()->after('pickup_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table) {
            $table->dropColumn(['seller_pickup_required', 'pickup_status', 'hold_date']);
        });
    }
};
