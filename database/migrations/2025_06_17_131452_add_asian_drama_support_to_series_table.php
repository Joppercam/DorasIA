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
        Schema::table('series', function (Blueprint $table) {
            // Expandir soporte para todos los doramas asiáticos
            $table->string('drama_type', 20)->default('kdrama')->after('is_korean_drama');
            $table->string('country_code', 5)->default('KR')->after('drama_type');
            $table->string('country_name', 50)->default('Corea del Sur')->after('country_code');
            $table->string('language_name', 50)->default('Coreano')->after('country_name');
            
            // Índices para filtros por país/tipo
            $table->index(['drama_type']);
            $table->index(['country_code']);
            $table->index(['drama_type', 'vote_average']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->dropIndex(['drama_type']);
            $table->dropIndex(['country_code']);
            $table->dropIndex(['drama_type', 'vote_average']);
            $table->dropColumn(['drama_type', 'country_code', 'country_name', 'language_name']);
        });
    }
};
