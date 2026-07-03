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
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('seller_id')->nullable()->after('id');
            $table->string('source')->nullable()->after('seller_id');
            $table->unsignedBigInteger('brand_id')->nullable()->after('source');
            $table->unsignedBigInteger('branch_id')->nullable()->after('brand_id');
            $table->string('company_name')->nullable()->after('phone');
            $table->string('gstin')->nullable()->after('company_name');
            $table->string('country')->nullable()->after('gstin');
            $table->string('state')->nullable()->after('country');
            $table->string('city')->nullable()->after('state');
            $table->string('address_line1')->nullable()->after('city');
            $table->string('pincode')->nullable()->after('address_line1');
            $table->string('profile_picture')->nullable()->after('pincode');
            $table->string('status')->nullable()->after('password');
            $table->string('otp')->nullable()->after('status');
            $table->timestamp('otp_generated_at')->nullable()->after('otp');
            $table->boolean('is_active')->default(1)->after('otp_generated_at');
            $table->string('type')->nullable()->after('is_active');
            $table->string('gender')->nullable()->after('type');
            $table->date('dob')->nullable()->after('gender');
            $table->string('marital_status')->nullable()->after('dob');
            $table->date('anniversary')->nullable()->after('marital_status');
            $table->string('photo')->nullable()->after('anniversary');
            $table->string('reference')->nullable()->after('photo');
            $table->string('latitude')->nullable()->after('reference');
            $table->string('longitude')->nullable()->after('latitude');
            $table->integer('is_blocked')->default(0)->after('longitude');
            $table->integer('risk_level')->default(0)->after('is_blocked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'seller_id', 'source', 'brand_id', 'branch_id', 'company_name',
                'gstin', 'country', 'state', 'city', 'address_line1', 'pincode',
                'profile_picture', 'status', 'otp', 'otp_generated_at', 'is_active',
                'type', 'gender', 'dob', 'marital_status', 'anniversary', 'photo',
                'reference', 'latitude', 'longitude', 'is_blocked', 'risk_level'
            ]);
        });
    }
};
