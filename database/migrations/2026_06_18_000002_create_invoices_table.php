<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->default('INV');
            $table->string('style')->default('Professional');
            $table->string('company_name');
            $table->string('company_address');
            $table->string('company_email');
            $table->string('company_phone');
            $table->string('company_gst')->nullable();
            $table->json('footer')->nullable();
            $table->json('format')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('invoices');
    }
};
?>
