<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test de Imágenes básico</title>
    <style>
        body {
            background-color: #111;
            color: white;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        img {
            border: 1px solid red;
            max-width: 200px;
            max-height: 300px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <h1>Test de Imágenes Muy Básico</h1>
    <p>Este script muestra las imágenes en su formato más simple para diagnosticar problemas.</p>
    
    <h2>Imagen #1</h2>
    <img src="posters/poster-1.jpg" alt="Poster 1">
    
    <h2>Información del archivo</h2>
    <?php
    $file = __DIR__ . '/posters/poster-1.jpg';
    if (file_exists($file)) {
        echo "El archivo existe en el sistema de archivos<br>";
        echo "Tamaño: " . filesize($file) . " bytes<br>";
        echo "Última modificación: " . date("Y-m-d H:i:s", filemtime($file)) . "<br>";
        
        // Intentar obtener información de la imagen
        $info = getimagesize($file);
        if ($info) {
            echo "Dimensiones: " . $info[0] . "x" . $info[1] . "<br>";
            echo "Tipo MIME: " . $info['mime'] . "<br>";
        } else {
            echo "No se pudo obtener información de la imagen<br>";
        }
    } else {
        echo "El archivo NO existe en el sistema de archivos<br>";
    }
    ?>
    
    <h2>Otras pruebas</h2>
    <div>
        <h3>Con URL relativa</h3>
        <img src="./posters/poster-1.jpg" alt="Prueba 1">
        
        <h3>Con URL absoluta desde raíz</h3>
        <img src="/posters/poster-1.jpg" alt="Prueba 2">
        
        <h3>Con etiqueta de tiempo para forzar recarga</h3>
        <img src="posters/poster-1.jpg?t=<?php echo time(); ?>" alt="Prueba 3">
        
        <h3>Imagen como Base64 directamente en HTML</h3>
        <img src="data:image/jpeg;base64,<?php echo base64_encode(file_get_contents($file)); ?>" alt="Prueba Base64">
    </div>
    
    <script>
        // Verificar si las imágenes se cargan correctamente mediante JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            let images = document.querySelectorAll('img');
            images.forEach((img, index) => {
                img.addEventListener('load', function() {
                    console.log(`Imagen #${index+1} cargada correctamente`);
                });
                img.addEventListener('error', function() {
                    console.error(`Error al cargar la imagen #${index+1}`);
                    img.style.border = "3px solid red";
                    let errorMsg = document.createElement('p');
                    errorMsg.textContent = `Error al cargar: ${img.src}`;
                    errorMsg.style.color = "red";
                    img.insertAdjacentElement('afterend', errorMsg);
                });
            });
        });
    </script>
</body>
</html>