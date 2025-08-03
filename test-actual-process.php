<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

// Simulate the exact POST request that the form makes
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

$email = $_GET['email'] ?? '11klassniki.ru@gmail.com';

echo "<h2>Simulating Actual Form Process</h2>";
echo "<pre>";

// Set up session like the form would
$_SESSION['csrf_token'] = 'test_token';

// Simulate the POST data
$_POST['email'] = $email;
$_POST['csrf_token'] = 'test_token';
$_SERVER['REQUEST_METHOD'] = 'POST';

echo "=== Simulating POST to forgot-password-process.php ===\n";
echo "Email: $email\n";
echo "CSRF Token: test_token\n\n";

// Capture any output from the process file
ob_start();

try {
    // Include the actual process file
    include $_SERVER['DOCUMENT_ROOT'] . '/forgot-password-process.php';
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "✅ Process file executed successfully\n";
    if ($output) {
        echo "Output: " . $output . "\n";
    }
    
    // Check session messages
    if (isset($_SESSION['reset_success'])) {
        echo "✅ Success message set: " . $_SESSION['reset_success'] . "\n";
    }
    if (isset($_SESSION['reset_error'])) {
        echo "❌ Error message set: " . $_SESSION['reset_error'] . "\n";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Error in process file: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>