<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Direct Category Debug</h1>";

// Step 1: Check PHP version
echo "<h2>PHP Version:</h2>";
echo phpversion() . "<br><br>";

// Step 2: Direct database connection
echo "<h2>Database Connection Test:</h2>";
try {
    $connection = new mysqli('localhost', 'u2709849_default', 'JWBr0F_0', 'u2709849_default');
    
    if ($connection->connect_error) {
        echo "❌ Connection failed: " . $connection->connect_error . "<br>";
    } else {
        echo "✅ Database connected successfully<br>";
        $connection->set_charset('utf8mb4');
        
        // Test query
        $result = $connection->query("SELECT * FROM categories WHERE url_category = 'mir-uvlecheniy'");
        if ($result) {
            echo "✅ Category query successful<br>";
            if ($row = $result->fetch_assoc()) {
                echo "✅ Category found: " . $row['title_category'] . "<br>";
            }
        } else {
            echo "❌ Query failed: " . $connection->error . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}

echo "<br><h2>File System Check:</h2>";

// Check required files
$files = [
    '/config/loadEnv.php',
    '/database/db_connections.php',
    '/common-components/check_under_construction.php',
    '/pages/category/category.php',
    '/pages/category/category-data-fetch.php',
    '/pages/category/category-content-unified.php',
    '/common-components/template-engine-ultimate.php',
];

foreach ($files as $file) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($fullPath)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file MISSING<br>";
    }
}

// Check what's in config directory
echo "<br><h2>Config Directory Contents:</h2>";
$configDir = $_SERVER['DOCUMENT_ROOT'] . '/config';
if (is_dir($configDir)) {
    $files = scandir($configDir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- $file<br>";
        }
    }
} else {
    echo "❌ Config directory not found!<br>";
}

// Try to load the actual category page step by step
echo "<br><h2>Loading Category Page Components:</h2>";

// Step 1: Set up minimal environment
$_ENV['APP_ENV'] = 'production';
$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_USER'] = 'u2709849_default';
$_ENV['DB_PASS'] = 'JWBr0F_0';
$_ENV['DB_NAME'] = 'u2709849_default';

define('DB_HOST', 'localhost');
define('DB_USER', 'u2709849_default');
define('DB_PASS', 'JWBr0F_0');
define('DB_NAME', 'u2709849_default');

echo "✅ Environment variables set<br>";

// Step 2: Try to include check_under_construction.php
$checkFile = $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
if (file_exists($checkFile)) {
    echo "📝 Attempting to include check_under_construction.php...<br>";
    // Don't actually include it, just check what it needs
    $content = file_get_contents($checkFile);
    if (strpos($content, 'loadEnv.php') !== false) {
        echo "⚠️  check_under_construction.php requires loadEnv.php<br>";
    }
} else {
    echo "❌ check_under_construction.php not found<br>";
}

// Check server info
echo "<br><h2>Server Information:</h2>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
?>

<br><h2>Test Direct Category Rendering:</h2>
<a href="/simple_category_test.php">Test Simple Category Page</a>