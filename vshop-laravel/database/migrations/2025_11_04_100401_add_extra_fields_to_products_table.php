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
        Schema::table('products', function (Blueprint $table) {
        $table->string('thumbnail')->nullable()->after('moq');
        $table->json('images')->nullable()->change();
        $table->string('meta_title')->nullable();
        $table->string('meta_keywords')->nullable();
        $table->text('meta_description')->nullable();
        $table->boolean('is_featured')->default(0);
        $table->boolean('status')->default(1); // 1 = Active, 0 = Inactive
        $table->string('brand')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
