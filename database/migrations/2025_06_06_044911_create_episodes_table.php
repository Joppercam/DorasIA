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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->foreignId('season_id')->constrained()->onDelete('cascade');
            $table->integer('episode_number');
            $table->integer('season_number');
            $table->string('name');
            $table->text('overview')->nullable();
            $table->text('detailed_summary')->nullable();
            $table->date('air_date')->nullable();
            $table->integer('runtime')->nullable(); // in minutes
            $table->string('still_path')->nullable(); // episode screenshot
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('vote_count')->default(0);
            $table->integer('tmdb_id')->nullable();
            $table->json('guest_stars')->nullable(); // guest actors for this episode
            $table->json('crew')->nullable(); // directors, writers for this episode
            $table->timestamps();
            
            $table->unique(['series_id', 'season_number', 'episode_number']);
            $table->index(['tmdb_id']);
            $table->index(['air_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
