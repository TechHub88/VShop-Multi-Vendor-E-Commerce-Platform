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
        Schema::create('boosted_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('per_day', 15, 2)->default(0);
            $table->decimal('boost_amount', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired', 'rule'])->default('active');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('enquiries')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('revenue_generated', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boosted_listings');
    }
};
