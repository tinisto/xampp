<?php
echo "<h2>Verify Server File Content</h2>";

$file = $_SERVER['DOCUMENT_ROOT'] . '/pages/login/login_process_simple.php';

echo "<h3>File: /pages/login/login_process_simple.php</h3>";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($file)) . "<br>";
echo "Size: " . filesize($file) . " bytes<br><br>";

echo "<h3>Key parts of the file:</h3>";
$content = file_get_contents($file);

// Check for redirect parameter extraction
if (strpos($content, '$redirect = $_POST[\'redirect\'] ?? null;') !== false) {
    echo "✓ Extracts redirect parameter<br>";
} else {
    echo "✗ Missing redirect parameter extraction<br>";
}

// Check for new redirect logic
if (strpos($content, 'strpos($redirect, \'/\') === 0') !== false) {
    echo "✓ Contains new redirect security check<br>";
} else {
    echo "✗ Missing new redirect security check<br>";
}

// Show the redirect section
echo "<h3>Redirect Logic Section:</h3>";
$lines = explode("\n", $content);
$inRedirectSection = false;
$redirectLines = [];

foreach ($lines as $i => $line) {
    if (strpos($line, 'Redirect based on redirect parameter') !== false) {
        $inRedirectSection = true;
    }
    
    if ($inRedirectSection) {
        $redirectLines[] = ($i + 1) . ": " . htmlspecialchars($line);
        
        if (strpos($line, 'exit();') !== false && count($redirectLines) > 10) {
            break;
        }
    }
}

echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
echo implode("\n", $redirectLines);
echo "</pre>";
?>