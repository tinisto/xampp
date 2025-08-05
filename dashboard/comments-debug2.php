<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once dirname(__DIR__) . '/config/loadEnv.php';
require_once dirname(__DIR__) . '/database/db_connections.php';

// Get comments with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$sql = "SELECT c.*, u.first_name, u.last_name, u.email
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.id 
        ORDER BY c.date DESC 
        LIMIT ? OFFSET ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$comments = $stmt->get_result();

// Get total count
$countSql = "SELECT COUNT(*) as total FROM comments";
$countResult = $connection->query($countSql);
$totalComments = $countResult->fetch_assoc()['total'];

echo "<h2>Debug Info:</h2>";
echo "Total comments: " . $totalComments . "<br>";
echo "Page: " . $page . "<br>";
echo "Limit: " . $limit . "<br>";
echo "Offset: " . $offset . "<br>";
echo "Comments fetched: " . $comments->num_rows . "<br><br>";

echo "<h3>First 5 comments:</h3>";
$count = 0;
while ($comment = $comments->fetch_assoc()) {
    echo "Comment #" . ($count + 1) . ":<br>";
    echo "ID: " . $comment['id'] . "<br>";
    echo "Author: " . ($comment['author_of_comment'] ?? 'N/A') . "<br>";
    echo "User: " . $comment['first_name'] . " " . $comment['last_name'] . " (" . $comment['email'] . ")<br>";
    echo "Entity: " . $comment['entity_type'] . " (ID: " . $comment['id_entity'] . ")<br>";
    echo "Text: " . htmlspecialchars(substr($comment['comment_text'], 0, 50)) . "...<br>";
    echo "Date: " . $comment['date'] . "<br><br>";
    
    $count++;
    if ($count >= 5) break;
}

if ($comments->num_rows == 0) {
    echo "No comments found!<br>";
}
?>