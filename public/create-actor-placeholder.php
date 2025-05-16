<?php

// Tamaño de la imagen
$width = 300;
$height = 450;

// Crear una imagen
$im = imagecreate($width, $height);

// Color de fondo gris oscuro
$backgroundColor = imagecolorallocate($im, 45, 45, 45);
$textColor = imagecolorallocate($im, 255, 255, 255);
$borderColor = imagecolorallocate($im, 100, 100, 100);

// Rellenar el fondo
imagefill($im, 0, 0, $backgroundColor);

// Texto para el placeholder
$text = "ACTOR";

// Calcular la posición del texto
$fontWidth = imagefontwidth(5) * strlen($text);
$fontHeight = imagefontheight(5);
$x = ($width - $fontWidth) / 2;
$y = ($height - $fontHeight) / 2;

// Dibujar el texto
imagestring($im, 5, $x, $y, $text, $textColor);

// Agregar un borde
imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
imagerectangle($im, 1, 1, $width - 2, $height - 2, $borderColor);

// Agregar un ícono de persona
$iconSize = 60;
$iconX = ($width - $iconSize) / 2;
$iconY = $y - $iconSize - 20;

// Dibujar un círculo para la cabeza
imageellipse($im, $width / 2, $iconY, $iconSize / 2, $iconSize / 2, $textColor);

// Dibujar el cuerpo
$bodyY = $iconY + $iconSize / 4;
$bodyHeight = $iconSize;
imageellipse($im, $width / 2, $bodyY + $bodyHeight / 2, $iconSize, $bodyHeight, $textColor);

// Guardar la imagen
$filepath = __DIR__ . '/images/actor-placeholder.jpg';
imagejpeg($im, $filepath, 90);
imagedestroy($im);

echo "Imagen placeholder de actor creada en: $filepath\n";