<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Index Page</h1>";
echo "<p>PHP version: " . phpversion() . "</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";

// Test database connection
echo "<h2>Database Connection Test:</h2>";
try {
    $dbFile = __DIR__ . '/database/db_connections.php';
    if (file_exists($dbFile)) {
        echo "<p>Database file exists</p>";
        require_once $dbFile;
        if (isset($connection) && !$connection->connect_error) {
            echo "<p style='color: green;'>Database connected successfully</p>";
        } else {
            echo "<p style='color: red;'>Database connection failed</p>";
        }
    } else {
        echo "<p style='color: red;'>Database file not found at: $dbFile</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

// Test the index-simple.php file
echo "<h2>Testing index-simple.php:</h2>";
$indexFile = __DIR__ . '/index-simple.php';
if (file_exists($indexFile)) {
    echo "<p>index-simple.php exists</p>";
    echo "<p>File size: " . filesize($indexFile) . " bytes</p>";
    
    // Try to include it
    try {
        echo "<h3>Including index-simple.php:</h3>";
        include $indexFile;
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error including index-simple.php: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>index-simple.php not found</p>";
}
?>