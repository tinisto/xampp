<?php
session_start();

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once dirname(__DIR__) . '/config/loadEnv.php';
require_once dirname(__DIR__) . '/database/db_connections.php';

echo "<h2>Database Tables Check</h2>";

// Get all tables
$result = $connection->query("SHOW TABLES");
echo "<h3>Available Tables:</h3><ul>";
while ($row = $result->fetch_array()) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";

// Check if comments table exists
$result = $connection->query("SHOW TABLES LIKE 'comments'");
if ($result->num_rows > 0) {
    echo "<h3>Comments table exists!</h3>";
    
    // Show table structure
    $result = $connection->query("DESCRIBE comments");
    echo "<h4>Comments table structure:</h4>";
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Count comments
    $result = $connection->query("SELECT COUNT(*) as count FROM comments");
    $count = $result->fetch_assoc()['count'];
    echo "<p>Total comments: " . $count . "</p>";
} else {
    echo "<h3 style='color: red;'>Comments table does NOT exist!</h3>";
}
?>