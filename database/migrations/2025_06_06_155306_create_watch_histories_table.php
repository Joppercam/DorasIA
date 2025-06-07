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
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->integer('episodes_watched')->default(0);
            $table->integer('total_episodes')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0.00);
            $table->enum('status', ['watching', 'completed', 'dropped', 'on_hold'])->default('watching');
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'series_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
