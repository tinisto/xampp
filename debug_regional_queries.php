<?php
// Direct database connection for debugging
$connection = new mysqli(
    '11klassnikiru67871.ipagemysql.com',
    'admin_claude',
    'W4eZ!#9uwLmrMay',
    '11klassniki_claude'
);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$connection->set_charset('utf8mb4');

echo "<h1>🔍 Regional Query Debugging</h1>";
echo "<style>
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .code { background: #f5f5f5; padding: 10px; font-family: monospace; margin: 10px 0; }
</style>";

// Check current database
$db_result = $connection->query("SELECT DATABASE() as db_name");
$current_db = $db_result ? $db_result->fetch_assoc()['db_name'] : 'unknown';
echo "<p><strong>Current database:</strong> $current_db</p>";

echo "<h2>1️⃣ Table Structures</h2>";

// Check regions table structure
echo "<h3>Regions Table Structure:</h3>";
$regions = $connection->query("DESCRIBE regions");
if ($regions) {
    echo "<table><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $regions->fetch_assoc()) {
        echo "<tr><td>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
}

// Check universities table
echo "<h3>Universities Table Structure:</h3>";
$universities = $connection->query("DESCRIBE universities");
if ($universities) {
    echo "<table><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $universities->fetch_assoc()) {
        $highlight = ($row['Field'] == 'region_id') ? ' style="background-color: yellow;"' : '';
        echo "<tr><td$highlight>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>Universities table not found!</p>";
}

// Check VPO table
echo "<h3>VPO Table Structure:</h3>";
$vpo = $connection->query("DESCRIBE vpo");
if ($vpo) {
    echo "<table><tr><th>Field</th><th>Type</th></tr>";
    while ($row = $vpo->fetch_assoc()) {
        $highlight = ($row['Field'] == 'id_region') ? ' style="background-color: yellow;"' : '';
        echo "<tr><td$highlight>" . $row['Field'] . "</td><td>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
}

echo "<h2>2️⃣ Sample Data</h2>";

// Sample regions
echo "<h3>Sample Regions:</h3>";
$regions_data = $connection->query("SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 LIMIT 5");
if ($regions_data) {
    echo "<table><tr><th>id_region</th><th>region_name</th><th>region_name_en</th></tr>";
    while ($row = $regions_data->fetch_assoc()) {
        echo "<tr><td>" . $row['id_region'] . "</td><td>" . $row['region_name'] . "</td><td>" . $row['region_name_en'] . "</td></tr>";
    }
    echo "</table>";
}

// Check VPO data
echo "<h3>VPO Data by Region:</h3>";
$vpo_query = "SELECT r.id_region, r.region_name, COUNT(v.id_vpo) as vpo_count 
              FROM regions r 
              LEFT JOIN vpo v ON r.id_region = v.id_region 
              WHERE r.id_country = 1 
              GROUP BY r.id_region 
              HAVING vpo_count > 0 
              ORDER BY vpo_count DESC 
              LIMIT 10";
$vpo_data = $connection->query($vpo_query);
if ($vpo_data && $vpo_data->num_rows > 0) {
    echo "<table><tr><th>Region ID</th><th>Region Name</th><th>VPO Count</th></tr>";
    while ($row = $vpo_data->fetch_assoc()) {
        echo "<tr><td>" . $row['id_region'] . "</td><td>" . $row['region_name'] . "</td><td>" . $row['vpo_count'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>No VPO data found by region!</p>";
}

// Check Universities data
echo "<h3>Universities Data by Region:</h3>";
$uni_query = "SELECT r.id_region, r.region_name, COUNT(u.id) as uni_count 
              FROM regions r 
              LEFT JOIN universities u ON r.id_region = u.region_id 
              WHERE r.id_country = 1 
              GROUP BY r.id_region 
              HAVING uni_count > 0 
              ORDER BY uni_count DESC 
              LIMIT 10";
$uni_data = $connection->query($uni_query);
if ($uni_data && $uni_data->num_rows > 0) {
    echo "<table><tr><th>Region ID</th><th>Region Name</th><th>University Count</th></tr>";
    while ($row = $uni_data->fetch_assoc()) {
        echo "<tr><td>" . $row['id_region'] . "</td><td>" . $row['region_name'] . "</td><td>" . $row['uni_count'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>No Universities data found by region!</p>";
}

echo "<h2>3️⃣ Testing Regional Queries</h2>";

// Test a specific region
$test_region_id = 77; // Moscow
echo "<h3>Testing Region ID $test_region_id (Moscow):</h3>";

// Test VPO query
$vpo_test = $connection->query("SELECT COUNT(*) as count FROM vpo WHERE id_region = $test_region_id");
$vpo_count = $vpo_test ? $vpo_test->fetch_assoc()['count'] : 0;
echo "<p>VPO in region $test_region_id: <strong>$vpo_count</strong></p>";

// Test Universities query  
$uni_test = $connection->query("SELECT COUNT(*) as count FROM universities WHERE region_id = $test_region_id");
$uni_count = $uni_test ? $uni_test->fetch_assoc()['count'] : 0;
echo "<p>Universities in region $test_region_id: <strong>$uni_count</strong></p>";

// Show sample VPO
if ($vpo_count > 0) {
    echo "<h4>Sample VPO:</h4>";
    $sample_vpo = $connection->query("SELECT vpo_name, vpo_url FROM vpo WHERE id_region = $test_region_id LIMIT 3");
    echo "<ul>";
    while ($row = $sample_vpo->fetch_assoc()) {
        echo "<li>" . $row['vpo_name'] . " (URL: " . $row['vpo_url'] . ")</li>";
    }
    echo "</ul>";
}

// Show sample Universities
if ($uni_count > 0) {
    echo "<h4>Sample Universities:</h4>";
    $sample_uni = $connection->query("SELECT university_name, url_slug FROM universities WHERE region_id = $test_region_id LIMIT 3");
    echo "<ul>";
    while ($row = $sample_uni->fetch_assoc()) {
        echo "<li>" . $row['university_name'] . " (URL: " . $row['url_slug'] . ")</li>";
    }
    echo "</ul>";
}

echo "<h2>4️⃣ Query Analysis</h2>";

echo "<div class='code'>";
echo "<strong>Issue Analysis:</strong><br>";
echo "1. The new schema uses 'universities' and 'colleges' tables with 'region_id' field<br>";
echo "2. The old schema uses 'vpo' and 'spo' tables with 'id_region' field<br>";
echo "3. The code is querying the OLD tables (vpo/spo) but might need to query the NEW tables<br>";
echo "4. Need to check which tables actually have data<br>";
echo "</div>";

// Check table counts
echo "<h3>Table Row Counts:</h3>";
$tables = ['vpo', 'spo', 'universities', 'colleges'];
echo "<table><tr><th>Table</th><th>Row Count</th></tr>";
foreach ($tables as $table) {
    $count_query = $connection->query("SELECT COUNT(*) as count FROM $table");
    if ($count_query) {
        $count = $count_query->fetch_assoc()['count'];
        echo "<tr><td>$table</td><td>$count</td></tr>";
    } else {
        echo "<tr><td>$table</td><td class='error'>Table not found</td></tr>";
    }
}
echo "</table>";

$connection->close();
?>