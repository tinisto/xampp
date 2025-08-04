<?php
// Test image paths
$imagePath = '/uploads/content/news_689089eecbe64.png';
$fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

echo "<!DOCTYPE html>";
echo "<html><head><title>Image Test</title></head><body>";
echo "<h1>Image Path Test</h1>";

echo "<h2>Path Information:</h2>";
echo "<p>Image Path: " . htmlspecialchars($imagePath) . "</p>";
echo "<p>Full Server Path: " . htmlspecialchars($fullPath) . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h2>File Exists Check:</h2>";
if (file_exists($fullPath)) {
    echo "<p style='color: green;'>✅ File exists on server</p>";
    echo "<p>File size: " . filesize($fullPath) . " bytes</p>";
} else {
    echo "<p style='color: red;'>❌ File does not exist at: " . htmlspecialchars($fullPath) . "</p>";
}

echo "<h2>Image Display Tests:</h2>";
echo "<h3>1. Using relative path:</h3>";
echo "<img src='" . htmlspecialchars($imagePath) . "' alt='Test' style='max-width: 500px; border: 1px solid #ccc;'>";

echo "<h3>2. Using absolute URL:</h3>";
$absoluteUrl = 'https://11klassniki.ru' . $imagePath;
echo "<p>URL: " . htmlspecialchars($absoluteUrl) . "</p>";
echo "<img src='" . htmlspecialchars($absoluteUrl) . "' alt='Test' style='max-width: 500px; border: 1px solid #ccc;'>";

echo "<h2>Directory Listing:</h2>";
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/content/';
if (is_dir($uploadDir)) {
    echo "<p>Directory exists: " . htmlspecialchars($uploadDir) . "</p>";
    $files = scandir($uploadDir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>Directory does not exist: " . htmlspecialchars($uploadDir) . "</p>";
}

echo "</body></html>";
?>