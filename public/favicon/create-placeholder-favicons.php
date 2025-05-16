<?php

// Generar un simple PNG sólido de color rojo Dorasia
function createPlaceholderPNG($width, $height, $filename) {
    $image = imagecreatetruecolor($width, $height);
    
    // Color rojo de Dorasia
    $red = imagecolorallocate($image, 229, 16, 19);
    
    // Rellenar con el color rojo
    imagefilledrectangle($image, 0, 0, $width-1, $height-1, $red);
    
    // Añadir una 'D' simple en el centro
    $white = imagecolorallocate($image, 255, 255, 255);
    $fontSize = min($width, $height) * 0.6;
    $font = 5; // Fuente incorporada
    $text = 'D';
    
    // Calcular posición central
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    // Escribir el texto con una fuente más grande simulada
    $scale = $fontSize / 20;
    if ($scale > 1) {
        // Crear una imagen temporal más grande
        $tempImage = imagecreatetruecolor($width * $scale, $height * $scale);
        $tempRed = imagecolorallocate($tempImage, 229, 16, 19);
        $tempWhite = imagecolorallocate($tempImage, 255, 255, 255);
        imagefilledrectangle($tempImage, 0, 0, $width * $scale - 1, $height * $scale - 1, $tempRed);
        
        // Escribir el texto más grande
        imagestring($tempImage, 5, $x * $scale, $y * $scale, $text, $tempWhite);
        
        // Redimensionar al tamaño original
        imagecopyresampled($image, $tempImage, 0, 0, 0, 0, $width, $height, $width * $scale, $height * $scale);
        imagedestroy($tempImage);
    } else {
        imagestring($image, $font, $x, $y, $text, $white);
    }
    
    // Guardar como PNG
    imagepng($image, $filename);
    imagedestroy($image);
    
    echo "Creado: $filename\n";
}

// Crear los tamaños necesarios para el manifest
$sizes = [
    ['width' => 192, 'height' => 192],
    ['width' => 384, 'height' => 384],
    ['width' => 512, 'height' => 512],
];

foreach ($sizes as $size) {
    $filename = __DIR__ . "/favicon-{$size['width']}x{$size['height']}.png";
    createPlaceholderPNG($size['width'], $size['height'], $filename);
}

// Crear también el favicon.ico principal
$ico_sizes = [16, 32, 48];
$images = [];

foreach ($ico_sizes as $size) {
    $image = imagecreatetruecolor($size, $size);
    $red = imagecolorallocate($image, 229, 16, 19);
    imagefilledrectangle($image, 0, 0, $size-1, $size-1, $red);
    
    // Añadir 'D' en blanco
    $white = imagecolorallocate($image, 255, 255, 255);
    $x = $size * 0.3;
    $y = $size * 0.3;
    imagestring($image, 3, $x, $y, 'D', $white);
    
    $images[] = $image;
}

// Guardar el primer tamaño como favicon.ico (simple)
if (count($images) > 0) {
    imagepng($images[0], __DIR__ . '/../favicon.ico');
    echo "Creado: favicon.ico\n";
}

// Limpiar memoria
foreach ($images as $image) {
    imagedestroy($image);
}

echo "Favicons placeholder creados exitosamente.\n";