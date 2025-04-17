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
            $table->string('original_title')->nullable();
            $table->string('slug')->unique();
            $table->text('overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->integer('runtime')->nullable();
            $table->date('release_date')->nullable();
            $table->string('original_language', 10)->nullable();
            $table->string('country_of_origin', 10)->nullable();
            $table->decimal('popularity', 10, 2)->default(0);
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('vote_count')->default(0);
            $table->string('status', 20)->default('released');
            $table->string('api_id')->nullable();
            $table->string('api_source', 30)->nullable();
            $table->timestamps();
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