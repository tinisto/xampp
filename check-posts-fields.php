<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Posts Table Structure</h1>";
echo "<pre>";

// Get all columns from posts table
$columns = mysqli_query($connection, "SHOW COLUMNS FROM posts");
if ($columns) {
    echo "Posts table columns:\n";
    while ($col = mysqli_fetch_assoc($columns)) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")";
        if (stripos($col['Field'], 'id') !== false) {
            echo " <-- ID field";
        }
        echo "\n";
    }
}

// Get sample post data
$samplePost = mysqli_query($connection, "SELECT * FROM posts LIMIT 1");
if ($samplePost && mysqli_num_rows($samplePost) > 0) {
    echo "\nSample post data:\n";
    $post = mysqli_fetch_assoc($samplePost);
    print_r($post);
}

echo "</pre>";
?>