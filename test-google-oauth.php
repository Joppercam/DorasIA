<?php

require_once 'vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== VERIFICADOR DE CONFIGURACIÓN GOOGLE OAUTH ===\n\n";

// Verificar variables de entorno
$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? 'NO CONFIGURADO';
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? 'NO CONFIGURADO';
$redirectUri = $_ENV['GOOGLE_REDIRECT_URI'] ?? 'NO CONFIGURADO';

echo "📋 Configuración actual:\n";
echo "CLIENT_ID: " . ($clientId !== 'your-google-client-id' ? '✅ Configurado' : '❌ No configurado') . "\n";
echo "CLIENT_SECRET: " . ($clientSecret !== 'your-google-client-secret' ? '✅ Configurado' : '❌ No configurado') . "\n";
echo "REDIRECT_URI: {$redirectUri}\n\n";

if ($clientId === 'your-google-client-id' || $clientSecret === 'your-google-client-secret') {
    echo "❌ CREDENCIALES NO CONFIGURADAS\n";
    echo "1. Ve a Google Cloud Console\n";
    echo "2. Sigue setup-google-oauth.md\n";
    echo "3. Ejecuta: php configure-google.php\n\n";
} else {
    echo "✅ CREDENCIALES CONFIGURADAS CORRECTAMENTE\n";
    echo "🚀 Ya puedes probar Google OAuth en: http://localhost:8000/login\n\n";
}

echo "🔧 Para limpiar cache después de cambios:\n";
echo "php artisan config:clear && php artisan cache:clear\n";