<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\User;
use App\Models\Title;
use App\Models\Watchlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Start session
$app->make('session')->start();

// Simular login del usuario
$user = User::where('email', 'jpablo.basualdo@gmail.com')->first();
if (!$user) {
    die("Usuario no encontrado\n");
}

Auth::login($user);

echo "Usuario logueado: {$user->email}\n";
echo "ID de usuario: {$user->id}\n";

// Obtener perfil activo
$profile = $user->getActiveProfile();
if ($profile) {
    echo "Perfil activo: {$profile->name} (ID: {$profile->id})\n";
} else {
    echo "No hay perfil activo\n";
}

// Obtener un título cualquiera
$title = Title::first();
if ($title) {
    echo "\nTítulo de prueba: {$title->title} (ID: {$title->id})\n";
    
    // Verificar si está en la watchlist
    if ($profile) {
        $inWatchlist = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $title->id)
            ->exists();
        
        echo "¿Está en la watchlist?: " . ($inWatchlist ? 'Sí' : 'No') . "\n";
        
        // Probar añadir/quitar de la watchlist
        if ($inWatchlist) {
            $watchlistItem = Watchlist::where('profile_id', $profile->id)
                ->where('title_id', $title->id)
                ->first();
            $watchlistItem->delete();
            echo "Título eliminado de la watchlist\n";
        } else {
            Watchlist::create([
                'profile_id' => $profile->id,
                'title_id' => $title->id,
                'category' => 'default',
                'position' => 1,
                'priority' => 'medium'
            ]);
            echo "Título añadido a la watchlist\n";
        }
        
        // Verificar de nuevo
        $inWatchlist = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $title->id)
            ->exists();
        
        echo "¿Está en la watchlist ahora?: " . ($inWatchlist ? 'Sí' : 'No') . "\n";
    }
}

// Verificar la estructura de la tabla watchlists
echo "\n--- Estructura de la tabla watchlists ---\n";
$schema = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='watchlists'");
if (!empty($schema)) {
    echo $schema[0]->sql . "\n";
}

// Verificar registros en watchlists
echo "\n--- Registros en watchlists ---\n";
$watchlistItems = Watchlist::all();
foreach ($watchlistItems as $item) {
    echo "ID: {$item->id}, Profile: {$item->profile_id}, Title: {$item->title_id}, Category: {$item->category}\n";
}