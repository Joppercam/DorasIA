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
        imagefilledrectangle($im, 0, 0, 299, 449, $bg);
        
        // Add text
        $text_color = imagecolorallocate($im, 255, 255, 255);
        $name = str_replace('-', ' ', strtoupper($actor));
        
        // Use built-in font
        $font_size = 5;
        $text_width = imagefontwidth($font_size) * strlen($name);
        $text_height = imagefontheight($font_size);
        $x = (300 - $text_width) / 2;
        $y = (450 - $text_height) / 2;
        
        imagestring($im, $font_size, $x, $y, $name, $text_color);
        
        // Add subtle border
        $border = imagecolorallocate($im, 255, 255, 255);
        imagerectangle($im, 0, 0, 299, 449, $border);
        
        imagejpeg($im, $imgPath, 90);
        imagedestroy($im);
        
        echo "Created placeholder for: {$actor}\n";
    }
}

echo "\nActor placeholder images created successfully!\n";