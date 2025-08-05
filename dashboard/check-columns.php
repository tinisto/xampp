<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once dirname(__DIR__) . '/config/loadEnv.php';
require_once dirname(__DIR__) . '/database/db_connections.php';

echo "<h2>Checking Table Columns</h2>";

// Check posts table
echo "<h3>Posts Table:</h3>";
$result = $connection->query("DESCRIBE posts");
echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
}
echo "</table><br>";

// Check news table
echo "<h3>News Table:</h3>";
$result = $connection->query("DESCRIBE news");
echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
}
echo "</table><br>";

// Check vpo table
echo "<h3>VPO Table:</h3>";
$result = $connection->query("DESCRIBE vpo");
echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
$count = 0;
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    $count++;
    if ($count > 10) {
        echo "<tr><td colspan='2'>... (more fields)</td></tr>";
        break;
    }
}
echo "</table><br>";

// Check spo table
echo "<h3>SPO Table:</h3>";
$result = $connection->query("DESCRIBE spo");
echo "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
$count = 0;
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    $count++;
    if ($count > 10) {
        echo "<tr><td colspan='2'>... (more fields)</td></tr>";
        break;
    }
}
echo "</table><br>";

// Test a sample post
echo "<h3>Sample Post:</h3>";
$result = $connection->query("SELECT * FROM posts LIMIT 1");
if ($row = $result->fetch_assoc()) {
    echo "ID field name: " . (isset($row['id']) ? 'id' : (isset($row['id_post']) ? 'id_post' : 'UNKNOWN')) . "<br>";
    echo "URL field: " . (isset($row['url_post']) ? 'url_post exists' : 'url_post NOT FOUND') . "<br>";
    echo "<pre>";
    print_r(array_keys($row));
    echo "</pre>";
}
?>