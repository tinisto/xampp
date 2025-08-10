<?php
require_once __DIR__ . '/config/database.local.php';

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->set_charset(DB_CHARSET);

// Check posts table structure
echo "<h2>Posts Table Structure:</h2>";
$result = $mysqli->query("SHOW COLUMNS FROM posts");
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
echo "</pre>";

// Show sample posts with actual columns
echo "<h2>Sample Posts:</h2>";
$result = $mysqli->query("SELECT * FROM posts LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }
}

// Check users table structure
echo "<h2>Users Table Structure:</h2>";
$result = $mysqli->query("SHOW COLUMNS FROM users");
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
echo "</pre>";

$mysqli->close();
?>