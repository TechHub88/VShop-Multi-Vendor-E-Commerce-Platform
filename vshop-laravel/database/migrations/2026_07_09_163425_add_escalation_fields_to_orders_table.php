<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'cancel_status')) {
                $table->string('cancel_status')->nullable()->after('status');
                // pending_seller | approved_by_seller | rejected_by_seller | escalated_to_admin | refund_completed
            }
            if (!Schema::hasColumn('orders', 'return_status_workflow')) {
                $table->string('return_status_workflow')->nullable()->after('cancel_status');
                // pending_seller | pickup_initiated | approved_by_seller | rejected_by_seller | escalated_to_admin | refund_completed
            }
            if (!Schema::hasColumn('orders', 'escalated_to_admin_at')) {
                $table->timestamp('escalated_to_admin_at')->nullable()->after('return_status_workflow');
            }
            if (!Schema::hasColumn('orders', 'seller_action_deadline')) {
                $table->timestamp('seller_action_deadline')->nullable()->after('escalated_to_admin_at');
            }
        });

        // Also add escalation columns to order_returns table
        Schema::table('order_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('order_returns', 'escalated_to_admin_at')) {
                $table->timestamp('escalated_to_admin_at')->nullable();
            }
            if (!Schema::hasColumn('order_returns', 'seller_action_deadline')) {
                $table->timestamp('seller_action_deadline')->nullable();
            }
            if (!Schema::hasColumn('order_returns', 'pickup_initiated_at')) {
                $table->timestamp('pickup_initiated_at')->nullable();
            }
            if (!Schema::hasColumn('order_returns', 'admin_refund_status')) {
                $table->string('admin_refund_status')->nullable();
                // pending | processing | completed
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cancel_status', 'return_status_workflow', 'escalated_to_admin_at', 'seller_action_deadline']);
        });
        Schema::table('order_returns', function (Blueprint $table) {
            $table->dropColumn(['escalated_to_admin_at', 'seller_action_deadline', 'pickup_initiated_at', 'admin_refund_status']);
        });
    }
};
