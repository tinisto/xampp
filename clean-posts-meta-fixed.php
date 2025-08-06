<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Clean Posts Meta Fields - Fixed Syntax</h1>";
echo "<pre>";

echo "Current posts table structure:\n";
$cols = mysqli_query($connection, "SHOW COLUMNS FROM posts WHERE Field LIKE '%meta%'");
while ($col = mysqli_fetch_assoc($cols)) {
    echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
}

// Check if meta_k_post exists before trying to drop it
$checkCol = mysqli_query($connection, "SHOW COLUMNS FROM posts LIKE 'meta_k_post'");
if (mysqli_num_rows($checkCol) > 0) {
    echo "\nRemoving meta_k_post field...\n";
    if (mysqli_query($connection, "ALTER TABLE posts DROP COLUMN meta_k_post")) {
        echo "✓ Successfully removed meta_k_post\n";
    } else {
        echo "✗ Error: " . mysqli_error($connection) . "\n";
    }
} else {
    echo "\nmeta_k_post field doesn't exist - nothing to remove\n";
}

echo "\nFinal posts meta fields:\n";
$cols = mysqli_query($connection, "SHOW COLUMNS FROM posts WHERE Field LIKE '%meta%'");
while ($col = mysqli_fetch_assoc($cols)) {
    echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
}

echo "\n✓ Posts table cleaned!\n";
echo "</pre>";
?>