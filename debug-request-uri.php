<?php
echo "<h3>REQUEST_URI Debug</h3>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "</p>";
echo "<p><strong>PATH_INFO:</strong> " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "</p>";
echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'NOT SET') . "</p>";

$uri = $_SERVER['REQUEST_URI'] ?? '';
$cleanPath = parse_url($uri, PHP_URL_PATH);

echo "<p><strong>Clean Path:</strong> {$cleanPath}</p>";

// Test exact matches  
$testUrls = ['/news', '/news/novosti-spo', '/news/novosti-shkol'];

echo "<h4>Match Tests:</h4>";
foreach ($testUrls as $testUrl) {
    $isMatch = ($cleanPath === $testUrl);
    echo "<p>'{$cleanPath}' === '{$testUrl}' = " . ($isMatch ? 'TRUE ✅' : 'false ❌') . "</p>";
}

echo "<h4>URL Analysis:</h4>";
if (strpos($cleanPath, '/news/') === 0) {
    echo "<p>✅ URL starts with '/news/'</p>";
    $parts = explode('/', trim($cleanPath, '/'));
    echo "<p>URL parts: " . implode(', ', $parts) . "</p>";
} else {
    echo "<p>❌ URL does not start with '/news/'</p>";
}
?>