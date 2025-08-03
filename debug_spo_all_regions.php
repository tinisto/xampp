<?php
// Debug SPO all regions issue
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

echo "<h1>Debug SPO All Regions</h1>";
echo "<p>Database: " . DB_NAME . "</p>";

// Check what the page is receiving
echo "<h2>Request Information</h2>";
echo "<p>REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Type parameter: " . ($_GET['type'] ?? 'not set') . "</p>";

// Check SPO table
echo "<h2>SPO Table Status</h2>";
$spo_count = $connection->query("SELECT COUNT(*) as count FROM spo")->fetch_assoc()['count'];
echo "<p>Total SPO records: $spo_count</p>";

// Check regions in SPO
echo "<h2>Regions in SPO Table</h2>";
$regions_query = "SELECT DISTINCT id_region FROM spo WHERE id_region IS NOT NULL ORDER BY id_region";
$regions_result = $connection->query($regions_query);
echo "<p>Distinct regions count: " . $regions_result->num_rows . "</p>";

// Sample SPO data
echo "<h2>Sample SPO Data</h2>";
$sample_query = "SELECT id_spo, spo_name, id_region FROM spo LIMIT 5";
$sample_result = $connection->query($sample_query);
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Region ID</th></tr>";
while ($row = $sample_result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id_spo'] . "</td>";
    echo "<td>" . htmlspecialchars($row['spo_name']) . "</td>";
    echo "<td>" . $row['id_region'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check colleges table (new)
echo "<h2>Colleges Table Status</h2>";
$colleges_count = $connection->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'];
echo "<p>Total colleges records: $colleges_count</p>";

// Check what table the code is trying to use
echo "<h2>Table Mapping Check</h2>";
$type = $_GET['type'] ?? 'schools';
$table = '';
switch ($type) {
    case 'spo':
        $table = 'colleges';  // This might be the issue
        break;
    case 'vpo':
        $table = 'universities';
        break;
    default:
        $table = 'schools';
}
echo "<p>Type '$type' maps to table '$table'</p>";

// Test the actual query
echo "<h2>Testing Query</h2>";
$test_query = "SELECT COUNT(*) as count FROM $table";
try {
    $result = $connection->query($test_query);
    if ($result) {
        $count = $result->fetch_assoc()['count'];
        echo "<p>Query successful: $count records in $table</p>";
    }
} catch (Exception $e) {
    echo "<p>Query failed: " . $e->getMessage() . "</p>";
}

$connection->close();
?>