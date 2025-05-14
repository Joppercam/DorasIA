<?php
// Script para importar dramas coreanos directamente usando la API TMDB

require __DIR__.'/vendor/autoload.php';

// Configuración
$apiKey = '69e4c68f7f83d0388b2f66bfaca96337';
$baseUrl = 'https://api.themoviedb.org/3';
$language = 'es-ES';

// Función para realizar peticiones a la API
function fetchFromApi($url, $params = []) {
    global $apiKey, $baseUrl;
    
    $fullUrl = "{$baseUrl}/{$url}?" . http_build_query(array_merge([
        'api_key' => $apiKey,
        'language' => 'es-ES',
    ], $params));
    
    echo "Consultando: " . str_replace($apiKey, '[API_KEY]', $fullUrl) . "\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode != 200) {
        echo "Error HTTP {$httpCode}:\n";
        echo $response;
        return null;
    }
    
    return json_decode($response, true);
}

echo "Iniciando importación de dramas coreanos...\n";

// Obtener dramas coreanos
$data = fetchFromApi('discover/tv', [
    'sort_by' => 'popularity.desc',
    'page' => 1,
    'with_origin_country' => 'KR',
    'include_adult' => false,
]);

if (!$data || empty($data['results'])) {
    echo "No se encontraron dramas coreanos o hubo un error en la consulta.\n";
    exit(1);
}

// Mostrar información
echo "Se encontraron " . count($data['results']) . " dramas (de un total de " . $data['total_results'] . ")\n";
echo "Página " . $data['page'] . " de " . $data['total_pages'] . "\n\n";

// Mostrar los primeros 5 dramas
echo "Primeros 5 dramas:\n";
foreach (array_slice($data['results'], 0, 5) as $index => $drama) {
    echo ($index + 1) . ". " . $drama['name'] . " (" . substr($drama['first_air_date'] ?? 'Desconocido', 0, 4) . ")\n";
    echo "   TMDB ID: " . $drama['id'] . "\n";
    echo "   Popularidad: " . $drama['popularity'] . "\n";
    echo "   Sinopsis: " . (empty($drama['overview']) ? "No disponible" : substr($drama['overview'], 0, 100) . "...") . "\n\n";
}

echo "Importación completada con éxito.\n";