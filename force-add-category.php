<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Force Add 11-klassniki Category</h2>";

// First, let's see what the categories table structure looks like
echo "<h3>Categories Table Structure:</h3>";
$describe_query = "DESCRIBE categories";
$describe_result = mysqli_query($connection, $describe_query);

if ($describe_result) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($field = mysqli_fetch_assoc($describe_result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($field['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($field['Default'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($field['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Show all existing categories
echo "<h3>All Categories:</h3>";
$all_cats_query = "SELECT * FROM categories ORDER BY id_category";
$all_cats_result = mysqli_query($connection, $all_cats_query);

if ($all_cats_result) {
    $num_rows = mysqli_num_rows($all_cats_result);
    echo "<p>Found " . $num_rows . " categories</p>";
    
    if ($num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        // Get field names
        $fields = mysqli_fetch_fields($all_cats_result);
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        
        // Reset result pointer
        mysqli_data_seek($all_cats_result, 0);
        
        while ($cat = mysqli_fetch_assoc($all_cats_result)) {
            echo "<tr>";
            foreach ($cat as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Try to insert the category
echo "<h3>Adding 11-klassniki Category:</h3>";

// Check if it exists first
$check_query = "SELECT * FROM categories WHERE title_category = '11-классники' OR url_category = '11-klassniki'";
$check_result = mysqli_query($connection, $check_query);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    echo "<p>Category already exists!</p>";
    $existing = mysqli_fetch_assoc($check_result);
    echo "<pre>" . print_r($existing, true) . "</pre>";
} else {
    // Insert new category
    $insert_query = "INSERT INTO categories (title_category, url_category) VALUES ('11-классники', '11-klassniki')";
    
    if (mysqli_query($connection, $insert_query)) {
        $new_id = mysqli_insert_id($connection);
        echo "<p style='color: green;'>✓ Successfully inserted category with ID: " . $new_id . "</p>";
        
        // Now update posts
        $update_query = "UPDATE posts SET category = " . $new_id . " WHERE category = 1";
        if (mysqli_query($connection, $update_query)) {
            $affected = mysqli_affected_rows($connection);
            echo "<p style='color: green;'>✓ Updated " . $affected . " posts to new category</p>";
        }
    } else {
        echo "<p style='color: red;'>Error inserting category: " . mysqli_error($connection) . "</p>";
    }
}
?>