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
    {  Schema::create('cart_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('session_id')->nullable();
        $table->unsignedBigInteger('product_id');
        $table->unsignedBigInteger('color_id')->nullable();
        $table->unsignedBigInteger('size_id')->nullable();
        $table->integer('quantity')->default(1);
        $table->decimal('price', 10, 2);
        $table->decimal('gst_percent', 5, 2)->default(5); // default GST 5%
        $table->decimal('courier_charge', 10, 2)->nullable();
        $table->timestamps();

        $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
