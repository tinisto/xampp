<?php
echo "<h3>URL Parameters Debug</h3>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>PATH_INFO:</strong> " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "</p>";

echo "<h4>GET Parameters:</h4>";
foreach ($_GET as $key => $value) {
    echo "<p>{$key} = {$value}</p>";
}

echo "<h4>URL Analysis:</h4>";
$url = $_SERVER['REQUEST_URI'];
$parsed = parse_url($url);
echo "<p>Path: " . ($parsed['path'] ?? 'none') . "</p>";
echo "<p>Query: " . ($parsed['query'] ?? 'none') . "</p>";

// Test what the news.php logic should detect
if (strpos($url, '/novosti-spo') !== false) {
    echo "<p>✅ URL contains /novosti-spo</p>";
    echo "<p>Should activate: Новости СПО</p>";
} else {
    echo "<p>❌ URL does not contain /novosti-spo</p>";
}

if (strpos($url, '/novosti-shkol') !== false) {
    echo "<p>✅ URL contains /novosti-shkol</p>";  
    echo "<p>Should activate: Новости школ</p>";
} else {
    echo "<p>❌ URL does not contain /novosti-shkol</p>";
}
?>