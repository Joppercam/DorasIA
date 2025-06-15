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
        Schema::create('upcoming_series', function (Blueprint $table) {
            $table->id();
            $table->integer('tmdb_id')->unique();
            $table->string('title');
            $table->string('spanish_title')->nullable();
            $table->text('overview')->nullable();
            $table->text('spanish_overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->date('release_date')->nullable();
            $table->enum('type', ['new_series', 'new_season'])->default('new_series');
            $table->integer('season_number')->nullable();
            $table->integer('episode_count')->nullable();
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->decimal('popularity', 10, 3)->default(0);
            $table->enum('status', ['upcoming', 'released', 'cancelled'])->default('upcoming');
            $table->foreignId('existing_series_id')->nullable()->constrained('series')->onDelete('cascade');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['status', 'release_date']);
            $table->index(['type', 'release_date']);
            $table->index(['popularity']);
            $table->index(['release_date']);
            
            // Índice único compuesto para evitar duplicados
            $table->unique(['tmdb_id', 'type', 'season_number'], 'unique_upcoming_series');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upcoming_series');
    }
};
