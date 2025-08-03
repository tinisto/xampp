<?php
session_start();
require_once "config/loadEnv.php";
require_once "database/db_connections.php";

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

echo "<h2>Dashboard Debug</h2>";
echo "<p>Checking dashboard-related tables...</p>";

// List of tables used by dashboard
$tables_to_check = [
    'schools_verification',
    'schools',
    'users',
    'news',
    'posts',
    'spo',
    'vpo'
];

echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
echo "<tr><th>Table</th><th>Status</th><th>Row Count</th><th>Error</th></tr>";

foreach ($tables_to_check as $table) {
    echo "<tr>";
    echo "<td>$table</td>";
    
    $query = "SELECT COUNT(*) as count FROM $table";
    $result = $connection->query($query);
    
    if ($result === false) {
        echo "<td style='color: red;'>❌ Failed</td>";
        echo "<td>-</td>";
        echo "<td style='color: red;'>" . htmlspecialchars($connection->error) . "</td>";
    } else {
        $row = $result->fetch_assoc();
        $count = $row['count'];
        echo "<td style='color: green;'>✅ OK</td>";
        echo "<td>$count rows</td>";
        echo "<td>-</td>";
    }
    echo "</tr>";
}

echo "</table>";

echo "<h3>Database Connection Info:</h3>";
echo "<ul>";
echo "<li>Host: " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "</li>";
echo "<li>Database: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "</li>";
echo "<li>Connection status: " . ($connection ? "✅ Connected" : "❌ Failed") . "</li>";
echo "</ul>";

echo "<p><a href='/dashboard'>← Back to Dashboard</a></p>";
?>