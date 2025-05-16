<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;

// Contar noticias totales
$totalNews = News::count();
echo "Total de noticias: {$totalNews}\n\n";

// Mostrar últimas 10 noticias
$latestNews = News::latest()->take(10)->get();

echo "Últimas 10 noticias:\n";
echo str_repeat("-", 80) . "\n";

foreach ($latestNews as $index => $news) {
    echo ($index + 1) . ". {$news->title}\n";
    echo "   Fuente: {$news->source_name}\n";
    echo "   Fecha: {$news->published_at}\n";
    echo "   Contenido (primeras 200 letras): " . substr($news->content, 0, 200) . "...\n";
    echo str_repeat("-", 80) . "\n";
}

// Verificar idioma
echo "\nVerificando idioma de las noticias...\n";
$spanishWords = ['el', 'la', 'los', 'las', 'de', 'en', 'con', 'para', 'por', 'que', 'es', 'un', 'una'];
$notSpanish = 0;

foreach (News::all() as $news) {
    $content = strtolower($news->title . ' ' . $news->content);
    $hasSpanish = false;
    
    foreach ($spanishWords as $word) {
        if (str_contains($content, ' ' . $word . ' ')) {
            $hasSpanish = true;
            break;
        }
    }
    
    if (!$hasSpanish) {
        $notSpanish++;
        echo "Posible noticia no en español: {$news->title}\n";
    }
}

echo "\nResumen:\n";
echo "- Total de noticias: {$totalNews}\n";
echo "- Noticias posiblemente no en español: {$notSpanish}\n";
echo "- Porcentaje en español: " . round((($totalNews - $notSpanish) / $totalNews) * 100, 2) . "%\n";