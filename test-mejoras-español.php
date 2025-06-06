<?php

echo "🇪🇸 PROBANDO MEJORAS EN ESPAÑOL 🇪🇸\n";
echo "==================================\n\n";

// Test database with Spanish context
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Romance series count
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM series 
        WHERE title LIKE '%love%' 
           OR title LIKE '%romance%' 
           OR title LIKE '%marry%' 
           OR title LIKE '%wedding%' 
           OR title LIKE '%heart%'
           OR overview LIKE '%romance%'
           OR overview LIKE '%love%'
           OR overview LIKE '%romantic%'
    ");
    $romanceCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Mystery series count
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM series s
        JOIN series_genre sg ON s.id = sg.series_id
        JOIN genres g ON sg.genre_id = g.id
        WHERE g.name = 'Mystery'
    ");
    $mysteryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Historical series count
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM series 
        WHERE title LIKE '%king%' 
           OR title LIKE '%queen%' 
           OR title LIKE '%emperor%' 
           OR title LIKE '%prince%' 
           OR title LIKE '%dynasty%'
           OR overview LIKE '%historical%'
           OR overview LIKE '%ancient%'
           OR overview LIKE '%palace%'
    ");
    $historicalCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Top romance series
    $stmt = $pdo->query("
        SELECT title, vote_average 
        FROM series 
        WHERE (title LIKE '%love%' OR title LIKE '%romance%' OR title LIKE '%marry%')
          AND vote_average > 7
        ORDER BY vote_average DESC 
        LIMIT 5
    ");
    $topRomance = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "💕 CATEGORÍAS MEJORADAS:\n";
    echo "──────────────────────\n";
    echo "💖 Romance: {$romanceCount} series\n";
    echo "🔍 Misterio: {$mysteryCount} series\n";
    echo "👑 Históricos: {$historicalCount} series\n\n";
    
    echo "🏆 TOP ROMANCE K-DRAMAS:\n";
    echo "─────────────────────\n";
    foreach ($topRomance as $series) {
        echo "💕 {$series['title']} (⭐{$series['vote_average']})\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "✨ MEJORAS IMPLEMENTADAS:\n";
echo "──────────────────────\n";
echo "🇪🇸 Interfaz 100% en español\n";
echo "🎭 Nuevas categorías: Misterio y Históricos\n";
echo "📱 Navbar mejorado con emojis\n";
echo "🎨 Efectos hover mejorados\n";
echo "📊 Estados traducidos (Finalizada/En Emisión)\n";
echo "🌍 Idiomas traducidos (Coreano)\n";
echo "🇰🇷 País localizado (Corea del Sur)\n";
echo "📅 Fechas en formato español\n";
echo "⭐ Mejor ordenamiento por calificación\n\n";

echo "🌐 ACCEDE A VER LAS MEJORAS:\n";
echo "─────────────────────────\n";
echo "🔗 http://localhost:8001\n";
echo "✨ ¡Disfruta la experiencia mejorada!\n";

?>