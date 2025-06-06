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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable'); // polymorphic relationship
            $table->string('type'); // 'poster', 'backdrop', 'still', 'profile', 'logo'
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('aspect_ratio')->nullable();
            $table->decimal('vote_average', 3, 1)->default(0);
            $table->integer('vote_count')->default(0);
            $table->string('iso_639_1', 5)->nullable(); // language code
            $table->timestamps();
            
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
