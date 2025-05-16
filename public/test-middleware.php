<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test de Middleware</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        code { background: #f4f4f4; padding: 2px 5px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Test de Middleware</h1>
    
    <div class="section">
        <h2>1. Middleware Registrados</h2>
        <?php
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $middleware = $kernel->getMiddleware();
        $routeMiddleware = $kernel->getMiddlewareGroups();
        
        echo "<h3>Middleware de Ruta:</h3>";
        $reflection = new ReflectionClass($kernel);
        $property = $reflection->getProperty('middlewareAliases');
        $property->setAccessible(true);
        $aliases = $property->getValue($kernel);
        
        foreach ($aliases as $name => $class) {
            echo "<div>";
            echo "<code>$name</code> => ";
            if (class_exists($class)) {
                echo "<span class='success'>$class ✓</span>";
            } else {
                echo "<span class='error'>$class ✗ (Clase no encontrada)</span>";
            }
            echo "</div>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>2. Rutas que usan has.active.profile</h2>
        <?php
        $routes = Route::getRoutes();
        $count = 0;
        
        foreach ($routes as $route) {
            if (in_array('has.active.profile', $route->middleware())) {
                $count++;
                echo "<div class='info'>";
                echo "Ruta: <code>" . $route->uri() . "</code><br>";
                echo "Método: " . implode(', ', $route->methods()) . "<br>";
                echo "Acción: " . $route->getActionName() . "<br>";
                echo "</div>";
            }
        }
        
        if ($count === 0) {
            echo "<span class='error'>No se encontraron rutas usando has.active.profile</span>";
        } else {
            echo "<div class='success'>Se encontraron $count rutas usando el middleware</div>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Archivos de Middleware</h2>
        <?php
        $middlewarePath = app_path('Http/Middleware');
        $files = glob($middlewarePath . '/*.php');
        
        foreach ($files as $file) {
            $filename = basename($file);
            echo "<div>";
            echo "<code>$filename</code>";
            if (strpos($filename, 'HasActiveProfile') !== false) {
                echo " <span class='success'>✓ Middleware de perfiles encontrado</span>";
                echo "<br><small>Ruta: $file</small>";
            }
            echo "</div>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Verificación de Namespace</h2>
        <?php
        $middlewareClass = '\\App\\Http\\Middleware\\HasActiveProfile';
        echo "<div>";
        echo "Clase: <code>$middlewareClass</code><br>";
        if (class_exists($middlewareClass)) {
            echo "<span class='success'>✓ La clase existe</span><br>";
            $reflection = new ReflectionClass($middlewareClass);
            echo "Archivo: " . $reflection->getFileName() . "<br>";
            echo "Namespace: " . $reflection->getNamespaceName() . "<br>";
        } else {
            echo "<span class='error'>✗ La clase no existe</span>";
        }
        echo "</div>";
        ?>
    </div>
    
    <div class="section">
        <h2>5. Solución Recomendada</h2>
        <p>Si el middleware no funciona, ejecuta estos comandos:</p>
        <pre>
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan optimize:clear
composer dump-autoload
        </pre>
        
        <p>Si el error persiste, verifica que:</p>
        <ul>
            <li>El archivo existe en <code>app/Http/Middleware/HasActiveProfile.php</code></li>
            <li>El namespace es correcto: <code>namespace App\Http\Middleware;</code></li>
            <li>La clase se llama exactamente <code>HasActiveProfile</code></li>
            <li>Está registrado en <code>app/Http/Kernel.php</code></li>
        </ul>
    </div>
</body>
</html>