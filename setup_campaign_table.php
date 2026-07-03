<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasTable('seller_campaign_participations')) {
    Schema::create('seller_campaign_participations', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('seller_id');
        $table->unsignedBigInteger('product_id');
        $table->string('campaign_type'); // 'percentage_20', 'bogo', 'buy2_get1'
        $table->text('combo_details')->nullable();
        $table->string('status')->default('approved');
        $table->timestamps();

        // Foreign keys
        $table->foreign('seller_id')->references('id')->on('sellers')->onDelete('cascade');
        $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
    echo "Table created successfully!";
} else {
    echo "Table already exists!";
}
