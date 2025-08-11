<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>\n";

// Load the environment
if (file_exists('config/loadEnv.php')) {
    require_once 'config/loadEnv.php';
    echo "<p>✅ Config loaded</p>\n";
} else {
    echo "<p>❌ Config file not found</p>\n";
}

// Check if constants are defined
echo "<h2>Database Constants:</h2>\n";
echo "<ul>\n";
echo "<li>DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "</li>\n";
echo "<li>DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "</li>\n";
echo "<li>DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "</li>\n";
echo "<li>DB_PASS: " . (defined('DB_PASS') ? '***hidden***' : 'NOT DEFINED') . "</li>\n";
echo "</ul>\n";

// Try to connect
if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS')) {
    echo "<h2>Connection Test:</h2>\n";
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p>✅ Database connection successful!</p>\n";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM news");
        $result = $stmt->fetch();
        echo "<p>✅ Found " . $result['count'] . " news articles in database</p>\n";
        
    } catch (PDOException $e) {
        echo "<p>❌ Connection failed: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }
} else {
    echo "<p>❌ Database constants not defined</p>\n";
}

// Check if we can load db_modern.php
echo "<h2>Loading db_modern.php:</h2>\n";
if (file_exists('database/db_modern.php')) {
    try {
        require_once 'database/db_modern.php';
        echo "<p>✅ db_modern.php loaded</p>\n";
        
        // Try a query
        $count = db_fetch_column("SELECT COUNT(*) FROM news");
        echo "<p>✅ Query via db_modern.php: Found $count news articles</p>\n";
        
    } catch (Exception $e) {
        echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    }
} else {
    echo "<p>❌ database/db_modern.php not found</p>\n";
}

echo "\n<p><a href='/'>Go to homepage</a></p>\n";
?>