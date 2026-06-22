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
        if (Schema::hasTable('combo_product_items')) {
            // Drop the foreign key constraint if it exists
            $fk = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'combo_product_items' AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'combo_product_items_combo_id_foreign'");
            if (!empty($fk)) {
                Schema::table('combo_product_items', function (Blueprint $table) {
                    $table->dropForeign(['combo_id']);
                });
            }

            // Make combo_id nullable and change it to store main_product_id
            Schema::table('combo_product_items', function (Blueprint $table) {
                $table->unsignedBigInteger('combo_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the foreign key constraint
        Schema::table('combo_product_items', function (Blueprint $table) {
            $table->unsignedBigInteger('combo_id')->nullable(false)->change();
            $table->foreign('combo_id')->references('id')->on('combo_products')->onDelete('cascade');
        });
    }
};

