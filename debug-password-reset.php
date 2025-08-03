<?php
// Debug script for password reset issues
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';

echo "<h2>Password Reset Debug</h2>";
echo "<pre>";

// Test 1: Check environment variables
echo "=== Environment Variables ===\n";
echo "SMTP_HOST: " . ($_ENV['SMTP_HOST'] ?? 'NOT SET') . "\n";
echo "SMTP_USERNAME: " . ($_ENV['SMTP_USERNAME'] ?? 'NOT SET') . "\n";
echo "SMTP_PASSWORD: " . (isset($_ENV['SMTP_PASSWORD']) ? '[SET]' : 'NOT SET') . "\n";
echo "SMTP_PORT: " . ($_ENV['SMTP_PORT'] ?? 'NOT SET') . "\n";
echo "ADMIN_EMAIL: " . ($_ENV['ADMIN_EMAIL'] ?? 'NOT SET') . "\n\n";

// Test 2: Check constants
echo "=== Constants ===\n";
echo "SMTP_HOST constant: " . (defined('SMTP_HOST') ? SMTP_HOST : 'NOT DEFINED') . "\n";
echo "SMTP_USERNAME constant: " . (defined('SMTP_USERNAME') ? SMTP_USERNAME : 'NOT DEFINED') . "\n";
echo "SMTP_PASSWORD constant: " . (defined('SMTP_PASSWORD') ? '[DEFINED]' : 'NOT DEFINED') . "\n";
echo "SMTP_PORT constant: " . (defined('SMTP_PORT') ? SMTP_PORT : 'NOT DEFINED') . "\n";
echo "ADMIN_EMAIL constant: " . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'NOT DEFINED') . "\n\n";

// Test 3: Check database connection
echo "=== Database Connection ===\n";
if (isset($connection) && $connection) {
    echo "✅ Database connected\n";
    
    // Check if users table exists and has test email
    $testEmail = $_GET['email'] ?? '11klassniki.ru@gmail.com';
    $stmt = $connection->prepare("SELECT id, firstname FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $testEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo "✅ Found user: ID={$user['id']}, Name={$user['firstname']}\n";
        } else {
            echo "❌ No user found with email: $testEmail\n";
        }
        $stmt->close();
    }
    
    // Check password_resets table
    $tableCheck = $connection->query("SHOW TABLES LIKE 'password_resets'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        echo "✅ password_resets table exists\n";
    } else {
        echo "❌ password_resets table does not exist\n";
    }
} else {
    echo "❌ Database connection failed\n";
}

// Test 4: Try to send a simple email
echo "\n=== Email Test ===\n";
try {
    $testEmail = $_GET['email'] ?? '11klassniki.ru@gmail.com';
    $result = sendToUser($testEmail, 'Debug Test - 11классники', '<p>This is a debug test email from password reset system.</p>');
    echo "✅ Email function executed\n";
} catch (Exception $e) {
    echo "❌ Email error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>