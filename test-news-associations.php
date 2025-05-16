<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;

$news = News::with('people')->where('title', 'LIKE', '%Jun Ji-hyun%')->first();

if ($news) {
    echo "Noticia encontrada: " . $news->title . "\n";
    echo "Actores asociados: " . $news->people->count() . "\n";
    foreach ($news->people as $person) {
        echo " - " . $person->name . " (ID: " . $person->id . ", Profile: " . $person->profile_path . ")\n";
    }
} else {
    echo "No se encontró la noticia\n";
}

// Verificar también los actores creados
echo "\nActores coreanos en la base de datos:\n";
$koreanActors = App\Models\Person::where('name', 'LIKE', '%Jun Ji-hyun%')
    ->orWhere('name', 'LIKE', '%Lee Min-ho%')
    ->orWhere('name', 'LIKE', '%Bae Suzy%')
    ->get(['id', 'name', 'profile_path']);

foreach ($koreanActors as $actor) {
    echo " - " . $actor->name . " (ID: " . $actor->id . ", Profile: " . $actor->profile_path . ")\n";
}