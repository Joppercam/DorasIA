<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\News;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$latestNews = News::with('people')
    ->orderBy('published_at', 'desc')
    ->take(10)
    ->get();

echo "=== ÚLTIMAS 10 NOTICIAS ===\n\n";

foreach ($latestNews as $news) {
    echo "Título: " . $news->title . "\n";
    echo "Fuente: " . $news->source_name . "\n";
    echo "Fecha: " . $news->published_at->format('Y-m-d') . "\n";
    echo "Imagen: " . $news->image . "\n";
    
    if ($news->people->isNotEmpty()) {
        echo "Actores relacionados: ";
        foreach ($news->people as $person) {
            echo $person->name . ", ";
        }
        echo "\n";
    }
    
    echo "---\n\n";
}

echo "Total de noticias: " . News::count() . "\n";
echo "Noticias destacadas: " . News::where('featured', true)->count() . "\n";