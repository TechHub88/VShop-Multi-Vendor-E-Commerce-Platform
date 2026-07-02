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
      Schema::create('institution_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->enum('type', ['school', 'hospital', 'agency', 'petrol_pump']);

    $table->string('institution_name')->nullable();
    $table->string('board')->nullable();
    $table->string('medium')->nullable();
    $table->integer('student_strength')->nullable();
    $table->string('uniform_pattern')->nullable();

    $table->integer('doctor_count')->nullable();
    $table->integer('nurse_count')->nullable();
    $table->string('staff_uniform_type')->nullable();

    $table->string('gst_number')->nullable();
    $table->string('distribution_area')->nullable();

    $table->string('company')->nullable();
    $table->integer('staff_count')->nullable();

    $table->string('city')->nullable();
    $table->string('area')->nullable();
    $table->text('clothing_requirement')->nullable();
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institution_details');
    }
};
