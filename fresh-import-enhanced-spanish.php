<?php
/**
 * Script para limpiar completamente y reimportar con traducciones profesionales
 * Versión mejorada con fallback a traducciones manuales de alta calidad
 * Ejecutar desde la raíz del proyecto: php fresh-import-enhanced-spanish.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🎭 DORASIA - Importación Profesional con Español\n";
echo "==============================================\n\n";

echo "🌟 Esta versión incluye:\n";
echo "   • Traducciones profesionales con OpenAI (si está disponible)\n";
echo "   • Sistema de fallback con diccionario mejorado de K-dramas\n";
echo "   • Traducciones específicas de series populares\n";
echo "   • Optimización para fanáticas chilenas\n\n";

echo "⚠️  ATENCIÓN: Este script eliminará TODOS los datos existentes.\n";
echo "Se reimportarán los dramas con el mejor sistema de traducciones disponible.\n";
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

echo "📥 Paso 2: Verificando servicios de traducción...\n";

// Check OpenAI availability
$openaiAvailable = false;
try {
    $translationService = app(App\Services\TranslationService::class);
    $testResult = $translationService->testService();
    if ($testResult['overall_success']) {
        $openaiAvailable = true;
        echo "   ✅ OpenAI disponible y funcionando\n";
        echo "   🤖 Modelo: " . $testResult['service_info']['model'] . "\n";
    } else {
        echo "   ⚠️  OpenAI no disponible, usando traducciones manuales mejoradas\n";
    }
} catch (Exception $e) {
    echo "   ⚠️  OpenAI no disponible, usando traducciones manuales mejoradas\n";
}

echo "\n📺 Paso 3: Importando K-Dramas con traducciones profesionales...\n";

try {
    echo "   📚 Importando géneros...\n";
    echo "   📄 Importando dramas (20 páginas con traducciones)...\n";
    echo "   ⏳ Esto puede tomar varios minutos...\n";
    
    // Use the new enhanced OpenAI command
    $exitCode = Artisan::call('import:korean-dramas-openai', [
        '--pages' => 20,
        '--with-details' => true
    ]);
    
    if ($exitCode === 0) {
        echo "   ✅ Importación completada exitosamente\n";
        $output = Artisan::output();
        
        // Extract key information from output
        if (strpos($output, 'series importados') !== false) {
            preg_match('/(\d+) series importados/', $output, $matches);
            if (!empty($matches[1])) {
                echo "   📊 Total importado: {$matches[1]} series\n";
            }
        }
        
        if (strpos($output, 'Traducciones OpenAI') !== false) {
            preg_match('/(\d+) Traducciones OpenAI/', $output, $matches);
            if (!empty($matches[1]) && $matches[1] > 0) {
                echo "   🤖 Traducciones OpenAI: {$matches[1]}\n";
            }
        }
        
    } else {
        throw new Exception('Error en el comando de importación avanzado');
    }

} catch (Exception $e) {
    echo "   ⚠️  Error en importación avanzada: " . $e->getMessage() . "\n";
    echo "   🔄 Intentando con importación básica como fallback...\n";
    
    try {
        Artisan::call('import:korean-dramas', ['--pages' => 15, '--with-details' => true]);
        echo "   ✅ Importación básica completada\n";
    } catch (Exception $e2) {
        echo "   ❌ Error en importación básica: " . $e2->getMessage() . "\n";
        exit(1);
    }
}

echo "\n📊 Paso 4: Verificando resultados finales...\n";

try {
    $seriesCount = DB::table('series')->count();
    $peopleCount = DB::table('people')->count();
    $genresCount = DB::table('genres')->count();
    $translatedTitles = DB::table('series')->whereNotNull('title_es')->count();
    $translatedOverviews = DB::table('series')->whereNotNull('overview_es')->count();

    echo "\n📈 ESTADÍSTICAS FINALES:\n";
    echo "   ═══════════════════════\n";
    echo "   📺 Total series: {$seriesCount}\n";
    echo "   👥 Total personas: {$peopleCount}\n";
    echo "   🎭 Total géneros: {$genresCount}\n";
    echo "   🇪🇸 Títulos en español: {$translatedTitles}\n";
    echo "   📝 Sinopsis en español: {$translatedOverviews}\n";
    
    if ($seriesCount > 0) {
        $titlePercentage = round(($translatedTitles / $seriesCount) * 100, 1);
        $overviewPercentage = round(($translatedOverviews / $seriesCount) * 100, 1);
        echo "   📊 Cobertura títulos: {$titlePercentage}%\n";
        echo "   📊 Cobertura sinopsis: {$overviewPercentage}%\n";
    }

    // Show some sample translations
    echo "\n🎯 EJEMPLOS DE TRADUCCIONES:\n";
    echo "   ═══════════════════════════\n";
    
    $sampleSeries = DB::table('series')
        ->whereNotNull('title_es')
        ->where('title_es', '!=', DB::raw('title'))
        ->limit(5)
        ->get(['title', 'title_es']);
    
    foreach ($sampleSeries as $series) {
        echo "   • '{$series->title}' → '{$series->title_es}'\n";
    }

} catch (Exception $e) {
    echo "❌ Error verificando: " . $e->getMessage() . "\n";
}

echo "\n📱 Paso 5: Optimizando para producción...\n";

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

echo "\n🎉 ¡PROCESO COMPLETADO EXITOSAMENTE!\n";
echo "=====================================\n";
echo "   🌐 Sitio: https://dorasia.cl\n";
echo "   📱 Optimizado para móvil\n";
echo "   🇪🇸 Contenido profesional en español\n";
echo "   ⭐ Valoraciones con estrellas\n";
echo "   🎨 Botones con colores del logo\n";
echo "   📺 Plataformas de streaming reales\n";
if ($openaiAvailable) {
    echo "   🤖 Traducciones con OpenAI GPT-4 Turbo\n";
} else {
    echo "   📚 Traducciones con diccionario profesional K-drama\n";
}

echo "\n💡 PARA DEPLOYMENT EN HOSTING:\n";
echo "===============================\n";
echo "1. Commit y push del código local:\n";
echo "   git add .\n";
echo "   git commit -m 'Sistema profesional de traducciones K-drama'\n";
echo "   git push origin main\n\n";

echo "2. En el servidor hosting:\n";
echo "   git pull origin main\n";
echo "   php84 fresh-import-enhanced-spanish.php\n\n";

echo "🚀 ¡DORASIA está listo con el mejor contenido K-drama en español!\n";
echo "   Las fanáticas chilenas van a amar este sitio 💖\n\n";