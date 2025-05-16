<?php

// Create placeholder images for actors
$actors = [
    'kim-soo-hyun',
    'iu',
    'song-hye-kyo',
    'park-seo-joon',
    'ji-chang-wook',
    'jun-ji-hyun',
    'lee-min-ho',
    'bae-suzy',
    'hyun-bin',
    'park-bo-gum',
    'song-joong-ki',
    'gong-yoo'
];

// Ensure directories exist
if (!file_exists('public/posters')) {
    mkdir('public/posters', 0755, true);
}

// Create placeholder images
foreach ($actors as $i => $actor) {
    $imgPath = "public/posters/{$actor}.jpg";
    
    if (!file_exists($imgPath)) {
        $im = imagecreatetruecolor(300, 450);
        
        // Create gradient background
        $colors = [
            [139, 69, 19],   // saddle brown
            [25, 25, 112],   // midnight blue
            [128, 0, 128],   // purple
            [0, 100, 0],     // dark green
            [128, 0, 0],     // maroon
            [75, 0, 130],    // indigo
            [47, 79, 79],    // dark slate gray
            [112, 128, 144], // slate gray
            [139, 0, 0],     // dark red
            [85, 107, 47],   // dark olive green
            [72, 61, 139],   // dark slate blue
            [105, 105, 105]  // dim gray
        ];
        
        $color = $colors[$i % count($colors)];
        $bg = imagecolorallocate($im, $color[0], $color[1], $color[2]);
        $light = imagecolorallocate($im, min(255, $color[0] + 40), min(255, $color[1] + 40), min(255, $color[2] + 40));
        
        // Create gradient
        for ($y = 0; $y < 450; $y++) {
            $alpha = $y / 450;
            $r = $color[0] + ($alpha * (min(255, $color[0] + 60) - $color[0]));
            $g = $color[1] + ($alpha * (min(255, $color[1] + 60) - $color[1]));
            $b = $color[2] + ($alpha * (min(255, $color[2] + 60) - $color[2]));
            $grad = imagecolorallocate($im, $r, $g, $b);
            imageline($im, 0, $y, 300, $y, $grad);
        }
        
        // Add text
        $text_color = imagecolorallocate($im, 255, 255, 255);
        $font_size = 20;
        $name = str_replace('-', ' ', strtoupper($actor));
        
        // Calculate text position
        $bbox = imagettfbbox($font_size, 0, __DIR__ . '/resources/fonts/DejaVuSans-Bold.ttf', $name);
        $text_width = $bbox[2] - $bbox[0];
        $text_height = $bbox[1] - $bbox[7];
        $x = (300 - $text_width) / 2;
        $y = (450 - $text_height) / 2;
        
        // Add shadow
        $shadow = imagecolorallocate($im, 0, 0, 0);
        imagettftext($im, $font_size, 0, $x + 2, $y + 2, $shadow, __DIR__ . '/resources/fonts/DejaVuSans-Bold.ttf', $name);
        imagettftext($im, $font_size, 0, $x, $y, $text_color, __DIR__ . '/resources/fonts/DejaVuSans-Bold.ttf', $name);
        
        // Add subtle border
        $border = imagecolorallocate($im, 255, 255, 255);
        imageline($im, 0, 0, 299, 0, $border);
        imageline($im, 0, 0, 0, 449, $border);
        imageline($im, 299, 0, 299, 449, $border);
        imageline($im, 0, 449, 299, 449, $border);
        
        imagejpeg($im, $imgPath, 90);
        imagedestroy($im);
        
        echo "Created placeholder for: {$actor}\n";
    }
}

echo "\nActor placeholder images created successfully!\n";