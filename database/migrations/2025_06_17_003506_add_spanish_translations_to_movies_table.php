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
        Schema::table('movies', function (Blueprint $table) {
            // Agregar columnas para traducciones en español
            $table->text('title_es')->nullable()->after('title');
            $table->text('spanish_title')->nullable()->after('title_es');
            $table->text('original_title')->nullable()->after('spanish_title');
            $table->text('overview_es')->nullable()->after('overview');
            $table->text('spanish_overview')->nullable()->after('overview_es');
            
            // Índices para búsquedas
            $table->index(['title_es']);
            $table->index(['spanish_title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropIndex(['title_es']);
            $table->dropIndex(['spanish_title']);
            $table->dropColumn(['title_es', 'spanish_title', 'original_title', 'overview_es', 'spanish_overview']);
        });
    }
};
