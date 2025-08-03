<?php
// Test wrapper to ensure post data is available
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Test with a known post
$url_post = 'ledi-v-pogonah';

// Fetch post data
$query = "SELECT * FROM posts WHERE url_post = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $url_post);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Set all the variables that post-content.php expects
    $postData = $row;
    $rowPost = $row;
    $pageTitle = $row['title_post'];
    
    echo "<h2>Post Data Set Successfully</h2>";
    echo "<p>Title: " . htmlspecialchars($pageTitle) . "</p>";
    echo "<p>ID: " . $row['id_post'] . "</p>";
    
    // Now include post-content.php
    echo "<h3>Including post-content.php:</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 20px;'>";
    
    include $_SERVER['DOCUMENT_ROOT'] . '/pages/post/post-content.php';
    
    echo "</div>";
} else {
    echo "<p>Post not found in database</p>";
}

mysqli_stmt_close($stmt);
?>