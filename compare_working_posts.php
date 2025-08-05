<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Compare Working vs Non-Working Posts</h2>";

// Working posts
$working = ['prinosit-dobro-lyudyam', 'proektirovanie-zdaniy'];
// Non-working posts (sample)
$not_working = ['ledi-v-pogonah', 'kogda-ege-ostalis-pozadi', 'ya-reshila-stat-vrachom'];

echo "<h3>Working Posts Analysis:</h3>";
echo "<table border='1'>";
echo "<tr><th>URL</th><th>ID</th><th>Title Length</th><th>Text Length</th><th>Has Images</th><th>Special Fields</th></tr>";

foreach ($working as $url) {
    $query = "SELECT * FROM posts WHERE url_slug = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $url);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo "<tr style='background: #d4edda;'>";
        echo "<td>" . htmlspecialchars($url) . "</td>";
        echo "<td>" . $row['id_post'] . "</td>";
        echo "<td>" . strlen($row['title_post']) . "</td>";
        echo "<td>" . strlen($row['text_post']) . "</td>";
        
        // Check for images
        $img1 = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row['id_post']}_1.jpg";
        echo "<td>" . (file_exists($img1) ? "Yes" : "No") . "</td>";
        
        // Check special fields
        $special = [];
        if (!empty($row['meta_d_post'])) $special[] = "meta_d";
        if (!empty($row['meta_k_post'])) $special[] = "meta_k";
        if (!empty($row['author_post'])) $special[] = "author";
        echo "<td>" . implode(", ", $special) . "</td>";
        echo "</tr>";
    }
    mysqli_stmt_close($stmt);
}

echo "</table>";

echo "<h3>Non-Working Posts Analysis:</h3>";
echo "<table border='1'>";
echo "<tr><th>URL</th><th>ID</th><th>Title Length</th><th>Text Length</th><th>Has Images</th><th>Special Fields</th></tr>";

foreach ($not_working as $url) {
    $query = "SELECT * FROM posts WHERE url_slug = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $url);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo "<tr style='background: #f8d7da;'>";
        echo "<td>" . htmlspecialchars($url) . "</td>";
        echo "<td>" . $row['id_post'] . "</td>";
        echo "<td>" . strlen($row['title_post']) . "</td>";
        echo "<td>" . strlen($row['text_post']) . "</td>";
        
        // Check for images
        $img1 = $_SERVER['DOCUMENT_ROOT'] . "/images/posts-images/{$row['id_post']}_1.jpg";
        echo "<td>" . (file_exists($img1) ? "Yes" : "No") . "</td>";
        
        // Check special fields
        $special = [];
        if (!empty($row['meta_d_post'])) $special[] = "meta_d";
        if (!empty($row['meta_k_post'])) $special[] = "meta_k";
        if (!empty($row['author_post'])) $special[] = "author";
        echo "<td>" . implode(", ", $special) . "</td>";
        echo "</tr>";
    }
    mysqli_stmt_close($stmt);
}

echo "</table>";

// Test with minimal content
echo "<h3>Test Minimal Post Display:</h3>";
echo "<p>Testing if we can display a non-working post with minimal content...</p>";

$test_url = 'ledi-v-pogonah';
$query = "SELECT * FROM posts WHERE url_slug = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "s", $test_url);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo "<div style='border: 1px solid #ccc; padding: 20px; margin: 20px 0;'>";
    echo "<h2>" . htmlspecialchars($row['title_post']) . "</h2>";
    echo "<p>Date: " . $row['date_post'] . "</p>";
    echo "<p>Views: " . $row['view_post'] . "</p>";
    echo "<p>First 200 chars of text:</p>";
    echo "<div style='background: #f5f5f5; padding: 10px;'>";
    echo htmlspecialchars(substr($row['text_post'], 0, 200)) . "...";
    echo "</div>";
    echo "<a href='/post/" . $row['url_post'] . "' target='_blank' style='display: inline-block; margin-top: 10px; padding: 10px 20px; background: #28a745; color: white; text-decoration: none;'>Try to View Full Post</a>";
    echo "</div>";
}

mysqli_close($connection);
?>