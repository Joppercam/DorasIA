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
        // Limpiar noticias no relacionadas con contenido asiático
        DB::table('news')
            ->where(function($query) {
                $query->where('title', 'LIKE', '%basketball%')
                      ->orWhere('title', 'LIKE', '%Indiana University%')
                      ->orWhere('title', 'LIKE', '%IU%')
                      ->orWhere('title', 'NOT LIKE', '%drama%')
                      ->orWhere('title', 'NOT LIKE', '%movie%')
                      ->orWhere('title', 'NOT LIKE', '%serie%')
                      ->orWhere('title', 'NOT LIKE', '%actor%')
                      ->orWhere('title', 'NOT LIKE', '%actriz%');
            })
            ->where(function($query) {
                $query->where('content', 'NOT LIKE', '%korea%')
                      ->where('content', 'NOT LIKE', '%japan%')
                      ->where('content', 'NOT LIKE', '%china%')
                      ->where('content', 'NOT LIKE', '%taiwan%')
                      ->where('content', 'NOT LIKE', '%thai%')
                      ->where('content', 'NOT LIKE', '%asia%')
                      ->where('content', 'NOT LIKE', '%drama%');
            })
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se puede revertir la eliminación de datos
    }
};