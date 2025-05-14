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
        Schema::table('titles', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->after('id');
            $table->string('content_rating')->nullable()->after('featured');
            $table->string('status')->nullable()->after('content_rating');
            $table->string('original_language')->nullable()->after('status');
            $table->unsignedInteger('number_of_seasons')->nullable()->after('original_language');
            $table->unsignedInteger('number_of_episodes')->nullable()->after('number_of_seasons');

            // Add index for faster lookups
            $table->index('tmdb_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->dropIndex(['tmdb_id']);
            $table->dropColumn([
                'tmdb_id',
                'content_rating',
                'status',
                'original_language',
                'number_of_seasons',
                'number_of_episodes',
            ]);
        });
    }
};