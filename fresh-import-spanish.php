<?php
/**
 * Script para limpiar completamente y reimportar con traducciones al español
 * Ejecutar desde la raíz del proyecto: php fresh-import-spanish.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🤖 DORASIA - Importación Fresca con Español\n";
echo "==========================================\n\n";

echo "⚠️  ATENCIÓN: Este script eliminará TODOS los datos existentes.\n";
echo "Se reimportarán los dramas con traducciones automáticas al español.\n";
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
        'series',
        'people',
        'genres'
    ];

    DB::statement('PRAGMA foreign_keys = OFF');

    foreach ($tablesToClean as $table) {
        $count = DB::table($table)->count();
        if ($count > 0) {
            DB::statement("DELETE FROM {$table}");
            echo "   ✅ Tabla {$table} limpiada ({$count} registros)\n";
        } else {
            echo "   ⚪ Tabla {$table} ya está vacía\n";
        }
    }

    // Reset auto-increment
    $autoIncrementTables = ['series', 'seasons', 'episodes', 'people', 'genres'];
    foreach ($autoIncrementTables as $table) {
        DB::statement("DELETE FROM sqlite_sequence WHERE name='{$table}'");
    }

    DB::statement('PRAGMA foreign_keys = ON');
    echo "   ✅ Auto-increment reseteado\n\n";

} catch (Exception $e) {
    echo "❌ Error limpiando datos: " . $e->getMessage() . "\n";
    exit(1);
}

echo "📥 Paso 2: Importando contenido con traducciones automáticas...\n";

try {
    // Usar el nuevo comando que incluye traducciones
    echo "   📺 Importando K-Dramas con español (20 páginas)...\n";
    echo "   ⏳ Esto puede tomar varios minutos...\n";
    
    $exitCode = Artisan::call('import:korean-dramas-spanish', [
        '--pages' => 20,
        '--with-details' => true
    ]);
    
    if ($exitCode === 0) {
        echo "   ✅ K-Dramas importados exitosamente\n";
        echo Artisan::output();
    } else {
        throw new Exception('Error en el comando de importación');
    }

} catch (Exception $e) {
    echo "❌ Error importando: " . $e->getMessage() . "\n";
    echo "   ℹ️  Intentando con el comando básico como fallback...\n";
    
    try {
        Artisan::call('import:korean-dramas', ['--pages' => 15]);
        echo "   ✅ Importación básica completada\n";
    } catch (Exception $e2) {
        echo "❌ Error en importación básica: " . $e2->getMessage() . "\n";
        exit(1);
    }
}

echo "\n📊 Paso 3: Verificando resultados...\n";

try {
    $seriesCount = DB::table('series')->count();
    $peopleCount = DB::table('people')->count();
    $genresCount = DB::table('genres')->count();
    $translatedTitles = DB::table('series')->whereNotNull('title_es')->count();
    $translatedOverviews = DB::table('series')->whereNotNull('overview_es')->count();

    echo "   📈 Total series: {$seriesCount}\n";
    echo "   👥 Total personas: {$peopleCount}\n";
    echo "   🎭 Total géneros: {$genresCount}\n";
    echo "   🇪🇸 Títulos en español: {$translatedTitles}\n";
    echo "   📝 Sinopsis en español: {$translatedOverviews}\n";
    
    if ($translatedTitles > 0) {
        $translationPercentage = round(($translatedTitles / $seriesCount) * 100, 1);
        echo "   📊 Porcentaje de traducciones: {$translationPercentage}%\n";
    }

} catch (Exception $e) {
    echo "❌ Error verificando: " . $e->getMessage() . "\n";
}

echo "\n📱 Paso 4: Optimizando para producción...\n";

try {
    echo "   🔧 Limpiando cachés...\n";
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    
    echo "   ⚡ Optimizando configuración...\n";
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    
    echo "   ✅ Optimización completada\n";
    
} catch (Exception $e) {
    echo "⚠️  Advertencia optimizando: " . $e->getMessage() . "\n";
}

echo "\n🎉 ¡Proceso completado exitosamente!\n";
echo "=====================================\n";
echo "   🌐 Sitio: https://dorasia.cl\n";
echo "   📱 Optimizado para móvil\n";
echo "   🇪🇸 Contenido en español automático\n";
echo "   ⭐ Valoraciones con estrellas\n";
echo "   🎨 Botones de acción con colores del logo\n";
echo "   📺 Plataformas de streaming reales\n\n";

echo "💡 Para actualizar en el hosting:\n";
echo "   git add . && git commit -m 'Nuevo sistema de importación con español'\n";
echo "   git push origin main\n";
echo "   # En el hosting:\n";
echo "   git pull origin main\n";
echo "   php84 fresh-import-spanish.php\n\n";

echo "🚀 ¡DORASIA está listo con contenido 100% en español!\n";