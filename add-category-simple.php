<?php
// Simple category addition using existing database connection pattern
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Adding 11-классники Category</h2>";

try {
    // Check if category already exists
    $check_query = "SELECT * FROM categories WHERE title_category = '11-классники' OR url_category = '11-klassniki'";
    $check_result = mysqli_query($connection, $check_query);
    
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        echo "<p>Category '11-классники' already exists!</p>";
        $existing = mysqli_fetch_assoc($check_result);
        echo "<p>ID: " . $existing['id_category'] . "<br>";
        echo "Title: " . htmlspecialchars($existing['title_category']) . "<br>";
        echo "URL: " . htmlspecialchars($existing['url_category']) . "</p>";
    } else {
        // Insert the missing category
        $insert_query = "INSERT INTO categories (title_category, url_category) VALUES ('11-классники', '11-klassniki')";
        
        if (mysqli_query($connection, $insert_query)) {
            $new_id = mysqli_insert_id($connection);
            echo "<p style='color: green;'>✓ Successfully added '11-классники' category with ID: " . $new_id . "</p>";
        } else {
            echo "<p style='color: red;'>Error adding category: " . mysqli_error($connection) . "</p>";
        }
    }
    
    // Show all categories
    echo "<h3>All Categories:</h3>";
    $all_query = "SELECT * FROM categories ORDER BY id_category";
    $all_result = mysqli_query($connection, $all_query);
    
    if ($all_result && mysqli_num_rows($all_result) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>URL</th></tr>";
        while ($row = mysqli_fetch_assoc($all_result)) {
            echo "<tr>";
            echo "<td>" . $row['id_category'] . "</td>";
            echo "<td>" . htmlspecialchars($row['title_category']) . "</td>";
            echo "<td>" . htmlspecialchars($row['url_category']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>