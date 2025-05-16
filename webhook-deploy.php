<?php
/**
 * GitHub Webhook Deployment Script
 * ================================
 * Coloca este archivo en tu hosting y configura el webhook en GitHub
 */

// Configuración
$secret = 'tu_webhook_secret_aqui'; // Cambiar por un secreto seguro
$branch = 'production';
$deployment_path = '/home/tu_usuario/public_html';
$log_file = '/home/tu_usuario/webhook.log';

// Función para escribir logs
function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Verificar que es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

// Obtener el payload
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

// Verificar la firma del webhook
$expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
if (!hash_equals($signature, $expected_signature)) {
    writeLog('Invalid webhook signature');
    http_response_code(403);
    die('Forbidden');
}

// Decodificar el payload
$data = json_decode($payload, true);

// Verificar que es un push al branch correcto
if ($data['ref'] !== "refs/heads/$branch") {
    writeLog("Push to different branch: " . $data['ref']);
    die('Not production branch');
}

writeLog("Webhook received for branch: $branch");

// Cambiar al directorio del proyecto
chdir($deployment_path);

// Ejecutar el script de sincronización
$output = [];
$return_var = 0;
exec('./sync-production.sh 2>&1', $output, $return_var);

// Log the output
foreach ($output as $line) {
    writeLog($line);
}

if ($return_var === 0) {
    writeLog("Deployment successful");
    echo "Deployment successful";
} else {
    writeLog("Deployment failed with code: $return_var");
    http_response_code(500);
    echo "Deployment failed";
}
?>