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
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('api_id')->nullable();
            $table->timestamps();
        });
        
        // Tablas pivote para relaciones N:M con géneros
        Schema::create('genre_movie', function (Blueprint $table) {
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->primary(['genre_id', 'movie_id']);
        });
        
        Schema::create('genre_tv_show', function (Blueprint $table) {
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->foreignId('tv_show_id')->constrained()->onDelete('cascade');
            $table->primary(['genre_id', 'tv_show_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_tv_show');
        Schema::dropIfExists('genre_movie');
        Schema::dropIfExists('genres');
    }
};