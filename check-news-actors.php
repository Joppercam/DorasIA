<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;
use App\Models\Person;

// Contar noticias totales
$totalNews = News::count();
echo "Total de noticias: {$totalNews}\n\n";

// Noticias sin actores
$newsWithoutActors = News::doesntHave('people')->count();
echo "Noticias sin actores asociados: {$newsWithoutActors}\n\n";

// Noticias con actores sin imagen
$newsWithActorsNoImage = 0;
$newsWithActors = News::with('people')->has('people')->get();

foreach ($newsWithActors as $news) {
    $hasActorWithImage = false;
    
    foreach ($news->people as $person) {
        if ($person->profile_path && file_exists(public_path($person->profile_path))) {
            $hasActorWithImage = true;
            break;
        }
    }
    
    if (!$hasActorWithImage) {
        $newsWithActorsNoImage++;
        echo "Noticia con actores pero sin imagen: {$news->title}\n";
        echo "  Actores: " . $news->people->pluck('name')->implode(', ') . "\n";
    }
}

echo "\nNoticias con actores pero sin imágenes válidas: {$newsWithActorsNoImage}\n\n";

// Buscar los actores mencionados en las noticias sin asociación
$newsWithoutActors = News::doesntHave('people')->get();
echo "Analizando noticias sin actores asociados:\n";

foreach ($newsWithoutActors->take(5) as $news) {
    echo "\nNoticia: {$news->title}\n";
    
    // Buscar actores mencionados en el título o contenido
    $actorNames = Person::pluck('name');
    $foundActors = [];
    
    foreach ($actorNames as $actorName) {
        if (stripos($news->title . ' ' . $news->content, $actorName) !== false) {
            $foundActors[] = $actorName;
        }
    }
    
    if (count($foundActors) > 0) {
        echo "  Actores encontrados en texto: " . implode(', ', $foundActors) . "\n";
    } else {
        echo "  No se encontraron actores conocidos en el texto\n";
    }
}

// Verificar cuántos actores tienen imágenes
$actorsWithImages = Person::whereNotNull('profile_path')->count();
$totalActors = Person::count();
echo "\n\nEstadísticas de actores:\n";
echo "- Total de actores: {$totalActors}\n";
echo "- Actores con imágenes: {$actorsWithImages}\n";
echo "- Porcentaje con imágenes: " . round(($actorsWithImages / $totalActors) * 100, 2) . "%\n";