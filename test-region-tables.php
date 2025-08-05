<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Testing Region Tables</h2>";

// Check which tables exist
$tables_to_check = ['universities', 'colleges', 'vpo', 'spo', 'schools'];

foreach ($tables_to_check as $table) {
    $result = $connection->query("SHOW TABLES LIKE '$table'");
    echo "$table: " . ($result->num_rows > 0 ? "✅ EXISTS" : "❌ NOT FOUND") . "<br>";
}

echo "<br><h3>Checking VPO table structure:</h3>";
$result = $connection->query("DESCRIBE vpo");
echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
$count = 0;
while ($row = $result->fetch_assoc()) {
    if ($count < 10) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    $count++;
}
echo "</table>";
echo "Total fields: $count<br>";

echo "<br><h3>Checking SPO table structure:</h3>";
$result = $connection->query("DESCRIBE spo");
echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
$count = 0;
while ($row = $result->fetch_assoc()) {
    if ($count < 10) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    $count++;
}
echo "</table>";
echo "Total fields: $count<br>";

// Check if region_id exists in tables
echo "<br><h3>Checking region_id field:</h3>";
$tables = ['vpo', 'spo', 'schools'];
foreach ($tables as $table) {
    $result = $connection->query("SHOW COLUMNS FROM $table LIKE 'region_id'");
    echo "$table has region_id: " . ($result->num_rows > 0 ? "✅ YES" : "❌ NO") . "<br>";
}

// Check what region fields exist
echo "<br><h3>Region-related fields in VPO:</h3>";
$result = $connection->query("SHOW COLUMNS FROM vpo LIKE '%region%'");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")<br>";
}

echo "<br><h3>Sample VPO record:</h3>";
$result = $connection->query("SELECT * FROM vpo LIMIT 1");
if ($row = $result->fetch_assoc()) {
    echo "<pre>";
    print_r(array_slice($row, 0, 20)); // First 20 fields
    echo "</pre>";
}
?>