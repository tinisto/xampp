<?php
// Debug version of post.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Output buffer to catch any redirects
ob_start();

echo "<!DOCTYPE html><html><body>";
echo "<h1>Post Debug</h1>";
echo "<p>Script: " . __FILE__ . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>GET params: " . print_r($_GET, true) . "</p>";

// Check if url_post is provided
if (!isset($_GET['url_post']) || empty($_GET['url_post'])) {
    echo "<h2 style='color:red;'>No url_post parameter!</h2>";
    echo "<p>This would normally redirect to /404</p>";
} else {
    echo "<h2 style='color:green;'>url_post = " . htmlspecialchars($_GET['url_post']) . "</h2>";
    
    // Try to load database
    $db_file = $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    if (file_exists($db_file)) {
        echo "<p>Loading database...</p>";
        require_once $db_file;
        
        if (isset($connection)) {
            echo "<p>✓ Database loaded</p>";
            
            // Try to fetch post
            $url_post = mysqli_real_escape_string($connection, $_GET['url_post']);
            $query = "SELECT * FROM posts WHERE url_slug = '$url_post'";
            $result = mysqli_query($connection, $query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $post = mysqli_fetch_assoc($result);
                echo "<h3>✓ Post found: " . htmlspecialchars($post['title_post']) . "</h3>";
            } else {
                echo "<p style='color:red;'>Post not found in database</p>";
            }
        }
    }
}

echo "</body></html>";

// Get the output
$output = ob_get_contents();
ob_end_clean();

// Check if any redirect was attempted
$headers = headers_list();
$has_redirect = false;
foreach ($headers as $header) {
    if (stripos($header, 'Location:') !== false) {
        $has_redirect = true;
        echo "REDIRECT DETECTED: $header<br>";
    }
}

if (!$has_redirect) {
    echo $output;
}
?>