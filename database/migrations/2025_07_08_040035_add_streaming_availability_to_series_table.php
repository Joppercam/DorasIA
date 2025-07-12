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
        Schema::table('series', function (Blueprint $table) {
            $table->boolean('netflix_available')->default(false)->after('trailer_youtube_id');
            $table->boolean('disney_available')->default(false)->after('netflix_available');
            $table->boolean('amazon_available')->default(false)->after('disney_available');
            $table->boolean('apple_available')->default(false)->after('amazon_available');
            $table->boolean('hbo_available')->default(false)->after('apple_available');
            $table->boolean('crunchyroll_available')->default(false)->after('hbo_available');
            $table->boolean('viki_available')->default(false)->after('crunchyroll_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->dropColumn([
                'netflix_available',
                'disney_available', 
                'amazon_available',
                'apple_available',
                'hbo_available',
                'crunchyroll_available',
                'viki_available'
            ]);
        });
    }
};
