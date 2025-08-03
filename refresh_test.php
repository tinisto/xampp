<?php
/**
 * Test if caching is disabled and new DB is accessible
 */

// Kill any existing script execution
if (function_exists('opcache_reset')) {
    opcache_reset();
}

// Force new timestamp
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo "<h1>🔄 Cache-Disabled Test - " . date('Y-m-d H:i:s') . "</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .code { background: #f5f5f5; padding: 10px; font-family: monospace; }
</style>";

// Method 1: Force reload environment file
echo "<h2>1️⃣ Attempting Fresh Environment Load</h2>";

// Clear any existing constants (won't work if already defined, but try)
$constants_to_check = ['DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME'];

// Read .env file directly and parse it
$env_path = $_SERVER['DOCUMENT_ROOT'] . '/.env';
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
    
    echo "<div class='code'>";
    echo "Parsed from .env file:<br>";
    echo "DB_NAME = " . ($env_vars['DB_NAME'] ?? 'not found') . "<br>";
    echo "DB_USER = " . ($env_vars['DB_USER'] ?? 'not found') . "<br>";
    echo "</div>";
}

// Method 2: Test connection with parsed values
echo "<h2>2️⃣ Testing Direct Connection</h2>";

if (isset($env_vars['DB_HOST']) && isset($env_vars['DB_USER']) && isset($env_vars['DB_PASS']) && isset($env_vars['DB_NAME'])) {
    try {
        $test_conn = @new mysqli(
            $env_vars['DB_HOST'],
            $env_vars['DB_USER'],
            $env_vars['DB_PASS'],
            $env_vars['DB_NAME']
        );
        
        if (!$test_conn->connect_error) {
            $db_result = $test_conn->query("SELECT DATABASE() as db");
            $current_db = $db_result->fetch_assoc()['db'];
            
            echo "<p class='success'>✅ Connected to: <strong>$current_db</strong></p>";
            
            // Check for new tables
            $tables_exist = [
                'universities' => $test_conn->query("SHOW TABLES LIKE 'universities'")->num_rows > 0,
                'colleges' => $test_conn->query("SHOW TABLES LIKE 'colleges'")->num_rows > 0
            ];
            
            if ($tables_exist['universities'] && $tables_exist['colleges']) {
                echo "<p class='success'>✅ New database structure confirmed!</p>";
            }
            
            $test_conn->close();
        } else {
            echo "<p class='error'>❌ Connection failed</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    }
}

// Method 3: Try alternative approach
echo "<h2>3️⃣ Alternative: Temporary Override</h2>";
echo "<p>Since PHP-FPM can't be restarted on shared hosting, here are your options:</p>";
echo "<ol>";
echo "<li><strong>Wait approach:</strong> With caching disabled, try again in 10-15 minutes</li>";
echo "<li><strong>File modification:</strong> Try modifying a core PHP file to force reload</li>";
echo "<li><strong>Alternative method:</strong> We can create a bootstrap file that forces the new DB</li>";
echo "</ol>";

// Create a bootstrap override file
$bootstrap_content = '<?php
// Force new database connection
if (!defined("DB_HOST")) define("DB_HOST", "11klassnikiru67871.ipagemysql.com");
if (!defined("DB_USER")) define("DB_USER", "admin_claude");
if (!defined("DB_PASS")) define("DB_PASS", "W4eZ!#9uwLmrMay");
if (!defined("DB_NAME")) define("DB_NAME", "11klassniki_claude");
';

$bootstrap_path = $_SERVER['DOCUMENT_ROOT'] . '/force_db_bootstrap.php';
if (file_put_contents($bootstrap_path, $bootstrap_content)) {
    echo "<p class='success'>✅ Created bootstrap override file</p>";
}

echo "<h2>4️⃣ Next Steps</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>Since iPage disabled caching:</strong></p>";
echo "<ol>";
echo "<li>Clear your browser cache (Ctrl+F5)</li>";
echo "<li>Wait 10-15 minutes for changes to propagate</li>";
echo "<li>Test again: <a href='/test_new_structure.php'>Test Structure</a></li>";
echo "</ol>";
echo "<p>If still not working after 15 minutes, we'll need to use a workaround.</p>";
echo "</div>";

echo "<p style='margin-top: 20px;'>";
echo "<a href='/test_new_structure.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Again</a> ";
echo "<a href='/' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Home</a>";
echo "</p>";
?>