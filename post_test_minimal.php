<?php
// Minimal test to see what's happening
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Post Test - Minimal</h1>";
echo "<p>This file is at: " . __FILE__ . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>GET params: " . print_r($_GET, true) . "</p>";

// If this is accessed via /post/something, it should have url_post parameter
if (isset($_GET['url_post'])) {
    echo "<h2>✓ URL parameter received: " . htmlspecialchars($_GET['url_post']) . "</h2>";
    
    // Test database connection
    $db_file = $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    if (file_exists($db_file)) {
        require_once $db_file;
        
        if (isset($connection)) {
            echo "<p>✓ Database connected</p>";
            
            // Try to fetch the post
            $url = mysqli_real_escape_string($connection, $_GET['url_post']);
            $query = "SELECT * FROM posts WHERE url_slug = '$url'";
            $result = mysqli_query($connection, $query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $post = mysqli_fetch_assoc($result);
                echo "<h3>✓ Post found:</h3>";
                echo "<p>ID: " . $post['id_post'] . "</p>";
                echo "<p>Title: " . htmlspecialchars($post['title_post']) . "</p>";
                echo "<p>URL: " . htmlspecialchars($post['url_post']) . "</p>";
            } else {
                echo "<p style='color:red;'>✗ Post not found in database</p>";
            }
        } else {
            echo "<p style='color:red;'>✗ Database connection failed</p>";
        }
    } else {
        echo "<p style='color:red;'>✗ Database file not found</p>";
    }
} else {
    echo "<h2 style='color:red;'>✗ No url_post parameter</h2>";
    echo "<p>This suggests the rewrite rule is not working properly.</p>";
}

// Test links
echo "<hr>";
echo "<h3>Test Links:</h3>";
echo "<a href='/post/ledi-v-pogonah'>Rewrite: /post/ledi-v-pogonah</a><br>";
echo "<a href='/post_test_minimal.php?url_post=ledi-v-pogonah'>Direct: /post_test_minimal.php?url_post=ledi-v-pogonah</a><br>";
?>