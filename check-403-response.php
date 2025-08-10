<?php
// Check what the 403 response contains
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>403 Response Analysis</h1>";

// Check if the saved file exists
$outputFile = 'news-page-output.html';
if (file_exists($outputFile)) {
    $content = file_get_contents($outputFile);
    echo "<h2>403 Response Content (" . strlen($content) . " characters):</h2>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ccc;'>";
    echo htmlspecialchars($content);
    echo "</pre>";
} else {
    echo "<p>Output file not found</p>";
}

// Test direct access to news page from server
echo "<h2>Testing Internal Access:</h2>";

// Test different news URLs
$testUrls = [
    '/news',
    '/news-new.php',
    '/pages/common/news/news.php'
];

foreach ($testUrls as $testUrl) {
    echo "<h3>Testing: $testUrl</h3>";
    
    if ($testUrl[0] === '/') {
        // Internal server access
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $testUrl;
        
        if (file_exists($fullPath)) {
            echo "<p style='color: green;'>✓ File exists at: $fullPath</p>";
            
            // Try to include it
            ob_start();
            try {
                if ($testUrl === '/news') {
                    // This is likely a virtual path
                    echo "<p>Virtual path - cannot include directly</p>";
                } else {
                    include $fullPath;
                    $output = ob_get_clean();
                    echo "<p>Include successful - output length: " . strlen($output) . " characters</p>";
                    
                    if (strlen($output) > 0) {
                        echo "<p>First 200 characters:</p>";
                        echo "<pre>" . htmlspecialchars(substr($output, 0, 200)) . "...</pre>";
                    }
                }
            } catch (Exception $e) {
                ob_end_clean();
                echo "<p style='color: red;'>Include failed: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ File not found at: $fullPath</p>";
        }
    }
}

// Check .htaccess rules for news
echo "<h2>Checking .htaccess Rules:</h2>";
$htaccessFile = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
if (file_exists($htaccessFile)) {
    $htaccess = file_get_contents($htaccessFile);
    echo "<p>✓ .htaccess file exists (" . strlen($htaccess) . " characters)</p>";
    
    // Look for news-related rules
    $lines = explode("\n", $htaccess);
    $newsRules = [];
    foreach ($lines as $i => $line) {
        if (stripos($line, 'news') !== false) {
            $newsRules[] = ($i + 1) . ": " . $line;
        }
    }
    
    if (!empty($newsRules)) {
        echo "<p>Found " . count($newsRules) . " news-related rules:</p>";
        echo "<pre style='background: #f5f5f5; padding: 10px;'>";
        foreach ($newsRules as $rule) {
            echo htmlspecialchars($rule) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<p>No news-related .htaccess rules found</p>";
    }
    
    // Look for Deny/Allow rules
    $restrictionRules = [];
    foreach ($lines as $i => $line) {
        if (stripos($line, 'deny') !== false || stripos($line, 'allow') !== false || stripos($line, '403') !== false) {
            $restrictionRules[] = ($i + 1) . ": " . $line;
        }
    }
    
    if (!empty($restrictionRules)) {
        echo "<p>Found " . count($restrictionRules) . " restriction rules:</p>";
        echo "<pre style='background: #fff3cd; padding: 10px;'>";
        foreach ($restrictionRules as $rule) {
            echo htmlspecialchars($rule) . "\n";
        }
        echo "</pre>";
    }
} else {
    echo "<p style='color: red;'>✗ .htaccess file not found</p>";
}
?>