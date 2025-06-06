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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->integer('season_number');
            $table->string('name')->nullable();
            $table->text('overview')->nullable();
            $table->date('air_date')->nullable();
            $table->integer('episode_count')->default(0);
            $table->string('poster_path')->nullable();
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('tmdb_id')->nullable();
            $table->timestamps();
            
            $table->unique(['series_id', 'season_number']);
            $table->index(['tmdb_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
