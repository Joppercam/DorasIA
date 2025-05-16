<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Http\Controllers\RomanticDramaController;
use App\Services\TmdbService;
use Illuminate\Http\Request;

// Simular el controlador
$tmdbService = app(TmdbService::class);
$controller = new RomanticDramaController($tmdbService);

// Simular el request
$request = Request::create('/romantic-dramas', 'GET');

// Ejecutar el método index
$response = $controller->index($request);
$data = $response->getData();

echo "=== DATOS ENVIADOS A LA VISTA ===\n";
foreach ($data as $key => $value) {
    if (is_object($value) && method_exists($value, 'count')) {
        echo "$key: " . $value->count() . " items\n";
        
        // Mostrar algunos items si es una colección
        if ($value->count() > 0) {
            echo "  Primeros items:\n";
            foreach ($value->take(3) as $item) {
                if (isset($item['title'])) {
                    echo "    - " . $item['title'] . "\n";
                } elseif (isset($item['name'])) {
                    echo "    - " . $item['name'] . "\n";
                } elseif (method_exists($item, 'getAttribute') && $item->getAttribute('title')) {
                    echo "    - " . $item->getAttribute('title') . "\n";
                }
            }
        }
    } elseif (is_array($value)) {
        echo "$key: " . count($value) . " items\n";
    } else {
        echo "$key: " . gettype($value) . "\n";
    }
}

// Verificar caché
echo "\n=== VERIFICAR CACHÉ ===\n";
$cacheKeys = [
    'popular_romantic_kdramas',
    'popular_romantic_jdramas',
    'popular_romantic_cdramas'
];

foreach ($cacheKeys as $key) {
    $cached = \Illuminate\Support\Facades\Cache::has($key);
    echo "$key: " . ($cached ? 'EN CACHÉ' : 'NO EN CACHÉ') . "\n";
}