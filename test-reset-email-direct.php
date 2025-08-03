<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions/email_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';

$email = $_GET['email'] ?? '11klassniki.ru@gmail.com';

echo "<h2>Direct Password Reset Email Test</h2>";
echo "<pre>";

// Generate test data
$resetLink = "https://11klassniki.ru/reset-password?token=TEST123&email=" . urlencode($email);
$userName = "Test User";

echo "=== Email Details ===\n";
echo "To: $email\n";
echo "Subject: Восстановление пароля - 11классники\n";
echo "Reset Link: $resetLink\n\n";

// Test 1: Using password reset template
echo "=== Test 1: Password Reset Template ===\n";
try {
    $emailBody = getPasswordResetEmailTemplate($userName, $resetLink);
    echo "✅ Template generated successfully\n";
    echo "Body preview: " . substr(strip_tags($emailBody), 0, 100) . "...\n\n";
    
    sendPasswordResetEmail($email, $resetLink, 'Восстановление пароля - 11классники', $emailBody);
    echo "✅ Password reset email sent\n";
} catch (Exception $e) {
    echo "❌ Password reset email failed: " . $e->getMessage() . "\n";
}

// Test 2: Simple email for comparison
echo "\n=== Test 2: Simple Email ===\n"; 
try {
    $simpleBody = "<h2>Password Reset Test</h2><p>This is a simple test email.</p><p>Reset link: <a href='$resetLink'>Click here</a></p>";
    sendToUser($email, 'Simple Test - 11классники', $simpleBody);
    echo "✅ Simple email sent\n";
} catch (Exception $e) {
    echo "❌ Simple email failed: " . $e->getMessage() . "\n";
}

// Test 3: Check if password reset template exists and is working
echo "\n=== Test 3: Template Check ===\n";
$templatePath = $_SERVER['DOCUMENT_ROOT'] . '/includes/email-templates/password-reset.php';
if (file_exists($templatePath)) {
    echo "✅ Password reset template file exists\n";
    
    // Show template content preview
    $templateContent = file_get_contents($templatePath);
    echo "Template size: " . strlen($templateContent) . " bytes\n";
} else {
    echo "❌ Password reset template file missing\n";
}

echo "\n=== Check your email inbox for both test emails ===\n";
echo "If you receive the simple email but not the password reset email,\n";
echo "there's an issue with the password reset template.\n";

echo "</pre>";
?>