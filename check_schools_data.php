<?php
// Check schools data structure
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h2>Schools Data Analysis</h2>";

// Check schools table
echo "<h3>Schools table structure:</h3>";
$columns = $connection->query("DESCRIBE schools");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th></tr>";
while ($col = $columns->fetch_assoc()) {
    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
}
echo "</table>";

// Count schools
echo "<h3>Schools count by region:</h3>";
$count_query = "SELECT r.region_name, r.id, COUNT(s.id) as school_count 
                FROM regions r 
                LEFT JOIN schools s ON s.region_id = r.id 
                WHERE r.country_id = 1 
                GROUP BY r.id 
                HAVING school_count > 0 
                ORDER BY school_count DESC 
                LIMIT 10";

$result = $connection->query($count_query);
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Region</th><th>Region ID</th><th>School Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['school_count']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Error: " . $connection->error . "</p>";
}

// Check total schools
$total = $connection->query("SELECT COUNT(*) as total FROM schools")->fetch_assoc();
echo "<p><strong>Total schools in database: {$total['total']}</strong></p>";

// Sample schools data
echo "<h3>Sample schools data:</h3>";
$sample = $connection->query("SELECT * FROM schools LIMIT 5");
if ($sample && $sample->num_rows > 0) {
    $first = true;
    echo "<table border='1' cellpadding='5'>";
    while ($row = $sample->fetch_assoc()) {
        if ($first) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<th>$key</th>";
            }
            echo "</tr>";
            $first = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Check current page URL structure
echo "<h3>Current schools-all-regions page location:</h3>";
$page_path = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
if (file_exists($page_path)) {
    echo "<p>✅ Page exists at standard location</p>";
    
    // Check what type parameter is being used
    echo "<p>The page should work with these URLs:</p>";
    echo "<ul>";
    echo "<li><a href='/schools-all-regions'>https://11klassniki.ru/schools-all-regions</a> (default)</li>";
    echo "<li><a href='/vpo-all-regions'>https://11klassniki.ru/vpo-all-regions</a> (universities)</li>";
    echo "<li><a href='/spo-all-regions'>https://11klassniki.ru/spo-all-regions</a> (colleges)</li>";
    echo "</ul>";
} else {
    echo "<p>❌ Page not found at expected location</p>";
}

$connection->close();
?>