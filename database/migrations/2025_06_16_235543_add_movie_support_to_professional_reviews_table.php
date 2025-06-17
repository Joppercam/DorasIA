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
        Schema::table('professional_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('movie_id')->nullable()->after('series_id');
            $table->string('reviewable_type')->default('series')->after('movie_id'); // 'series' or 'movie'
            
            // Hacer series_id nullable para permitir reseñas de películas
            $table->unsignedBigInteger('series_id')->nullable()->change();
            
            // Índices
            $table->index(['movie_id']);
            $table->index(['reviewable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_reviews', function (Blueprint $table) {
            $table->dropIndex(['movie_id']);
            $table->dropIndex(['reviewable_type']);
            $table->dropColumn(['movie_id', 'reviewable_type']);
        });
    }
};
