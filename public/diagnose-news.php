<?php

use App\Models\News;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/plain');

echo "=== News System Diagnostics ===\n\n";

// 1. Check database connection
try {
    DB::connection()->getPdo();
    echo "✓ Database connection: OK\n";
} catch (\Exception $e) {
    echo "✗ Database connection: " . $e->getMessage() . "\n";
}

// 2. Check news table
try {
    $newsCount = News::count();
    echo "✓ News articles in database: $newsCount\n";
} catch (\Exception $e) {
    echo "✗ Error accessing news table: " . $e->getMessage() . "\n";
}

// 3. Check routes
try {
    $newsRoute = route('news.show', 'test-slug');
    echo "✓ News route exists: $newsRoute\n";
} catch (\Exception $e) {
    echo "✗ News route error: " . $e->getMessage() . "\n";
}

// 4. Check recent news
echo "\n=== Recent News (Last 5) ===\n";
try {
    $recentNews = News::orderBy('created_at', 'desc')->take(5)->get();
    foreach ($recentNews as $news) {
        echo sprintf("\n%s\n", str_repeat('-', 50));
        echo "Title: " . $news->title . "\n";
        echo "Slug: " . $news->slug . "\n";
        echo "Created: " . $news->created_at . "\n";
        echo "URL: " . route('news.show', $news->slug) . "\n";
        
        // Check if slug has any special characters
        if (preg_match('/[^a-z0-9\-]/', $news->slug)) {
            echo "⚠️  Warning: Slug contains special characters\n";
        }
        
        // Check image paths
        $peopleCount = $news->people->count();
        echo "Associated actors: $peopleCount\n";
        if ($peopleCount > 0) {
            foreach ($news->people->take(3) as $person) {
                $imagePath = $person->profile_path;
                echo "  - $person->name: ";
                if ($imagePath && file_exists(public_path($imagePath))) {
                    echo "✓ Image OK\n";
                } else {
                    echo "✗ Image missing: $imagePath\n";
                }
            }
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// 5. Check for problematic slugs
echo "\n=== Slug Analysis ===\n";
try {
    // Check for duplicates
    $duplicates = DB::table('news')
        ->select('slug', DB::raw('COUNT(*) as count'))
        ->groupBy('slug')
        ->having('count', '>', 1)
        ->get();
    
    if ($duplicates->isEmpty()) {
        echo "✓ No duplicate slugs found\n";
    } else {
        echo "✗ Found duplicate slugs:\n";
        foreach ($duplicates as $dup) {
            echo "  - $dup->slug (count: $dup->count)\n";
        }
    }
    
    // Check for empty/null slugs
    $invalidSlugs = News::whereNull('slug')->orWhere('slug', '')->count();
    if ($invalidSlugs == 0) {
        echo "✓ No null/empty slugs\n";
    } else {
        echo "✗ Found $invalidSlugs articles with invalid slugs\n";
    }
    
} catch (\Exception $e) {
    echo "Error in slug analysis: " . $e->getMessage() . "\n";
}

// 6. Test specific problematic slug (if any)
if (isset($_GET['slug'])) {
    echo "\n=== Testing Specific Slug ===\n";
    $testSlug = $_GET['slug'];
    echo "Testing: $testSlug\n";
    
    try {
        $news = News::where('slug', $testSlug)->first();
        if ($news) {
            echo "✓ Found in database\n";
            echo "ID: " . $news->id . "\n";
            echo "Title: " . $news->title . "\n";
            echo "URL: " . route('news.show', $news->slug) . "\n";
        } else {
            echo "✗ Not found in database\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== End Diagnostics ===\n";