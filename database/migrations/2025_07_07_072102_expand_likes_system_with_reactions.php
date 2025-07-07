<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add reaction_type column to likes table
        Schema::table('likes', function (Blueprint $table) {
            $table->enum('reaction_type', ['like', 'love'])->default('like')->after('likeable_id');
            $table->index(['reaction_type', 'likeable_type', 'likeable_id']);
        });

        // Update the unique constraint to include reaction_type
        Schema::table('likes', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'likeable_type', 'likeable_id']);
            $table->unique(['user_id', 'likeable_type', 'likeable_id', 'reaction_type'], 'likes_user_likeable_reaction_unique');
        });

        // Add base_loves column to movies table
        Schema::table('movies', function (Blueprint $table) {
            $table->unsignedInteger('base_loves')->default(0)->after('base_likes');
            $table->index('base_loves');
        });

        // Add base_loves column to series table
        Schema::table('series', function (Blueprint $table) {
            $table->unsignedInteger('base_loves')->default(0)->after('base_likes');
            $table->index('base_loves');
        });

        // Update existing likes to have 'like' as reaction_type (already set as default)
        DB::table('likes')->whereNull('reaction_type')->update(['reaction_type' => 'like']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove base_loves from movies
        Schema::table('movies', function (Blueprint $table) {
            $table->dropIndex(['base_loves']);
            $table->dropColumn('base_loves');
        });

        // Remove base_loves from series
        Schema::table('series', function (Blueprint $table) {
            $table->dropIndex(['base_loves']);
            $table->dropColumn('base_loves');
        });

        // Restore original unique constraint in likes table
        Schema::table('likes', function (Blueprint $table) {
            $table->dropUnique('likes_user_likeable_reaction_unique');
            $table->unique(['user_id', 'likeable_type', 'likeable_id']);
        });

        // Remove reaction_type column from likes table
        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex(['reaction_type', 'likeable_type', 'likeable_id']);
            $table->dropColumn('reaction_type');
        });
    }
};