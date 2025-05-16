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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('slug')->unique();
            $table->text('biography')->nullable();
            $table->string('profile_path')->nullable();
            $table->date('birthday')->nullable();
            $table->date('deathday')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->decimal('popularity', 10, 2)->default(0);
            $table->string('api_id')->nullable();
            $table->string('api_source', 30)->nullable();
            $table->timestamps();
        });
        
        // Tablas para roles en películas y series
        Schema::create('movie_cast', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained()->onDelete('cascade');
            $table->string('character')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        Schema::create('movie_crew', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained()->onDelete('cascade');
            $table->string('job')->nullable();
            $table->string('department')->nullable();
            $table->timestamps();
        });
        
        Schema::create('tv_show_cast', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained()->onDelete('cascade');
            $table->string('character')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
        
        Schema::create('tv_show_crew', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained()->onDelete('cascade');
            $table->string('job')->nullable();
            $table->string('department')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_show_crew');
        Schema::dropIfExists('tv_show_cast');
        Schema::dropIfExists('movie_crew');
        Schema::dropIfExists('movie_cast');
        Schema::dropIfExists('people');
    }
};