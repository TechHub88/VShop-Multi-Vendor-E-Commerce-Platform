<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('product_sample_prices')) {
            return;
        }

        // Add `price` column if missing
        if (!Schema::hasColumn('product_sample_prices', 'price')) {
            Schema::table('product_sample_prices', function (Blueprint $table) {
                $table->decimal('price', 12, 2)->default(0)->after('name');
            });
        }

        // Migrate values from existing columns if present
        $hasBase = DB::select("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_sample_prices' AND COLUMN_NAME = 'base_price'");
        if (!empty($hasBase)) {
            DB::statement("UPDATE product_sample_prices SET price = base_price WHERE (price IS NULL OR price = 0) AND base_price IS NOT NULL");
        }

        $hasPcs = DB::select("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_sample_prices' AND COLUMN_NAME = 'pcs_price'");
        if (!empty($hasPcs)) {
            DB::statement("UPDATE product_sample_prices SET price = pcs_price WHERE (price IS NULL OR price = 0) AND pcs_price IS NOT NULL");
        }

        // Drop old columns if they exist
        if (Schema::hasColumn('product_sample_prices', 'base_price')) {
            Schema::table('product_sample_prices', function (Blueprint $table) {
                $table->dropColumn('base_price');
            });
        }

        if (Schema::hasColumn('product_sample_prices', 'total_pcs')) {
            Schema::table('product_sample_prices', function (Blueprint $table) {
                $table->dropColumn('total_pcs');
            });
        }

        if (Schema::hasColumn('product_sample_prices', 'pcs_price')) {
            Schema::table('product_sample_prices', function (Blueprint $table) {
                $table->dropColumn('pcs_price');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('product_sample_prices')) {
            return;
        }

        // Re-create old columns if missing
        if (!Schema::hasColumn('product_sample_prices', 'base_price')) {
            Schema::table('product_sample_prices', function (Blueprint $table) {
                $table->decimal('base_price', 12, 2)->default(0)->after('name');
            });
            DB::statement("UPDATE product_sample_prices SET base_price = price WHERE price IS NOT NULL");
        }

        if (!Schema::hasColumn('product_sample_prices', 'total_pcs')) {
            Schema::table('product_sample_prices', function (Blueprint $table) {
                $table->integer('total_pcs')->nullable()->after('base_price');
            });
        }

        if (!Schema::hasColumn('product_sample_prices', 'pcs_price')) {
            Schema::table('product_sample_prices', function (Blueprint $table) {
                $table->decimal('pcs_price', 12, 2)->nullable()->after('total_pcs');
            });
            DB::statement("UPDATE product_sample_prices SET pcs_price = price WHERE price IS NOT NULL AND (pcs_price IS NULL OR pcs_price = 0)");
        }

        // Drop `price` column
        if (Schema::hasColumn('product_sample_prices', 'price')) {
            Schema::table('product_sample_prices', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }
};
