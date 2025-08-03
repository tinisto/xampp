<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'debug123') {
    die('Access denied');
}

echo "<h2>Simple Process Check</h2>";
echo "<pre>";

// Just check the file content
$processFile = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/forgot-password-process.php');

if (strpos($processFile, "SELECT id, firstname FROM users") !== false) {
    echo "❌ PROBLEM FOUND: Process file still uses 'firstname'\n";
    echo "The live forgot-password-process.php file was not updated!\n\n";
} else {
    echo "✅ Process file appears to use correct field name\n";
}

// Check for specific lines
if (strpos($processFile, "getPasswordResetEmailTemplate(\$user['firstname']") !== false) {
    echo "❌ FOUND: Template call uses 'firstname'\n";
} else if (strpos($processFile, "getPasswordResetEmailTemplate(\$user['first_name']") !== false) {
    echo "✅ Template call uses 'first_name'\n";
}

if (strpos($processFile, "{\$user['firstname']}") !== false) {
    echo "❌ FOUND: Alt body uses 'firstname'\n";
} else if (strpos($processFile, "{\$user['first_name']}") !== false) {
    echo "✅ Alt body uses 'first_name'\n";
}

echo "\nFile size: " . strlen($processFile) . " bytes\n";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'] . '/forgot-password-process.php')) . "\n";

echo "</pre>";
?>