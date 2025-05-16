<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/html; charset=utf-8');

$socialRoutes = [
    'social.login' => 'auth/{provider}',
    'social.callback' => 'auth/{provider}/callback',
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test de Navegación y Autenticación</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        code { background: #f4f4f4; padding: 2px 5px; }
        .test-item { margin: 10px 0; padding: 10px; background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Test de Navegación y Autenticación</h1>
    
    <div class="section">
        <h2>1. Verificación de Rutas</h2>
        <?php
        foreach ($socialRoutes as $name => $uri) {
            try {
                $route = Route::getRoutes()->getByName($name);
                if ($route) {
                    echo "<div class='test-item'>";
                    echo "<span class='success'>✓</span> Ruta <code>$name</code> existe: <code>$uri</code><br>";
                    echo "Middlewares: " . implode(', ', $route->middleware()) . "<br>";
                    echo "</div>";
                } else {
                    echo "<div class='test-item'><span class='error'>✗</span> Ruta <code>$name</code> no encontrada</div>";
                }
            } catch (Exception $e) {
                echo "<div class='test-item'><span class='error'>✗</span> Error verificando ruta <code>$name</code>: " . $e->getMessage() . "</div>";
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>2. Verificación de Middleware CSRF</h2>
        <?php
        $csrfPath = app_path('Http/Middleware/VerifyCsrfToken.php');
        if (file_exists($csrfPath)) {
            echo "<div class='test-item'><span class='success'>✓</span> Middleware CSRF personalizado existe</div>";
            
            // Check if it excludes social auth routes
            $csrfContent = file_get_contents($csrfPath);
            if (strpos($csrfContent, 'auth/*/callback') !== false) {
                echo "<div class='test-item'><span class='success'>✓</span> Rutas de callback social excluidas de CSRF</div>";
            } else {
                echo "<div class='test-item'><span class='warning'>⚠</span> Las rutas de callback social no están excluidas de CSRF</div>";
            }
        } else {
            echo "<div class='test-item'><span class='error'>✗</span> Middleware CSRF personalizado no encontrado</div>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Verificación de Controller</h2>
        <?php
        $controllerPath = app_path('Http/Controllers/SocialAuthController.php');
        if (file_exists($controllerPath)) {
            $controllerContent = file_get_contents($controllerPath);
            
            // Check for session regeneration
            if (strpos($controllerContent, 'session()->regenerate()') !== false) {
                echo "<div class='test-item'><span class='success'>✓</span> Regeneración de sesión implementada</div>";
            } else {
                echo "<div class='test-item'><span class='warning'>⚠</span> Regeneración de sesión no encontrada</div>";
            }
            
            // Check for error handling
            if (strpos($controllerContent, 'request->has(\'error\')') !== false) {
                echo "<div class='test-item'><span class='success'>✓</span> Manejo de errores del proveedor implementado</div>";
            } else {
                echo "<div class='test-item'><span class='warning'>⚠</span> Manejo de errores del proveedor no encontrado</div>";
            }
            
            // Check for logging
            if (strpos($controllerContent, '\Log::error') !== false) {
                echo "<div class='test-item'><span class='success'>✓</span> Registro de errores implementado</div>";
            } else {
                echo "<div class='test-item'><span class='warning'>⚠</span> Registro de errores no encontrado</div>";
            }
        } else {
            echo "<div class='test-item'><span class='error'>✗</span> SocialAuthController no encontrado</div>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Prueba de Enlaces</h2>
        <p>Prueba estos enlaces para verificar el comportamiento:</p>
        <ul>
            <li><a href="/login">Ir al Login</a></li>
            <li><a href="/auth/google">Iniciar sesión con Google</a></li>
            <li><a href="/">Volver al inicio</a></li>
            <li><a href="/news">Ver noticias</a></li>
        </ul>
        
        <h3>Instrucciones de prueba:</h3>
        <ol>
            <li>Intenta iniciar sesión con Google</li>
            <li>Cuando aparezca la pantalla de Google, usa el botón de retroceso del navegador</li>
            <li>Verifica que puedas navegar normalmente sin errores</li>
            <li>Los enlaces del menú y las noticias deberían funcionar correctamente</li>
        </ol>
    </div>
    
    <div class="section">
        <h2>5. Estado Actual de la Sesión</h2>
        <?php
        session_start();
        echo "<div class='test-item'>";
        echo "ID de sesión: <code>" . session_id() . "</code><br>";
        echo "Token CSRF: <code>" . (isset($_SESSION['_token']) ? $_SESSION['_token'] : 'No definido') . "</code><br>";
        echo "Usuario autenticado: <code>" . (auth()->check() ? 'Sí (' . auth()->user()->email . ')' : 'No') . "</code>";
        echo "</div>";
        ?>
    </div>
</body>
</html>