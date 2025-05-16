<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Title;
use App\Models\Genre;

// Check if we have titles
$totalTitles = Title::count();
echo "Total titles in database: $totalTitles\n\n";

// Check if we have Romance genre
$romanceGenre = Genre::where('name', 'Romance')->first();
if ($romanceGenre) {
    echo "Romance genre exists with ID: " . $romanceGenre->id . "\n\n";
} else {
    echo "Romance genre does not exist!\n\n";
}

// Check romantic titles
$romanticTitles = Title::romantic()->count();
echo "Romantic titles found: $romanticTitles\n\n";

// Check featured romantic titles
$featuredRomanticTitles = Title::romantic()->where('is_featured', true)->count();
echo "Featured romantic titles: $featuredRomanticTitles\n\n";

// Check titles with Romance genre
$titlesWithRomanceGenre = Title::whereHas('genres', function ($q) {
    $q->where('name', 'Romance');
})->count();
echo "Titles with Romance genre: $titlesWithRomanceGenre\n\n";

// Check first few romantic titles
echo "First 5 romantic titles:\n";
$firstRomanticTitles = Title::romantic()->take(5)->get();
foreach ($firstRomanticTitles as $title) {
    echo "- {$title->title} (ID: {$title->id})\n";
    echo "  Genres: " . $title->genres->pluck('name')->join(', ') . "\n";
    echo "  Metadata: " . json_encode($title->metadata) . "\n\n";
}

// Check different origin countries
$koreanTitles = Title::romantic()->korean()->count();
$japaneseTitles = Title::romantic()->japanese()->count();
$chineseTitles = Title::romantic()->chinese()->count();

echo "Romantic titles by origin:\n";
echo "- Korean: $koreanTitles\n";
echo "- Japanese: $japaneseTitles\n";
echo "- Chinese: $chineseTitles\n";