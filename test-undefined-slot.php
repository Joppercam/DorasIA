<?php

// Check each Blade component for proper slot usage
$componentsDir = __DIR__ . '/resources/views/components/';
$files = glob($componentsDir . '*.blade.php');

echo "Checking all Blade components for potential slot issues...\n\n";

foreach ($files as $file) {
    $content = file_get_contents($file);
    $filename = basename($file);
    
    // Check if component uses $slot
    if (strpos($content, '$slot') !== false) {
        // Check if it's a simple component that should receive content
        if (!strpos($content, '@props') || strpos($content, '@props([])') !== false) {
            echo "POTENTIAL ISSUE in $filename:\n";
            echo "  - Uses \$slot but might not be receiving content properly\n";
            
            // Show the line with $slot
            $lines = explode("\n", $content);
            foreach ($lines as $lineNo => $line) {
                if (strpos($line, '$slot') !== false) {
                    echo "  - Line " . ($lineNo + 1) . ": " . trim($line) . "\n";
                }
            }
            echo "\n";
        }
    }
}

// Also check for components that might be used incorrectly
echo "\nChecking for self-closing component tags that should have content...\n";

$viewsDir = __DIR__ . '/resources/views/';
$viewFiles = glob($viewsDir . '**/*.blade.php');

foreach ($viewFiles as $file) {
    $content = file_get_contents($file);
    $filename = str_replace(__DIR__ . '/', '', $file);
    
    // Look for self-closing component tags that typically need content
    $pattern = '/<x-(nav-link|dropdown-link|primary-button|secondary-button|danger-button|responsive-nav-link)\s[^>]*\/>/';
    
    if (preg_match_all($pattern, $content, $matches)) {
        echo "\nPOTENTIAL ISSUE in $filename:\n";
        echo "  - Found self-closing component tags that typically need content:\n";
        foreach ($matches[0] as $match) {
            echo "    - " . $match . "\n";
        }
    }
}