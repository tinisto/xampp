<?php
// Check column names in new tables
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Column Names in New Tables</h1>";

$tables = ['universities', 'colleges', 'regions'];

foreach ($tables as $table) {
    echo "<h2>$table table columns:</h2>";
    $result = $connection->query("SHOW COLUMNS FROM $table");
    if ($result) {
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

$connection->close();
?>