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
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('discount_code')->unique(); // Discount Code
                $table->decimal('coupon_amount', 10, 2); // Coupon amount
                $table->text('description')->nullable(); // Coupon Description
                $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage'); // Discount type
                $table->date('expiry_date'); // Expiry Date
                $table->decimal('minimum_spend', 10, 2)->default(0); // Minimum Spend
                $table->enum('status', ['active', 'inactive'])->default('active'); // Status
                $table->integer('usage_limit')->nullable(); // Optional: Limit how many times coupon can be used
                $table->integer('used_count')->default(0); // Track usage
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

