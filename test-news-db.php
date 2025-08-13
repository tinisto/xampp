<?php
// Direct database connection test
$connection = new mysqli('localhost', 'root', 'root', '11klassniki_ru');
if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}
$connection->set_charset('utf8mb4');

echo "<h2>Database Connection Test</h2>";
echo "<p>Connection successful!</p>";

// Test news query
$newsQuery = "SELECT n.*, c.name as category_name 
              FROM news n 
              LEFT JOIN categories c ON n.category_news = c.id 
              WHERE n.approved = 1
              ORDER BY n.date_news DESC LIMIT 5";
              
$result = mysqli_query($connection, $newsQuery);
echo "<p>News query executed. Found: " . mysqli_num_rows($result) . " records</p>";

echo "<h3>Sample news:</h3>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<p>";
    echo "ID: " . $row['id_news'] . "<br>";
    echo "Title: " . htmlspecialchars($row['title_news']) . "<br>";
    echo "Category: " . ($row['category_name'] ?? 'No category') . "<br>";
    echo "Date: " . $row['date_news'];
    echo "</p><hr>";
}

// Check categories
$catQuery = "SELECT DISTINCT c.id, c.name, COUNT(n.id_news) as news_count 
             FROM categories c 
             INNER JOIN news n ON n.category_news = c.id 
             WHERE n.approved = 1 
             GROUP BY c.id, c.name";
$catResult = mysqli_query($connection, $catQuery);
echo "<h3>Categories with news:</h3>";
while ($cat = mysqli_fetch_assoc($catResult)) {
    echo "<p>" . $cat['name'] . " (" . $cat['news_count'] . " news)</p>";
}
?>