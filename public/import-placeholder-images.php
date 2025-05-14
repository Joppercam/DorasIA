<?php
/**
 * Script para importar imágenes de muestra predefinidas
 * 
 * Este script copia imágenes de muestra del subdirectorio public/images
 * a las ubicaciones adecuadas para posters, backdrops, etc.
 */

// Determinar las rutas
$baseImagePath = __DIR__ . '/images';
$posterPath = __DIR__ . '/posters';
$backdropPath = __DIR__ . '/backdrops';

// Asegurarse de que existen los directorios
if (!is_dir($posterPath)) mkdir($posterPath, 0755, true);
if (!is_dir($backdropPath)) mkdir($backdropPath, 0755, true);

// Buscar imágenes de muestra en thumbnails para usar como pósters
$thumbnailPath = $baseImagePath . '/thumbnails';
if (is_dir($thumbnailPath)) {
    $thumbnails = glob($thumbnailPath . '/poster-*.jpg');
    
    foreach ($thumbnails as $index => $thumbnail) {
        $posterFile = $posterPath . '/poster-' . ($index + 1) . '.jpg';
        copy($thumbnail, $posterFile);
        echo "Copiada imagen: " . basename($thumbnail) . " a " . basename($posterFile) . "\n";
    }
}

// Buscar imágenes de muestra en featured para usar como backdrops
$featuredPath = $baseImagePath . '/featured';
if (is_dir($featuredPath)) {
    $backdrops = glob($featuredPath . '/backdrop-*.jpg');
    
    foreach ($backdrops as $index => $backdrop) {
        $backdropFile = $backdropPath . '/backdrop-' . ($index + 1) . '.jpg';
        copy($backdrop, $backdropFile);
        echo "Copiada imagen: " . basename($backdrop) . " a " . basename($backdropFile) . "\n";
    }
}

// Crear imágenes de placeholder si no existen suficientes
$minPosters = 10;
$minBackdrops = 5;

$existingPosters = count(glob($posterPath . '/*.jpg'));
if ($existingPosters < $minPosters) {
    for ($i = $existingPosters + 1; $i <= $minPosters; $i++) {
        // Crear imagen de póster
        $poster = imagecreatetruecolor(500, 750);
        $bgColor = imagecolorallocate($poster, rand(10, 90), rand(10, 90), rand(10, 90));
        imagefill($poster, 0, 0, $bgColor);
        
        // Añadir texto
        $textColor = imagecolorallocate($poster, 255, 255, 255);
        $text = "Póster {$i}";
        imagestring($poster, 5, 200, 375, $text, $textColor);
        
        // Guardar imagen
        $outputPath = "{$posterPath}/poster-{$i}.jpg";
        imagejpeg($poster, $outputPath, 90);
        imagedestroy($poster);
        
        echo "Creado póster de muestra: poster-{$i}.jpg\n";
    }
}

$existingBackdrops = count(glob($backdropPath . '/*.jpg'));
if ($existingBackdrops < $minBackdrops) {
    for ($i = $existingBackdrops + 1; $i <= $minBackdrops; $i++) {
        // Crear imagen de backdrop
        $backdrop = imagecreatetruecolor(1280, 720);
        $bgColor = imagecolorallocate($backdrop, rand(10, 90), rand(10, 90), rand(10, 90));
        imagefill($backdrop, 0, 0, $bgColor);
        
        // Añadir texto
        $textColor = imagecolorallocate($backdrop, 255, 255, 255);
        $text = "Backdrop {$i}";
        imagestring($backdrop, 5, 640, 360, $text, $textColor);
        
        // Guardar imagen
        $outputPath = "{$backdropPath}/backdrop-{$i}.jpg";
        imagejpeg($backdrop, $outputPath, 90);
        imagedestroy($backdrop);
        
        echo "Creado backdrop de muestra: backdrop-{$i}.jpg\n";
    }
}

// Asegurarse de que existe la imagen de perfil por defecto
$profileDir = $baseImagePath . '/profiles';
if (!is_dir($profileDir)) mkdir($profileDir, 0755, true);

if (!file_exists($profileDir . '/default.jpg')) {
    $profile = imagecreatetruecolor(300, 300);
    $bgColor = imagecolorallocate($profile, 100, 100, 100);
    imagefill($profile, 0, 0, $bgColor);
    
    // Dibujar un círculo para simular un perfil
    $circleColor = imagecolorallocate($profile, 200, 200, 200);
    imagefilledellipse($profile, 150, 150, 200, 200, $circleColor);
    
    // Dibujar silueta de persona
    $silhouetteColor = imagecolorallocate($profile, 150, 150, 150);
    imagefilledellipse($profile, 150, 120, 100, 100, $silhouetteColor);
    imagefilledrectangle($profile, 100, 170, 200, 250, $silhouetteColor);
    
    imagejpeg($profile, $profileDir . '/default.jpg', 90);
    imagedestroy($profile);
    
    echo "Creada imagen de perfil por defecto\n";
}

// Crear imagen de placeholder si no existe
if (!file_exists($posterPath . '/placeholder.jpg')) {
    copy($posterPath . '/poster-1.jpg', $posterPath . '/placeholder.jpg');
    echo "Copiado póster de muestra a placeholder.jpg\n";
}

if (!file_exists($backdropPath . '/placeholder.jpg')) {
    copy($backdropPath . '/backdrop-1.jpg', $backdropPath . '/placeholder.jpg');
    echo "Copiado backdrop de muestra a placeholder.jpg\n";
}

echo "¡Proceso completado con éxito!\n";