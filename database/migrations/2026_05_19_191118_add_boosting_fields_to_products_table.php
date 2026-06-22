<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('boost_status')->default(1)->after('price');
            $table->string('boost_duration')->nullable()->after('boost_status');
            $table->decimal('daily_budget', 10, 2)->nullable()->after('boost_duration');
            $table->decimal('daily_limit', 10, 2)->nullable()->after('daily_budget');
            $table->decimal('total_budget', 10, 2)->nullable()->after('daily_limit');
            
            $table->string('target_country')->nullable()->after('total_budget');
            $table->string('target_state')->nullable()->after('target_country');
            $table->text('target_city')->nullable()->after('target_state');
            
            $table->string('promotion_goal')->nullable()->after('target_city');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'boost_status',
                'boost_duration',
                'daily_budget',
                'daily_limit',
                'total_budget',
                'target_country',
                'target_state',
                'target_city',
                'promotion_goal'
            ]);
        });
    }
};
