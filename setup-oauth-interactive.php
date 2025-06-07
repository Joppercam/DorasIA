<?php

echo "\n🔧 CONFIGURADOR INTERACTIVO DE GOOGLE OAUTH\n";
echo "============================================\n\n";

echo "Paso 1: Ve a Google Cloud Console\n";
echo "URL: https://console.cloud.google.com/\n\n";

echo "Paso 2: Crea un nuevo proyecto\n";
echo "- Nombre: 'DORASIA K-Dramas'\n";
echo "- Selecciona el proyecto creado\n\n";

echo "Paso 3: Habilita las APIs necesarias\n";
echo "- Ve a 'APIs y servicios' > 'Biblioteca'\n";
echo "- Busca y habilita: 'Google+ API'\n";
echo "- Busca y habilita: 'People API'\n\n";

echo "Paso 4: Configura la pantalla de consentimiento\n";
echo "- Ve a 'APIs y servicios' > 'Pantalla de consentimiento de OAuth'\n";
echo "- Selecciona 'Externo'\n";
echo "- Completa SOLO los campos requeridos:\n";
echo "  * Nombre de la aplicación: DORASIA - K-Dramas\n";
echo "  * Correo de asistencia: jpablo.basualdo@gmail.com\n";
echo "  * Correo del desarrollador: jpablo.basualdo@gmail.com\n";
echo "- En 'Usuarios de prueba' agrega: jpablo.basualdo@gmail.com\n";
echo "- Guarda y continúa hasta completar\n\n";

echo "Paso 5: Crea las credenciales OAuth\n";
echo "- Ve a 'APIs y servicios' > 'Credenciales'\n";
echo "- Clic en 'CREAR CREDENCIALES' > 'ID de cliente de OAuth 2.0'\n";
echo "- Tipo: 'Aplicación web'\n";
echo "- Nombre: 'DORASIA Web Client'\n";
echo "- Orígenes autorizados: http://localhost:8000\n";
echo "- URI de redirección: http://localhost:8000/auth/google/callback\n";
echo "- CREAR\n\n";

echo "Paso 6: Copia las credenciales aquí\n";
echo "Después de crear, verás una ventana con las credenciales.\n\n";

// Solicitar credenciales
echo "Ingresa tu GOOGLE_CLIENT_ID: ";
$handle = fopen("php://stdin", "r");
$clientId = trim(fgets($handle));

echo "Ingresa tu GOOGLE_CLIENT_SECRET: ";
$clientSecret = trim(fgets($handle));
fclose($handle);

if (empty($clientId) || empty($clientSecret)) {
    echo "\n❌ Error: Ambas credenciales son requeridas.\n";
    echo "Vuelve a ejecutar el script cuando tengas las credenciales.\n\n";
    exit(1);
}

// Validar formato básico
if (!preg_match('/^[0-9]+-[a-zA-Z0-9]+\.apps\.googleusercontent\.com$/', $clientId)) {
    echo "\n⚠️  Advertencia: El CLIENT_ID no parece tener el formato correcto.\n";
    echo "Debería ser algo como: 123456789-abcdef.apps.googleusercontent.com\n";
    echo "¿Continuar de todos modos? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $continue = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($continue) !== 'y') {
        echo "Cancelado. Verifica las credenciales y vuelve a intentar.\n";
        exit(1);
    }
}

// Actualizar archivo .env
$envFile = '.env';
$envContent = file_get_contents($envFile);

$envContent = preg_replace('/^GOOGLE_CLIENT_ID=.*$/m', "GOOGLE_CLIENT_ID={$clientId}", $envContent);
$envContent = preg_replace('/^GOOGLE_CLIENT_SECRET=.*$/m', "GOOGLE_CLIENT_SECRET={$clientSecret}", $envContent);

file_put_contents($envFile, $envContent);

echo "\n✅ Credenciales guardadas exitosamente!\n";
echo "✅ Archivo .env actualizado\n\n";

echo "Paso 7: Limpiar cache y probar\n";
echo "Ejecuta estos comandos:\n";
echo "1. php artisan config:clear\n";
echo "2. php artisan serve\n";
echo "3. Ve a http://localhost:8000/login\n";
echo "4. Prueba el botón 'Continuar con Google'\n\n";

echo "🎉 ¡Configuración completada!\n";
echo "Si hay errores, revisa que las URLs en Google Console coincidan exactamente.\n\n";