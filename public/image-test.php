<?php
// Advanced image debugging script for Laravel Dorasia

// Helper function to check if a file exists and display status
function checkFileExists($path, $displayPath = null) {
    $exists = file_exists($path);
    $displayPath = $displayPath ?? $path;
    $status = $exists ? 
        "<span style='color:green'>EXISTS</span>" : 
        "<span style='color:red'>NOT FOUND</span>";
    
    echo "<tr>";
    echo "<td>{$displayPath}</td>";
    echo "<td>{$status}</td>";
    echo "<td>" . ($exists ? filesize($path) . " bytes" : "N/A") . "</td>";
    echo "<td>" . ($exists ? date("Y-m-d H:i:s", filemtime($path)) : "N/A") . "</td>";
    echo "</tr>";
    return $exists;
}

// Function to display an image with path info
function displayImageWithInfo($path, $webPath, $altText, $width = 200) {
    $exists = file_exists($path);
    $statusColor = $exists ? "green" : "red";
    $status = $exists ? "File exists" : "File not found";
    
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px; display: inline-block; width: {$width}px; vertical-align: top;'>";
    echo "<p><strong>File path:</strong> {$path}<br>";
    echo "<strong>Web path:</strong> {$webPath}<br>";
    echo "<strong>Status:</strong> <span style='color:{$statusColor}'>{$status}</span></p>";
    
    if ($exists) {
        echo "<img src='{$webPath}' alt='{$altText}' style='max-width:100%; height:auto;'>";
    } else {
        echo "<div style='background:#f8d7da; color:#721c24; padding:10px; text-align:center;'>Image not found</div>";
    }
    
    echo "</div>";
}

