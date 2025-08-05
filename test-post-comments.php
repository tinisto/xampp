<?php
// Direct test of post comments
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Get a sample post
$query = "SELECT * FROM posts ORDER BY id DESC LIMIT 1";
$result = mysqli_query($connection, $query);
$post = mysqli_fetch_assoc($result);

if (!$post) {
    die("No posts found in database");
}

echo "<h2>Testing Comments for Post: " . htmlspecialchars($post['title_post']) . "</h2>";
echo "<p>Post ID: " . $post['id'] . "</p>";
echo "<p>URL: <a href='/post/" . $post['url_slug'] . "'>View Post</a></p>";

// Check what ID field exists
echo "<h3>Post ID Fields:</h3>";
echo "<ul>";
if (isset($post['id'])) echo "<li>id: " . $post['id'] . "</li>";
if (isset($post['id_post'])) echo "<li>id_post: " . $post['id_post'] . "</li>";
echo "</ul>";

// Set up for comments
$entityType = 'post';
$entityId = $post['id'] ?? $post['id_post'] ?? null;

echo "<h3>Comment Variables:</h3>";
echo "<ul>";
echo "<li>entityType: " . $entityType . "</li>";
echo "<li>entityId: " . $entityId . "</li>";
echo "</ul>";

// Check comments for this post
$commentsQuery = "SELECT COUNT(*) as count FROM comments WHERE entity_id = ? AND entity_type = ?";
$stmt = mysqli_prepare($connection, $commentsQuery);
mysqli_stmt_bind_param($stmt, "is", $entityId, $entityType);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$count = mysqli_fetch_assoc($result)['count'];

echo "<h3>Comments Count: " . $count . "</h3>";

// Show recent comments
$recentQuery = "SELECT c.*, u.email, u.first_name, u.last_name 
                FROM comments c 
                LEFT JOIN users u ON c.user_id = u.id 
                WHERE c.entity_type = 'post' 
                ORDER BY c.date DESC 
                LIMIT 5";
$recentResult = mysqli_query($connection, $recentQuery);

echo "<h3>Recent Post Comments (any post):</h3>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Entity ID</th><th>User</th><th>Comment</th><th>Date</th></tr>";
while ($comment = mysqli_fetch_assoc($recentResult)) {
    echo "<tr>";
    echo "<td>" . $comment['id'] . "</td>";
    echo "<td>" . $comment['entity_id'] . "</td>";
    echo "<td>" . htmlspecialchars($comment['email'] ?? 'Guest') . "</td>";
    echo "<td>" . htmlspecialchars(substr($comment['comment_text'], 0, 50)) . "...</td>";
    echo "<td>" . $comment['date'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Include the comments component
echo "<h3>Comments Component Output:</h3>";
echo "<div style='border: 1px solid #ccc; padding: 20px;'>";
include $_SERVER['DOCUMENT_ROOT'] . '/comments/modern-comments-component.php';
echo "</div>";

mysqli_close($connection);
?>