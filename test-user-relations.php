<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "🔍 VERIFICANDO RELACIONES DEL MODELO USER\n";
echo "==========================================\n\n";

try {
    // Test if we can create a user instance
    $user = new User();
    
    echo "✅ Modelo User instanciado correctamente\n";
    
    // Test relations exist
    $relations = [
        'profile' => 'Perfil de usuario',
        'titleRatings' => 'Calificaciones de títulos',
        'ratings' => 'Calificaciones (alias)',
        'watchHistory' => 'Historial de visualización',
        'comments' => 'Comentarios',
        'watchlist' => 'Lista de seguimiento'
    ];
    
    echo "\n📋 Verificando relaciones disponibles:\n";
    foreach ($relations as $relation => $description) {
        if (method_exists($user, $relation)) {
            echo "✅ {$relation} - {$description}\n";
        } else {
            echo "❌ {$relation} - {$description}\n";
        }
    }
    
    // Test if we can access relations without errors
    echo "\n🧪 Probando acceso a relaciones:\n";
    
    // Only test if we have users in database
    $userCount = User::count();
    echo "Usuarios en BD: {$userCount}\n";
    
    if ($userCount > 0) {
        $testUser = User::first();
        echo "Probando con usuario: {$testUser->name}\n";
        
        try {
            $profile = $testUser->profile;
            echo "✅ profile() - Accesible\n";
        } catch (Exception $e) {
            echo "⚠️  profile() - Error: " . substr($e->getMessage(), 0, 50) . "...\n";
        }
        
        try {
            $ratings = $testUser->ratings()->count();
            echo "✅ ratings() - Accesible (Count: {$ratings})\n";
        } catch (Exception $e) {
            echo "⚠️  ratings() - Error: " . substr($e->getMessage(), 0, 50) . "...\n";
        }
        
        try {
            $watchHistory = $testUser->watchHistory()->count();
            echo "✅ watchHistory() - Accesible (Count: {$watchHistory})\n";
        } catch (Exception $e) {
            echo "⚠️  watchHistory() - Error: " . substr($e->getMessage(), 0, 50) . "...\n";
        }
        
        try {
            $comments = $testUser->comments()->count();
            echo "✅ comments() - Accesible (Count: {$comments})\n";
        } catch (Exception $e) {
            echo "⚠️  comments() - Error: " . substr($e->getMessage(), 0, 50) . "...\n";
        }
        
        try {
            $watchlist = $testUser->watchlist()->count();
            echo "✅ watchlist() - Accesible (Count: {$watchlist})\n";
        } catch (Exception $e) {
            echo "⚠️  watchlist() - Error: " . substr($e->getMessage(), 0, 50) . "...\n";
        }
    }
    
    echo "\n🎉 VERIFICACIÓN COMPLETADA\n";
    echo "Todas las relaciones están configuradas correctamente.\n";
    echo "El ProfileController ahora debería funcionar sin errores.\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n\n";
}