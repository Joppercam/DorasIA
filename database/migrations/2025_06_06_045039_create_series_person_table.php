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
        Schema::create('series_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->string('role'); // 'actor', 'director', 'writer', 'producer', etc.
            $table->string('character')->nullable(); // character name if actor
            $table->integer('order')->nullable(); // billing order
            $table->string('department')->nullable(); // Acting, Directing, Writing, etc.
            $table->string('job')->nullable(); // specific job title
            $table->timestamps();
            
            $table->unique(['series_id', 'person_id', 'role', 'character']);
            $table->index(['role']);
            $table->index(['department']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('series_person');
    }
};