// Set up page styling
echo "<!DOCTYPE html>
<html>
<head>
    <title>Dorasia Image Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .image-container { display: flex; flex-wrap: wrap; }
        .code { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body>";

echo "<h1>Dorasia Image Path Diagnostics</h1>";

// SECTION 1: System Information
echo "<div class='section'>";
echo "<h2>1. System Information</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>Server Software</td><td>" . $_SERVER['SERVER_SOFTWARE'] . "</td></tr>";
echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Document Root</td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>";
echo "<tr><td>Script Path</td><td>" . __FILE__ . "</td></tr>";
echo "<tr><td>Current Directory</td><td>" . __DIR__ . "</td></tr>";
echo "</table>";
echo "</div>";

// SECTION 2: Laravel Storage Configuration
echo "<div class='section'>";
echo "<h2>2. Storage Path Information</h2>";
echo "<table>";
echo "<tr><th>Path Type</th><th>Value</th></tr>";

// Application Root Path
$appRoot = dirname(__DIR__);
echo "<tr><td>Application Root</td><td>{$appRoot}</td></tr>";

// Public Path
$publicPath = __DIR__;
echo "<tr><td>Public Path</td><td>{$publicPath}</td></tr>";

// Storage Path
$storagePath = $appRoot . '/storage';
echo "<tr><td>Storage Path</td><td>{$storagePath}</td></tr>";

// Storage App Public Path
$storageAppPublicPath = $appRoot . '/storage/app/public';
echo "<tr><td>Storage App Public Path</td><td>{$storageAppPublicPath}</td></tr>";

// Storage Public Link
$storagePublicLink = $publicPath . '/storage';
echo "<tr><td>Storage Public Link</td><td>{$storagePublicLink}</td></tr>";

// Check if symbolic link exists
$isSymlink = is_link($storagePublicLink);
$symlinkTarget = $isSymlink ? readlink($storagePublicLink) : 'N/A';
echo "<tr><td>Is Symbolic Link?</td><td>" . ($isSymlink ? "Yes" : "No") . "</td></tr>";
echo "<tr><td>Symbolic Link Target</td><td>{$symlinkTarget}</td></tr>";
echo "</table>";
echo "</div>";

// SECTION 3: File Existence Checks
echo "<div class='section'>";
echo "<h2>3. File Existence Checks</h2>";
echo "<table>";
echo "<tr><th>Path</th><th>Status</th><th>Size</th><th>Last Modified</th></tr>";

// Check public directories
checkFileExists($publicPath . '/images');
checkFileExists($publicPath . '/images/categories');
checkFileExists($publicPath . '/backdrops');
checkFileExists($publicPath . '/posters');

// Check storage directories
checkFileExists($storageAppPublicPath);
checkFileExists($storageAppPublicPath . '/images');
checkFileExists($storageAppPublicPath . '/backdrops');
checkFileExists($storageAppPublicPath . '/posters');

// Check specific images in public
$backdropPublic = $publicPath . '/backdrops/backdrop-1.jpg';
checkFileExists($backdropPublic, 'public/backdrops/backdrop-1.jpg');

$posterPublic = $publicPath . '/posters/poster-1.jpg';
checkFileExists($posterPublic, 'public/posters/poster-1.jpg');

// Check specific images in storage
$backdropStorage = $storageAppPublicPath . '/backdrops/backdrop-1.jpg';
checkFileExists($backdropStorage, 'storage/app/public/backdrops/backdrop-1.jpg');

$posterStorage = $storageAppPublicPath . '/posters/poster-1.jpg';
checkFileExists($posterStorage, 'storage/app/public/posters/poster-1.jpg');

echo "</table>";
echo "</div>";

// SECTION 4: Image Display Tests
echo "<div class='section'>";
echo "<h2>4. Image Display Tests</h2>";

echo "<h3>4.1 Public Directory Images</h3>";
echo "<div class='image-container'>";

// Public backdrop image
displayImageWithInfo(
    $publicPath . '/backdrops/backdrop-1.jpg',
    '/backdrops/backdrop-1.jpg',
    'Backdrop 1 from public',
    300
);

// Public poster image
displayImageWithInfo(
    $publicPath . '/posters/poster-1.jpg',
    '/posters/poster-1.jpg',
    'Poster 1 from public',
    200
);

echo "</div>";

echo "<h3>4.2 Storage Directory Images</h3>";
echo "<div class='image-container'>";

// Storage backdrop image
displayImageWithInfo(
    $storageAppPublicPath . '/backdrops/backdrop-1.jpg',
    '/storage/backdrops/backdrop-1.jpg',
    'Backdrop 1 from storage',
    300
);

// Storage poster image
displayImageWithInfo(
    $storageAppPublicPath . '/posters/poster-1.jpg',
    '/storage/posters/poster-1.jpg',
    'Poster 1 from storage',
    200
);

echo "</div>";

echo "<h3>4.3 Different Path Formats for Database-stored Paths</h3>";
echo "<div class='image-container'>";

// Test with various path formats as they might appear in database
$samplePaths = [
    'storage/posters/poster-1.jpg',
    '/storage/posters/poster-1.jpg',
    'posters/poster-1.jpg',
    '/posters/poster-1.jpg'
];

foreach ($samplePaths as $dbPath) {
    // For paths starting with storage/
    if (strpos($dbPath, 'storage/') === 0) {
        $physicalPath = $appRoot . '/storage/app/public/' . substr($dbPath, 8);
        $webPath = '/' . $dbPath;
    }
    // For paths starting with /storage/
    elseif (strpos($dbPath, '/storage/') === 0) {
        $physicalPath = $appRoot . '/storage/app/public/' . substr($dbPath, 9);
        $webPath = $dbPath;
    }
    // For paths without storage/ prefix but in public
    else {
        $physicalPath = $publicPath . '/' . ltrim($dbPath, '/');
        $webPath = '/' . ltrim($dbPath, '/');
    }
    
    displayImageWithInfo(
        $physicalPath,
        $webPath,
        'DB format: ' . $dbPath,
        200
    );
}

echo "</div>";
echo "</div>";

// SECTION 5: Title Record Simulation
echo "<div class='section'>";
echo "<h2>5. Title Record Simulation</h2>";
echo "<p>Let's simulate how blade templates would render title records with different poster path formats:</p>";

// Create mock title objects with different poster path formats
$mockTitles = [
    (object)['id' => 1, 'title' => 'Title with storage/ path', 'poster' => 'storage/posters/poster-1.jpg', 'slug' => 'title-1'],
    (object)['id' => 2, 'title' => 'Title with /storage/ path', 'poster' => '/storage/posters/poster-1.jpg', 'slug' => 'title-2'],
    (object)['id' => 3, 'title' => 'Title with posters/ path', 'poster' => 'posters/poster-1.jpg', 'slug' => 'title-3'],
    (object)['id' => 4, 'title' => 'Title with /posters/ path', 'poster' => '/posters/poster-1.jpg', 'slug' => 'title-4'],
    (object)['id' => 5, 'title' => 'Title with empty path', 'poster' => '', 'slug' => 'title-5'],
    (object)['id' => 6, 'title' => 'Title with null path', 'poster' => null, 'slug' => 'title-6'],
];

echo "<div class='image-container'>";
foreach ($mockTitles as $title) {
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px; width: 220px;'>";
    echo "<h3>{$title->title}</h3>";
    echo "<p>DB poster path: <code>" . ($title->poster ?? 'NULL') . "</code></p>";
    
    // Render image as it would appear in blade template
    echo "<div style='position: relative; height: 300px; background-color: #444; overflow: hidden;'>";
    
    // This mimics the enhanced-card.blade.php logic
    if (!empty($title->poster)) {
        // Simulating asset() helper
        $assetPath = '/' . ltrim($title->poster, '/');
        echo "<img src='{$assetPath}' alt='{$title->title}' style='width: 100%; height: 100%; object-fit: cover;'>";
    } else {
        // Fallback image
        echo "<img src='/posters/placeholder.jpg' alt='{$title->title}' style='width: 100%; height: 100%; object-fit: cover;'>";
        echo "<div style='position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background-color: rgba(0,0,0,0.5); color: white; text-align: center; padding: 8px;'>";
        echo "<span>{$title->title}</span>";
        echo "</div>";
    }
    
    echo "</div>";
    echo "</div>";
}
echo "</div>";
echo "</div>";

// SECTION 6: Examining Blade Template
echo "<div class='section'>";
echo "<h2>6. Enhanced Card Component Analysis</h2>";
echo "<p>The current enhanced-card.blade.php component uses this code to display images:</p>";

echo "<pre class='code'>";
echo htmlspecialchars('@if(!empty($title->poster))
    <img src="{{ asset($title->poster) }}" alt="{{ $title->title }}" 
         class="absolute inset-0 h-full w-full object-cover">
@else
    <!-- Imagen de poster predeterminada -->
    <img src="{{ asset(\'posters/placeholder.jpg\') }}" alt="{{ $title->title }}" 
         class="absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 flex items-center justify-center bg-black/50 text-white text-center p-2">
        <span>{{ $title->title }}</span>
    </div>
@endif');
echo "</pre>";

echo "<p>Based on our testing, here are potential issues:</p>";
echo "<ol>";
echo "<li>Using <code>asset($title->poster)</code> might resolve differently depending on path format</li>";
echo "<li>The database uses 'storage/posters/poster-1.jpg' which may need a path correction</li>";
echo "<li>The symbolic link is correctly pointing to storage/app/public, but path resolution might be incorrect</li>";
echo "<li>The image might not be found due to case sensitivity in filenames</li>";
echo "</ol>";

echo "<p><strong>Recommended Fix:</strong></p>";
echo "<pre class='code'>";
echo htmlspecialchars('@if(!empty($title->poster))
    @php
        // Ensure paths starting with "storage/" are properly resolved
        $posterPath = Str::startsWith($title->poster, "storage/") 
            ? $title->poster                 // Keep as is for asset() helper
            : (Str::startsWith($title->poster, "/") 
                ? substr($title->poster, 1)  // Remove leading slash
                : $title->poster);           // Use as is
    @endphp
    <img src="{{ asset($posterPath) }}" alt="{{ $title->title }}" 
         class="absolute inset-0 h-full w-full object-cover">
@else
    <!-- Imagen de poster predeterminada -->
    <img src="{{ asset(\'posters/placeholder.jpg\') }}" alt="{{ $title->title }}" 
         class="absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 flex items-center justify-center bg-black/50 text-white text-center p-2">
        <span>{{ $title->title }}</span>
    </div>
@endif');
echo "</pre>";
echo "</div>";

// SECTION 7: Troubleshooting Recommendations
echo "<div class='section'>";
echo "<h2>7. Troubleshooting Tips</h2>";
echo "<ol>";
echo "<li><strong>Path normalization:</strong> Make sure all paths in the database are consistent (with or without leading slashes)</li>";
echo "<li><strong>Laravel asset() helper:</strong> asset() will prepend the APP_URL to the path, ensure this works with your path format</li>";
echo "<li><strong>Storage symlink:</strong> Run <code>php artisan storage:link</code> to ensure the symbolic link is properly created</li>";
echo "<li><strong>Check actual files:</strong> Ensure the image files actually exist in the expected locations</li>";
echo "<li><strong>Laravel URL helper:</strong> Consider using <code>Storage::url()</code> for storage paths instead of raw asset() calls</li>";
echo "<li><strong>Path debugging:</strong> Add temporary debug output in the blade template to see the actual resolved paths</li>";
echo "<li><strong>Clear cache:</strong> Run <code>php artisan cache:clear</code> and <code>php artisan view:clear</code> after making changes</li>";
echo "</ol>";
echo "</div>";

// SECTION 8: Quick Access Test
echo "<div class='section'>";
echo "<h2>8. Quick Access URL Test</h2>";
echo "<p>Try accessing these URLs directly to test if the web server can serve the images:</p>";

$testUrls = [
    '/posters/poster-1.jpg',
    '/storage/posters/poster-1.jpg',
    '/backdrops/backdrop-1.jpg',
    '/storage/backdrops/backdrop-1.jpg'
];

echo "<table>";
echo "<tr><th>Test URL</th><th>Direct Link</th></tr>";
foreach ($testUrls as $url) {
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $fullUrl = $baseUrl . $url;
    echo "<tr>";
    echo "<td>{$url}</td>";
    echo "<td><a href='{$url}' target='_blank'>Test Direct Access</a></td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

echo "</body></html>";
?>