<?php

echo "🔍 VERIFICADOR DE GOOGLE OAUTH PARA DORASIA\n";
echo "==========================================\n\n";

// Cargar configuración
$env = parse_ini_file('.env');

$clientId = $env['GOOGLE_CLIENT_ID'] ?? 'NO ENCONTRADO';
$clientSecret = $env['GOOGLE_CLIENT_SECRET'] ?? 'NO ENCONTRADO';
$redirectUri = str_replace('"${APP_URL}"', $env['APP_URL'] ?? 'http://localhost', $env['GOOGLE_REDIRECT_URI'] ?? 'NO ENCONTRADO');

echo "📋 Configuración detectada:\n";
echo "CLIENT_ID: ";
if ($clientId === 'your-google-client-id' || $clientId === 'NO ENCONTRADO') {
    echo "❌ NO CONFIGURADO\n";
} else {
    echo "✅ " . substr($clientId, 0, 20) . "...\n";
}

echo "CLIENT_SECRET: ";
if ($clientSecret === 'your-google-client-secret' || $clientSecret === 'NO ENCONTRADO') {
    echo "❌ NO CONFIGURADO\n";
} else {
    echo "✅ " . substr($clientSecret, 0, 10) . "...\n";
}

echo "REDIRECT_URI: {$redirectUri}\n\n";

// Verificar formato del CLIENT_ID
if ($clientId !== 'your-google-client-id' && $clientId !== 'NO ENCONTRADO') {
    if (preg_match('/^[0-9]+-[a-zA-Z0-9]+\.apps\.googleusercontent\.com$/', $clientId)) {
        echo "✅ Formato de CLIENT_ID correcto\n";
    } else {
        echo "⚠️  Formato de CLIENT_ID inusual (puede estar bien)\n";
    }
}

// Estado general
if ($clientId === 'your-google-client-id' || $clientSecret === 'your-google-client-secret') {
    echo "\n❌ CONFIGURACIÓN INCOMPLETA\n";
    echo "Pasos siguientes:\n";
    echo "1. Ejecuta: php setup-oauth-interactive.php\n";
    echo "2. Sigue las instrucciones paso a paso\n";
    echo "3. Obtén credenciales de Google Cloud Console\n\n";
} else {
    echo "\n✅ CONFIGURACIÓN DETECTADA\n";
    echo "Pasos siguientes:\n";
    echo "1. php artisan config:clear\n";
    echo "2. php artisan serve\n";
    echo "3. Probar en: http://localhost:8000/login\n\n";
    
    echo "🧪 URLs que deben estar en Google Console:\n";
    echo "Origen autorizado: " . ($env['APP_URL'] ?? 'http://localhost:8000') . "\n";
    echo "URI de redirección: {$redirectUri}\n\n";
}

echo "📚 Documentación disponible:\n";
echo "- setup-google-oauth.md (guía detallada)\n";
echo "- GOOGLE_OAUTH_SETUP.md (documentación completa)\n\n";