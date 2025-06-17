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
            $table->string('name_es')->nullable()->after('name');
            
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('people', 'biography_es')) {
                $table->text('biography_es')->nullable()->after('biography');
            }
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
            $table->dropColumn(['name_es', 'biography_es', 'place_of_birth_es']);
        });
    }
};
