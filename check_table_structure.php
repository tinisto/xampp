<?php
// Check the exact structure of universities and colleges tables
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Force connection to new database
$connection = new mysqli(
    '11klassnikiru67871.ipagemysql.com',
    'admin_claude',
    'W4eZ!#9uwLmrMay',
    '11klassniki_claude'
);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

echo "<h1>Table Structure Analysis</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// Check universities table structure
echo "<h2>Universities Table Structure</h2>";
$uni_cols = $connection->query("SHOW COLUMNS FROM universities");
if ($uni_cols) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($col = $uni_cols->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check colleges table structure
echo "<h2>Colleges Table Structure</h2>";
$col_cols = $connection->query("SHOW COLUMNS FROM colleges");
if ($col_cols) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($col = $col_cols->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Show sample data from universities
echo "<h2>Sample Data from Universities</h2>";
$sample = $connection->query("SELECT * FROM universities LIMIT 1");
if ($sample && $sample->num_rows > 0) {
    $row = $sample->fetch_assoc();
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "<p>No data in universities table</p>";
}

// Show sample data from colleges
echo "<h2>Sample Data from Colleges</h2>";
$sample = $connection->query("SELECT * FROM colleges LIMIT 1");
if ($sample && $sample->num_rows > 0) {
    $row = $sample->fetch_assoc();
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "<p>No data in colleges table</p>";
}

$connection->close();
?>