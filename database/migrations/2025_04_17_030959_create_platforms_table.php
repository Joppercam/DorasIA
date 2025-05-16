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
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('website_url')->nullable();
            $table->text('description')->nullable();
            $table->string('api_id')->nullable();
            $table->timestamps();
        });
        
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 5)->unique();
            $table->timestamps();
        });
        
        Schema::create('availability', function (Blueprint $table) {
            $table->id();
            $table->morphs('content'); // Para películas o series
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->string('url')->nullable(); // URL directa al contenido
            $table->enum('quality', ['SD', 'HD', '4K'])->default('HD');
            $table->enum('type', ['subscription', 'rent', 'buy', 'free'])->default('subscription');
            $table->decimal('price', 6, 2)->nullable(); // Para alquiler/compra
            $table->timestamps();
            
            $table->unique(['content_type', 'content_id', 'platform_id', 'country_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('platforms');
    }
};