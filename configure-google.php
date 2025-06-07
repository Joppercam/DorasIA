<?php

echo "=== CONFIGURADOR DE GOOGLE OAUTH PARA DORASIA ===\n\n";

echo "1. Ve a Google Cloud Console: https://console.cloud.google.com/\n";
echo "2. Sigue las instrucciones en setup-google-oauth.md\n";
echo "3. Cuando tengas las credenciales, ejecuta este script\n\n";

echo "Ingresa tu GOOGLE_CLIENT_ID: ";
$clientId = trim(fgets(STDIN));

echo "Ingresa tu GOOGLE_CLIENT_SECRET: ";
$clientSecret = trim(fgets(STDIN));

if (empty($clientId) || empty($clientSecret)) {
    echo "❌ Error: Ambos campos son requeridos\n";
    exit(1);
}

// Leer el archivo .env actual
$envFile = '.env';
$envContent = file_get_contents($envFile);

// Reemplazar las líneas de Google OAuth
$envContent = preg_replace('/^GOOGLE_CLIENT_ID=.*$/m', "GOOGLE_CLIENT_ID={$clientId}", $envContent);
$envContent = preg_replace('/^GOOGLE_CLIENT_SECRET=.*$/m', "GOOGLE_CLIENT_SECRET={$clientSecret}", $envContent);

// Escribir el archivo actualizado
file_put_contents($envFile, $envContent);

echo "\n✅ Credenciales configuradas exitosamente!\n";
echo "✅ Archivo .env actualizado\n\n";

echo "Próximos pasos:\n";
echo "1. php artisan config:clear\n";
echo "2. php artisan serve\n";
echo "3. Ve a http://localhost:8000/login\n";
echo "4. Prueba 'Continuar con Google'\n\n";

echo "🎉 ¡DORASIA está listo para Google OAuth!\n";