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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('display_title')->nullable();
            $table->text('overview')->nullable();
            $table->text('display_overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->date('release_date')->nullable();
            $table->integer('runtime')->nullable(); // duración en minutos
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('vote_count')->default(0);
            $table->string('status')->default('released'); // released, upcoming, etc.
            $table->string('original_language', 10)->default('ko');
            $table->bigInteger('tmdb_id')->unique()->nullable();
            $table->string('imdb_id')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->decimal('revenue', 15, 2)->nullable();
            $table->json('production_companies')->nullable();
            $table->json('production_countries')->nullable();
            $table->json('spoken_languages')->nullable();
            $table->string('tagline')->nullable();
            $table->boolean('adult')->default(false);
            $table->decimal('popularity', 8, 3)->default(0);
            $table->timestamps();
            
            $table->index(['status', 'release_date']);
            $table->index(['vote_average', 'vote_count']);
            $table->index('popularity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
