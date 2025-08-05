<?php
// Fix comment entity_id migration
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Fix Comment entity_id Migration</h1>";

try {
    // First, check if both columns exist
    echo "<h2>Checking table structure...</h2>";
    $result = $connection->query("SHOW COLUMNS FROM comments");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    
    $has_entity_id = in_array('entity_id', $columns);
    $has_id_entity = in_array('id_entity', $columns);
    
    echo "<p>Has entity_id column: " . ($has_entity_id ? 'YES' : 'NO') . "</p>";
    echo "<p>Has id_entity column: " . ($has_id_entity ? 'YES' : 'NO') . "</p>";
    
    if (!$has_entity_id && $has_id_entity) {
        // Need to rename column
        echo "<h2>Renaming id_entity to entity_id...</h2>";
        $connection->query("ALTER TABLE comments CHANGE COLUMN id_entity entity_id INT");
        echo "<p>✅ Column renamed successfully</p>";
    } elseif ($has_entity_id && $has_id_entity) {
        // Both columns exist - need to migrate data
        echo "<h2>Both columns exist - migrating data...</h2>";
        
        // Copy data from id_entity to entity_id where entity_id is NULL
        $update_query = "UPDATE comments SET entity_id = id_entity WHERE entity_id IS NULL AND id_entity IS NOT NULL";
        $result = $connection->query($update_query);
        $affected = $connection->affected_rows;
        echo "<p>✅ Updated $affected rows</p>";
        
        // Drop the old column
        echo "<h2>Dropping old id_entity column...</h2>";
        $connection->query("ALTER TABLE comments DROP COLUMN id_entity");
        echo "<p>✅ Old column dropped</p>";
    } elseif ($has_entity_id && !$has_id_entity) {
        echo "<p>✅ Table structure is already correct (only entity_id exists)</p>";
    } else {
        echo "<p>❌ ERROR: Neither entity_id nor id_entity column exists!</p>";
    }
    
    // Show current comments
    echo "<h2>Current comments in database:</h2>";
    $comments = $connection->query("SELECT id, entity_id, entity_type, user_id, comment_text, date FROM comments ORDER BY id DESC LIMIT 10");
    
    if ($comments && $comments->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>entity_id</th><th>entity_type</th><th>user_id</th><th>comment_text</th><th>date</th></tr>";
        while ($comment = $comments->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $comment['id'] . "</td>";
            echo "<td>" . $comment['entity_id'] . "</td>";
            echo "<td>" . htmlspecialchars($comment['entity_type']) . "</td>";
            echo "<td>" . $comment['user_id'] . "</td>";
            echo "<td>" . htmlspecialchars(substr($comment['comment_text'], 0, 50)) . "...</td>";
            echo "<td>" . $comment['date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test a specific post
        echo "<h2>Testing specific post comments:</h2>";
        $test_post_id = $comments->fetch_assoc()['entity_id'] ?? 1;
        $test_query = "SELECT COUNT(*) as count FROM comments WHERE entity_id = ? AND entity_type = 'post'";
        $stmt = $connection->prepare($test_query);
        $stmt->bind_param("i", $test_post_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];
        echo "<p>Comments for post ID $test_post_id: $count</p>";
    } else {
        echo "<p>No comments found in database.</p>";
    }
    
    echo "<h2>✅ Migration complete!</h2>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>