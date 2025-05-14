<?php
// Script para probar y diagnosticar problemas de imágenes

// Función para verificar si una imagen existe
function checkImage($path) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($path, '/');
    $exists = file_exists($fullPath);
    $size = $exists ? filesize($fullPath) : 0;
    $type = $exists ? mime_content_type($fullPath) : 'unknown';
    
    return [
        'path' => $path,
        'fullPath' => $fullPath,
        'exists' => $exists,
        'size' => $size,
        'type' => $type
    ];
}

// Probar varias rutas de imágenes
$testImages = [
    '/posters/poster-1.jpg',
    '/posters/poster-2.jpg',
    '/posters/poster-3.jpg',
    '/backdrops/backdrop-1.jpg',
    '/backdrops/backdrop-2.jpg',
    '/backdrops/backdrop-3.jpg',
    '/storage/posters/poster-1.jpg',
    '/storage/posters/poster-2.jpg',
    '/storage/backdrops/backdrop-1.jpg'
];

$results = [];
foreach ($testImages as $img) {
    $results[] = checkImage($img);
}

// HTML de salida
echo '<!DOCTYPE html>
<html>
<head>
    <title>Test de Imágenes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #111; color: #eee; }
        .container { max-width: 1200px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #444; padding: 8px; text-align: left; }
        th { background: #222; }
        .success { color: #4CAF50; }
        .error { color: #F44336; }
        .image-test { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; }
        .image-card { width: 200px; }
        .image-container { position: relative; height: 300px; background: #222; overflow: hidden; border-radius: 4px; }
        img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Diagnóstico de Imágenes</h1>
        
        <h2>Resultados de Verificación</h2>
        <table>
            <tr>
                <th>Ruta</th>
                <th>Existe</th>
                <th>Tamaño</th>
                <th>Tipo MIME</th>
            </tr>';

foreach ($results as $result) {
    echo '<tr>
            <td>' . htmlspecialchars($result['path']) . '</td>
            <td class="' . ($result['exists'] ? 'success' : 'error') . '">' . ($result['exists'] ? 'Sí' : 'No') . '</td>
            <td>' . number_format($result['size']) . ' bytes</td>
            <td>' . htmlspecialchars($result['type']) . '</td>
        </tr>';
}

echo '</table>
        
        <h2>Prueba Visual de Imágenes</h2>
        <div class="image-test">';

foreach ($testImages as $index => $img) {
    echo '<div class="image-card">
            <div class="image-container">
                <img src="' . htmlspecialchars($img) . '" alt="Test Image ' . $index . '">
            </div>
            <p>' . htmlspecialchars($img) . '</p>
        </div>';
}

echo '</div>
    </div>
</body>
</html>';