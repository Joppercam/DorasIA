<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Title;
use App\Models\Genre;

// Comprobar géneros disponibles
echo "=== GÉNEROS DISPONIBLES ===\n";
$genres = Genre::all();
foreach ($genres as $genre) {
    echo "- {$genre->name} (ID: {$genre->id})\n";
}

// Verificar títulos por género Romance
echo "\n=== TÍTULOS CON GÉNERO ROMANCE ===\n";
$romanticTitles = Title::romantic()->take(5)->get();
echo "Cantidad de títulos románticos: " . Title::romantic()->count() . "\n";
echo "Primeros 5 títulos:\n";
foreach ($romanticTitles as $title) {
    echo "- {$title->title} (ID: {$title->id})\n";
    echo "  Poster: {$title->poster_path}\n";
    echo "  Genres: " . $title->genres->pluck('name')->join(', ') . "\n";
    echo "  Popularity: {$title->popularity}\n";
}

// Verificar títulos destacados
echo "\n=== TÍTULOS ROMÁNTICOS DESTACADOS ===\n";
$featuredTitles = Title::romantic()
    ->where('is_featured', true)
    ->orderBy('popularity', 'desc')
    ->take(6)
    ->get();
echo "Cantidad de títulos destacados: " . $featuredTitles->count() . "\n";

// Verificar por origen
echo "\n=== TÍTULOS POR ORIGEN ===\n";
$koreanCount = Title::romantic()->korean()->count();
$japaneseCount = Title::romantic()->japanese()->count();
$chineseCount = Title::romantic()->chinese()->count();
echo "- Coreanos: $koreanCount\n";
echo "- Japoneses: $japaneseCount\n";
echo "- Chinos: $chineseCount\n";

// Verificar metadata
echo "\n=== EJEMPLO DE METADATA ===\n";
$sampleTitle = Title::romantic()->first();
if ($sampleTitle) {
    echo "Título: {$sampleTitle->title}\n";
    echo "Metadata: " . json_encode($sampleTitle->metadata, JSON_PRETTY_PRINT) . "\n";
}

// Verificar las rutas de imágenes
echo "\n=== RUTAS DE IMÁGENES ===\n";
$titleWithImages = Title::romantic()->whereNotNull('poster_path')->first();
if ($titleWithImages) {
    echo "Título: {$titleWithImages->title}\n";
    echo "Poster path: {$titleWithImages->poster_path}\n";
    echo "Poster URL: {$titleWithImages->poster_url}\n";
    echo "Backdrop path: {$titleWithImages->backdrop_path}\n";
    echo "Backdrop URL: {$titleWithImages->backdrop_url}\n";
    echo "¿Existe poster? " . (file_exists(public_path($titleWithImages->poster_url)) ? 'SÍ' : 'NO') . "\n";
}