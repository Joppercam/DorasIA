<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actor_content_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_content_id')->constrained('actor_contents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['like', 'dislike', 'love']); // me gusta, no me gusta, me encanta
            $table->timestamps();

            // Un usuario solo puede tener una reacción por contenido
            $table->unique(['actor_content_id', 'user_id']);
            $table->index(['actor_content_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('actor_content_reactions');
    }
};