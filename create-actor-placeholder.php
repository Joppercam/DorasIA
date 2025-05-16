<?php

// Create a generic actor placeholder image
$im = imagecreatetruecolor(300, 300);

// Background color (dark gray)
$bg = imagecolorallocate($im, 55, 55, 55);
imagefilledrectangle($im, 0, 0, 299, 299, $bg);

// Icon color (light gray)
$icon_color = imagecolorallocate($im, 150, 150, 150);

// Draw a simple person icon
// Head (circle)
$center_x = 150;
$center_y = 100;
$radius = 40;
imagefilledellipse($im, $center_x, $center_y, $radius * 2, $radius * 2, $icon_color);

// Body (triangle/shoulders)
$points = array(
    $center_x - 60, 299,  // bottom left
    $center_x + 60, 299,  // bottom right
    $center_x + 40, 160,  // right shoulder
    $center_x + 20, 140,  // right neck
    $center_x - 20, 140,  // left neck
    $center_x - 40, 160,  // left shoulder
);
imagefilledpolygon($im, $points, count($points)/2, $icon_color);

// Save the image
$output_path = "public/images/actor-placeholder.jpg";
imagejpeg($im, $output_path, 90);
imagedestroy($im);

echo "Actor placeholder created at: {$output_path}\n";