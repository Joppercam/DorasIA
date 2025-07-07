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
        Schema::create('soundtracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            
            // Información básica de la canción
            $table->string('title');
            $table->string('artist');
            $table->string('album')->nullable();
            $table->text('lyrics')->nullable();
            $table->text('lyrics_spanish')->nullable();
            $table->text('lyrics_romanized')->nullable();
            
            // Tipo de soundtrack
            $table->enum('type', ['opening', 'ending', 'ost', 'insert_song', 'theme'])->default('ost');
            $table->integer('episode_number')->nullable(); // Para canciones específicas de episodio
            
            // Enlaces y recursos
            $table->string('spotify_url')->nullable();
            $table->string('apple_music_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('youtube_id')->nullable();
            $table->string('preview_url')->nullable(); // URL de preview de 30 segundos
            
            // Metadatos
            $table->integer('duration')->nullable(); // Duración en segundos
            $table->date('release_date')->nullable();
            $table->string('genre')->nullable();
            $table->decimal('popularity', 5, 2)->default(0);
            
            // Imágenes
            $table->string('cover_image')->nullable();
            $table->string('artist_image')->nullable();
            
            // Control
            $table->boolean('is_active')->default(true);
            $table->integer('play_count')->default(0);
            $table->decimal('user_rating', 3, 1)->nullable();
            
            $table->timestamps();
            
            // Índices para búsqueda
            $table->index(['series_id', 'type']);
            $table->index(['artist', 'title']);
            $table->index(['popularity', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soundtracks');
    }
};