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
        // La tabla users ya existe en Laravel por defecto, pero la expandiremos
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('avatar')->nullable()->after('username');
            $table->enum('role', ['user', 'moderator', 'admin'])->default('user')->after('avatar');
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
        
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->date('birth_date')->nullable();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->json('social_links')->nullable(); // Para almacenar enlaces a redes sociales
            $table->timestamps();
        });
        
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('favorite_genres')->nullable();
            $table->json('favorite_countries')->nullable(); // Para preferencias de origen (China, Japón, Corea)
            $table->boolean('email_notifications')->default(true);
            $table->boolean('dark_mode')->default(true);
            $table->enum('content_language', ['original', 'dubbed', 'both'])->default('both');
            $table->timestamps();
        });
        
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'slug']);
        });
        
        Schema::create('watchlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('watchlist_id')->constrained()->onDelete('cascade');
            $table->morphs('content'); // Para películas o series
            $table->integer('position')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->unique(['watchlist_id', 'content_type', 'content_id']);
        });
        
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('content'); // Para películas o series
            $table->timestamps();
            
            $table->unique(['user_id', 'content_type', 'content_id']);
        });
        
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('content'); // Para películas o series
            $table->decimal('rating', 3, 1); // Puntuación de 0 a 10
            $table->text('review')->nullable();
            $table->boolean('contains_spoilers')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'content_type', 'content_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('watchlist_items');
        Schema::dropIfExists('watchlists');
        Schema::dropIfExists('preferences');
        Schema::dropIfExists('profiles');
        
        // Revertir cambios en la tabla users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'avatar', 'role', 'is_active', 'last_login_at']);
        });
    }
};