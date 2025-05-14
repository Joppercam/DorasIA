<?php
/**
 * Script simple para crear imágenes de marcadores de posición para Dorasia
 */

// Configuración
$types = [
    'poster' => ['width' => 500, 'height' => 750, 'path' => 'posters', 'count' => 10],
    'backdrop' => ['width' => 1280, 'height' => 720, 'path' => 'backdrops', 'count' => 5],
    'category' => ['width' => 600, 'height' => 400, 'path' => 'images/categories', 'count' => 5],
    'hero' => ['width' => 1920, 'height' => 1080, 'path' => 'images/heroes', 'count' => 5],
    'profile' => ['width' => 300, 'height' => 300, 'path' => 'images/profiles', 'count' => 1],
];

// Colores para categorías
$colors = [
    'k-drama' => ['bg' => [50, 120, 200], 'text' => [255, 255, 255]],
    'j-drama' => ['bg' => [200, 50, 50], 'text' => [255, 255, 255]],
    'c-drama' => ['bg' => [200, 120, 50], 'text' => [255, 255, 255]],
    'movies' => ['bg' => [50, 150, 50], 'text' => [255, 255, 255]],
    'originals' => ['bg' => [100, 50, 150], 'text' => [255, 255, 255]],
];

// Crear imágenes para cada tipo
foreach ($types as $type => $config) {
    echo "Creando imágenes de tipo {$type}...\n";
    
    for ($i = 1; $i <= $config['count']; $i++) {
        // Crear imagen
        $image = imagecreatetruecolor($config['width'], $config['height']);
        
        // Color de fondo aleatorio
        $bgColor = imagecolorallocate($image, rand(10, 90), rand(10, 90), rand(10, 90));
        imagefill($image, 0, 0, $bgColor);
        
        // Añadir un degradado
        $gradientColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
        for ($y = 0; $y < $config['height']; $y++) {
            imageline($image, 0, $y, $config['width'], $y, imagecolorallocatealpha($image, rand(0, 255), rand(0, 255), rand(0, 255), 100 - ($y / $config['height'] * 100)));
        }
        
        // Añadir texto
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $text = "Dorasia {$type} {$i}";
        $fontSize = $config['width'] / 20;
        $fontFile = __DIR__ . '/arial.ttf'; // Asegúrate de que este archivo exista o usa otro
        
        // Intentar usar un font TTF si está disponible
        if (function_exists('imagettftext') && file_exists($fontFile)) {
            // Centrar texto
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
            $x = ($config['width'] - ($bbox[2] - $bbox[0])) / 2;
            $y = ($config['height'] - ($bbox[7] - $bbox[1])) / 2;
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontFile, $text);
        } else {
            // Fallback a texto simple
            $x = ($config['width'] - (strlen($text) * 5)) / 2;
            $y = $config['height'] / 2;
            imagestring($image, 5, $x, $y, $text, $textColor);
        }
        
        // Guardar imagen
        $outputPath = "{$config['path']}/placeholder.jpg";
        if ($config['count'] > 1) {
            $outputPath = "{$config['path']}/{$type}-{$i}.jpg";
        }
        
        imagejpeg($image, $outputPath, 90);
        imagedestroy($image);
        
        echo "  Creada: {$outputPath}\n";
    }
}

// Crear imágenes específicas para categorías
foreach ($colors as $category => $colorConfig) {
    echo "Creando imagen para categoría {$category}...\n";
    
    // Imagen para categoría
    $image = imagecreatetruecolor(600, 400);
    $bgColor = imagecolorallocate($image, $colorConfig['bg'][0], $colorConfig['bg'][1], $colorConfig['bg'][2]);
    imagefill($image, 0, 0, $bgColor);
    
    // Añadir texto
    $textColor = imagecolorallocate($image, $colorConfig['text'][0], $colorConfig['text'][1], $colorConfig['text'][2]);
    $text = strtoupper($category);
    imagestring($image, 5, 250, 200, $text, $textColor);
    
    imagejpeg($image, "images/categories/{$category}.jpg", 90);
    imagedestroy($image);
    
    // Imagen para hero
    $image = imagecreatetruecolor(1920, 1080);
    $bgColor = imagecolorallocate($image, $colorConfig['bg'][0], $colorConfig['bg'][1], $colorConfig['bg'][2]);
    imagefill($image, 0, 0, $bgColor);
    
    // Añadir texto
    $textColor = imagecolorallocate($image, $colorConfig['text'][0], $colorConfig['text'][1], $colorConfig['text'][2]);
    $text = strtoupper($category);
    imagestring($image, 5, 960, 540, $text, $textColor);
    
    imagejpeg($image, "images/heroes/{$category}.jpg", 90);
    imagedestroy($image);
    
    echo "  Creadas imágenes para {$category}\n";
}

echo "¡Proceso completado!\n";