<?php
// Direct test of about page bypassing template engine
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Testing about page directly<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current file: " . __FILE__ . "<br>";

// Check if files exist
$files_to_check = [
    '/pages/about/about_content.php',
    '/common-components/template-engine-ultimate.php',
    '/common-components/header.php',
    '/common-components/footer-unified.php'
];

foreach ($files_to_check as $file) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $file;
    echo "Checking $file: " . (file_exists($full_path) ? "EXISTS" : "NOT FOUND") . "<br>";
}

// Try to include the about content directly
echo "<br>About content:<br>";
echo "<div style='border: 1px solid black; padding: 10px;'>";
include $_SERVER['DOCUMENT_ROOT'] . '/pages/about/about_content.php';
echo "</div>";
?>