<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

echo "Testing environment variables:\n";
echo "TMDB_API_KEY: " . (env('TMDB_API_KEY') ? 'Exists ('.substr(env('TMDB_API_KEY'), 0, 3).'...)' : 'Not found') . "\n";
echo "TMDB_ACCESS_TOKEN: " . (env('TMDB_ACCESS_TOKEN') ? 'Exists ('.substr(env('TMDB_ACCESS_TOKEN'), 0, 3).'...)' : 'Not found') . "\n";

// Try direct file loading
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

echo "\nChecking config values:\n";
echo "services.tmdb.api_key: " . (config('services.tmdb.api_key') ? 'Exists' : 'Not found') . "\n";
echo "services.tmdb.access_token: " . (config('services.tmdb.access_token') ? 'Exists' : 'Not found') . "\n";