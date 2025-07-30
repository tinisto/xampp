<?php
// Test file to verify deployment and functionality

// Test 1: Check if new includes work
echo "<h2>Deployment Test</h2>";

echo "<h3>1. Testing includes:</h3>";
$includes = [
    '/includes/Database.php',
    '/includes/Security.php',
    '/includes/helpers.php',
    '/includes/Cache.php'
];

foreach ($includes as $file) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file NOT FOUND<br>";
    }
}

// Test 2: Check if we can use the Security class
echo "<h3>2. Testing Security class:</h3>";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/includes/Security.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/Security.php';
    $token = Security::generateCSRFToken();
    echo "✅ CSRF Token generated: " . substr($token, 0, 10) . "...<br>";
    echo "✅ Security class is working<br>";
} else {
    echo "❌ Security class not found<br>";
}

// Test 3: Show current directory structure
echo "<h3>3. Current directory:</h3>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script location: " . __FILE__ . "<br>";

// Test 4: Database connection
echo "<h3>4. Testing database connection:</h3>";
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php')) {
    echo "✅ Database connection file exists<br>";
} else {
    echo "❌ Database connection file not found<br>";
}

echo "<hr>";
echo "<p>If you see mostly ❌, the files might be in a different location on the server.</p>";
echo "<p><strong>Delete this file after testing!</strong></p>";