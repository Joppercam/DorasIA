<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actor_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->enum('type', [
                'interview', 
                'behind_scenes', 
                'biography', 
                'news', 
                'gallery', 
                'video', 
                'article', 
                'timeline', 
                'trivia', 
                'social'
            ]);
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('media_url')->nullable();
            $table->enum('media_type', ['image', 'video', 'audio', 'document'])->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->integer('duration')->nullable(); // en segundos
            $table->boolean('is_exclusive')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('source')->nullable();
            $table->string('external_url')->nullable();
            $table->json('tags')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['person_id', 'type']);
            $table->index(['published_at', 'is_featured']);
            $table->index('view_count');
        });
    }

    public function down()
    {
        Schema::dropIfExists('actor_contents');
    }
};