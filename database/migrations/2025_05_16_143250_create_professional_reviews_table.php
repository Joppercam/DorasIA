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
        Schema::create('professional_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('title_id')->constrained()->onDelete('cascade');
            $table->string('reviewer_name');
            $table->string('reviewer_source');
            $table->text('content');
            $table->float('rating', 3, 1)->nullable(); // Rating de 0.0 a 10.0
            $table->date('review_date')->nullable();
            $table->string('review_url')->nullable();
            $table->string('language')->default('es');
            $table->boolean('is_featured')->default(false);
            $table->string('external_id')->nullable()->unique(); // ID de la reseña en la fuente externa
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index(['title_id', 'is_featured']);
            $table->index('reviewer_source');
            $table->index('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_reviews');
    }
};