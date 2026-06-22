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
         Schema::create('partners_content', function (Blueprint $table) {
        $table->id();
        $table->string('title')->nullable();     // Example: Trusted Partnerships That Go Beyond Business
        $table->string('tagline')->nullable();   // Example: "Beyond business, we believe in building a better world together."
        $table->longText('description')->nullable(); // Paragraph text
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners_content');
    }
};
