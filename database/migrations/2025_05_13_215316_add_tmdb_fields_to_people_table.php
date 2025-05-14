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
        Schema::table('people', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->after('id');
            $table->date('birthday')->nullable()->after('biography');
            $table->date('deathday')->nullable()->after('birthday');
            $table->string('gender')->nullable()->after('deathday');
            $table->string('place_of_birth')->nullable()->after('gender');
            $table->decimal('popularity', 8, 2)->nullable()->after('place_of_birth');
            $table->string('imdb_id')->nullable()->after('popularity');
            $table->string('instagram_id')->nullable()->after('imdb_id');
            $table->string('twitter_id')->nullable()->after('instagram_id');

            // Add index for faster lookups
            $table->index('tmdb_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropIndex(['tmdb_id']);
            $table->dropColumn([
                'tmdb_id',
                'birthday',
                'deathday',
                'gender',
                'place_of_birth',
                'popularity',
                'imdb_id',
                'instagram_id',
                'twitter_id',
            ]);
        });
    }
};