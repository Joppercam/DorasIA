<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 VERIFICACIÓN DE CONFIGURACIÓN GOOGLE OAUTH\n";
echo "============================================\n\n";

$clientId = config('services.google.client_id');
$clientSecret = config('services.google.client_secret');
$redirectUri = config('services.google.redirect');

echo "📋 Configuración Laravel:\n";
echo "CLIENT_ID: " . ($clientId ? '✅ ' . substr($clientId, 0, 20) . '...' : '❌ NO CONFIGURADO') . "\n";
echo "CLIENT_SECRET: " . ($clientSecret ? '✅ ' . substr($clientSecret, 0, 10) . '...' : '❌ NO CONFIGURADO') . "\n";
echo "REDIRECT_URI: " . ($redirectUri ?: 'NO CONFIGURADO') . "\n\n";

if ($clientId && $clientSecret && $redirectUri) {
    echo "✅ CONFIGURACIÓN CORRECTA\n";
    echo "🚀 OAuth está listo para usar!\n\n";
    
    echo "🧪 URLs de prueba:\n";
    echo "- Login: http://localhost:8000/login\n";
    echo "- Google OAuth: http://localhost:8000/auth/google\n";
    echo "- Callback: http://localhost:8000/auth/google/callback\n\n";
    
    echo "📱 Instrucciones para probar:\n";
    echo "1. Ve a http://localhost:8000/login\n";
    echo "2. Haz clic en 'Continuar con Google'\n";
    echo "3. Autoriza la aplicación con tu cuenta Google\n";
    echo "4. Deberías ser redirigido de vuelta autenticado\n\n";
} else {
    echo "❌ CONFIGURACIÓN INCOMPLETA\n";
    echo "Verifica el archivo config/services.php\n\n";
}

echo "🎯 Estado del servidor: ";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000');
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ Servidor funcionando\n";
} else {
    echo "❌ Servidor no disponible (código: $httpCode)\n";
    echo "Ejecuta: php artisan serve\n";
}

echo "\n🎉 ¡Configuración verificada!\n";