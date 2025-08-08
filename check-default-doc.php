<?php
// Check what happens at root
echo "<h1>Root Directory Check</h1>";

// List all index files
$files = glob($_SERVER['DOCUMENT_ROOT'] . '/index*');
echo "<h2>Index files in root:</h2>";
echo "<ul>";
foreach ($files as $file) {
    echo "<li>" . basename($file) . " - " . filesize($file) . " bytes - Modified: " . date('Y-m-d H:i:s', filemtime($file)) . "</li>";
}
echo "</ul>";

// Check if real_components.php is being used as index
echo "<h2>Other checks:</h2>";
echo "<p>real_components.php size: " . filesize($_SERVER['DOCUMENT_ROOT'] . '/real_components.php') . " bytes</p>";
echo "<p>real_template.php size: " . filesize($_SERVER['DOCUMENT_ROOT'] . '/real_template.php') . " bytes</p>";

// Check if there's a symbolic link or something
if (function_exists('readlink')) {
    $indexLink = @readlink($_SERVER['DOCUMENT_ROOT'] . '/index.php');
    if ($indexLink) {
        echo "<p>index.php is a symbolic link to: " . $indexLink . "</p>";
    }
}
?>