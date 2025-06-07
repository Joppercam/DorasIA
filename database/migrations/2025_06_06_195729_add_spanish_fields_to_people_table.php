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
            // Check if biography_es column already exists
            if (!Schema::hasColumn('people', 'biography_es')) {
                $table->text('biography_es')->nullable()->after('biography');
            }
            
            // Always add place_of_birth_es since it doesn't exist
            if (!Schema::hasColumn('people', 'place_of_birth_es')) {
                $table->string('place_of_birth_es')->nullable()->after('place_of_birth');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn(['biography_es', 'place_of_birth_es']);
        });
    }
};
