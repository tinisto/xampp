<?php
// Fix the VPO/SPO error display issue
echo "<h1>Fix VPO/SPO Error Display</h1>";

$file_path = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';

if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    
    // Show current SQL query
    preg_match('/\$sql = "([^"]+)";/', $content, $matches);
    if (isset($matches[1])) {
        echo "<p>Current SQL query: <code>" . htmlspecialchars($matches[1]) . "</code></p>";
    }
    
    // Check what might be causing the error
    if (strpos($content, 'if ($result && $result->num_rows > 0):') !== false) {
        echo "<p>✅ Has result check</p>";
    }
    
    // Find the issue - the query might be failing due to missing connection
    echo "<h2>Possible Issues:</h2>";
    echo "<ol>";
    echo "<li>Database connection might be closed before query</li>";
    echo "<li>Query might have syntax error</li>";
    echo "<li>Error suppression might be hiding the real issue</li>";
    echo "</ol>";
    
    if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
        // Add error logging
        $search = '$result = $connection->query($sql);';
        $replace = '$result = $connection->query($sql);
                if (!$result) {
                    error_log("VPO Query Error: " . $connection->error);
                }';
        
        $content = str_replace($search, $replace, $content);
        
        if (file_put_contents($file_path, $content)) {
            echo "<p style='color: green;'>✅ Added error logging to the file</p>";
        }
    } else {
        echo "<p><a href='?fix=yes'>Add Error Logging</a></p>";
    }
} else {
    echo "<p style='color: red;'>❌ File not found!</p>";
}

// Check error log
$error_log = '/var/log/apache2/error.log';
if (file_exists($error_log) && is_readable($error_log)) {
    echo "<h2>Recent Error Log:</h2>";
    $lines = array_slice(file($error_log), -10);
    echo "<pre>";
    foreach ($lines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
}
?>