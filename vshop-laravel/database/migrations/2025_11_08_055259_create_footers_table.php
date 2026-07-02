<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('footers', function (Blueprint $table) {
        $table->id();

        // Section 1: Quick Links
        $table->string('quick_home')->nullable();
        $table->string('quick_about')->nullable();
        $table->string('quick_faq')->nullable();
        $table->string('quick_testimonials')->nullable();
        $table->string('quick_blogs')->nullable();

        // Section 2: Company
        $table->string('company_privacy_policy')->nullable();
        $table->string('company_return_policy')->nullable();
        $table->string('company_terms')->nullable();

        // Section 3: Category
        $table->string('category_corporate')->nullable();
        $table->string('category_work')->nullable();
        $table->string('category_healthcare')->nullable();
        $table->string('category_school')->nullable();

        // Section 4: Contact Us
        $table->string('address')->nullable();
        $table->string('phone')->nullable();
        $table->string('fax')->nullable();
        $table->string('email')->nullable();

        // Social Media Icons
        $table->string('facebook')->nullable();
        $table->string('linkedin')->nullable();
        $table->string('youtube')->nullable();
        $table->string('instagram')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footers');
    }
};
