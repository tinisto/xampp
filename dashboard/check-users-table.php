<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once dirname(__DIR__) . '/config/loadEnv.php';
require_once dirname(__DIR__) . '/database/db_connections.php';

echo "<h2>Users Table Structure</h2>";

// Show table structure
$result = $connection->query("DESCRIBE users");
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Show first few users to see what columns contain names
echo "<h3>Sample Users:</h3>";
$result = $connection->query("SELECT * FROM users LIMIT 3");
echo "<pre>";
while ($row = $result->fetch_assoc()) {
    print_r($row);
}
echo "</pre>";
?>