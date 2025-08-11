<?php
// Simple test file to verify PHP is working
echo "<!DOCTYPE html>\n";
echo "<html><head><title>Server Test</title></head><body>\n";
echo "<h1>PHP is working!</h1>\n";
echo "<p>Server time: " . date('Y-m-d H:i:s') . "</p>\n";
echo "<p>PHP version: " . phpversion() . "</p>\n";
echo "<h2>File check:</h2>\n";
echo "<ul>\n";

$files_to_check = [
    'index_modern.php',
    'router.php',
    'database/db_modern.php',
    'config/loadEnv.php',
    '.htaccess'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<li>✅ $file exists (" . filesize($file) . " bytes)</li>\n";
    } else {
        echo "<li>❌ $file NOT FOUND</li>\n";
    }
}

echo "</ul>\n";
echo "<h2>Database test:</h2>\n";

// Try to include database
if (file_exists('database/db_modern.php')) {
    echo "<p>Including database file...</p>\n";
    try {
        require_once 'database/db_modern.php';
        echo "<p>✅ Database file loaded</p>\n";
    } catch (Exception $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>\n";
    }
} else {
    echo "<p>❌ Database file not found</p>\n";
}

echo "</body></html>\n";
?>