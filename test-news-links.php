<?php

use Illuminate\Support\Facades\DB;
use App\Models\News;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n=== Testing News Links ===\n\n";

// Get all news with their slugs
$allNews = News::select('id', 'title', 'slug')->get();
echo "Total news articles in database: " . $allNews->count() . "\n\n";

// Test some specific slugs
$testSlugs = [
    'drama-de-terror-psicologico-the-guest-2-inicia-produccion-confirmado',
    'song-kang-confirma-papel-principal-en-adaptacion-de-popular-webtoon-ultima-hora',
    'fake-slug-that-doesnt-exist'
];

foreach ($testSlugs as $slug) {
    echo "Testing slug: $slug\n";
    
    try {
        $news = News::where('slug', $slug)->firstOrFail();
        echo "✓ Found: " . $news->title . "\n";
        
        // Test route generation
        $route = route('news.show', $slug);
        echo "✓ Route: $route\n";
        
    } catch (\Exception $e) {
        echo "✗ NOT FOUND - This would show a 404 error\n";
    }
    
    echo "\n";
}

// Check for any null or empty slugs
$invalidSlugs = News::whereNull('slug')
    ->orWhere('slug', '')
    ->count();

if ($invalidSlugs > 0) {
    echo "⚠️  Found $invalidSlugs news articles with invalid slugs\n";
} else {
    echo "✓ All news articles have valid slugs\n";
}

// Show examples of recent news with their URLs
echo "\n=== Recent News URLs ===\n";
$recentNews = News::orderBy('created_at', 'desc')->take(5)->get();
foreach ($recentNews as $news) {
    echo "- " . substr($news->title, 0, 50) . "...\n";
    echo "  URL: " . route('news.show', $news->slug) . "\n\n";
}

echo "=== End Test ===\n";