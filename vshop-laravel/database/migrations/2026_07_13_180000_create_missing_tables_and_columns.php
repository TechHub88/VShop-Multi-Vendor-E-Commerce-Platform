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
        // 1. activity_logs table
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->string('activity_type');
                $table->string('old_status')->nullable();
                $table->string('new_status')->nullable();
                $table->text('description')->nullable();
                $table->text('remarks')->nullable();
                $table->json('metadata')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 2. recommendation_rules table
        if (!Schema::hasTable('recommendation_rules')) {
            Schema::create('recommendation_rules', function (Blueprint $table) {
                $table->id();
                $table->enum('type', ['product', 'location', 'ai', 'sponsored'])->comment('product: product-to-product, location: city->services, ai: collaborative filtering, sponsored: global sponsored priority');
                $table->string('source')->comment('product_id, city_name, category_id, or "global"');
                $table->string('source_type', 50)->nullable()->comment('product, category, location');
                $table->json('targets')->comment('array of recommended item ids or names');
                $table->integer('priority')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. seller_banners table
        if (!Schema::hasTable('seller_banners')) {
            Schema::create('seller_banners', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('seller_id')->nullable();
                $table->longText('banner_image')->nullable();
                $table->json('banner_image_hash')->nullable();
                $table->json('validation_warnings')->nullable();
                $table->string('banner_title')->nullable();
                $table->text('banner_sub_title')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'na'])->default('pending');
                $table->text('admin_remark')->nullable();
                $table->string('type')->nullable();
                $table->timestamps();
            });
        }

        // 4. seller_seos table
        if (!Schema::hasTable('seller_seos')) {
            Schema::create('seller_seos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('seller_id')->nullable();
                $table->string('home_title');
                $table->text('meta_description');
                $table->text('meta_keywords')->nullable();
                $table->text('search_hashtags')->nullable();
                $table->timestamps();
            });
        }

        // 5. seller_website_templates table
        if (!Schema::hasTable('seller_website_templates')) {
            Schema::create('seller_website_templates', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('seller_id')->nullable();
                $table->bigInteger('template_id');
                $table->timestamps();
            });
        }

        // 6. website_templates table
        if (!Schema::hasTable('website_templates')) {
            Schema::create('website_templates', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('seller_id')->nullable();
                $table->string('name', 150)->nullable();
                $table->string('application_type', 50)->nullable();
                $table->string('preview_image')->nullable();
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        // 7. commission_management table
        if (!Schema::hasTable('commission_management')) {
            Schema::create('commission_management', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('seller_id')->nullable();
                $table->string('app_type', 50)->nullable();
                $table->string('sell_type', 50)->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->decimal('total_sales', 15, 2)->default(0.00);
                $table->string('commission_type', 20)->default('percentage');
                $table->decimal('seller_commission', 10, 2)->default(60.00);
                $table->decimal('referrer_commission', 10, 2)->default(10.00);
                $table->decimal('platform_commission', 10, 2)->default(30.00);
                $table->unsignedBigInteger('referral_id')->nullable();
                $table->string('referral_code', 100)->nullable();
                $table->integer('priority')->default(0);
                $table->date('effective_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->boolean('boost_enabled')->default(false);
                $table->string('status', 20)->default('active');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 8. Add columns to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'platform')) {
                $table->string('platform')->default('vshop')->after('inventory_type');
            }
            if (!Schema::hasColumn('products', 'sold')) {
                $table->integer('sold')->default(0)->after('platform');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['platform', 'sold']);
        });

        Schema::dropIfExists('commission_management');
        Schema::dropIfExists('website_templates');
        Schema::dropIfExists('seller_website_templates');
        Schema::dropIfExists('seller_seos');
        Schema::dropIfExists('seller_banners');
        Schema::dropIfExists('recommendation_rules');
        Schema::dropIfExists('activity_logs');
    }
};
