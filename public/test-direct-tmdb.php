<?php

// Include the autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Dotenv setup 
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Get API key from .env
$apiKey = $_ENV['TMDB_API_KEY'];

echo "<h1>TMDB API Direct Test</h1>";
echo "<p>API Key: " . ($apiKey ? substr($apiKey, 0, 3) . '...' . substr($apiKey, -3) : 'Not configured') . "</p>";

// Get configuration
$config = include(__DIR__ . '/../config/services.php');
$tmdbConfig = $config['tmdb'] ?? [];

echo "<p>Language: " . ($tmdbConfig['language'] ?? 'Not configured') . "</p>";
echo "<p>Base URL: " . ($tmdbConfig['base_url'] ?? 'Not configured') . "</p>";

// Test the direct API URL
$url = "https://api.themoviedb.org/3/discover/tv?api_key={$apiKey}&language=es-ES&sort_by=popularity.desc&page=1&with_origin_country=KR&include_adult=false";
echo "<p>Test URL: " . str_replace($apiKey, '[API_KEY]', $url) . "</p>";

try {
    // Make a direct cURL request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    curl_close($ch);
    
    echo "<h2>Response Details</h2>";
    echo "<p>HTTP Code: {$httpCode}</p>";
    
    echo "<h3>Headers</h3>";
    echo "<pre>" . htmlspecialchars($headers) . "</pre>";
    
    echo "<h3>Response Body</h3>";
    
    if ($httpCode == 200) {
        $data = json_decode($body, true);
        echo "<p>Connection successful!</p>";
        echo "<p>Total Results: " . ($data['total_results'] ?? 'unknown') . "</p>";
        
        if (!empty($data['results'])) {
            echo "<p>Found " . count($data['results']) . " Korean dramas</p>";
            echo "<h4>First 3 Korean dramas:</h4>";
            echo "<ul>";
            foreach (array_slice($data['results'], 0, 3) as $drama) {
                echo "<li>" . htmlspecialchars($drama['name']) . " (" . substr($drama['first_air_date'] ?? 'Unknown', 0, 4) . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No Korean dramas found in the results</p>";
        }
        
        echo "<h4>Full Response (JSON)</h4>";
        echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
    } else {
        echo "<p>Error Response:</p>";
        echo "<pre>" . htmlspecialchars($body) . "</pre>";
    }
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p>Exception: " . $e->getMessage() . "</p>";
}