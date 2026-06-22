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
         Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_no');
        $table->string('status')->default('Pending'); // Pending, Completed, Cancelled
        $table->string('payment_method')->nullable(); // Stripe, Paypal, Cash on Delivery
        $table->string('payment_status')->default('Unpaid'); // Paid, Unpaid
        $table->string('voucher')->nullable(); // N/A or coupon code
        $table->string('customer_name')->nullable();
        $table->decimal('amount', 10, 2)->default(0);
        $table->timestamp('created_at')->useCurrent();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
