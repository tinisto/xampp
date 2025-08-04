<?php
/**
 * Asset Minification Build Script
 * Minifies CSS and JavaScript files for production
 */

require_once __DIR__ . '/../includes/utils/minifier.php';

echo "11klassniki Asset Minifier\n";
echo "=========================\n\n";

// Define directories
$cssDir = __DIR__ . '/../css';
$jsDir = __DIR__ . '/../js';
$buildDir = __DIR__ . '/../build/assets';

// Create build directory if it doesn't exist
if (!is_dir($buildDir)) {
    mkdir($buildDir, 0755, true);
}

// CSS files to minify (excluding already minified files)
$cssFiles = [
    $cssDir . '/styles.css',
    $cssDir . '/unified-styles.css',
    $cssDir . '/buttons-styles.css',
    $cssDir . '/post-styles.css',
    $cssDir . '/dashboard/styles.css',
    $cssDir . '/dashboard/dashboard.css',
    $cssDir . '/authorization.css',
    $cssDir . '/theme-variables.css',
    $cssDir . '/site-logo.css'
];

// Filter existing files
$existingCssFiles = array_filter($cssFiles, 'file_exists');

echo "Minifying CSS files...\n";
echo "Found " . count($existingCssFiles) . " CSS files to process.\n\n";

$totalSavings = 0;
$processedFiles = 0;

foreach ($existingCssFiles as $cssFile) {
    echo "Processing: " . basename($cssFile) . "\n";
    
    $minifiedPath = str_replace('.css', '.min.css', $cssFile);
    $success = Minifier::minifyCSS_File($cssFile, $minifiedPath);
    
    if ($success) {
        $stats = Minifier::getSizeReduction($cssFile, $minifiedPath);
        echo "  ✅ Minified: {$stats['original_size_formatted']} → {$stats['minified_size_formatted']} ({$stats['reduction_percentage']}% reduction)\n";
        $totalSavings += $stats['reduction_bytes'];
        $processedFiles++;
    } else {
        echo "  ❌ Failed to minify\n";
    }
    echo "\n";
}

// Create combined CSS bundle
echo "Creating combined CSS bundle...\n";
$combinedCssPath = $buildDir . '/bundle.min.css';
$success = Minifier::combineCSS($existingCssFiles, $combinedCssPath);

if ($success) {
    echo "✅ Combined CSS bundle created: " . basename($combinedCssPath) . "\n";
} else {
    echo "❌ Failed to create combined CSS bundle\n";
}
echo "\n";

// Find and minify JavaScript files
$jsFiles = glob($jsDir . '/*.js');
$jsFiles = array_filter($jsFiles, function($file) {
    return strpos(basename($file), '.min.') === false; // Skip already minified
});

echo "Minifying JavaScript files...\n";
echo "Found " . count($jsFiles) . " JS files to process.\n\n";

foreach ($jsFiles as $jsFile) {
    echo "Processing: " . basename($jsFile) . "\n";
    
    $minifiedPath = str_replace('.js', '.min.js', $jsFile);
    $success = Minifier::minifyJS_File($jsFile, $minifiedPath);
    
    if ($success) {
        $stats = Minifier::getSizeReduction($jsFile, $minifiedPath);
        echo "  ✅ Minified: {$stats['original_size_formatted']} → {$stats['minified_size_formatted']} ({$stats['reduction_percentage']}% reduction)\n";
        $totalSavings += $stats['reduction_bytes'];
        $processedFiles++;
    } else {
        echo "  ❌ Failed to minify\n";
    }
    echo "\n";
}

// Create combined JS bundle if files exist
if (!empty($jsFiles)) {
    echo "Creating combined JavaScript bundle...\n";
    $combinedJsPath = $buildDir . '/bundle.min.js';
    $success = Minifier::combineJS($jsFiles, $combinedJsPath);
    
    if ($success) {
        echo "✅ Combined JS bundle created: " . basename($combinedJsPath) . "\n";
    } else {
        echo "❌ Failed to create combined JS bundle\n";
    }
} else {
    echo "No JavaScript files found to combine.\n";
}

echo "\n";
echo "Minification Summary:\n";
echo "====================\n";
echo "Files processed: {$processedFiles}\n";
echo "Total space saved: " . Minifier::formatBytes($totalSavings) . "\n";
echo "Build directory: {$buildDir}\n";

// Generate asset manifest
$manifest = [
    'build_time' => date('Y-m-d H:i:s'),
    'css_files' => [],
    'js_files' => [],
    'bundles' => []
];

// Add CSS files to manifest
foreach ($existingCssFiles as $cssFile) {
    $minifiedPath = str_replace('.css', '.min.css', $cssFile);
    if (file_exists($minifiedPath)) {
        $relativePath = str_replace(__DIR__ . '/../', '', $minifiedPath);
        $manifest['css_files'][] = [
            'original' => str_replace(__DIR__ . '/../', '', $cssFile),
            'minified' => $relativePath,
            'size' => filesize($minifiedPath)
        ];
    }
}

// Add JS files to manifest
foreach ($jsFiles as $jsFile) {
    $minifiedPath = str_replace('.js', '.min.js', $jsFile);
    if (file_exists($minifiedPath)) {
        $relativePath = str_replace(__DIR__ . '/../', '', $minifiedPath);
        $manifest['js_files'][] = [
            'original' => str_replace(__DIR__ . '/../', '', $jsFile),
            'minified' => $relativePath,
            'size' => filesize($minifiedPath)
        ];
    }
}

// Add bundles to manifest
if (file_exists($combinedCssPath)) {
    $manifest['bundles']['css'] = str_replace(__DIR__ . '/../', '', $combinedCssPath);
}
if (file_exists($combinedJsPath)) {
    $manifest['bundles']['js'] = str_replace(__DIR__ . '/../', '', $combinedJsPath);
}

// Save manifest
$manifestPath = $buildDir . '/manifest.json';
file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));
echo "✅ Asset manifest created: " . basename($manifestPath) . "\n";

echo "\nMinification complete! 🎉\n";

/**
 * Helper function to format bytes (duplicate from Minifier class for standalone use)
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>