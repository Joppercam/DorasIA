<?php
/**
 * Generador de Favicons para Dorasia
 * 
 * Este script genera todos los tamaños necesarios de favicon
 * a partir del archivo favicon.svg
 */

// Verificar que GD esté disponible
if (!extension_loaded('gd')) {
    die('La extensión GD de PHP es requerida para generar los favicons');
}

// Tamaños requeridos para favicons (ancho x alto)
$sizes = [
    16, 32, 48, 64, 72, 96, 120, 128, 144, 152, 180, 192, 384, 512
];

// Directorio actual
$dir = __DIR__;

// Cargar el SVG original
$svgFile = $dir . '/favicon.svg';
if (!file_exists($svgFile)) {
    die('Archivo SVG no encontrado: ' . $svgFile);
}

// Generar PNG para cada tamaño
foreach ($sizes as $size) {
    // Usar Imagick si está disponible
    if (extension_loaded('imagick')) {
        try {
            $im = new Imagick();
            $im->readImage($svgFile);
            $im->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
            $im->setImageFormat('png');
            $im->writeImage($dir . "/favicon-{$size}x{$size}.png");
            echo "Creado favicon-{$size}x{$size}.png con Imagick\n";
        } catch (Exception $e) {
            echo "Error al crear favicon-{$size}x{$size}.png: " . $e->getMessage() . "\n";
        }
    } else {
        // Intentar usar métodos alternativos para convertir SVG a PNG
        // Esto dependerá de las herramientas disponibles en tu sistema
        echo "Se requiere Imagick para convertir SVG a PNG de forma óptima\n";
        echo "Alternativa: usa una herramienta en línea para convertir el favicon.svg a los diferentes tamaños PNG\n";
        break;
    }
}

// Generar archivo .ico (16x16, 32x32, 48x48)
if (extension_loaded('imagick')) {
    try {
        $icon = new Imagick();
        foreach ([16, 32, 48] as $size) {
            $im = new Imagick();
            $im->readImage($svgFile);
            $im->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1);
            $im->setImageFormat('png');
            $icon->addImage($im);
        }
        $icon->setImageFormat('ico');
        $icon->writeImages($dir . '/../favicon.ico', true);
        echo "Creado favicon.ico\n";
    } catch (Exception $e) {
        echo "Error al crear favicon.ico: " . $e->getMessage() . "\n";
    }
}

// Generar archivo webmanifest para PWA
$manifest = [
    'name' => 'Dorasia',
    'short_name' => 'Dorasia',
    'description' => 'La mejor plataforma de streaming de contenido asiático',
    'icons' => [],
    'theme_color' => '#E51013',
    'background_color' => '#141414',
    'display' => 'standalone',
    'start_url' => '/'
];

// Añadir los iconos al manifest
foreach ([192, 384, 512] as $size) {
    $manifest['icons'][] = [
        'src' => "/favicon/favicon-{$size}x{$size}.png",
        'sizes' => "{$size}x{$size}",
        'type' => 'image/png',
        'purpose' => 'any maskable'
    ];
}

// Guardar el manifest
file_put_contents($dir . '/site.webmanifest', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Creado site.webmanifest\n";

echo "Proceso completado. Recuerda añadir las etiquetas necesarias en el layout principal.\n";