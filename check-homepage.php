<?php
echo "<h1>Homepage Diagnostic</h1>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>PHP Self: " . $_SERVER['PHP_SELF'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h2>Files Check:</h2>";
echo "<p>index.php exists: " . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/index.php') ? 'YES' : 'NO') . "</p>";
echo "<p>real_template.php exists: " . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/real_template.php') ? 'YES' : 'NO') . "</p>";
echo "<p>real_components.php exists: " . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/real_components.php') ? 'YES' : 'NO') . "</p>";

// Check what index.php would include
echo "<h2>Index.php first 20 lines:</h2>";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/index.php')) {
    $lines = file($_SERVER['DOCUMENT_ROOT'] . '/index.php');
    echo "<pre>";
    for ($i = 0; $i < min(20, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]);
    }
    echo "</pre>";
}
?>