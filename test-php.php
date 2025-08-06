<?php
// Simple PHP test
echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";

// Check if .env file exists
$envFile = $_SERVER['DOCUMENT_ROOT'] . '/.env';
if (file_exists($envFile)) {
    echo ".env file exists<br>";
} else {
    echo ".env file NOT found<br>";
}

// Check if .env.production file exists
$envProdFile = $_SERVER['DOCUMENT_ROOT'] . '/.env.production';
if (file_exists($envProdFile)) {
    echo ".env.production file exists<br>";
} else {
    echo ".env.production file NOT found<br>";
}
?>