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
        Schema::table('watch_histories', function (Blueprint $table) {
            $table->float('progress')->default(0)->after('watched_seconds');
            $table->integer('season_number')->nullable()->after('progress');
            $table->integer('episode_number')->nullable()->after('season_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('watch_histories', function (Blueprint $table) {
            $table->dropColumn(['progress', 'season_number', 'episode_number']);
        });
    }
};
