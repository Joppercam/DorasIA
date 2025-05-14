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
        Schema::create('title_person', function (Blueprint $table) {
            $table->id();
            $table->foreignId('title_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['actor', 'director', 'writer', 'producer']);
            $table->string('character')->nullable()->comment('Character name for actors');
            $table->integer('order')->default(0)->comment('Display order');
            $table->timestamps();

            $table->unique(['title_id', 'person_id', 'role', 'character'], 'title_person_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title_person');
    }
};
