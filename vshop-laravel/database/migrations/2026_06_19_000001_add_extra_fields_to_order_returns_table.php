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
        Schema::table('order_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('order_returns', 'refund_mode')) {
                $table->string('refund_mode')->nullable()->after('status');
            }
            if (!Schema::hasColumn('order_returns', 'pickup_date')) {
                $table->date('pickup_date')->nullable()->after('refund_mode');
            }
            if (!Schema::hasColumn('order_returns', 'pickup_time')) {
                $table->string('pickup_time')->nullable()->after('pickup_date');
            }
            if (!Schema::hasColumn('order_returns', 'return_type')) {
                $table->string('return_type')->default('return')->after('pickup_time'); // 'return' or 'replacement'
            }
            if (!Schema::hasColumn('order_returns', 'otp')) {
                $table->string('otp')->nullable()->after('return_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table) {
            $table->dropColumn(['refund_mode', 'pickup_date', 'pickup_time', 'return_type', 'otp']);
        });
    }
};
