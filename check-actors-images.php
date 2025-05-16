<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Person;
use App\Models\News;

// Contar actores totales
$totalActors = Person::whereHas('news')->count();
echo "Total de actores en noticias: {$totalActors}\n\n";

// Ver algunos ejemplos de actores con sus rutas de imágenes
$actors = Person::whereHas('news')->take(10)->get();

echo "Ejemplos de actores y sus imágenes:\n";
echo str_repeat("-", 80) . "\n";

foreach ($actors as $actor) {
    $newsCount = $actor->news()->count();
    echo "Actor: {$actor->name}\n";
    echo "  ID: {$actor->id}\n";
    echo "  profile_path: " . ($actor->profile_path ?: "(vacío)") . "\n";
    echo "  photo: " . ($actor->photo ?: "(vacío)") . "\n";
    echo "  tmdb_id: " . ($actor->tmdb_id ?: "(vacío)") . "\n";
    echo "  Aparece en {$newsCount} noticias\n";
    
    // Verificar si la imagen existe físicamente
    if ($actor->profile_path) {
        $imagePath = public_path($actor->profile_path);
        if (file_exists($imagePath)) {
            echo "  ✓ La imagen existe físicamente\n";
        } else {
            echo "  ✗ La imagen NO existe en: {$imagePath}\n";
        }
    }
    
    echo str_repeat("-", 80) . "\n";
}

// Buscar actores con rutas de imágenes pero sin archivo físico
echo "\nBuscando actores con imágenes faltantes...\n";
$actorsWithMissingImages = 0;

foreach (Person::whereHas('news')->get() as $actor) {
    if ($actor->profile_path && !file_exists(public_path($actor->profile_path))) {
        $actorsWithMissingImages++;
        echo "Actor {$actor->name} tiene profile_path pero la imagen no existe: {$actor->profile_path}\n";
    }
}

echo "\nActores con imágenes faltantes físicamente: {$actorsWithMissingImages}\n";