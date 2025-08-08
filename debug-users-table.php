<?php
// Debug users table structure
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Users Table Debug</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if ($connection) {
    echo "<h3>1. Users table structure:</h3>";
    $describeQuery = "DESCRIBE users";
    $result = mysqli_query($connection, $describeQuery);
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Users table doesn't exist: " . mysqli_error($connection) . "</p>";
    }
    
    echo "<h3>2. Sample users data:</h3>";
    $sampleQuery = "SELECT * FROM users LIMIT 3";
    $result = mysqli_query($connection, $sampleQuery);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        $first = true;
        while ($row = mysqli_fetch_assoc($result)) {
            if ($first) {
                echo "<tr>";
                foreach (array_keys($row) as $key) {
                    echo "<th>" . htmlspecialchars($key) . "</th>";
                }
                echo "</tr>";
                $first = false;
            }
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found or query failed: " . mysqli_error($connection) . "</p>";
    }
    
    echo "<h3>3. Test fixed news query without users:</h3>";
    $newsQuery = "SELECT n.*, c.title_category, c.url_category
                  FROM news n
                  LEFT JOIN categories c ON n.category_news = c.id_category
                  WHERE n.approved = 1 
                  LIMIT 1";
    
    $result = mysqli_query($connection, $newsQuery);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo "<p>✅ Fixed query works!</p>";
        echo "<p>Sample article: <strong>" . htmlspecialchars($row['title_news']) . "</strong></p>";
        echo "<p>URL slug: <strong>" . htmlspecialchars($row['url_slug']) . "</strong></p>";
        echo "<p><a href='/news/" . htmlspecialchars($row['url_slug']) . "' target='_blank'>Test this article</a></p>";
    } else {
        echo "<p>❌ Fixed query failed: " . mysqli_error($connection) . "</p>";
    }
    
    mysqli_close($connection);
} else {
    echo "<p>❌ Database connection failed</p>";
}
?>

<style>
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f0f0f0; }
</style>