<?php
// Simple test page
echo "School test page is working!<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "PHP Version: " . phpversion() . "<br>";

// Test if we can access the database config
$configFile = $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
if (file_exists($configFile)) {
    echo "Config file exists<br>";
    require_once $configFile;
    echo "DB_HOST defined: " . (defined('DB_HOST') ? 'YES' : 'NO') . "<br>";
} else {
    echo "Config file NOT found at: " . $configFile . "<br>";
}
?>