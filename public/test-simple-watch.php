<?php

use App\Models\Title;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$kernel->bootstrap();

// Get a movie title
$movie = Title::where('type', 'movie')->first();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Simple Watch</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Test de Reproducción Simple</h1>
    
    <?php if (!Auth::check()): ?>
        <p style="color: red;">No estás autenticado. Por favor <a href="/login">inicia sesión</a> primero.</p>
    <?php else: ?>
        <p style="color: green;">Usuario autenticado: <?= Auth::user()->email ?></p>
        
        <?php 
        $profile = Auth::user()->getActiveProfile();
        if (!$profile): 
        ?>
            <p style="color: red;">No tienes un perfil activo. <a href="/user-profiles/create">Crea uno</a>.</p>
        <?php else: ?>
            <p style="color: green;">Perfil activo: <?= $profile->name ?></p>
            
            <?php if ($movie): ?>
                <h2>Película: <?= htmlspecialchars($movie->title) ?></h2>
                <p>Tipo: <?= $movie->type ?></p>
                <p>Slug: <?= $movie->slug ?></p>
                
                <h3>Enlaces de prueba:</h3>
                <ol>
                    <li>
                        <a href="/watch/<?= $movie->slug ?>">
                            Link directo: /watch/<?= $movie->slug ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route('titles.watch', $movie->slug) ?>">
                            Ruta generada: <?= route('titles.watch', $movie->slug) ?>
                        </a>
                    </li>
                </ol>
                
                <h3>Debug Info:</h3>
                <pre><?php
                echo "Ruta esperada: /watch/{$movie->slug}\n";
                echo "Ruta generada: " . route('titles.watch', $movie->slug) . "\n";
                echo "Middleware esperado: auth, has.active.profile\n";
                ?></pre>
            <?php else: ?>
                <p style="color: red;">No se encontraron películas en la base de datos.</p>
            <?php endif; ?>
            
        <?php endif; ?>
    <?php endif; ?>
    
    <hr>
    <h3>Test con Series:</h3>
    <?php
    $series = Title::where('type', 'series')->first();
    if ($series && $series->seasons->count() > 0):
        $firstSeason = $series->seasons->first();
        if ($firstSeason->episodes->count() > 0):
            $firstEpisode = $firstSeason->episodes->first();
    ?>
        <p>Serie: <?= htmlspecialchars($series->title) ?></p>
        <p>Temporada 1, Episodio 1</p>
        <a href="<?= route('titles.watch', [$series->slug, $firstSeason->number, $firstEpisode->number]) ?>">
            Ver primer episodio
        </a>
    <?php 
        endif;
    endif; 
    ?>
</body>
</html>