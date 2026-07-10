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
            if (!Schema::hasColumn('products', 'meeting_link')) {
                $table->string('meeting_link')->nullable();
            }
            if (!Schema::hasColumn('products', 'professional_slots')) {
                $table->text('professional_slots')->nullable();
            }
            if (!Schema::hasColumn('products', 'cancel_policy_prof')) {
                $table->text('cancel_policy_prof')->nullable();
            }
            if (!Schema::hasColumn('products', 'portfolio')) {
                $table->text('portfolio')->nullable();
            }
            if (!Schema::hasColumn('products', 'languages')) {
                $table->text('languages')->nullable();
            }
            if (!Schema::hasColumn('products', 'max_booking')) {
                $table->integer('max_booking')->nullable();
            }
            if (!Schema::hasColumn('products', 'session_mode')) {
                $table->string('session_mode')->nullable();
            }
            if (!Schema::hasColumn('products', 'professional_type')) {
                $table->string('professional_type')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columnsToDrop = [];
            $cols = ['meeting_link', 'professional_slots', 'cancel_policy_prof', 'portfolio', 'languages', 'max_booking', 'session_mode', 'professional_type'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('products', $c)) {
                    $columnsToDrop[] = $c;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
