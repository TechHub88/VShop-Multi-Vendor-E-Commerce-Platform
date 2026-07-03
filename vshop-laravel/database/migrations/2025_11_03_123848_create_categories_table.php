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
         Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                              // Category name
            $table->string('slug')->unique();                   // SEO friendly URL
            $table->unsignedBigInteger('parent_id')->nullable(); // For sub-categories
            $table->string('banner')->nullable();               // Banner image
            $table->text('description')->nullable();            // Description
            $table->boolean('is_featured')->default(false);     // Featured collection
            $table->integer('sort_order')->default(0);          // Optional display order

            $table->timestamps();

            // Self relationship for subcategories
            $table->foreign('parent_id')->references('id')->on('categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
