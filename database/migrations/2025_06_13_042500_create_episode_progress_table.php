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
        Schema::create('episode_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->foreignId('episode_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['not_started', 'watching', 'completed']);
            $table->integer('progress_minutes')->default(0);
            $table->integer('total_minutes')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Índices para rendimiento
            $table->index(['user_id', 'series_id']);
            $table->index(['user_id', 'episode_id']);
            $table->unique(['user_id', 'episode_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episode_progress');
    }
};
