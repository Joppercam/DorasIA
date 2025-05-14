<?php
// Script para probar directamente la API key de TMDB

require __DIR__.'/vendor/autoload.php';

// Cargar variables de entorno directamente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener la API key
$apiKey = $_ENV['TMDB_API_KEY'] ?? null;

echo "TMDB API Key: " . ($apiKey ? substr($apiKey, 0, 5) . '...' . substr($apiKey, -5) : 'No encontrada') . "\n";

// Hacer una solicitud directa a la API
$url = "https://api.themoviedb.org/3/discover/tv?api_key={$apiKey}&language=es-ES&sort_by=popularity.desc&page=1&with_origin_country=KR";

echo "URL: " . str_replace($apiKey, '[APIKEY]', $url) . "\n";

// Realizar solicitud HTTP
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Código de respuesta HTTP: " . $httpCode . "\n";

// Analizar respuesta
$data = json_decode($response, true);

if ($httpCode == 200) {
    echo "Conexión exitosa!\n";
    echo "Total de resultados: " . ($data['total_results'] ?? 0) . "\n";
    
    if (!empty($data['results'])) {
        echo "Encontrados " . count($data['results']) . " dramas coreanos.\n";
        echo "Primeros 3 dramas:\n";
        
        foreach (array_slice($data['results'], 0, 3) as $index => $drama) {
            echo ($index + 1) . ". " . $drama['name'] . " (" . substr($drama['first_air_date'] ?? 'Desconocido', 0, 4) . ")\n";
        }
    } else {
        echo "No se encontraron dramas coreanos en los resultados.\n";
    }
} else {
    echo "Error en la respuesta:\n";
    print_r($data);
}