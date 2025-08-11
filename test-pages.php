<?php
// Test main pages for errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing Main Pages</h1>";

$pages = [
    ['url' => '/', 'name' => 'Homepage'],
    ['url' => '/posts', 'name' => 'Posts'],
    ['url' => '/news', 'name' => 'News'],
    ['url' => '/schools', 'name' => 'Schools'],
    ['url' => '/spo', 'name' => 'SPO'],
    ['url' => '/vpo', 'name' => 'VPO'],
    ['url' => '/login', 'name' => 'Login'],
    ['url' => '/register', 'name' => 'Register'],
    ['url' => '/contact', 'name' => 'Contact'],
    ['url' => '/about', 'name' => 'About']
];

foreach ($pages as $page) {
    echo "<h2>Testing: " . $page['name'] . " (" . $page['url'] . ")</h2>";
    
    // Make request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8888" . $page['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "<p style='color: red;'>✗ cURL Error: " . $error . "</p>";
    } else {
        if ($httpCode == 200) {
            echo "<p style='color: green;'>✓ HTTP 200 OK</p>";
            
            // Check for PHP errors in response
            if (strpos($response, 'Fatal error') !== false || strpos($response, 'Parse error') !== false) {
                echo "<p style='color: red;'>✗ PHP Error detected in response</p>";
                // Extract error
                preg_match('/(Fatal error|Parse error):.*?</', $response, $matches);
                if (!empty($matches)) {
                    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($matches[0]) . "</pre>";
                }
            } else if (strpos($response, 'Warning:') !== false) {
                echo "<p style='color: orange;'>⚠ PHP Warning detected</p>";
            } else {
                echo "<p style='color: green;'>✓ No PHP errors detected</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ HTTP " . $httpCode . "</p>";
        }
    }
    echo "<hr>";
}
?>