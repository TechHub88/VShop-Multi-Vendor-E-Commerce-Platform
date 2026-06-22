<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::dropIfExists('orders');
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        // User Info
        $table->unsignedBigInteger('user_id');

        // Address Fields
        $table->string('full_name');
        $table->string('mobile');
        $table->string('alternate_mobile')->nullable();
        $table->string('address_line_1');
        $table->string('address_line_2')->nullable();
        $table->string('landmark')->nullable();
        $table->string('city');
        $table->string('state');
        $table->string('pincode');

        // Payment Info
        $table->enum('payment_method', ['upi', 'card', 'other']);
        $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');

        // Price Details
        $table->decimal('sub_total', 10, 2);
        $table->decimal('gst_amount', 10, 2)->default(0);
        $table->decimal('courier_charge', 10, 2)->default(0);
        $table->decimal('total_amount', 10, 2);

        $table->timestamps();
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
