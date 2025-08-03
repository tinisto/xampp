<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Full Category Debug</h1>";

// Step 1: Test database connection with production credentials
echo "<h2>1. Database Connection Test:</h2>";
$connection = new mysqli('11klassnikiru67871.ipagemysql.com', '11klone_user', 'K8HqqBV3hTf4mha', '11klassniki_ru');

if ($connection->connect_error) {
    echo "❌ Connection failed: " . $connection->connect_error . "<br>";
} else {
    echo "✅ Database connected successfully<br>";
    $connection->set_charset('utf8mb4');
}

// Step 2: Check environment files
echo "<h2>2. Environment Files:</h2>";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env.production')) {
    echo "✅ .env.production exists<br>";
    $content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/.env.production');
    echo "Content preview: " . substr($content, 0, 100) . "...<br>";
} else {
    echo "❌ .env.production NOT found<br>";
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/.env')) {
    echo "✅ .env exists<br>";
} else {
    echo "❌ .env NOT found<br>";
}

// Step 3: Test loadEnv.php
echo "<h2>3. LoadEnv Test:</h2>";
$loadEnvPath = $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
if (file_exists($loadEnvPath)) {
    echo "✅ loadEnv.php exists<br>";
    
    // Check if it has syntax errors
    $loadEnvContent = file_get_contents($loadEnvPath);
    if (strpos($loadEnvContent, 'use Dotenv') !== false) {
        echo "⚠️  loadEnv.php contains 'use Dotenv' - might cause syntax error<br>";
    }
    
    // Try to include it
    try {
        include_once $loadEnvPath;
        echo "✅ loadEnv.php included successfully<br>";
        
        // Check if constants are defined
        if (defined('DB_HOST')) {
            echo "✅ DB_HOST defined: " . DB_HOST . "<br>";
        } else {
            echo "❌ DB_HOST NOT defined<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error including loadEnv.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ loadEnv.php NOT found<br>";
}

// Step 4: Check Composer autoloader
echo "<h2>4. Composer Check:</h2>";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    echo "✅ Composer autoloader exists<br>";
} else {
    echo "❌ Composer autoloader NOT found (this is OK, we have fallback)<br>";
}

// Step 5: Check category page files
echo "<h2>5. Category Page Files:</h2>";
$categoryFiles = [
    '/pages/category/category.php',
    '/pages/category/category-data-fetch.php',
    '/pages/category/category-content-unified.php',
    '/common-components/template-engine-ultimate.php',
    '/common-components/check_under_construction.php',
    '/database/db_connections.php',
];

foreach ($categoryFiles as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file MISSING<br>";
    }
}

// Step 6: Test the category page flow
echo "<h2>6. Category Page Flow Test:</h2>";

// Set up the environment manually
$_ENV['APP_ENV'] = 'production';
$_ENV['DB_HOST'] = '11klassnikiru67871.ipagemysql.com';
$_ENV['DB_USER'] = '11klone_user';
$_ENV['DB_PASS'] = 'K8HqqBV3hTf4mha';
$_ENV['DB_NAME'] = '11klassniki_ru';

if (!defined('DB_HOST')) {
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_PASS', $_ENV['DB_PASS']);
    define('DB_NAME', $_ENV['DB_NAME']);
}

echo "✅ Environment set up manually<br>";

// Try to include check_under_construction.php
$checkPath = $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';
if (file_exists($checkPath)) {
    echo "Attempting to include check_under_construction.php...<br>";
    
    // Read the file to see what it requires
    $checkContent = file_get_contents($checkPath);
    if (strpos($checkContent, 'loadEnv.php') !== false) {
        echo "⚠️  check_under_construction.php requires loadEnv.php<br>";
    }
    if (strpos($checkContent, 'session_util.php') !== false) {
        echo "⚠️  check_under_construction.php requires session_util.php<br>";
        
        // Check if session_util exists
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/functions/session_util.php')) {
            echo "✅ session_util.php exists<br>";
        } else {
            echo "❌ session_util.php MISSING<br>";
        }
    }
}

// Step 7: Error log check
echo "<h2>7. Recent Errors:</h2>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $errors = file_get_contents($errorLog);
    $lines = explode("\n", $errors);
    $recent = array_slice($lines, -5);
    foreach ($recent as $line) {
        if (trim($line)) {
            echo htmlspecialchars($line) . "<br>";
        }
    }
} else {
    echo "Cannot access error log<br>";
}

echo "<hr>";
echo "<h2>Recommended Actions:</h2>";
echo "1. <a href='/category_working.php?url_category=mir-uvlecheniy'>Test Working Standalone Page</a><br>";
echo "2. <a href='/simple_category_test.php'>Test Simple Category Page</a><br>";
echo "3. Main category page: <a href='/category/mir-uvlecheniy'>/category/mir-uvlecheniy</a><br>";
?>