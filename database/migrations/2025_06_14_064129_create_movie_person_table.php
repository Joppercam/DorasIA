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
        Schema::create('movie_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained()->onDelete('cascade');
            $table->string('character')->nullable(); // Para actores
            $table->string('department'); // Acting, Directing, Writing, etc.
            $table->string('job')->nullable(); // Director, Writer, etc. para crew
            $table->integer('order')->default(0); // Orden de aparición
            $table->timestamps();
            
            $table->unique(['movie_id', 'person_id', 'department'], 'movie_person_unique');
            $table->index(['movie_id', 'department']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_person');
    }
};
