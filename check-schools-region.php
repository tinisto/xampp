<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Checking Schools Table Region Field</h2>";

// Check region-related fields in schools
echo "<h3>Region-related fields in schools table:</h3>";
$result = $connection->query("SHOW COLUMNS FROM schools LIKE '%region%'");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")<br>";
}

// Check id_region field
$result = $connection->query("SHOW COLUMNS FROM schools LIKE 'id_region'");
echo "<br>schools has id_region: " . ($result->num_rows > 0 ? "✅ YES" : "❌ NO") . "<br>";

// Show first few fields of schools table
echo "<br><h3>First fields of schools table:</h3>";
$result = $connection->query("DESCRIBE schools");
echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
$count = 0;
while ($row = $result->fetch_assoc()) {
    if ($count < 15) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    $count++;
}
echo "</table>";

// Sample schools record
echo "<br><h3>Sample school record:</h3>";
$result = $connection->query("SELECT * FROM schools LIMIT 1");
if ($row = $result->fetch_assoc()) {
    echo "<pre>";
    print_r(array_slice($row, 0, 20)); // First 20 fields
    echo "</pre>";
}
?>