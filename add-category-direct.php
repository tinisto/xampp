<?php
// Direct database connection to add missing category
$connection = new mysqli("mysql.ipage.com", "franckmgbrou", "F4p1eVUV5jGnQJGzPAKFvJQJ", "11klassnikiru_wor");
$connection->set_charset("utf8mb4");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if 11-классники category exists
$check_query = "SELECT * FROM categories WHERE title_category = '11-классники' OR url_category = '11-klassniki'";
$check_result = mysqli_query($connection, $check_query);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    echo "Category '11-классники' already exists!\n";
    $existing = mysqli_fetch_assoc($check_result);
    echo "ID: " . $existing['id_category'] . "\n";
    echo "Title: " . $existing['title_category'] . "\n";
    echo "URL: " . $existing['url_category'] . "\n";
} else {
    // Insert the missing category
    $insert_query = "INSERT INTO categories (title_category, url_category) VALUES ('11-классники', '11-klassniki')";
    
    if (mysqli_query($connection, $insert_query)) {
        $new_id = mysqli_insert_id($connection);
        echo "Successfully added '11-классники' category with ID: " . $new_id . "\n";
    } else {
        echo "Error adding category: " . mysqli_error($connection) . "\n";
    }
}

// Show all categories for verification
echo "\nAll categories in database:\n";
$all_query = "SELECT * FROM categories ORDER BY id_category";
$all_result = mysqli_query($connection, $all_query);

if ($all_result && mysqli_num_rows($all_result) > 0) {
    echo "ID | Title | URL\n";
    echo "---|-------|-----\n";
    while ($row = mysqli_fetch_assoc($all_result)) {
        echo $row['id_category'] . " | " . $row['title_category'] . " | " . $row['url_category'] . "\n";
    }
}

mysqli_close($connection);
?>