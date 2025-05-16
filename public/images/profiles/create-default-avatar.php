<?php

// Create a default avatar SVG
$svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
<circle cx="50" cy="50" r="50" fill="#4B5563"/>
<path d="M50 45C56.627 45 62 39.627 62 33C62 26.373 56.627 21 50 21C43.373 21 38 26.373 38 33C38 39.627 43.373 45 50 45Z" fill="#9CA4B0"/>
<path d="M30 79.5C30 65.969 40.969 55 54.5 55H45.5C59.031 55 70 65.969 70 79.5V80H30V79.5Z" fill="#9CA4B0"/>
</svg>';

// Save as default.svg
file_put_contents(__DIR__ . '/default.svg', $svg);

// Convert SVG to PNG using ImageMagick if available, or create a simple default image
if (extension_loaded('imagick')) {
    try {
        $imagick = new Imagick();
        $imagick->readImageBlob($svg);
        $imagick->setImageFormat('png');
        $imagick->writeImage(__DIR__ . '/default.png');
        echo "Created default.png using ImageMagick\n";
    } catch (Exception $e) {
        echo "Error creating PNG with ImageMagick: " . $e->getMessage() . "\n";
    }
} else {
    // Create a simple PNG using GD
    $image = imagecreate(100, 100);
    $gray = imagecolorallocate($image, 75, 85, 99);
    $lightGray = imagecolorallocate($image, 156, 164, 176);
    imagefill($image, 0, 0, $gray);
    imagefilledellipse($image, 50, 33, 24, 24, $lightGray);
    imagefilledrectangle($image, 30, 55, 70, 80, $lightGray);
    imagepng($image, __DIR__ . '/default.png');
    imagedestroy($image);
    echo "Created default.png using GD\n";
}

// Copy as default.jpg
if (file_exists(__DIR__ . '/default.png')) {
    copy(__DIR__ . '/default.png', __DIR__ . '/default.jpg');
    echo "Created default.jpg\n";
}

echo "Default avatar files created successfully!\n";