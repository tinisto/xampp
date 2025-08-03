<?php
session_start();

// Only allow access with a secret parameter for security
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

echo "<h2>Email System Debug</h2>";
echo "<pre>";

// Check if required files exist
$requiredFiles = [
    '/config/loadEnv.php',
    '/config/constants.php',
    '/vendor/autoload.php',
    '/includes/functions/email_functions.php',
    '/includes/email-templates/password-reset.php'
];

echo "1. Checking required files:\n";
foreach ($requiredFiles as $file) {
    $path = $_SERVER['DOCUMENT_ROOT'] . $file;
    echo "   $file: " . (file_exists($path) ? "✓ EXISTS" : "✗ MISSING") . "\n";
}

// Try to load environment
echo "\n2. Loading environment:\n";
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
    echo "   ✓ Environment loaded\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// Check SMTP configuration
echo "\n3. SMTP Configuration:\n";
echo "   SMTP_HOST: " . (defined('SMTP_HOST') ? SMTP_HOST : 'NOT DEFINED') . "\n";
echo "   SMTP_USERNAME: " . (defined('SMTP_USERNAME') ? (SMTP_USERNAME ? '***SET***' : 'EMPTY') : 'NOT DEFINED') . "\n";
echo "   SMTP_PASSWORD: " . (defined('SMTP_PASSWORD') ? (SMTP_PASSWORD ? '***SET***' : 'EMPTY') : 'NOT DEFINED') . "\n";
echo "   SMTP_SECURITY: " . (defined('SMTP_SECURITY') ? SMTP_SECURITY : 'NOT DEFINED') . "\n";
echo "   SMTP_PORT: " . (defined('SMTP_PORT') ? SMTP_PORT : 'NOT DEFINED') . "\n";
echo "   ADMIN_EMAIL: " . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'NOT DEFINED') . "\n";

// Check environment variables directly
echo "\n4. Environment Variables:\n";
echo "   \$_ENV['SMTP_HOST']: " . (isset($_ENV['SMTP_HOST']) ? $_ENV['SMTP_HOST'] : 'NOT SET') . "\n";
echo "   \$_ENV['SMTP_USERNAME']: " . (isset($_ENV['SMTP_USERNAME']) ? '***SET***' : 'NOT SET') . "\n";
echo "   \$_ENV['ADMIN_EMAIL']: " . (isset($_ENV['ADMIN_EMAIL']) ? $_ENV['ADMIN_EMAIL'] : 'NOT SET') . "\n";

// Try to send a test email
if (isset($_GET['send']) && $_GET['send'] === 'true') {
    echo "\n5. Attempting to send test email:\n";
    
    try {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';
        
        $testEmail = $_GET['email'] ?? 'test@example.com';
        $resetLink = 'https://11klassniki.ru/test-link';
        $emailBody = getPasswordResetEmailTemplate('Test User', $resetLink);
        
        // Try to send
        sendPasswordResetEmail($testEmail, $resetLink, 'Test Password Reset - 11классники', $emailBody);
        echo "   ✓ Email function called (check email)\n";
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n5. To send a test email, add &send=true&email=your@email.com to the URL\n";
}

echo "</pre>";

// Clean up
echo "<br><a href='?secret=debug123'>Refresh</a>";
?>