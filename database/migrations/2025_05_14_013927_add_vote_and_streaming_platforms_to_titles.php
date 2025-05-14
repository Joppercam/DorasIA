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
            $table->float('vote_average', 3, 1)->nullable()->after('featured');
            $table->unsignedInteger('vote_count')->nullable()->after('vote_average');
            $table->float('popularity', 8, 3)->nullable()->after('vote_count');
            $table->text('streaming_platforms')->nullable()->after('popularity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->dropColumn([
                'vote_average',
                'vote_count',
                'popularity',
                'streaming_platforms',
            ]);
        });
    }
};
