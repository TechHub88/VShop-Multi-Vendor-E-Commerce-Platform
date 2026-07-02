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
        Schema::table('sellers', function (Blueprint $table) {
            $table->json('requested_platforms')->nullable()->after('status');
            $table->json('approved_platforms')->nullable()->after('requested_platforms');
            $table->timestamp('platform_request_time')->nullable()->after('approved_platforms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['requested_platforms', 'approved_platforms', 'platform_request_time']);
        });
    }
};
