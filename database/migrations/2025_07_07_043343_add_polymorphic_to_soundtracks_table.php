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
        Schema::table('soundtracks', function (Blueprint $table) {
            // Agregar campos polimórficos
            $table->string('soundtrackable_type')->nullable()->after('series_id');
            $table->unsignedBigInteger('soundtrackable_id')->nullable()->after('soundtrackable_type');
            
            // Agregar campos adicionales para mejor soporte
            $table->string('youtube_id')->nullable()->after('youtube_url');
            $table->string('preview_url')->nullable()->after('youtube_id');
            $table->decimal('popularity', 5, 2)->default(0)->after('duration');
            $table->boolean('is_active')->default(true)->after('popularity');
            
            // Índices para performance
            $table->index(['soundtrackable_type', 'soundtrackable_id']);
        });
        
        // Migrar datos existentes
        DB::statement("UPDATE soundtracks SET soundtrackable_type = 'App\\Models\\Series', soundtrackable_id = series_id WHERE series_id IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soundtracks', function (Blueprint $table) {
            $table->dropColumn([
                'soundtrackable_type',
                'soundtrackable_id', 
                'youtube_id',
                'preview_url',
                'popularity',
                'is_active'
            ]);
        });
    }
};