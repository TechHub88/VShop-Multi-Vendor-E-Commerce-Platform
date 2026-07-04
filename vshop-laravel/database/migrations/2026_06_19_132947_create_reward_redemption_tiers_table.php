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
        Schema::create('reward_redemption_tiers', function (Blueprint $table) {
            $table->id();
            $table->integer('points_required')->unique();
            $table->decimal('discount_amount', 10, 2);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Insert default tiers
        DB::table('reward_redemption_tiers')->insert([
            ['points_required' => 100, 'discount_amount' => 10.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['points_required' => 500, 'discount_amount' => 50.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['points_required' => 1000, 'discount_amount' => 100.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_redemption_tiers');
    }
};
