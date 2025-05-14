<?php
/**
 * Script para crear imágenes de categorías y héroes
 */

// Categorías y sus colores
$categories = [
    'k-drama' => ['bg' => [50, 120, 200], 'text' => [255, 255, 255], 'name' => 'K-Drama'],
    'j-drama' => ['bg' => [200, 50, 50], 'text' => [255, 255, 255], 'name' => 'J-Drama'],
    'c-drama' => ['bg' => [200, 120, 50], 'text' => [255, 255, 255], 'name' => 'C-Drama'],
    'peliculas' => ['bg' => [50, 150, 50], 'text' => [255, 255, 255], 'name' => 'Películas'],
    'dorasia-originals' => ['bg' => [100, 50, 150], 'text' => [255, 255, 255], 'name' => 'Dorasia Originals'],
];

// Asegurar que existen los directorios
$categoryDir = __DIR__ . '/images/categories';
$heroDir = __DIR__ . '/images/heroes';

if (!is_dir($categoryDir)) {
    mkdir($categoryDir, 0755, true);
}

if (!is_dir($heroDir)) {
    mkdir($heroDir, 0755, true);
}

// Crear imágenes para cada categoría
foreach ($categories as $slug => $config) {
    echo "Creando imágenes para la categoría {$config['name']}...\n";
    
    // Imagen para categoría (600x400)
    $categoryImage = imagecreatetruecolor(600, 400);
    $bgColor = imagecolorallocate($categoryImage, $config['bg'][0], $config['bg'][1], $config['bg'][2]);
    imagefill($categoryImage, 0, 0, $bgColor);
    
    // Añadir texto
    $textColor = imagecolorallocate($categoryImage, $config['text'][0], $config['text'][1], $config['text'][2]);
    imagestring($categoryImage, 5, 250, 180, strtoupper($config['name']), $textColor);
    
    $categoryPath = "{$categoryDir}/{$slug}.jpg";
    imagejpeg($categoryImage, $categoryPath, 90);
    imagedestroy($categoryImage);
    
    echo "  Creada imagen de categoría: {$categoryPath}\n";
    
    // Imagen para hero (1920x1080)
    $heroImage = imagecreatetruecolor(1920, 1080);
    $bgColor = imagecolorallocate($heroImage, $config['bg'][0], $config['bg'][1], $config['bg'][2]);
    imagefill($heroImage, 0, 0, $bgColor);
    
    // Añadir texto
    $textColor = imagecolorallocate($heroImage, $config['text'][0], $config['text'][1], $config['text'][2]);
    imagestring($heroImage, 5, 960, 540, strtoupper($config['name']), $textColor);
    
    $heroPath = "{$heroDir}/{$slug}.jpg";
    imagejpeg($heroImage, $heroPath, 90);
    imagedestroy($heroImage);
    
    echo "  Creada imagen de hero: {$heroPath}\n";
}

// Crear hero background genérico
$heroBackground = imagecreatetruecolor(1920, 1080);
$bgColor = imagecolorallocate($heroBackground, 20, 20, 40);
imagefill($heroBackground, 0, 0, $bgColor);

// Añadir degradado (alpha debe estar entre 0 y 127)
for ($y = 0; $y < 1080; $y++) {
    // Convertir 0-255 a 0-127 para alpha (0 = opaco, 127 = transparente)
    $alpha = min(127, intval(($y / 1080) * 127));
    $color = imagecolorallocatealpha($heroBackground, 100, 50, 100, $alpha);
    imageline($heroBackground, 0, $y, 1920, $y, $color);
}

$textColor = imagecolorallocate($heroBackground, 220, 220, 220);
imagestring($heroBackground, 5, 960, 540, "DORASIA", $textColor);

$backgroundPath = "{$heroDir}/hero-bg.jpg";
imagejpeg($heroBackground, $backgroundPath, 90);
imagedestroy($heroBackground);

echo "  Creada imagen de fondo genérica: {$backgroundPath}\n";
echo "¡Proceso completado!\n";