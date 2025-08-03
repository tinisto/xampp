<?php
// Debug regions and VPO issue
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';

$connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$connection->set_charset("utf8mb4");

echo "<h1>Debug Regions and VPO</h1>";
echo "<p>Database: " . DB_NAME . "</p>";

// Check regions table
echo "<h2>1. Regions Table</h2>";
$regions_check = $connection->query("SHOW TABLES LIKE 'regions'");
if ($regions_check->num_rows > 0) {
    echo "<p>✅ Regions table exists</p>";
    
    // Count regions
    $regions_count = $connection->query("SELECT COUNT(*) as count FROM regions WHERE id_country = 1")->fetch_assoc()['count'];
    echo "<p>Total regions for country 1: $regions_count</p>";
    
    // Sample regions
    echo "<h3>Sample regions:</h3>";
    $sample_regions = $connection->query("SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 LIMIT 5");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Region Name</th><th>Region Name EN</th></tr>";
    while ($row = $sample_regions->fetch_assoc()) {
        echo "<tr><td>{$row['id_region']}</td><td>{$row['region_name']}</td><td>{$row['region_name_en']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Regions table does not exist!</p>";
}

// Check VPO table
echo "<h2>2. VPO Table Analysis</h2>";
$vpo_count = $connection->query("SELECT COUNT(*) as count FROM vpo")->fetch_assoc()['count'];
echo "<p>Total VPO records: $vpo_count</p>";

// Check VPO with regions
$vpo_with_regions = $connection->query("SELECT COUNT(*) as count FROM vpo WHERE id_region IS NOT NULL AND id_region > 0")->fetch_assoc()['count'];
echo "<p>VPO with valid region IDs: $vpo_with_regions</p>";

// Check distinct regions in VPO
$distinct_regions = $connection->query("SELECT COUNT(DISTINCT id_region) as count FROM vpo WHERE id_region IS NOT NULL AND id_region > 0")->fetch_assoc()['count'];
echo "<p>Distinct regions in VPO: $distinct_regions</p>";

// Sample VPO with regions
echo "<h3>Sample VPO with regions:</h3>";
$sample_vpo = $connection->query("SELECT id_vpo, vpo_name, id_region FROM vpo WHERE id_region IS NOT NULL AND id_region > 0 LIMIT 5");
echo "<table border='1'>";
echo "<tr><th>ID</th><th>VPO Name</th><th>Region ID</th></tr>";
while ($row = $sample_vpo->fetch_assoc()) {
    echo "<tr><td>{$row['id_vpo']}</td><td>" . htmlspecialchars($row['vpo_name']) . "</td><td>{$row['id_region']}</td></tr>";
}
echo "</table>";

// Test the actual query used in the page
echo "<h2>3. Testing Actual Page Query</h2>";
$test_query = "SELECT COUNT(DISTINCT r.id_region) as total FROM regions r 
               INNER JOIN vpo i ON r.id_region = i.id_region 
               WHERE r.id_country = 1";
$test_result = $connection->query($test_query);
if ($test_result) {
    $total = $test_result->fetch_assoc()['total'];
    echo "<p>Regions with VPO institutions: $total</p>";
} else {
    echo "<p>Query failed: " . $connection->error . "</p>";
}

// Check for regions with VPO count
echo "<h2>4. Regions with VPO Count</h2>";
$regions_with_vpo = $connection->query("
    SELECT r.id_region, r.region_name, COUNT(v.id_vpo) as vpo_count 
    FROM regions r 
    LEFT JOIN vpo v ON r.id_region = v.id_region 
    WHERE r.id_country = 1 
    GROUP BY r.id_region 
    HAVING vpo_count > 0 
    ORDER BY vpo_count DESC 
    LIMIT 10
");

echo "<table border='1'>";
echo "<tr><th>Region ID</th><th>Region Name</th><th>VPO Count</th></tr>";
while ($row = $regions_with_vpo->fetch_assoc()) {
    echo "<tr><td>{$row['id_region']}</td><td>{$row['region_name']}</td><td>{$row['vpo_count']}</td></tr>";
}
echo "</table>";

$connection->close();
?>