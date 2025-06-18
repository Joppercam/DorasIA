<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actor_content_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_content_id')->constrained('actor_contents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('actor_content_comments')->onDelete('cascade'); // Para respuestas
            $table->text('content');
            $table->boolean('is_approved')->default(true); // Moderación opcional
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index(['actor_content_id', 'created_at']);
            $table->index(['parent_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('actor_content_comments');
    }
};