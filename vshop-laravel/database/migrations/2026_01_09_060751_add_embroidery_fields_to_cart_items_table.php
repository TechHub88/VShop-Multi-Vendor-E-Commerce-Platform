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
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('embroidery_logo')->nullable()->after('courier_charge');
            $table->decimal('embroidery_price', 10, 2)->nullable()->default(0)->after('embroidery_logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn(['embroidery_logo', 'embroidery_price']);
        });
    }
};
