<?php

echo "🎬 ¡DORASIA - APLICACIÓN COMPLETADA! 🎬\n";
echo "=====================================\n\n";

// Database stats
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Series count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM series");
    $seriesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Episodes count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM episodes");
    $episodesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // People count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM people");
    $peopleCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Top rated series
    $stmt = $pdo->query("SELECT title, vote_average FROM series WHERE vote_average > 8 ORDER BY vote_average DESC LIMIT 5");
    $topSeries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Series by genre
    $stmt = $pdo->query("
        SELECT g.name, COUNT(*) as count 
        FROM genres g 
        JOIN series_genre sg ON g.id = sg.genre_id 
        GROUP BY g.name 
        ORDER BY count DESC 
        LIMIT 8
    ");
    $genreStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📊 ESTADÍSTICAS DE LA BASE DE DATOS:\n";
    echo "─────────────────────────────────────\n";
    echo "🎭 Series Coreanas: {$seriesCount}\n";
    echo "📺 Episodios: {$episodesCount}\n";
    echo "👥 Personas (Actores/Directores): {$peopleCount}\n\n";
    
    echo "🏆 TOP 5 SERIES MEJOR CALIFICADAS:\n";
    echo "──────────────────────────────────\n";
    foreach ($topSeries as $series) {
        echo "⭐ {$series['title']} ({$series['vote_average']}/10)\n";
    }
    echo "\n";
    
    echo "🎬 SERIES POR GÉNERO:\n";
    echo "───────────────────\n";
    foreach ($genreStats as $genre) {
        echo "📂 {$genre['name']}: {$genre['count']} series\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "🌐 ACCESO A LA APLICACIÓN:\n";
echo "─────────────────────────\n";
echo "🔗 URL: http://localhost:8001\n";
echo "💻 Puerto alternativo: http://127.0.0.1:8001\n\n";

echo "✨ CARACTERÍSTICAS IMPLEMENTADAS:\n";
echo "────────────────────────────────\n";
echo "🎯 Página principal tipo Netflix\n";
echo "🎭 Hero destacado con serie premium\n";
echo "🎠 Carruseles por categorías\n";
echo "📱 Diseño responsivo\n";
echo "🔍 Páginas de detalle completas\n";
echo "👥 Información de reparto\n";
echo "📺 Detalles de episodios\n";
echo "🖼️ Imágenes de alta calidad\n";
echo "🎨 Interfaz moderna y atractiva\n\n";

echo "🚀 ¡DISFRUTA EXPLORANDO K-DRAMAS!\n";
echo "================================\n";

?>