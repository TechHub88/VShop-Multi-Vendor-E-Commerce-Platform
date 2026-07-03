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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_address_id')) {
                $table->unsignedBigInteger('delivery_address_id')->nullable()->after('address_id');
                $table->foreign('delivery_address_id')->references('id')->on('addresses')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_address_id')) {
                $table->dropForeign(['delivery_address_id']);
                $table->dropColumn('delivery_address_id');
            }
        });
    }
};
