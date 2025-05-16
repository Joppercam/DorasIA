<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\User;
use App\Models\Profile;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::all();

foreach ($users as $user) {
    echo "Usuario: {$user->email}\n";
    echo "ID: {$user->id}\n";
    echo "Perfiles: " . $user->profiles->count() . "\n";
    
    if ($user->profiles->count() > 0) {
        foreach ($user->profiles as $profile) {
            echo "  - Perfil: {$profile->name} (ID: {$profile->id})\n";
            echo "    Activo: " . ($profile->is_active ? 'Sí' : 'No') . "\n";
        }
    }
    
    $activeProfile = $user->getActiveProfile();
    if ($activeProfile) {
        echo "Perfil activo: {$activeProfile->name} (ID: {$activeProfile->id})\n";
    } else {
        echo "No hay perfil activo\n";
    }
    
    echo "\n";
}

// También verificar la relación con watchlist
$profile = Profile::first();
if ($profile) {
    echo "Primer perfil: {$profile->name}\n";
    echo "Títulos en watchlist: " . $profile->watchlist()->count() . "\n";
}