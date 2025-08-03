<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Analyzing HTML Entities in Posts</h2>";

// Get sample posts with HTML entities
$query = "SELECT id_post, title_post, 
          SUBSTRING(text_post, 1, 200) as text_sample,
          LENGTH(text_post) as text_length
          FROM posts 
          WHERE text_post LIKE '%&%' 
          LIMIT 20";

$result = mysqli_query($connection, $query);

echo "<h3>Posts with HTML Entities:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Title</th><th>Sample Text (Raw)</th><th>Sample Text (Decoded)</th><th>Entities Found</th></tr>";

$entities_count = [];

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['id_post']}</td>";
    echo "<td>" . htmlspecialchars($row['title_post']) . "</td>";
    echo "<td style='font-size: 12px;'>" . htmlspecialchars($row['text_sample']) . "</td>";
    echo "<td style='font-size: 12px;'>" . htmlspecialchars(html_entity_decode($row['text_sample'], ENT_QUOTES, 'UTF-8')) . "</td>";
    
    // Find entities
    preg_match_all('/&[a-zA-Z]+;|&#\d+;|&#x[0-9a-fA-F]+;/', $row['text_sample'], $matches);
    $entities = array_unique($matches[0]);
    echo "<td>" . implode(", ", $entities) . "</td>";
    
    foreach ($entities as $entity) {
        if (!isset($entities_count[$entity])) {
            $entities_count[$entity] = 0;
        }
        $entities_count[$entity]++;
    }
    
    echo "</tr>";
}

echo "</table>";

// Show entity statistics
echo "<h3>Common HTML Entities Found:</h3>";
arsort($entities_count);
echo "<table border='1'>";
echo "<tr><th>Entity</th><th>Count</th><th>Decoded</th></tr>";
foreach ($entities_count as $entity => $count) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($entity) . "</td>";
    echo "<td>$count</td>";
    echo "<td>" . html_entity_decode($entity, ENT_QUOTES, 'UTF-8') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Count total affected posts
$count_query = "SELECT COUNT(*) as total FROM posts WHERE text_post LIKE '%&%'";
$count_result = mysqli_query($connection, $count_query);
$count_row = mysqli_fetch_assoc($count_result);

echo "<h3>Summary:</h3>";
echo "<p>Total posts with HTML entities: <strong>{$count_row['total']}</strong></p>";

// Show a preview of what cleaning would do
echo "<h3>Preview of Cleaning (First Post):</h3>";
$preview_query = "SELECT id_post, title_post, text_post FROM posts WHERE text_post LIKE '%&%' LIMIT 1";
$preview_result = mysqli_query($connection, $preview_query);
if ($preview_row = mysqli_fetch_assoc($preview_result)) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<h4>Original (first 500 chars):</h4>";
    echo "<pre style='white-space: pre-wrap;'>" . htmlspecialchars(substr($preview_row['text_post'], 0, 500)) . "</pre>";
    echo "<h4>After Cleaning (first 500 chars):</h4>";
    $cleaned = html_entity_decode($preview_row['text_post'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    echo "<pre style='white-space: pre-wrap;'>" . htmlspecialchars(substr($cleaned, 0, 500)) . "</pre>";
    echo "</div>";
}

mysqli_close($connection);
?>