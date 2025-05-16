<?php

use App\Models\Title;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$title = Title::first();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Watch Route</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .link { margin: 10px 0; padding: 10px; background: #f4f4f4; }
        code { background: #ddd; padding: 2px 5px; }
    </style>
</head>
<body>
    <h1>Test Watch Route</h1>
    
    <h2>Ejemplo con: <?= htmlspecialchars($title->title) ?></h2>
    
    <div class="link">
        <p><strong>Ruta generada:</strong></p>
        <code><?= route('titles.watch', $title->slug) ?></code>
        <br><br>
        <a href="<?= route('titles.watch', $title->slug) ?>" class="btn">
            Probar Reproducir
        </a>
    </div>
    
    <div class="link">
        <p><strong>Enlaces manuales:</strong></p>
        <ul>
            <li><a href="/watch/<?= htmlspecialchars($title->slug) ?>">Sin autenticación (debería redirigir)</a></li>
            <li><a href="<?= route('titles.show', $title->slug) ?>">Ver página de detalles</a></li>
        </ul>
    </div>
    
    <h3>Información del título:</h3>
    <pre><?php
    echo json_encode([
        'id' => $title->id,
        'title' => $title->title,
        'slug' => $title->slug,
        'type' => $title->type,
    ], JSON_PRETTY_PRINT);
    ?></pre>
    
    <h3>Estado de autenticación:</h3>
    <pre><?php
    if (auth()->check()) {
        $user = auth()->user();
        $profile = $user->getActiveProfile();
        echo json_encode([
            'user' => $user->email,
            'active_profile' => $profile ? $profile->name : 'NO PROFILE',
        ], JSON_PRETTY_PRINT);
    } else {
        echo "No autenticado";
    }
    ?></pre>
</body>
</html>