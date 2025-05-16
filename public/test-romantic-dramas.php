<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Title;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Doramas Románticos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #141414; color: #fff; }
        .title-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .title-card { background: #222; padding: 10px; border-radius: 8px; }
        .title-card img { width: 100%; height: 300px; object-fit: cover; border-radius: 4px; }
        .title-card h3 { margin: 10px 0 5px 0; font-size: 16px; }
        .title-card p { margin: 0; color: #888; font-size: 14px; }
        .section { margin: 40px 0; }
        .section h2 { color: #e50914; margin-bottom: 20px; }
        .error { background: #ff0000; color: #fff; padding: 10px; margin: 10px 0; }
        .success { background: #00ff00; color: #000; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test de Sección Doramas Románticos</h1>
    
    <?php
    try {
        // Obtener títulos románticos
        $romanticTitles = Title::romantic()->take(12)->get();
        $totalCount = Title::romantic()->count();
        
        echo "<div class='success'>✓ Conexión a base de datos exitosa</div>";
        echo "<div class='success'>✓ Total de títulos románticos: $totalCount</div>";
        
        // Títulos destacados
        $featuredTitles = Title::romantic()
            ->where('is_featured', true)
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get();
            
        if ($featuredTitles->isEmpty()) {
            $featuredTitles = Title::romantic()
                ->orderBy('popularity', 'desc')
                ->take(6)
                ->get();
        }
        
        echo "<div class='success'>✓ Títulos destacados: " . $featuredTitles->count() . "</div>";
        ?>
        
        <div class="section">
            <h2>Títulos Destacados</h2>
            <div class="title-grid">
                <?php foreach ($featuredTitles as $title): ?>
                    <div class="title-card">
                        <?php if ($title->poster_url): ?>
                            <img src="<?php echo $title->poster_url; ?>" alt="<?php echo htmlspecialchars($title->title); ?>" loading="lazy"
                                 onerror="this.src='/posters/placeholder.jpg'">
                        <?php else: ?>
                            <img src="/posters/placeholder.jpg" alt="<?php echo htmlspecialchars($title->title); ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($title->title); ?></h3>
                        <p>Popularidad: <?php echo number_format($title->popularity, 2); ?></p>
                        <p>Géneros: <?php echo $title->genres->pluck('name')->join(', '); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php
        // K-Dramas populares
        $popularKdramas = Title::romantic()
            ->korean()
            ->orderBy('popularity', 'desc')
            ->take(12)
            ->get();
        ?>
        
        <div class="section">
            <h2>K-Dramas Románticos Populares (<?php echo $popularKdramas->count(); ?>)</h2>
            <div class="title-grid">
                <?php foreach ($popularKdramas->take(6) as $title): ?>
                    <div class="title-card">
                        <?php if ($title->poster_url): ?>
                            <img src="<?php echo $title->poster_url; ?>" alt="<?php echo htmlspecialchars($title->title); ?>" loading="lazy"
                                 onerror="this.src='/posters/placeholder.jpg'">
                        <?php else: ?>
                            <img src="/posters/placeholder.jpg" alt="<?php echo htmlspecialchars($title->title); ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($title->title); ?></h3>
                        <p>País: Corea</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php
        // J-Dramas populares
        $popularJdramas = Title::romantic()
            ->japanese()
            ->orderBy('popularity', 'desc')
            ->take(12)
            ->get();
        ?>
        
        <div class="section">
            <h2>J-Dramas Románticos Populares (<?php echo $popularJdramas->count(); ?>)</h2>
            <div class="title-grid">
                <?php foreach ($popularJdramas->take(6) as $title): ?>
                    <div class="title-card">
                        <?php if ($title->poster_url): ?>
                            <img src="<?php echo $title->poster_url; ?>" alt="<?php echo htmlspecialchars($title->title); ?>" loading="lazy"
                                 onerror="this.src='/posters/placeholder.jpg'">
                        <?php else: ?>
                            <img src="/posters/placeholder.jpg" alt="<?php echo htmlspecialchars($title->title); ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($title->title); ?></h3>
                        <p>País: Japón</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    <?php } catch (Exception $e) { ?>
        <div class="error">Error: <?php echo $e->getMessage(); ?></div>
    <?php } ?>
    
    <div style="margin-top: 40px; padding: 20px; background: #333;">
        <h3>Información de Debug</h3>
        <p>Servidor: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
        <p>PHP: <?php echo phpversion(); ?></p>
        <p>Laravel: <?php echo app()->version(); ?></p>
        <p>Configuración Cache: <?php echo config('cache.default'); ?></p>
    </div>
</body>
</html>