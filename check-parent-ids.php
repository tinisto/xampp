<?php
require_once 'database/db_connections.php';

echo "<h1>Check Comment Parent IDs</h1>";

// Check parent_id values for post 124
echo "<h2>Post 124 Comments - Parent ID Status:</h2>";
$query = "SELECT id, entity_id, entity_type, parent_id, comment_text FROM comments WHERE entity_id = 124 AND entity_type = 'post'";
$result = mysqli_query($connection, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Entity ID</th><th>Entity Type</th><th>Parent ID</th><th>Is NULL?</th><th>Comment (preview)</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $preview = substr($row['comment_text'], 0, 50) . '...';
        $isNull = is_null($row['parent_id']) ? 'YES' : 'NO';
        $parentDisplay = is_null($row['parent_id']) ? 'NULL' : $row['parent_id'];
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['entity_id']}</td>";
        echo "<td>{$row['entity_type']}</td>";
        echo "<td>{$parentDisplay}</td>";
        echo "<td>{$isNull}</td>";
        echo "<td>" . htmlspecialchars($preview) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Count comments by parent_id status
echo "<h2>Parent ID Distribution:</h2>";
$query = "SELECT 
    CASE 
        WHEN parent_id IS NULL THEN 'NULL'
        WHEN parent_id = 0 THEN '0'
        ELSE 'Other'
    END as parent_status,
    COUNT(*) as count
FROM comments 
WHERE entity_type = 'post'
GROUP BY parent_status";

$result = mysqli_query($connection, $query);
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Parent Status</th><th>Count</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['parent_status']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
}

// Fix NULL parent_ids
echo "<h2>Fixing NULL parent_ids...</h2>";
$fixQuery = "UPDATE comments SET parent_id = 0 WHERE parent_id IS NULL";
$result = mysqli_query($connection, $fixQuery);

if ($result) {
    $affected = mysqli_affected_rows($connection);
    echo "<p>✅ Fixed $affected comments with NULL parent_id (set to 0)</p>";
} else {
    echo "<p>❌ Error: " . mysqli_error($connection) . "</p>";
}

// Check post 124 after fix
echo "<h2>Post 124 Comments After Fix:</h2>";
$query = "SELECT COUNT(*) as total, 
         SUM(CASE WHEN parent_id = 0 THEN 1 ELSE 0 END) as top_level
         FROM comments WHERE entity_id = 124 AND entity_type = 'post'";
$result = mysqli_query($connection, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "<p>Total comments: {$row['total']}</p>";
    echo "<p>Top-level comments (parent_id = 0): {$row['top_level']}</p>";
}

echo "<hr>";
echo "<p><a href='/post/kogda-ege-ostalis-pozadi'>Test the post now</a></p>";
?>