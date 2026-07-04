<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('referral_histories', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('referrer_id'); // Buyer A
            $table->unsignedBigInteger('buyer_id'); // Buyer B
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('order_id');
            
            $table->decimal('amount', 10, 2)->default(0); // Commission amount
            $table->decimal('commission_percent', 5, 2)->default(0);
            
            $table->string('status')->default('paid'); // pending, paid
            
            $table->timestamps();
            
            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_histories');
    }
};
