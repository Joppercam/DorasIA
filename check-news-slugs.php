<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;
use Illuminate\Support\Facades\DB;

// Verificar slugs duplicados
$duplicateSlugs = DB::table('news')
    ->select('slug', DB::raw('COUNT(*) as count'))
    ->groupBy('slug')
    ->having('count', '>', 1)
    ->get();

echo "=== VERIFICACIÓN DE SLUGS DE NOTICIAS ===\n\n";

if ($duplicateSlugs->count() > 0) {
    echo "⚠️  ENCONTRADOS SLUGS DUPLICADOS:\n";
    foreach ($duplicateSlugs as $duplicate) {
        echo "  - Slug: '{$duplicate->slug}' (repetido {$duplicate->count} veces)\n";
        
        // Mostrar noticias con este slug
        $newsWithSlug = News::where('slug', $duplicate->slug)->get();
        foreach ($newsWithSlug as $news) {
            echo "    → ID: {$news->id}, Título: {$news->title}\n";
        }
        echo "\n";
    }
} else {
    echo "✓ No hay slugs duplicados\n\n";
}

// Verificar slugs vacíos o null
$emptySlugNews = News::where('slug', '')
    ->orWhereNull('slug')
    ->get();

if ($emptySlugNews->count() > 0) {
    echo "⚠️  NOTICIAS SIN SLUG:\n";
    foreach ($emptySlugNews as $news) {
        echo "  - ID: {$news->id}, Título: {$news->title}\n";
    }
    echo "\n";
} else {
    echo "✓ Todas las noticias tienen slug\n\n";
}

// Verificar formato de slugs (compatible con SQLite)
$allNews = News::all();
$invalidSlugNews = $allNews->filter(function($news) {
    return !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $news->slug);
});

if ($invalidSlugNews->count() > 0) {
    echo "⚠️  SLUGS CON FORMATO INVÁLIDO:\n";
    foreach ($invalidSlugNews as $news) {
        echo "  - ID: {$news->id}, Slug: '{$news->slug}', Título: {$news->title}\n";
    }
    echo "\n";
} else {
    echo "✓ Todos los slugs tienen formato válido\n\n";
}

// Verificar longitud de slugs
$longSlugNews = News::all()->filter(function($news) {
    return strlen($news->slug) > 100;
});

if ($longSlugNews->count() > 0) {
    echo "⚠️  SLUGS MUY LARGOS (más de 100 caracteres):\n";
    foreach ($longSlugNews as $news) {
        echo "  - ID: {$news->id}, Slug (". strlen($news->slug) ." chars): '{$news->slug}'\n";
    }
    echo "\n";
}

// Verificar rutas
echo "=== CONFIGURACIÓN DE RUTAS ===\n";
echo "Ruta para lista de noticias: /news\n";
echo "Ruta para detalle de noticia: /news/{slug}\n";
echo "Ruta para noticias por persona: /news/person/{slug}\n\n";

// Probar algunas URLs
echo "=== EJEMPLOS DE URLs ===\n";
$sampleNews = News::limit(5)->get();
foreach ($sampleNews as $news) {
    $url = "/news/{$news->slug}";
    echo "Noticia: {$news->title}\n";
    echo "  URL: {$url}\n";
    echo "  Estado: " . (strlen($news->slug) > 0 ? "✓ OK" : "✗ SLUG VACÍO") . "\n\n";
}

// Sugerencias de corrección
$needsFixing = News::where('slug', '')->orWhereNull('slug')->count();
$invalidFormat = $allNews->filter(function($news) {
    return !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $news->slug);
})->count();
$needsFixing += $invalidFormat;

if ($needsFixing > 0) {
    echo "=== SUGERENCIAS ===\n";
    echo "Hay {$needsFixing} noticias que necesitan corrección de slug.\n";
    echo "Ejecuta: php artisan news:fix-slugs\n";
}