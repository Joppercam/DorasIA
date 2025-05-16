<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Title;

// Get first romantic titles
$titles = Title::romantic()->take(5)->get();

echo "First 5 romantic titles with image information:\n\n";

foreach ($titles as $title) {
    echo "Title: {$title->title}\n";
    echo "Poster path: {$title->poster_path}\n";
    echo "Poster URL: {$title->poster_url}\n";
    echo "Backdrop path: {$title->backdrop_path}\n";
    echo "Backdrop URL: {$title->backdrop_url}\n";
    
    // Check if poster file exists
    $posterPath = public_path(ltrim($title->poster_url, '/'));
    echo "Poster file exists: " . (file_exists($posterPath) ? 'YES' : 'NO') . "\n";
    
    // Check if backdrop file exists
    $backdropPath = public_path(ltrim($title->backdrop_url, '/'));
    echo "Backdrop file exists: " . (file_exists($backdropPath) ? 'YES' : 'NO') . "\n";
    
    echo "\n---\n\n";
}