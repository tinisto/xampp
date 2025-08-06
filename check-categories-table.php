<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

echo "<h2>All Categories:</h2>";
$query = "SELECT * FROM categories ORDER BY id_category";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Title</th><th>URL</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_category'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_category']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_category']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No categories found";
}

echo "<h2>Posts with category = 1:</h2>";
$query = "SELECT title_post, category FROM posts WHERE category = 1 LIMIT 5";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<li>" . htmlspecialchars($row['title_post']) . " (category: " . $row['category'] . ")</li>";
    }
    echo "</ul>";
}

$connection->close();
?>