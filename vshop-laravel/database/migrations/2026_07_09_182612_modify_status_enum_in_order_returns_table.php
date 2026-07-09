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
        DB::statement("ALTER TABLE order_returns MODIFY COLUMN status ENUM('requested', 'pickup_initiated', 'approved', 'rejected', 'refunded') DEFAULT 'requested'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE order_returns MODIFY COLUMN status ENUM('requested', 'approved', 'rejected', 'refunded') DEFAULT 'requested'");
    }
};
