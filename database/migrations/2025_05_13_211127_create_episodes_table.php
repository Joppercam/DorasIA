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
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('number');
            $table->text('overview')->nullable();
            $table->string('still')->nullable();
            $table->integer('runtime')->nullable()->comment('Runtime in minutes');
            $table->date('air_date')->nullable();
            $table->string('video_url')->nullable();
            $table->timestamps();
            
            $table->unique(['season_id', 'number']);
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
