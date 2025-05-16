<?php

use App\Models\News;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$news = News::orderBy('created_at', 'desc')->take(5)->get();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test News Links</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .news-item { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; }
        .link { color: blue; text-decoration: underline; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Test News Links</h1>
    
    <?php foreach ($news as $item): ?>
    <div class="news-item">
        <h3><?= htmlspecialchars($item->title) ?></h3>
        <p>Slug: <code><?= htmlspecialchars($item->slug) ?></code></p>
        
        <!-- Direct link -->
        <p>
            Direct Link: 
            <a href="/news/<?= htmlspecialchars($item->slug) ?>" class="link">
                /news/<?= htmlspecialchars($item->slug) ?>
            </a>
        </p>
        
        <!-- Route-based link -->
        <p>
            Route Link: 
            <a href="<?= route('news.show', $item->slug) ?>" class="link">
                <?= route('news.show', $item->slug) ?>
            </a>
        </p>
        
        <!-- Test button -->
        <button onclick="testLink('<?= route('news.show', $item->slug) ?>', this)">
            Test This Link
        </button>
        <span class="result"></span>
    </div>
    <?php endforeach; ?>
    
    <script>
    function testLink(url, button) {
        const resultSpan = button.nextElementSibling;
        resultSpan.textContent = ' Testing...';
        
        fetch(url, { method: 'HEAD' })
            .then(response => {
                if (response.ok) {
                    resultSpan.className = 'result success';
                    resultSpan.textContent = ' ✓ Link works! (Status: ' + response.status + ')';
                } else {
                    resultSpan.className = 'result error';
                    resultSpan.textContent = ' ✗ Error: ' + response.status;
                }
            })
            .catch(error => {
                resultSpan.className = 'result error';
                resultSpan.textContent = ' ✗ Network error: ' + error.message;
            });
    }
    </script>
</body>
</html>