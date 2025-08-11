<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Site Bug Testing Report</h1>";
echo "<p>Generated: " . date('Y-m-d H:i:s') . "</p>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    require_once 'database/db_modern.php';
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test 2: Check main pages
echo "<h2>2. Main Pages Syntax Check</h2>";
$pages = [
    'home_modern.php',
    'login_modern.php',
    'register_modern.php',
    'posts_modern.php',
    'news_modern.php',
    'schools_modern.php',
    'spo_modern.php',
    'vpo_modern.php',
    'contact.php',
    'about.php'
];

foreach ($pages as $page) {
    if (file_exists($page)) {
        $output = shell_exec("php -l $page 2>&1");
        if (strpos($output, 'No syntax errors detected') !== false) {
            echo "<p style='color: green;'>✓ $page - No syntax errors</p>";
        } else {
            echo "<p style='color: red;'>✗ $page - Syntax error: $output</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ $page - File not found</p>";
    }
}

// Test 3: Check includes
echo "<h2>3. Include Files Check</h2>";
$includes = [
    'includes/header_modern.php',
    'includes/footer_modern.php',
    'includes/email.php',
    'includes/logo.php'
];

foreach ($includes as $include) {
    if (file_exists($include)) {
        $content = file_get_contents($include);
        // Check for common issues
        if (strpos($content, 'mysqli_query') !== false && strpos($content, 'if ($connection') === false) {
            echo "<p style='color: orange;'>⚠ $include - Uses mysqli_query without checking connection</p>";
        } else {
            echo "<p style='color: green;'>✓ $include - OK</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ $include - File not found</p>";
    }
}

// Test 4: Check for missing files referenced in errors
echo "<h2>4. Missing Files Check</h2>";
$checkFiles = [
    'config.php',
    'config/database_connection.php',
    'check-spo-titles.php'
];

foreach ($checkFiles as $file) {
    if (!file_exists($file)) {
        echo "<p style='color: orange;'>⚠ $file - Referenced but missing</p>";
    }
}

// Test 5: Check session handling
echo "<h2>5. Session Handling</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✓ Session active - User ID: " . $_SESSION['user_id'] . "</p>";
} else {
    echo "<p style='color: orange;'>⚠ No active user session</p>";
}

// Test 6: Check for common security issues
echo "<h2>6. Security Checks</h2>";
$securityIssues = [];

// Check for SQL injection vulnerabilities
$files = glob("**/*.php", GLOB_BRACE);
$sqlInjectionCount = 0;
foreach (array_slice($files, 0, 50) as $file) { // Check first 50 files
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (preg_match('/\$_(GET|POST|REQUEST)\[["\'].*["\']\].*mysqli_query/i', $content)) {
            $sqlInjectionCount++;
        }
    }
}

if ($sqlInjectionCount > 0) {
    echo "<p style='color: red;'>✗ Potential SQL injection vulnerabilities found in $sqlInjectionCount files</p>";
} else {
    echo "<p style='color: green;'>✓ No obvious SQL injection vulnerabilities in checked files</p>";
}

// Test 7: Check error logs
echo "<h2>7. Recent Errors (Last 10)</h2>";
$errorLog = '/Applications/XAMPP/xamppfiles/logs/php_error_log';
if (file_exists($errorLog)) {
    $errors = array_slice(file($errorLog), -10);
    if (!empty($errors)) {
        echo "<pre style='background: #f5f5f5; padding: 10px; overflow-x: auto;'>";
        foreach ($errors as $error) {
            echo htmlspecialchars($error);
        }
        echo "</pre>";
    } else {
        echo "<p style='color: green;'>✓ No recent errors in log</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Error log not found</p>";
}

echo "<hr>";
echo "<p><strong>Test completed.</strong></p>";
?>