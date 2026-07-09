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
            $table->json('cancelled_dates')->nullable();
        });

        Schema::table('order_cancels', function (Blueprint $table) {
            $table->json('cancelled_dates')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table) {
            $table->dropColumn('cancelled_dates');
        });

        Schema::table('order_cancels', function (Blueprint $table) {
            $table->dropColumn('cancelled_dates');
        });
    }
};
