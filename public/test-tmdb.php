<?php

// Include the autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Dotenv setup 
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Get API key from .env
$apiKey = $_ENV['TMDB_API_KEY'];

echo "<h1>TMDB API Test</h1>";
echo "<p>API Key: " . ($apiKey ? substr($apiKey, 0, 3) . '...' . substr($apiKey, -3) : 'Not configured') . "</p>";

// Test API connection
$url = "https://api.themoviedb.org/3/search/tv?api_key={$apiKey}&language=es-ES&query=Parasite&include_adult=false";
echo "<p>Testing URL: " . str_replace($apiKey, '[API_KEY]', $url) . "</p>";

// Make API request with curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Response Code: {$httpCode}</p>";

// Parse response
$data = json_decode($response, true);

if ($httpCode == 200) {
    echo "<p>Connection successful!</p>";
    
    if (!empty($data['results'])) {
        echo "<p>Found " . count($data['results']) . " results</p>";
        echo "<h2>First 3 results:</h2>";
        echo "<ul>";
        foreach (array_slice($data['results'], 0, 3) as $show) {
            echo "<li>" . htmlspecialchars($show['name']) . " (" . substr($show['first_air_date'] ?? 'Unknown', 0, 4) . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No results found for the query 'Parasite'</p>";
    }
} else {
    echo "<h2>Error</h2>";
    echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
}

// Test Korean dramas API
echo "<h1>Korean Dramas Test</h1>";
$url = "https://api.themoviedb.org/3/discover/tv?api_key={$apiKey}&language=es-ES&sort_by=popularity.desc&with_origin_country=KR&include_adult=false";
echo "<p>Testing URL: " . str_replace($apiKey, '[API_KEY]', $url) . "</p>";

// Make API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Response Code: {$httpCode}</p>";

// Parse response
$data = json_decode($response, true);

if ($httpCode == 200) {
    echo "<p>Connection successful!</p>";
    
    if (!empty($data['results'])) {
        echo "<p>Found " . count($data['results']) . " Korean dramas</p>";
        echo "<h2>First 3 Korean dramas:</h2>";
        echo "<ul>";
        foreach (array_slice($data['results'], 0, 3) as $drama) {
            echo "<li>" . htmlspecialchars($drama['name']) . " (" . substr($drama['first_air_date'] ?? 'Unknown', 0, 4) . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No Korean dramas found</p>";
    }
} else {
    echo "<h2>Error</h2>";
    echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
}