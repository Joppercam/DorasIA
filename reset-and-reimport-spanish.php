<?php
/**
 * Script para limpiar datos y reimportar con traducciones al español
 * Ejecutar desde la raíz del proyecto: php reset-and-reimport-spanish.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🤖 DORASIA - Reset y Reimportación con Español\n";
echo "===============================================\n\n";

echo "⚠️  ATENCIÓN: Este script eliminará TODOS los datos existentes.\n";
echo "¿Estás seguro de continuar? (y/N): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 'y') {
    echo "❌ Operación cancelada.\n";
    exit(1);
}

echo "\n🧹 Paso 1: Limpiando datos existentes...\n";

try {
    // Limpiar tablas en orden correcto (respetando foreign keys)
    $tablesToClean = [
        'series_person',
        'series_genre', 
        'seasons',
        'episodes',
        'series'
    ];

    foreach ($tablesToClean as $table) {
        DB::statement("DELETE FROM {$table}");
        echo "   ✅ Tabla {$table} limpiada\n";
    }

    // Reset auto-increment
    foreach (['series', 'seasons', 'episodes'] as $table) {
        DB::statement("DELETE FROM sqlite_sequence WHERE name='{$table}'");
    }

    echo "   ✅ Auto-increment reseteado\n\n";

} catch (Exception $e) {
    echo "❌ Error limpiando datos: " . $e->getMessage() . "\n";
    exit(1);
}

echo "📥 Paso 2: Importando contenido con traducciones...\n";

try {
    // Importar géneros primero
    echo "   📚 Importando géneros...\n";
    Artisan::call('import:korean-dramas', ['--pages' => 1, '--genres-only' => true]);
    echo "   ✅ Géneros importados\n";

    // Importar primeras 20 páginas de contenido
    echo "   📺 Importando K-Dramas (20 páginas)...\n";
    Artisan::call('import:korean-dramas', ['--pages' => 20]);
    echo "   ✅ K-Dramas importados\n";

} catch (Exception $e) {
    echo "❌ Error importando: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🌐 Paso 3: Traduciendo contenido al español...\n";

try {
    // Traducir títulos y sinopsis
    echo "   📝 Traduciendo títulos...\n";
    Artisan::call('translate:content', ['--type' => 'titles', '--limit' => 50]);
    
    echo "   📖 Traduciendo sinopsis...\n";
    Artisan::call('translate:content', ['--type' => 'synopsis', '--limit' => 50]);
    
    echo "   ✅ Traducciones completadas\n";

} catch (Exception $e) {
    echo "⚠️  Advertencia traduciendo: " . $e->getMessage() . "\n";
    echo "   ℹ️  Continuando sin traducciones automáticas...\n";
}

echo "\n📊 Paso 4: Verificando resultados...\n";

try {
    $seriesCount = DB::table('series')->count();
    $translatedTitles = DB::table('series')->whereNotNull('title_es')->count();
    $translatedOverviews = DB::table('series')->whereNotNull('overview_es')->count();

    echo "   📈 Total series: {$seriesCount}\n";
    echo "   🇪🇸 Títulos traducidos: {$translatedTitles}\n";
    echo "   📝 Sinopsis traducidas: {$translatedOverviews}\n";

} catch (Exception $e) {
    echo "❌ Error verificando: " . $e->getMessage() . "\n";
}

echo "\n🎉 ¡Proceso completado!\n";
echo "   🌐 Sitio disponible en: https://dorasia.cl\n";
echo "   📱 Optimizado para móvil\n";
echo "   🇪🇸 Contenido en español\n\n";

echo "💡 Comandos útiles:\n";
echo "   - Traducir más contenido: php artisan translate:content\n";
echo "   - Importar más dramas: php artisan import:korean-dramas --pages=30\n";
echo "   - Ver estadísticas: php artisan tinker --execute=\"echo 'Series: ' . App\\Models\\Series::count();\"\n\n";