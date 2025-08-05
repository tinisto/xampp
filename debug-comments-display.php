<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/getEntityIdFromURL.php';

// Debug post ID detection for "ledi-v-pogonah"
$url_slug = "ledi-v-pogonah";

echo "<h1>Comments Debug for Post: $url_slug</h1>";

// 1. Check if post exists
echo "<h2>1. Post Lookup:</h2>";
$postQuery = "SELECT id_post, title_post, url_post FROM posts WHERE url_slug = ?";
$postStmt = $connection->prepare($postQuery);
$postStmt->bind_param("s", $url_slug);
$postStmt->execute();
$postResult = $postStmt->get_result();

if ($postResult->num_rows > 0) {
    $post = $postResult->fetch_assoc();
    echo "✅ Post found: ID = " . $post['id_post'] . ", Title = " . $post['title_post'] . "<br>";
    $post_id = $post['id_post'];
} else {
    echo "❌ Post not found with URL slug: $url_slug<br>";
    $post_id = null;
}

// 2. Check what getEntityIdFromPostURL returns
echo "<h2>2. Entity ID Function Test:</h2>";
$_SERVER['REQUEST_URI'] = "/post/ledi-v-pogonah";
$entityData = getEntityIdFromPostURL($connection);
echo "Function returned: <pre>" . print_r($entityData, true) . "</pre>";

// 3. Check comments in database
echo "<h2>3. Comments in Database:</h2>";
if ($post_id) {
    // Check with correct post ID
    $commentsQuery = "SELECT COUNT(*) as count FROM comments WHERE id_entity = ? AND entity_type = 'post'";
    $commentsStmt = $connection->prepare($commentsQuery);
    $commentsStmt->bind_param("i", $post_id);
    $commentsStmt->execute();
    $commentsResult = $commentsStmt->get_result();
    $count = $commentsResult->fetch_assoc()['count'];
    
    echo "Comments for post ID $post_id: $count<br>";
    
    // Show actual comments
    $showCommentsQuery = "SELECT * FROM comments WHERE id_entity = ? AND entity_type = 'post' ORDER BY date DESC";
    $showStmt = $connection->prepare($showCommentsQuery);
    $showStmt->bind_param("i", $post_id);
    $showStmt->execute();
    $showResult = $showStmt->get_result();
    
    echo "<h3>Recent Comments:</h3>";
    while ($comment = $showResult->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px 0;'>";
        echo "ID: " . $comment['id'] . "<br>";
        echo "Entity ID: " . $comment['id_entity'] . "<br>";
        echo "Entity Type: " . $comment['entity_type'] . "<br>";
        echo "User ID: " . $comment['user_id'] . "<br>";
        echo "Text: " . htmlspecialchars(substr($comment['comment_text'], 0, 100)) . "...<br>";
        echo "Date: " . $comment['date'] . "<br>";
        echo "</div>";
    }
}

// 4. Check if there are comments with wrong entity IDs
echo "<h2>4. All Comments for Entity Type 'post':</h2>";
$allPostCommentsQuery = "SELECT id_entity, COUNT(*) as count FROM comments WHERE entity_type = 'post' GROUP BY id_entity";
$allPostCommentsResult = $connection->query($allPostCommentsQuery);

while ($row = $allPostCommentsResult->fetch_assoc()) {
    echo "Entity ID " . $row['id_entity'] . ": " . $row['count'] . " comments<br>";
}

// 5. Test the modern comments component logic
echo "<h2>5. Modern Comments Component Logic Test:</h2>";
$currentUrl = '/post/ledi-v-pogonah';

preg_match('/\/post\/([\w-]+)/', $currentUrl, $postMatches);
if (isset($postMatches[1])) {
    echo "URL regex matched: " . $postMatches[1] . "<br>";
    $entity_type = 'post';
    $entityData = getEntityIdFromPostURL($connection);
    $entity_id = $entityData['id_entity'];
    echo "Entity type: $entity_type<br>";
    echo "Entity ID: $entity_id<br>";
    
    if ($entity_id) {
        $commentsQuery = "SELECT COUNT(*) as count FROM comments WHERE id_entity = ? AND entity_type = ?";
        $commentsStmt = $connection->prepare($commentsQuery);
        $commentsStmt->bind_param("is", $entity_id, $entity_type);
        $commentsStmt->execute();
        $commentsResult = $commentsStmt->get_result();
        $commentsCount = $commentsResult->fetch_assoc()['count'];
        echo "Comments count for display: $commentsCount<br>";
    }
} else {
    echo "❌ URL regex didn't match<br>";
}
?>