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
            $table->string('known_for_department')->nullable(); // Acting, Directing, etc.
            $table->text('biography')->nullable();
            $table->date('birthday')->nullable();
            $table->date('deathday')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('profile_path')->nullable();
            $table->string('imdb_id')->nullable();
            $table->integer('tmdb_id')->unique()->nullable();
            $table->decimal('popularity', 8, 3)->default(0);
            $table->boolean('adult')->default(false);
            $table->string('homepage')->nullable();
            $table->json('also_known_as')->nullable(); // alternative names
            $table->integer('gender')->nullable(); // 0: Not specified, 1: Female, 2: Male, 3: Non-binary
            $table->timestamps();
            
            $table->index(['tmdb_id']);
            $table->index(['known_for_department']);
            $table->index(['popularity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
