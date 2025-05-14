<?php

// Este script crea imágenes de ejemplo que puedan aparecer correctamente en la plataforma
function createSampleImage($outputPath, $width = 500, $height = 750, $color = "#FF0000") {
    // Crear la imagen base
    $image = imagecreatetruecolor($width, $height);
    
    // Convertir el color hexadecimal a RGB
    $hex = ltrim($color, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Asignar el color
    $bgColor = imagecolorallocate($image, $r, $g, $b);
    imagefill($image, 0, 0, $bgColor);
    
    // Agregar un borde
    $borderColor = imagecolorallocate($image, 255, 255, 255);
    imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);
    
    // Agregar texto
    $textColor = imagecolorallocate($image, 255, 255, 255);
    $text = "Imagen " . basename($outputPath);
    // Centrar el texto
    $fontSize = 5;
    $textBox = imagettfbbox($fontSize, 0, "./arial.ttf", $text);
    $textWidth = abs($textBox[4] - $textBox[0]);
    $textHeight = abs($textBox[5] - $textBox[1]);
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    // Si no podemos usar fuentes TrueType, usar la función simple
    imagestring($image, 5, $width/2 - 50, $height/2, $text, $textColor);
    
    // Guardar la imagen
    imagejpeg($image, $outputPath, 90);
    imagedestroy($image);
    
    return "Creada imagen: " . $outputPath;
}

// Crear imágenes de póster
$colors = [
    "#e53935", "#d81b60", "#8e24aa", "#5e35b1", 
    "#3949ab", "#1e88e5", "#039be5", "#00acc1", 
    "#00897b", "#43a047"
];

echo "<h1>Creando imágenes de prueba</h1>";
echo "<pre>";

// Crear imágenes de póster
for ($i = 1; $i <= 10; $i++) {
    $color = $colors[($i-1) % count($colors)];
    $outputPath = __DIR__ . "/fix-poster-{$i}.jpg";
    echo createSampleImage($outputPath, 500, 750, $color) . "\n";
}

// Crear imágenes de backdrop
$backdropColors = [
    "#b71c1c", "#880e4f", "#4a148c", "#311b92", "#1a237e"
];

for ($i = 1; $i <= 5; $i++) {
    $color = $backdropColors[($i-1) % count($backdropColors)];
    $outputPath = __DIR__ . "/fix-backdrop-{$i}.jpg";
    echo createSampleImage($outputPath, 1600, 900, $color) . "\n";
}

echo "</pre>";
echo "<h2>¡Imágenes creadas con éxito!</h2>";
echo "<p>Ahora intenta usar estas imágenes fix-poster-*.jpg y fix-backdrop-*.jpg en tu aplicación.</p>";

?>