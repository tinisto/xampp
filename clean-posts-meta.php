<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Clean Posts Meta Fields</h1>";
echo "<pre>";

echo "Current posts table structure:\n";
$cols = mysqli_query($connection, "SHOW COLUMNS FROM posts WHERE Field LIKE '%meta%'");
while ($col = mysqli_fetch_assoc($cols)) {
    echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
}

echo "\nRemoving meta_k_post field...\n";
if (mysqli_query($connection, "ALTER TABLE posts DROP COLUMN IF EXISTS meta_k_post")) {
    echo "✓ Successfully removed meta_k_post\n";
} else {
    echo "✗ Error: " . mysqli_error($connection) . "\n";
}

echo "\nFinal posts meta fields:\n";
$cols = mysqli_query($connection, "SHOW COLUMNS FROM posts WHERE Field LIKE '%meta%'");
while ($col = mysqli_fetch_assoc($cols)) {
    echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
}

echo "\n✓ Posts table cleaned!\n";
echo "</pre>";
?>