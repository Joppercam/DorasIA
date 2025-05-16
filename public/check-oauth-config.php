<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificación de Configuración OAuth</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .warning { background: #fff3cd; color: #856404; }
        .info { background: #d1ecf1; color: #0c5460; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
        .provider { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        h2 { color: #333; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Verificación de Configuración OAuth</h1>
    
    <div class="provider">
        <h2>Google OAuth</h2>
        
        <?php
        $googleConfig = config('services.google');
        $clientId = $googleConfig['client_id'] ?? null;
        $clientSecret = $googleConfig['client_secret'] ?? null;
        $redirectUri = $googleConfig['redirect'] ?? null;
        ?>
        
        <div class="status <?= $clientId ? 'success' : 'error' ?>">
            <strong>Client ID:</strong> 
            <?= $clientId ? 'Configurado (' . substr($clientId, 0, 20) . '...)' : 'NO CONFIGURADO' ?>
        </div>
        
        <div class="status <?= $clientSecret ? 'success' : 'error' ?>">
            <strong>Client Secret:</strong> 
            <?= $clientSecret ? 'Configurado' : 'NO CONFIGURADO' ?>
        </div>
        
        <div class="status <?= $redirectUri ? 'success' : 'info' ?>">
            <strong>Redirect URI:</strong> 
            <code><?= htmlspecialchars($redirectUri) ?></code>
        </div>
        
        <?php if (!$clientId || !$clientSecret): ?>
        <div class="status warning">
            <h3>⚠️ Configuración Requerida</h3>
            <p>Para configurar Google OAuth, necesitas:</p>
            <ol>
                <li>Ir a <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
                <li>Crear un nuevo proyecto o seleccionar uno existente</li>
                <li>Habilitar la API de Google+ o Google Sign-In</li>
                <li>Crear credenciales OAuth 2.0</li>
                <li>Configurar las URIs de redirección autorizadas:
                    <pre><?= htmlspecialchars($redirectUri) ?></pre>
                </li>
                <li>Copiar el Client ID y Client Secret</li>
                <li>Actualizar tu archivo <code>.env</code>:
                    <pre>GOOGLE_CLIENT_ID=tu_client_id_aqui
GOOGLE_CLIENT_SECRET=tu_client_secret_aqui
GOOGLE_REDIRECT_URI=<?= htmlspecialchars($redirectUri) ?></pre>
                </li>
            </ol>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="provider">
        <h2>Estado Actual del Archivo .env</h2>
        <?php
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            $googleVars = [];
            preg_match_all('/^(GOOGLE_[A-Z_]+)=(.*)$/m', $envContent, $matches);
            
            if (!empty($matches[0])) {
                echo "<pre>";
                foreach ($matches[0] as $match) {
                    echo htmlspecialchars($match) . "\n";
                }
                echo "</pre>";
            } else {
                echo "<div class='status error'>No se encontraron variables de Google en .env</div>";
            }
        }
        ?>
    </div>
    
    <div class="provider">
        <h2>URIs de Callback Registradas</h2>
        <?php
        $routes = Route::getRoutes();
        foreach ($routes as $route) {
            if (strpos($route->uri(), 'auth/') === 0 && strpos($route->uri(), 'callback') !== false) {
                $fullUrl = url($route->uri());
                echo "<div class='status info'>";
                echo "<strong>" . $route->getName() . ":</strong> ";
                echo "<code>" . htmlspecialchars($fullUrl) . "</code>";
                echo "</div>";
            }
        }
        ?>
    </div>
    
    <div class="provider">
        <h2>Prueba de Configuración</h2>
        <?php if ($clientId && $clientSecret): ?>
            <a href="/auth/google" class="button" style="display: inline-block; background: #4285f4; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                Probar Login con Google
            </a>
        <?php else: ?>
            <div class="status error">
                No puedes probar el login hasta que configures las credenciales de Google OAuth.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="provider">
        <h2>Siguiente Paso</h2>
        <div class="status info">
            <p>Después de configurar las credenciales:</p>
            <ol>
                <li>Limpia la cache de configuración: <code>php artisan config:clear</code></li>
                <li>Reinicia tu servidor de desarrollo</li>
                <li>Vuelve a cargar esta página para verificar</li>
            </ol>
        </div>
    </div>
</body>
</html>