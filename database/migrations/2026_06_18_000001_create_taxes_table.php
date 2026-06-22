<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('rate', 5, 2); // e.g., 18.00 for 18%
            $table->string('region')->nullable(); // e.g., "India", "California"
            $table->string('type')->default('gst'); // gst, sales_tax, vat …
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('taxes');
    }
};
?>
