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
        Schema::create('title_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->enum('rating_type', ['dislike', 'like', 'love']); // No me gusta, Me gusta, Me encanta
            $table->tinyInteger('rating_value')->comment('1=dislike, 3=like, 5=love');
            $table->timestamps();
            
            // Ensure one rating per user per title
            $table->unique(['user_id', 'series_id']);
            
            // Add indexes for performance
            $table->index(['series_id', 'rating_type']);
            $table->index(['user_id', 'rating_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title_ratings');
    }
};
