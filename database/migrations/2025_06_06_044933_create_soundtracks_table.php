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
        Schema::create('soundtracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('artist');
            $table->string('album')->nullable();
            $table->text('lyrics')->nullable();
            $table->string('spotify_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('apple_music_url')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->boolean('is_main_theme')->default(false);
            $table->boolean('is_ending_theme')->default(false);
            $table->integer('track_number')->nullable();
            $table->timestamps();
            
            $table->index(['series_id']);
            $table->index(['is_main_theme']);
            $table->index(['is_ending_theme']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soundtracks');
    }
};
