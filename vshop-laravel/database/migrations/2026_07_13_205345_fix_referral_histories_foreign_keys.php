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
        Schema::table('referral_histories', function (Blueprint $table) {
            // Drop incorrect foreign keys pointing to users table
            $table->dropForeign(['referrer_id']);
            $table->dropForeign(['buyer_id']);
            
            // Add correct foreign keys pointing to customers table
            $table->foreign('referrer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_histories', function (Blueprint $table) {
            $table->dropForeign(['referrer_id']);
            $table->dropForeign(['buyer_id']);

            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
