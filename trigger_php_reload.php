<?php
/**
 * Attempt to trigger PHP reload
 */

echo "<h1>🔄 Attempting to Trigger PHP Reload</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    .code { background: #f5f5f5; padding: 10px; font-family: monospace; }
</style>";

// Method 1: Clear opcache if available
echo "<h2>1️⃣ Clearing OPcache</h2>";
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "<p class='success'>✅ OPcache cleared successfully</p>";
    } else {
        echo "<p class='error'>❌ Failed to clear OPcache</p>";
    }
} else {
    echo "<p class='warning'>⚠️ OPcache functions not available</p>";
}

// Method 2: Clear APC cache if available
echo "<h2>2️⃣ Clearing APC Cache</h2>";
if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
    apc_clear_cache('user');
    echo "<p class='success'>✅ APC cache cleared</p>";
} else {
    echo "<p class='warning'>⚠️ APC not available</p>";
}

// Method 3: Clear stat cache
echo "<h2>3️⃣ Clearing File Stat Cache</h2>";
clearstatcache(true);
echo "<p class='success'>✅ File stat cache cleared</p>";

// Method 4: Touch important files to trigger reload
echo "<h2>4️⃣ Touching Configuration Files</h2>";
$files_to_touch = [
    '/.user.ini',
    '/.htaccess',
    '/config/loadEnv.php'
];

foreach ($files_to_touch as $file) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($full_path)) {
        if (touch($full_path)) {
            echo "<p class='success'>✅ Touched: $file</p>";
        } else {
            echo "<p class='error'>❌ Could not touch: $file</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ File not found: $file</p>";
    }
}

// Method 5: Create a trigger file
echo "<h2>5️⃣ Creating Trigger File</h2>";
$trigger_file = $_SERVER['DOCUMENT_ROOT'] . '/php_reload_trigger_' . time() . '.tmp';
if (file_put_contents($trigger_file, 'trigger')) {
    echo "<p class='success'>✅ Created trigger file</p>";
    // Try to delete it immediately
    unlink($trigger_file);
}

// Method 6: Update .user.ini with timestamp
echo "<h2>6️⃣ Updating .user.ini</h2>";
$user_ini_path = $_SERVER['DOCUMENT_ROOT'] . '/.user.ini';
if (file_exists($user_ini_path)) {
    $content = file_get_contents($user_ini_path);
    $content = preg_replace('/; Last modified: .*/', '; Last modified: ' . date('Y-m-d H:i:s'), $content);
    if (file_put_contents($user_ini_path, $content)) {
        echo "<p class='success'>✅ Updated .user.ini with new timestamp</p>";
    }
}

// Test if constants changed
echo "<h2>7️⃣ Testing Configuration</h2>";
echo "<div class='code'>";
echo "DB_NAME constant: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";

// Try to read .env directly
$env_path = $_SERVER['DOCUMENT_ROOT'] . '/.env';
if (file_exists($env_path)) {
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'DB_NAME=') === 0) {
            echo ".env file says: $line\n";
            break;
        }
    }
}
echo "</div>";

// Summary
echo "<h2>📋 Summary</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p>We've attempted several methods to trigger PHP reload:</p>";
echo "<ul>";
echo "<li>Cleared various caches</li>";
echo "<li>Touched configuration files</li>";
echo "<li>Created .user.ini file</li>";
echo "</ul>";
echo "<p class='warning'><strong>Note:</strong> On shared hosting, these methods may not work immediately.</p>";
echo "<p>The most reliable solution is still to:</p>";
echo "<ol>";
echo "<li>Contact iPage support to restart PHP</li>";
echo "<li>Wait for automatic PHP restart (usually happens within a few hours)</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🧪 Test Again</h2>";
echo "<p><a href='/test_new_structure.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Database Structure</a></p>";
echo "<p style='margin-top: 10px;'><a href='/direct_db_test.php'>Direct Database Test</a></p>";
?>