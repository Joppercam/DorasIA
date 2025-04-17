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
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tv_show_id')->constrained()->onDelete('cascade');
            $table->foreignId('season_id')->constrained()->onDelete('cascade');
            $table->integer('episode_number');
            $table->string('name')->nullable();
            $table->text('overview')->nullable();
            $table->string('still_path')->nullable();
            $table->integer('runtime')->nullable();
            $table->date('air_date')->nullable();
            $table->string('api_id')->nullable();
            $table->timestamps();
            
            $table->unique(['season_id', 'episode_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};