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
        Schema::create('banned_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique();
            $table->timestamps();
        });

        // Seed initial fraud and vulgar keywords
        $initialKeywords = [
            'porn', 'sex', 'nude', 'naked', 'escort', 'massage', 'hooker', 
            'prostitute', 'fuck', 'shit', 'bitch', 'asshole', 'dick', 'cock', 
            'pussy', 'vagina', 'penis', 'slut', 'whore', 'cunt', 'fraud', 'scam'
        ];

        foreach ($initialKeywords as $keyword) {
            \App\Models\BannedKeyword::create(['keyword' => $keyword]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_keywords');
    }
};
