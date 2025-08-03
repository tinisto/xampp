<?php
/**
 * Force reload environment and test database connection
 */

echo "<h1>🔄 Force Environment Reload</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .code { background: #f5f5f5; padding: 15px; border: 1px solid #ddd; font-family: monospace; margin: 10px 0; white-space: pre-wrap; }
</style>";

// Clear any opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p class='success'>✅ OPcache cleared</p>";
}

// Show current constants before reload
echo "<h2>1️⃣ Current Constants (before reload)</h2>";
echo "<div class='code'>";
echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "\n";
echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "\n";
echo "</div>";

// Force reload the .env file
echo "<h2>2️⃣ Reloading .env file</h2>";

// Undefine constants if possible (usually not allowed)
// Instead, let's check what loadEnv.php does

$env_path = $_SERVER['DOCUMENT_ROOT'] . '/.env';
if (file_exists($env_path)) {
    $env_content = file_get_contents($env_path);
    $lines = explode("\n", $env_content);
    
    echo "<p>Reading .env file directly:</p>";
    echo "<div class='code'>";
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            echo "$key = $value\n";
            
            // Try to override the constant (won't work if already defined)
            if (!defined($key)) {
                define($key, $value);
            }
        }
    }
    echo "</div>";
}

// Test new connection with values from .env
echo "<h2>3️⃣ Testing Direct Connection</h2>";

try {
    // Parse .env file manually
    $env_vars = [];
    if (file_exists($env_path)) {
        $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $env_vars[trim($name)] = trim($value);
            }
        }
    }
    
    echo "<p>Parsed values from .env:</p>";
    echo "<div class='code'>";
    echo "DB_HOST: " . ($env_vars['DB_HOST'] ?? 'not found') . "\n";
    echo "DB_USER: " . ($env_vars['DB_USER'] ?? 'not found') . "\n";
    echo "DB_NAME: " . ($env_vars['DB_NAME'] ?? 'not found') . "\n";
    echo "</div>";
    
    // Test connection with parsed values
    if (isset($env_vars['DB_HOST']) && isset($env_vars['DB_USER']) && isset($env_vars['DB_PASS']) && isset($env_vars['DB_NAME'])) {
        $test_connection = new mysqli(
            $env_vars['DB_HOST'],
            $env_vars['DB_USER'],
            $env_vars['DB_PASS'],
            $env_vars['DB_NAME']
        );
        
        if ($test_connection->connect_error) {
            echo "<p class='error'>❌ Connection failed: " . $test_connection->connect_error . "</p>";
        } else {
            echo "<p class='success'>✅ Connected successfully to: " . $env_vars['DB_NAME'] . "</p>";
            
            // Check tables
            $tables_result = $test_connection->query("SHOW TABLES");
            $tables = [];
            while ($row = $tables_result->fetch_array()) {
                $tables[] = $row[0];
            }
            
            echo "<p>Tables found: " . count($tables) . "</p>";
            
            // Check for new structure
            $has_universities = in_array('universities', $tables);
            $has_colleges = in_array('colleges', $tables);
            
            if ($has_universities && $has_colleges) {
                echo "<p class='success'>✅ New database structure confirmed (universities & colleges tables exist)</p>";
            } else {
                echo "<p class='error'>❌ New structure not found</p>";
            }
            
            $test_connection->close();
        }
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

// Check if there's a config file overriding the database
echo "<h2>4️⃣ Checking for Database Config Files</h2>";

$config_files = [
    '/config/db_config.php',
    '/config/database.php',
    '/includes/db_connect.php',
    '/includes/config.php'
];

foreach ($config_files as $file) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($full_path)) {
        echo "<p class='warning'>⚠️ Found: $file</p>";
        $content = file_get_contents($full_path);
        if (strpos($content, '11klassniki_ru') !== false) {
            echo "<p class='error'>❌ This file contains reference to old database!</p>";
        }
    }
}

echo "<h2>5️⃣ Solution</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>The .env file is correct, but the application is not using it.</strong></p>";
echo "<p>Possible causes:</p>";
echo "<ol>";
echo "<li>PHP opcache is caching old values - Need to restart PHP/Apache</li>";
echo "<li>The loadEnv.php file has hardcoded values</li>";
echo "<li>Another config file is overriding the database settings</li>";
echo "<li>The application needs to be restarted to pick up new environment variables</li>";
echo "</ol>";
echo "<p class='warning'><strong>Action needed:</strong> Contact your hosting provider to restart PHP/Apache, or check if there's a control panel option to restart the application.</p>";
echo "</div>";

echo "<p style='margin-top: 30px;'><a href='/test_new_structure.php'>← Back to Structure Test</a> | <a href='/check_env_status.php'>Check Environment</a></p>";
?>