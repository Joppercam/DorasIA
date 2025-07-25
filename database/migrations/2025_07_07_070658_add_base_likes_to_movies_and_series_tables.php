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
        Schema::table('movies', function (Blueprint $table) {
            $table->unsignedInteger('base_likes')->default(0)->after('popularity');
            $table->index('base_likes');
        });

        Schema::table('series', function (Blueprint $table) {
            $table->unsignedInteger('base_likes')->default(0)->after('popularity');
            $table->index('base_likes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropIndex(['base_likes']);
            $table->dropColumn('base_likes');
        });

        Schema::table('series', function (Blueprint $table) {
            $table->dropIndex(['base_likes']);
            $table->dropColumn('base_likes');
        });
    }
};
