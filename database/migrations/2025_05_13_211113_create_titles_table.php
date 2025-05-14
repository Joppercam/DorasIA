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
        Schema::create('titles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('original_title')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->year('release_year')->nullable();
            $table->string('country')->default('Korea');
            $table->enum('type', ['movie', 'series'])->default('series');
            $table->integer('duration')->nullable()->comment('Duration in minutes for movies');
            $table->string('trailer_url')->nullable();
            $table->string('slug')->unique();
            $table->boolean('featured')->default(false);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
