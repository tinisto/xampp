<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Post System Diagnostic</h2>";

// Test 1: Check if we're getting the URL parameter
echo "<h3>1. Testing URL Rewriting:</h3>";
echo "Current URL: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Query String: " . $_SERVER['QUERY_STRING'] . "<br>";
echo "GET parameters: <pre>" . print_r($_GET, true) . "</pre>";

// Test 2: Check if post.php is accessible directly
echo "<h3>2. Direct Access Test:</h3>";
echo "<a href='/pages/post/post.php?url_post=ledi-v-pogonah'>Direct link to post.php with url_post parameter</a><br>";
echo "<a href='/post/ledi-v-pogonah'>Rewritten URL to /post/ledi-v-pogonah</a><br>";

// Test 3: Check what happens when we manually set the parameter
if (isset($_GET['test_post'])) {
    $_GET['url_post'] = 'ledi-v-pogonah';
    echo "<h3>3. Manually Including post.php:</h3>";
    
    // Save current directory
    $currentDir = getcwd();
    
    // Change to post directory
    chdir($_SERVER['DOCUMENT_ROOT'] . '/pages/post');
    
    // Capture output
    ob_start();
    $error = null;
    
    try {
        include 'post.php';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    
    $output = ob_get_clean();
    
    // Restore directory
    chdir($currentDir);
    
    if ($error) {
        echo "<p style='color:red;'>Error: $error</p>";
    } else {
        echo "<div style='border: 1px solid green; padding: 10px;'>";
        echo $output;
        echo "</div>";
    }
} else {
    echo "<h3>3. Manual Test:</h3>";
    echo "<a href='?test_post=1'>Click here to manually test post inclusion</a><br>";
}

// Test 4: Check error log
echo "<h3>4. Checking for Redirects:</h3>";
$error_php = $_SERVER['DOCUMENT_ROOT'] . '/pages/error/error.php';
if (file_exists($error_php)) {
    echo "✓ error.php exists<br>";
    
    // Check first few lines to see what triggers redirect
    $lines = file($error_php, FILE_IGNORE_NEW_LINES);
    echo "First 20 lines of error.php:<br>";
    echo "<pre>";
    for ($i = 0; $i < min(20, count($lines)); $i++) {
        echo ($i + 1) . ": " . htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
}

// Test 5: Check if there's a redirect in the includes
echo "<h3>5. Checking Common Includes for Redirects:</h3>";
$files_to_check = [
    '/common-components/header.php',
    '/includes/functions/redirectToErrorPage.php',
];

foreach ($files_to_check as $file) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $file;
    if (file_exists($full_path)) {
        echo "Checking $file...<br>";
        $content = file_get_contents($full_path);
        if (preg_match_all('/header\s*\(\s*["\']Location:\s*\/error/i', $content, $matches)) {
            echo "<span style='color:red;'>Found redirect to /error in $file</span><br>";
        }
    }
}
?>