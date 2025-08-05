<?php
// Fix comment entity_type inconsistencies
require_once 'database/db_connections.php';

echo "<h1>Fix Comment Entity Types</h1>";

// First, let's see what we're dealing with
echo "<h2>Current Entity Type Distribution:</h2>";
$query = "SELECT entity_type, COUNT(*) as count FROM comments GROUP BY entity_type ORDER BY count DESC";
$result = mysqli_query($connection, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Entity Type</th><th>Count</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['entity_type']}</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
}

// Check post 124 specifically
echo "<h2>Post 124 Comments Before Fix:</h2>";
$query = "SELECT id, entity_id, entity_type, comment_text FROM comments WHERE entity_id = 124 ORDER BY entity_type";
$result = mysqli_query($connection, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Entity ID</th><th>Entity Type</th><th>Comment (preview)</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $preview = substr($row['comment_text'], 0, 50) . '...';
        echo "<tr><td>{$row['id']}</td><td>{$row['entity_id']}</td><td>{$row['entity_type']}</td><td>" . htmlspecialchars($preview) . "</td></tr>";
    }
    echo "</table>";
}

// Fix the issue
echo "<h2>Fixing Entity Types...</h2>";

// For posts, we need to ensure all comments have entity_type = 'post'
// We can identify post comments by checking if entity_id exists in posts table
$fixQuery = "UPDATE comments c 
             INNER JOIN posts p ON c.entity_id = p.id 
             SET c.entity_type = 'post' 
             WHERE c.entity_type != 'post'";

$result = mysqli_query($connection, $fixQuery);

if ($result) {
    $affected = mysqli_affected_rows($connection);
    echo "<p>✅ Fixed $affected comments that were linked to posts but had wrong entity_type</p>";
} else {
    echo "<p>❌ Error: " . mysqli_error($connection) . "</p>";
}

// Check post 124 after fix
echo "<h2>Post 124 Comments After Fix:</h2>";
$query = "SELECT id, entity_id, entity_type, comment_text FROM comments WHERE entity_id = 124";
$result = mysqli_query($connection, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Entity ID</th><th>Entity Type</th><th>Comment (preview)</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $preview = substr($row['comment_text'], 0, 50) . '...';
        echo "<tr><td>{$row['id']}</td><td>{$row['entity_id']}</td><td>{$row['entity_type']}</td><td>" . htmlspecialchars($preview) . "</td></tr>";
    }
    echo "</table>";
    
    $count = mysqli_num_rows($result);
    echo "<p><strong>Total comments for post 124: $count</strong></p>";
}

echo "<hr>";
echo "<p><a href='/post/kogda-ege-ostalis-pozadi'>Test the post now</a></p>";
?>