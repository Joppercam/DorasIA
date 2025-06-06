<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->string('title_es')->nullable()->after('title');
            $table->text('overview_es')->nullable()->after('overview');
            $table->text('synopsis_es')->nullable()->after('synopsis');
            $table->string('tagline_es')->nullable()->after('tagline');
        });

        Schema::table('genres', function (Blueprint $table) {
            $table->string('name_es')->nullable()->after('name');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->text('biography_es')->nullable()->after('biography');
        });

        Schema::table('episodes', function (Blueprint $table) {
            $table->string('name_es')->nullable()->after('name');
            $table->text('overview_es')->nullable()->after('overview');
        });
    }

    public function down(): void
    {
        Schema::table('series', function (Blueprint $table) {
            $table->dropColumn(['title_es', 'overview_es', 'synopsis_es', 'tagline_es']);
        });

        Schema::table('genres', function (Blueprint $table) {
            $table->dropColumn('name_es');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('biography_es');
        });

        Schema::table('episodes', function (Blueprint $table) {
            $table->dropColumn(['name_es', 'overview_es']);
        });
    }
};