<?php

// Tamaño de la imagen
$width = 300;
$height = 450;

// Lista de actores coreanos
$actors = [
    'jun-ji-hyun' => 'Jun Ji-hyun',
    'lee-min-ho' => 'Lee Min-ho',
    'bae-suzy' => 'Bae Suzy',
    'hyun-bin' => 'Hyun Bin',
    'son-ye-jin' => 'Son Ye-jin',
    'park-bo-gum' => 'Park Bo-gum',
    'kim-soo-hyun' => 'Kim Soo-hyun',
    'song-hye-kyo' => 'Song Hye-kyo',
    'park-shin-hye' => 'Park Shin-hye',
    'lee-jong-suk' => 'Lee Jong-suk',
    'han-hyo-joo' => 'Han Hyo-joo',
    'lee-jung-jae' => 'Lee Jung-jae',
    'park-hae-soo' => 'Park Hae-soo',
    'cha-eun-woo' => 'Cha Eun-woo',
    'kim-tae-ri' => 'Kim Tae-ri',
    'nam-joo-hyuk' => 'Nam Joo-hyuk',
    'yoo-jae-suk' => 'Yoo Jae-suk',
    'jung-ho-yeon' => 'Jung Ho-yeon'
];

// Colores de fondo más suaves y agradables
$colors = [
    '#FF6B6B', // Coral suave
    '#4ECDC4', // Verde turquesa
    '#FFE66D', // Amarillo pastel
    '#92B4EC', // Azul suave
    '#C06C84', // Rosa viejo
    '#6C5B7B', // Púrpura oscuro
    '#45B7D1', // Azul cielo
    '#FED9B7', // Melocotón
    '#81C784', // Verde menta
    '#9FA8DA', // Lavanda
    '#F48FB1', // Rosa claro
    '#80CBC4', // Verde agua
    '#FFCCBC', // Naranja pastel
    '#CE93D8', // Orquídea
    '#A5D6A7', // Verde claro
    '#90CAF9', // Azul claro
    '#F8BBD0', // Rosa bebé
    '#80DEEA'  // Cian
];

// Crear las imágenes
foreach ($actors as $filename => $actorName) {
    // Crear una imagen
    $im = imagecreate($width, $height);
    
    // Obtener un color aleatorio del array
    $hexColor = $colors[array_rand($colors)];
    list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
    $backgroundColor = imagecolorallocate($im, $r, $g, $b);
    
    // Color del texto (blanco)
    $textColor = imagecolorallocate($im, 255, 255, 255);
    
    // Rellenar el fondo
    imagefill($im, 0, 0, $backgroundColor);
    
    // Configurar el texto
    $fontPath = __DIR__ . '/../fonts/Roboto-Bold.ttf';
    $fontSize = 60;
    
    // Si no existe la fuente, usar un tamaño de fuente predeterminado
    if (!file_exists($fontPath)) {
        // Obtener las iniciales
        $nameParts = explode(' ', $actorName);
        $initials = '';
        foreach ($nameParts as $part) {
            if (strlen($part) > 0) {
                $initials .= strtoupper($part[0]);
            }
        }
        
        // Calcular la posición del texto
        $fontWidth = imagefontwidth(5) * strlen($initials);
        $fontHeight = imagefontheight(5);
        $x = ($width - $fontWidth) / 2;
        $y = ($height - $fontHeight) / 2;
        
        // Dibujar las iniciales
        imagestring($im, 5, $x, $y, $initials, $textColor);
        
        // Dibujar el nombre completo más abajo
        $fontWidth2 = imagefontwidth(3) * strlen($actorName);
        $x2 = ($width - $fontWidth2) / 2;
        $y2 = $y + $fontHeight + 20;
        imagestring($im, 3, $x2, $y2, $actorName, $textColor);
    } else {
        // Si existe la fuente TrueType
        $initials = '';
        $nameParts = explode(' ', $actorName);
        foreach ($nameParts as $part) {
            if (strlen($part) > 0) {
                $initials .= strtoupper($part[0]);
            }
        }
        
        // Obtener el bounding box del texto
        $bbox = imagettfbbox($fontSize, 0, $fontPath, $initials);
        $textWidth = abs($bbox[4] - $bbox[0]);
        $textHeight = abs($bbox[5] - $bbox[1]);
        
        // Calcular la posición centrada
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 + $textHeight;
        
        // Dibujar las iniciales
        imagettftext($im, $fontSize, 0, $x, $y, $textColor, $fontPath, $initials);
        
        // Dibujar el nombre completo más abajo
        $nameSize = 20;
        $bbox2 = imagettfbbox($nameSize, 0, $fontPath, $actorName);
        $textWidth2 = abs($bbox2[4] - $bbox2[0]);
        $x2 = ($width - $textWidth2) / 2;
        $y2 = $y + 60;
        imagettftext($im, $nameSize, 0, $x2, $y2, $textColor, $fontPath, $actorName);
    }
    
    // Agregar un borde elegante
    $borderColor = imagecolorallocate($im, 255, 255, 255);
    imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
    imagerectangle($im, 1, 1, $width - 2, $height - 2, $borderColor);
    
    // Guardar la imagen
    $filepath = __DIR__ . '/posters/' . $filename . '.jpg';
    imagejpeg($im, $filepath, 90);
    imagedestroy($im);
    
    echo "Creada imagen para: $actorName\n";
}

// Crear también las imágenes para grupos
$groups = [
    'bts' => 'BTS',
    'blackpink' => 'BLACKPINK',
    'squid-game' => 'Squid Game Cast'
];

foreach ($groups as $filename => $groupName) {
    $im = imagecreate($width, $height);
    $hexColor = $colors[array_rand($colors)];
    list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
    $backgroundColor = imagecolorallocate($im, $r, $g, $b);
    $textColor = imagecolorallocate($im, 255, 255, 255);
    
    imagefill($im, 0, 0, $backgroundColor);
    
    // Usar las iniciales del grupo
    $initials = '';
    $nameParts = explode(' ', $groupName);
    foreach ($nameParts as $part) {
        if (strlen($part) > 0) {
            $initials .= strtoupper($part[0]);
        }
    }
    
    $fontWidth = imagefontwidth(5) * strlen($initials);
    $fontHeight = imagefontheight(5);
    $x = ($width - $fontWidth) / 2;
    $y = ($height - $fontHeight) / 2;
    
    imagestring($im, 5, $x, $y, $initials, $textColor);
    
    $fontWidth2 = imagefontwidth(3) * strlen($groupName);
    $x2 = ($width - $fontWidth2) / 2;
    $y2 = $y + $fontHeight + 20;
    imagestring($im, 3, $x2, $y2, $groupName, $textColor);
    
    $borderColor = imagecolorallocate($im, 255, 255, 255);
    imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
    imagerectangle($im, 1, 1, $width - 2, $height - 2, $borderColor);
    
    $filepath = __DIR__ . '/posters/' . $filename . '.jpg';
    imagejpeg($im, $filepath, 90);
    imagedestroy($im);
    
    echo "Creada imagen para: $groupName\n";
}

echo "\nTodas las imágenes han sido creadas exitosamente en public/posters/\n";