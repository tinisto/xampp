<?php
// Check homepage UI elements
require_once 'database/db_modern.php';

echo "<h1>Homepage UI/UX Check</h1>";

// Check if we have content
$db = Database::getInstance();
$conn = $db->getConnection();

echo "<h2>Content Statistics:</h2>";
echo "<ul>";

// Latest posts
$result = $conn->query("SELECT COUNT(*) as count FROM posts WHERE is_published = 1");
if ($result) {
    $count = $result->fetch()['count'];
    echo "<li>Published Posts: " . $count . "</li>";
}

// Latest news
$result = $conn->query("SELECT COUNT(*) as count FROM news WHERE approved = 1");
if ($result) {
    $count = $result->fetch()['count'];
    echo "<li>Approved News: " . $count . "</li>";
}

// Schools
$result = $conn->query("SELECT COUNT(*) as count FROM schools");
if ($result) {
    $count = $result->fetch()['count'];
    echo "<li>Schools: " . $count . "</li>";
}

// Comments
$result = $conn->query("SELECT COUNT(*) as count FROM comments");
if ($result) {
    $count = $result->fetch()['count'];
    echo "<li>Comments: " . $count . "</li>";
}

echo "</ul>";

echo "<h2>Recent Posts (Last 5):</h2>";
$result = $conn->query("SELECT id, title_post, date_post, views FROM posts WHERE is_published = 1 ORDER BY date_post DESC LIMIT 5");
if ($result && $result->rowCount() > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Views</th></tr>";
    while ($row = $result->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_post']) . "</td>";
        echo "<td>" . $row['date_post'] . "</td>";
        echo "<td>" . $row['views'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No published posts found.</p>";
}

echo "<h2>Recent News (Last 5):</h2>";
$result = $conn->query("SELECT id_news, title_news, date_news, view_news FROM news WHERE approved = 1 ORDER BY date_news DESC LIMIT 5");
if ($result && $result->rowCount() > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Title</th><th>Date</th><th>Views</th></tr>";
    while ($row = $result->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['id_news'] . "</td>";
        echo "<td>" . htmlspecialchars($row['title_news']) . "</td>";
        echo "<td>" . $row['date_news'] . "</td>";
        echo "<td>" . $row['view_news'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No approved news found.</p>";
}

echo "<hr>";
echo "<h2>UI/UX Checklist:</h2>";
echo "<ul>";
echo "<li><a href='/'>Homepage</a> - Check layout and content display</li>";
echo "<li><a href='/posts'>Posts Page</a> - Check card layout (4 per row)</li>";
echo "<li><a href='/news'>News Page</a> - Check news display</li>";
echo "<li><a href='/schools'>Schools</a> - Check educational institution cards</li>";
echo "<li>Test dark mode toggle</li>";
echo "<li>Test mobile responsiveness</li>";
echo "<li>Check search functionality</li>";
echo "</ul>";
?>