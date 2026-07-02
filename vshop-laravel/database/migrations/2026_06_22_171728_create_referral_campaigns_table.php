<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // e.g. standard, percentage, fixed
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('budget', 10, 2)->default(0);
            $table->decimal('reward', 10, 2)->default(0); // Reward value/percentage
            $table->integer('conversions')->default(0);
            $table->string('status')->default('active'); // active, completed, paused
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_campaigns');
    }
};
