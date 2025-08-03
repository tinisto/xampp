<?php
echo "<h2>Checking Error Page Configuration</h2>";

$error_file = $_SERVER['DOCUMENT_ROOT'] . '/pages/error/error.php';

if (file_exists($error_file)) {
    echo "<h3>Current error.php content:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo htmlspecialchars(file_get_contents($error_file));
    echo "</pre>";
    
    echo "<h3>File info:</h3>";
    echo "Size: " . filesize($error_file) . " bytes<br>";
    echo "Modified: " . date('Y-m-d H:i:s', filemtime($error_file)) . "<br>";
} else {
    echo "<p style='color:red;'>Error file not found!</p>";
}

// Check if there are other error files
echo "<h3>Other error files in directory:</h3>";
$error_dir = $_SERVER['DOCUMENT_ROOT'] . '/pages/error/';
$files = scandir($error_dir);
foreach ($files as $file) {
    if (strpos($file, '.php') !== false) {
        echo $file . " (" . filesize($error_dir . $file) . " bytes)<br>";
    }
}
?>