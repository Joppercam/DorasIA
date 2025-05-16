<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;

$firstNews = News::first();

if ($firstNews) {
    echo "First news slug: " . $firstNews->slug . "\n";
    echo "URL would be: /news/" . $firstNews->slug . "\n";
} else {
    echo "No news found\n";
}