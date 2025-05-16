<?php

// This script checks all blade files for potential slot issues

function checkBladeFile($file) {
    $content = file_get_contents($file);
    $relativePath = str_replace('/Users/juanpablobasualdo/Desktop/Dorasia/dorasia-new/', '', $file);
    $issues = [];
    
    // Check for components using slot without proper guard
    if (strpos($content, '{{ $slot }}') !== false) {
        // Check if it has @props declaration
        if (strpos($content, '@props') === false) {
            $issues[] = "Uses \$slot without @props declaration";
        }
        
        // Check if slot usage is guarded
        if (strpos($content, '{{ $slot }}') !== false && strpos($content, '$slot ??') === false) {
            $issues[] = "Uses \$slot without null coalescing operator";
        }
    }
    
    // Check for self-closing tags that might need content
    $selfClosingPattern = '/<x-(\w+)([^>]*?)\/>/';
    if (preg_match_all($selfClosingPattern, $content, $matches)) {
        foreach ($matches[1] as $componentName) {
            // These components typically need content
            $needsContent = ['nav-link', 'button', 'dropdown-link', 'primary-button', 'secondary-button', 'danger-button'];
            if (in_array($componentName, $needsContent)) {
                $issues[] = "Self-closing <x-$componentName /> might need content";
            }
        }
    }
    
    if (!empty($issues)) {
        echo "\n$relativePath:\n";
        foreach ($issues as $issue) {
            echo "  - $issue\n";
        }
    }
}

echo "Checking all Blade files for potential slot issues...\n";

// Check all blade files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('/Users/juanpablobasualdo/Desktop/Dorasia/dorasia-new/resources/views/')
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        checkBladeFile($file->getPathname());
    }
}

echo "\nDone checking files.\n";