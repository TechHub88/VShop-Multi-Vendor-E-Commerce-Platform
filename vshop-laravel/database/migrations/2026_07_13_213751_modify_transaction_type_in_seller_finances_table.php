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
        Schema::table('seller_finances', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE seller_finances MODIFY COLUMN transaction_type VARCHAR(100) NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_finances', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE seller_finances MODIFY COLUMN transaction_type VARCHAR(50) NOT NULL");
        });
    }
};
