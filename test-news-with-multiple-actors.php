<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;

// Buscar una noticia con múltiples actores
$news = News::with('people')
    ->where('title', 'LIKE', '%Bae Suzy y Nam Joo-hyuk%')
    ->first();

if ($news) {
    echo "Noticia encontrada: " . $news->title . "\n";
    echo "Actores asociados: " . $news->people->count() . "\n";
    foreach ($news->people as $person) {
        echo " - " . $person->name . " (ID: " . $person->id . ", Profile: " . $person->profile_path . ")\n";
    }
} else {
    echo "No se encontró la noticia\n";
}

// También verificar una noticia con Hyun Bin y Son Ye-jin
echo "\n";
$news2 = News::with('people')
    ->where('title', 'LIKE', '%Hyun Bin y Son Ye-jin%')
    ->first();

if ($news2) {
    echo "Noticia encontrada: " . $news2->title . "\n";
    echo "Actores asociados: " . $news2->people->count() . "\n";
    foreach ($news2->people as $person) {
        echo " - " . $person->name . " (ID: " . $person->id . ", Profile: " . $person->profile_path . ")\n";
    }
} else {
    echo "No se encontró la noticia\n";
}