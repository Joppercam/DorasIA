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
        Schema::create('profile_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade'); // Profile being followed
            $table->foreignId('follower_id')->constrained('profiles')->onDelete('cascade'); // Profile following
            $table->timestamps();
            
            // Prevent duplicate follows
            $table->unique(['profile_id', 'follower_id']);
            
            // Indexes for performance
            $table->index('profile_id');
            $table->index('follower_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_followers');
    }
};