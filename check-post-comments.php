<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

// Get database connection
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get URL parameter
$url_slug = isset($_GET['url_post']) ? mysqli_real_escape_string($connection, $_GET['url_post']) : '';

echo "<h2>Post Comments Diagnostic</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
</style>";

// 1. Check if we have a URL slug
echo "<div class='section'>";
echo "<h3>1. URL Slug Check</h3>";
if (empty($url_slug)) {
    echo "<p class='warning'>No URL slug provided. Usage: check-post-comments.php?url_post=your-post-slug</p>";
    
    // Show available posts
    echo "<h4>Available Posts:</h4>";
    $query = "SELECT id, title_post, url_slug FROM posts ORDER BY id DESC LIMIT 10";
    $result = mysqli_query($connection, $query);
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th><th>Check Comments</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['title_post']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_slug']) . "</td>";
        echo "<td><a href='?url_post=" . urlencode($row['url_slug']) . "'>Check</a></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    exit();
} else {
    echo "<p class='success'>URL slug provided: <strong>" . htmlspecialchars($url_slug) . "</strong></p>";
}
echo "</div>";

// 2. Get post data
echo "<div class='section'>";
echo "<h3>2. Post Data</h3>";
$query = "SELECT * FROM posts WHERE url_slug = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $url_slug);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo "<p class='success'>Post found!</p>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> {$row['id']}</li>";
    echo "<li><strong>Title:</strong> " . htmlspecialchars($row['title_post']) . "</li>";
    echo "<li><strong>Author:</strong> " . htmlspecialchars($row['author_post']) . "</li>";
    echo "<li><strong>Date:</strong> {$row['date_post']}</li>";
    echo "<li><strong>Views:</strong> {$row['view_post']}</li>";
    echo "</ul>";
    
    $postId = $row['id'];
} else {
    echo "<p class='error'>Post not found with URL slug: " . htmlspecialchars($url_slug) . "</p>";
    echo "</div>";
    exit();
}
mysqli_stmt_close($stmt);
echo "</div>";

// 3. Check variables that would be set in post-content-professional.php
echo "<div class='section'>";
echo "<h3>3. Comment System Variables</h3>";
echo "<p>In post-content-professional.php, these variables are set:</p>";
echo "<ul>";
echo "<li><strong>\$entityType:</strong> 'post'</li>";
echo "<li><strong>\$entityId:</strong> {$postId}</li>";
echo "</ul>";
echo "</div>";

// 4. Check all comments for this post
echo "<div class='section'>";
echo "<h3>4. Comments in Database</h3>";

// First, check total comments for this post
$countQuery = "SELECT COUNT(*) as total FROM comments WHERE entity_id = ? AND entity_type = 'post'";
$countStmt = mysqli_prepare($connection, $countQuery);
mysqli_stmt_bind_param($countStmt, "i", $postId);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$countRow = mysqli_fetch_assoc($countResult);
$totalComments = $countRow['total'];

echo "<p>Total comments for this post: <strong>{$totalComments}</strong></p>";
mysqli_stmt_close($countStmt);

// Get actual comments
$commentsQuery = "SELECT c.*, u.email, u.first_name, u.last_name 
                  FROM comments c 
                  LEFT JOIN users u ON c.user_id = u.id 
                  WHERE c.entity_id = ? AND c.entity_type = 'post' 
                  ORDER BY c.date DESC";
$commentsStmt = mysqli_prepare($connection, $commentsQuery);
mysqli_stmt_bind_param($commentsStmt, "i", $postId);
mysqli_stmt_execute($commentsStmt);
$commentsResult = mysqli_stmt_get_result($commentsStmt);

if (mysqli_num_rows($commentsResult) > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>User</th><th>Comment</th><th>Date</th><th>Parent ID</th></tr>";
    while ($comment = mysqli_fetch_assoc($commentsResult)) {
        $userName = $comment['first_name'] . ' ' . $comment['last_name'];
        if (trim($userName) == '') {
            $userName = $comment['email'] ?: 'Guest';
        }
        
        echo "<tr>";
        echo "<td>{$comment['id']}</td>";
        echo "<td>" . htmlspecialchars($userName) . "</td>";
        echo "<td>" . htmlspecialchars(substr($comment['comment_text'], 0, 100)) . "...</td>";
        echo "<td>{$comment['date']}</td>";
        echo "<td>{$comment['parent_id']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>No comments found for this post.</p>";
}
mysqli_stmt_close($commentsStmt);
echo "</div>";

// 5. Check if comments table structure is correct
echo "<div class='section'>";
echo "<h3>5. Comments Table Structure</h3>";
$tableQuery = "DESCRIBE comments";
$tableResult = mysqli_query($connection, $tableQuery);

echo "<table>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($field = mysqli_fetch_assoc($tableResult)) {
    echo "<tr>";
    foreach ($field as $value) {
        echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
    }
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// 6. Try adding a test comment
if (isset($_GET['add_test_comment']) && $_GET['add_test_comment'] == '1') {
    echo "<div class='section'>";
    echo "<h3>6. Adding Test Comment</h3>";
    
    $testComment = "Test comment added at " . date('Y-m-d H:i:s');
    $testUserId = 1; // Assuming admin user ID is 1
    $testQuery = "INSERT INTO comments (entity_id, user_id, comment_text, parent_id, entity_type, date) VALUES (?, ?, ?, 0, 'post', NOW())";
    $testStmt = mysqli_prepare($connection, $testQuery);
    mysqli_stmt_bind_param($testStmt, "iis", $postId, $testUserId, $testComment);
    
    if (mysqli_stmt_execute($testStmt)) {
        echo "<p class='success'>Test comment added successfully!</p>";
        echo "<p>Comment ID: " . mysqli_insert_id($connection) . "</p>";
    } else {
        echo "<p class='error'>Failed to add test comment: " . mysqli_error($connection) . "</p>";
    }
    mysqli_stmt_close($testStmt);
    echo "</div>";
} else {
    echo "<div class='section'>";
    echo "<p><a href='?url_post=" . urlencode($url_slug) . "&add_test_comment=1' onclick='return confirm(\"Add a test comment?\")'>Add Test Comment</a></p>";
    echo "</div>";
}

// 7. Direct link to post
echo "<div class='section'>";
echo "<h3>7. View Post</h3>";
echo "<p><a href='/post/{$url_slug}' target='_blank'>View this post on the website →</a></p>";
echo "</div>";

mysqli_close($connection);
?>