<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\News;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Check news count
$newsCount = News::count();
echo "Total news articles: " . $newsCount . "\n";

// Check latest news
$latestNews = News::with('people')
    ->orderBy('published_at', 'desc')
    ->take(6)
    ->get();

echo "\nLatest news articles:\n";
foreach ($latestNews as $news) {
    echo "- " . $news->title . " (" . $news->published_at->format('Y-m-d') . ")\n";
    if ($news->people->isNotEmpty()) {
        echo "  Related to: " . $news->people->pluck('name')->join(', ') . "\n";
    }
}

// Check featured news
$featuredNews = News::where('featured', true)->count();
echo "\nFeatured news: " . $featuredNews . "\n";