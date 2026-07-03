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
        // Add combo product fields only if they don't already exist
        if (!Schema::hasColumn('order_items', 'combo_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('combo_id')->nullable()->after('product_id');
            });
        }

        if (!Schema::hasColumn('order_items', 'combo_item_color_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('combo_item_color_id')->nullable()->after('combo_id');
            });
        }

        if (!Schema::hasColumn('order_items', 'combo_item_size_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('combo_item_size_id')->nullable()->after('combo_item_color_id');
            });
        }

        if (!Schema::hasColumn('order_items', 'main_product_attributes')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->json('main_product_attributes')->nullable()->after('combo_item_size_id');
            });
        }

        if (!Schema::hasColumn('order_items', 'combo_product_attributes')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->json('combo_product_attributes')->nullable()->after('main_product_attributes');
            });
        }

        if (!Schema::hasColumn('order_items', 'main_product_quantity')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->integer('main_product_quantity')->nullable()->after('combo_product_attributes');
            });
        }

        if (!Schema::hasColumn('order_items', 'main_product_price')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('main_product_price', 10, 2)->nullable()->after('main_product_quantity');
            });
        }

        if (!Schema::hasColumn('order_items', 'combo_product_quantity')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->integer('combo_product_quantity')->nullable()->after('main_product_price');
            });
        }

        if (!Schema::hasColumn('order_items', 'combo_product_price')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('combo_product_price', 10, 2)->nullable()->after('combo_product_quantity');
            });
        }

        if (!Schema::hasColumn('order_items', 'is_combo_item')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->boolean('is_combo_item')->default(false)->after('combo_product_price');
            });
        }

        if (!Schema::hasColumn('order_items', 'gst_percent')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('gst_percent', 5, 2)->nullable()->after('is_combo_item');
            });
        }

        if (!Schema::hasColumn('order_items', 'courier_charge')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('courier_charge', 10, 2)->nullable()->after('gst_percent');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'combo_id',
                'combo_item_color_id',
                'combo_item_size_id',
                'main_product_attributes',
                'combo_product_attributes',
                'main_product_quantity',
                'main_product_price',
                'combo_product_quantity',
                'combo_product_price',
                'is_combo_item',
                'gst_percent',
                'courier_charge'
            ]);
        });
    }
};


