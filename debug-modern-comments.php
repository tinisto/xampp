<?php
// Debug modern comments component
require_once 'database/db_connections.php';

// Simulate post page variables
$postData = [
    'id' => 124,
    'title_post' => 'Test Post',
    'url_slug' => 'kogda-ege-ostalis-pozadi'
];
$rowPost = $postData;

// Set up variables as post-content-professional.php does
$entityType = 'post';
$entityId = $rowPost['id'];

echo "<h1>Debug Modern Comments Component</h1>";
echo "<p>Variables set by post page:</p>";
echo "<pre>";
echo "\$entityType = '$entityType'\n";
echo "\$entityId = $entityId\n";
echo "</pre>";

echo "<hr>";
echo "<h2>Including modern-comments-component.php:</h2>";

// Add debug output to see what happens
ob_start();
include $_SERVER['DOCUMENT_ROOT'] . '/comments/modern-comments-component.php';
$output = ob_get_clean();

// Check what variables were set
echo "<h3>Variables after include:</h3>";
echo "<pre>";
echo "\$entity_type = " . (isset($entity_type) ? "'$entity_type'" : "NOT SET") . "\n";
echo "\$entity_id = " . (isset($entity_id) ? $entity_id : "NOT SET") . "\n";
echo "\$commentsCount = " . (isset($commentsCount) ? $commentsCount : "NOT SET") . "\n";
echo "</pre>";

echo "<h3>Comments component output:</h3>";
echo "<div style='border: 1px solid #ccc; padding: 20px;'>";
echo $output;
echo "</div>";

// Direct database check
echo "<h3>Direct Database Check:</h3>";
$query = "SELECT COUNT(*) as count FROM comments WHERE entity_id = 124 AND entity_type = 'post'";
$result = mysqli_query($connection, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "<p>Comments in database for post 124: " . $row['count'] . "</p>";
} else {
    echo "<p>Error: " . mysqli_error($connection) . "</p>";
}

// Check what's happening with the prepared statement
echo "<h3>Testing Prepared Statement:</h3>";
$test_entity_id = 124;
$test_entity_type = 'post';
$testQuery = "SELECT COUNT(*) as count FROM comments WHERE entity_id = ? AND entity_type = ?";
$testStmt = $connection->prepare($testQuery);
if ($testStmt) {
    echo "<p>✅ Statement prepared successfully</p>";
    $testStmt->bind_param("is", $test_entity_id, $test_entity_type);
    echo "<p>✅ Parameters bound (id: $test_entity_id, type: $test_entity_type)</p>";
    
    if ($testStmt->execute()) {
        echo "<p>✅ Statement executed</p>";
        $testResult = $testStmt->get_result();
        $count = $testResult->fetch_assoc()['count'];
        echo "<p>Result: $count comments found</p>";
    } else {
        echo "<p>❌ Execute failed: " . $testStmt->error . "</p>";
    }
    $testStmt->close();
} else {
    echo "<p>❌ Prepare failed: " . $connection->error . "</p>";
}

// List all comments to see what's in the database
echo "<h3>All Comments for Post 124:</h3>";
$listQuery = "SELECT id, entity_id, entity_type, parent_id, comment_text FROM comments WHERE entity_id = 124 LIMIT 10";
$listResult = mysqli_query($connection, $listQuery);
if ($listResult) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>entity_id</th><th>entity_type</th><th>parent_id</th><th>comment_text</th></tr>";
    while ($comment = mysqli_fetch_assoc($listResult)) {
        echo "<tr>";
        echo "<td>{$comment['id']}</td>";
        echo "<td>{$comment['entity_id']}</td>";
        echo "<td>{$comment['entity_type']}</td>";
        echo "<td>{$comment['parent_id']}</td>";
        echo "<td>" . substr($comment['comment_text'], 0, 50) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>