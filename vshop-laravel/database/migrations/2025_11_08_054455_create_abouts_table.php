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
        Schema::create('abouts', function (Blueprint $table) {
        $table->id();
        $table->string('about1')->nullable();
        $table->string('about2')->nullable();
        $table->string('about3')->nullable();
        $table->string('about4')->nullable();
        $table->string('about5')->nullable();
        $table->longText('description')->nullable();
         $table->longText('bgimage')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
