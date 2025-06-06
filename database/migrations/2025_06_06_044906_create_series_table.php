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
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->text('overview')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('status')->nullable();
            $table->date('first_air_date')->nullable();
            $table->date('last_air_date')->nullable();
            $table->integer('number_of_seasons')->default(0);
            $table->integer('number_of_episodes')->default(0);
            $table->integer('episode_run_time')->nullable();
            $table->string('original_language', 10)->nullable();
            $table->string('origin_country', 10)->nullable();
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('vote_count')->default(0);
            $table->decimal('popularity', 8, 3)->default(0);
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('homepage')->nullable();
            $table->boolean('in_production')->default(false);
            $table->json('production_companies')->nullable();
            $table->json('production_countries')->nullable();
            $table->json('spoken_languages')->nullable();
            $table->json('networks')->nullable();
            $table->string('tagline')->nullable();
            $table->string('type')->nullable(); // Scripted, Documentary, etc.
            $table->integer('tmdb_id')->unique()->nullable();
            $table->string('imdb_id')->nullable();
            $table->boolean('is_korean_drama')->default(false);
            $table->timestamps();
            
            $table->index(['tmdb_id']);
            $table->index(['is_korean_drama']);
            $table->index(['first_air_date']);
            $table->index(['vote_average']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
