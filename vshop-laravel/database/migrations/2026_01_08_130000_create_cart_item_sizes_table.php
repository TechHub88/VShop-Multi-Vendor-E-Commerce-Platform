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
        Schema::create('cart_item_sizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_item_id');
            $table->unsignedBigInteger('size_id'); // AttributeTerm ID for size
            $table->string('size_name')->nullable(); // Size name (e.g., S, M, L, XL)
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable(); // Price per unit for this size
            $table->timestamps();

            $table->foreign('cart_item_id')->references('id')->on('cart_items')->onDelete('cascade');
            // size_id foreign key removed because attribute_terms table does not exist
            
            $table->index(['cart_item_id', 'size_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_sizes');
    }
};

