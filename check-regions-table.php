<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Regions Table Structure</h1>";

// Show regions table structure
$result = $connection->query("DESCRIBE regions");
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Key</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td><td>" . $row['Key'] . "</td></tr>";
}
echo "</table>";

// Show sample region
echo "<h2>Sample Region Record:</h2>";
$result = $connection->query("SELECT * FROM regions WHERE region_name_en = 'amurskaya-oblast'");
if ($row = $result->fetch_assoc()) {
    echo "<pre>";
    print_r($row);
    echo "</pre>";
}
?>