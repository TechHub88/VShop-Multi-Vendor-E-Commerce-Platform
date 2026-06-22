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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'fixed_type')) {
                $table->text('fixed_type')->nullable();
            }
            if (!Schema::hasColumn('products', 'fixed_dates')) {
                $table->text('fixed_dates')->nullable();
            }
            if (!Schema::hasColumn('products', 'service_days_count')) {
                $table->integer('service_days_count')->nullable();
            }
            if (!Schema::hasColumn('products', 'service_nights')) {
                $table->integer('service_nights')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['fixed_type', 'fixed_dates', 'service_days_count', 'service_nights']);
        });
    }
};
