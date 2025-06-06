<?php

// Test script to verify the web application is working
echo "🌐 Testing Dorasia Web Application...\n\n";

// Test database connection
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Count series
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM series");
    $seriesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Database connected: {$seriesCount} series found\n";
    
    // Get featured series
    $stmt = $pdo->query("SELECT title, vote_average, overview FROM series WHERE vote_average > 8 ORDER BY vote_average DESC LIMIT 1");
    $featured = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($featured) {
        echo "✅ Featured series: {$featured['title']} (⭐{$featured['vote_average']})\n";
    }
    
    // Count by genres
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM genres");
    $genresCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Genres available: {$genresCount}\n";
    
    // Count episodes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM episodes");
    $episodesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Episodes imported: {$episodesCount}\n";
    
    // Count people
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM people");
    $peopleCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ People (cast/crew): {$peopleCount}\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n🚀 Web application is ready!\n";
echo "📱 Visit: http://localhost:8001\n";
echo "🎭 Enjoy exploring Korean dramas!\n";
?>