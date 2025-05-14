<?php

// Include the autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables directly
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Testing environment variables:\n";
echo "TMDB_API_KEY: " . (isset($_ENV['TMDB_API_KEY']) ? 'Exists ('.substr($_ENV['TMDB_API_KEY'], 0, 3).'...)' : 'Not found') . "\n";
echo "TMDB_ACCESS_TOKEN: " . (isset($_ENV['TMDB_ACCESS_TOKEN']) ? 'Exists ('.substr($_ENV['TMDB_ACCESS_TOKEN'], 0, 3).'...)' : 'Not found') . "\n";

// Try direct file access
echo "\nDirect .env file test:\n";
if (file_exists(__DIR__ . '/.env')) {
    echo ".env file exists\n";
    $contents = file_get_contents(__DIR__ . '/.env');
    $lines = explode("\n", $contents);
    
    foreach ($lines as $line) {
        if (strpos($line, 'TMDB_API_KEY') === 0) {
            echo "Found TMDB_API_KEY in .env file: " . $line . "\n";
        }
        if (strpos($line, 'TMDB_ACCESS_TOKEN') === 0) {
            echo "Found TMDB_ACCESS_TOKEN in .env file: " . $line . "\n";
        }
    }
} else {
    echo ".env file does not exist\n";
}