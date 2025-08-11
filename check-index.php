<?php
// Check what's happening with index
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Index Check</h1>";
echo "<p>PHP is working</p>";

// Check if database file exists
if (file_exists('database/db_modern.php')) {
    echo "<p>✅ Database file exists</p>";
    
    // Try to include it
    try {
        require_once 'database/db_modern.php';
        echo "<p>✅ Database included successfully</p>";
        
        // Check if we can query
        try {
            $test = db_query("SELECT 1 as test");
            echo "<p>✅ Database connection works!</p>";
        } catch (Exception $e) {
            echo "<p>❌ Database query error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Database include error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>❌ Database file not found</p>";
}

// Check if home file exists
if (file_exists('home_modern.php')) {
    echo "<p>✅ home_modern.php exists</p>";
} else {
    echo "<p>❌ home_modern.php not found</p>";
}

// Check current directory
echo "<h2>Current directory contents:</h2>";
echo "<pre>";
$files = scandir('.');
foreach ($files as $file) {
    if (substr($file, -4) === '.php') {
        echo $file . "\n";
    }
}
echo "</pre>";
?>