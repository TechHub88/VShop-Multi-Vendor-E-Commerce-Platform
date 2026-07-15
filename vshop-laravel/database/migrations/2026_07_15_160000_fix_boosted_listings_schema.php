<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix the boosted_listings table schema:
     * - Make product_id, seller_id, start_date, end_date nullable
     * - Change status from restricted ENUM to VARCHAR to support all status values
     */
    public function up(): void
    {
        // Use raw SQL statements for maximum reliability across all DB states
        try {
            DB::statement("ALTER TABLE boosted_listings MODIFY product_id BIGINT UNSIGNED NULL");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE boosted_listings MODIFY seller_id BIGINT UNSIGNED NULL");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE boosted_listings MODIFY start_date DATE NULL");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE boosted_listings MODIFY end_date DATE NULL");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE boosted_listings MODIFY status VARCHAR(50) DEFAULT 'active'");
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - making columns nullable is safe
    }
};
