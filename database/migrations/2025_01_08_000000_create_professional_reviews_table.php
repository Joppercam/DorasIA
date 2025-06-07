<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('professional_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->string('source'); // e.g., "The Hollywood Reporter", "Variety", etc.
            $table->string('source_url')->nullable();
            $table->string('author')->nullable();
            $table->string('author_url')->nullable();
            $table->decimal('rating', 3, 1)->nullable(); // e.g., 8.5
            $table->integer('max_rating')->default(10); // e.g., 10 for "8.5/10"
            $table->text('content'); // Full review content
            $table->text('content_es')->nullable(); // Spanish translation
            $table->text('excerpt')->nullable(); // Short excerpt
            $table->text('excerpt_es')->nullable(); // Spanish translation
            $table->date('review_date')->nullable();
            $table->boolean('is_positive')->default(true);
            $table->string('language')->default('en');
            $table->string('tmdb_review_id')->nullable();
            $table->timestamps();
            
            $table->index(['series_id', 'is_positive']);
            $table->index('source');
        });
    }

    public function down()
    {
        Schema::dropIfExists('professional_reviews');
    }
};