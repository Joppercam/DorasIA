<?php

use App\Models\Title;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Titles Database</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .movie { background-color: #e6f3ff; }
        .series { background-color: #fff3e6; }
    </style>
</head>
<body>
    <h1>Títulos en la Base de Datos</h1>
    
    <?php
    $movies = Title::where('type', 'movie')->get();
    $series = Title::where('type', 'series')->get();
    ?>
    
    <h2>Películas: <?= $movies->count() ?></h2>
    <?php if ($movies->count() > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Slug</th>
            <th>Año</th>
            <th>Enlaces</th>
        </tr>
        <?php foreach ($movies as $movie): ?>
        <tr class="movie">
            <td><?= $movie->id ?></td>
            <td><?= htmlspecialchars($movie->title) ?></td>
            <td><?= htmlspecialchars($movie->slug) ?></td>
            <td><?= $movie->release_year ?></td>
            <td>
                <a href="<?= route('titles.show', $movie->slug) ?>">Ver detalles</a> |
                <a href="<?= route('titles.watch', $movie->slug) ?>">Reproducir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>No hay películas en la base de datos.</p>
    <?php endif; ?>
    
    <h2>Series: <?= $series->count() ?></h2>
    <?php if ($series->count() > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Slug</th>
            <th>Año</th>
            <th>Temporadas</th>
            <th>Enlaces</th>
        </tr>
        <?php foreach ($series as $serie): ?>
        <tr class="series">
            <td><?= $serie->id ?></td>
            <td><?= htmlspecialchars($serie->title) ?></td>
            <td><?= htmlspecialchars($serie->slug) ?></td>
            <td><?= $serie->release_year ?></td>
            <td><?= $serie->seasons->count() ?></td>
            <td>
                <a href="<?= route('titles.show', $serie->slug) ?>">Ver detalles</a>
                <?php 
                if ($serie->seasons->count() > 0 && $serie->seasons->first()->episodes->count() > 0):
                    $firstSeason = $serie->seasons->first();
                    $firstEpisode = $firstSeason->episodes->first();
                ?>
                | <a href="<?= route('titles.watch', [$serie->slug, $firstSeason->number, $firstEpisode->number]) ?>">Ver S1E1</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>No hay series en la base de datos.</p>
    <?php endif; ?>
    
    <h2>Total de títulos: <?= Title::count() ?></h2>
    
    <hr>
    
    <h3>Importar Contenido</h3>
    <p>Para importar películas y series, ejecuta estos comandos:</p>
    <ul>
        <li><code>php artisan dorasia:import-romantic-dramas --pages=5</code> - Importa dramas románticos</li>
        <li><code>./triple-import.sh kr preserve 10 2</code> - Importa 10 páginas de dramas coreanos</li>
        <li><code>./mega-import.sh -m preserve -p 5 -c 2</code> - Importa contenido de varios países</li>
    </ul>
</body>
</html>