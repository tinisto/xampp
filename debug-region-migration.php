<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Debug Region Pages After Migration</h1>";

// Test the query from region page
$region_name_en = 'amurskaya-oblast';
$type = 'vpo';

echo "<h2>Testing region lookup:</h2>";
$query_region = "SELECT region_id, region_name FROM regions WHERE region_name_en = ?";
$stmt = $connection->prepare($query_region);
$stmt->bind_param("s", $region_name_en);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $region_id = $row['region_id'];
    $region_name = $row['region_name'];
    echo "✅ Region found: ID = $region_id, Name = $region_name<br>";
} else {
    echo "❌ Region not found<br>";
    exit;
}

echo "<h2>Testing VPO query with new field names:</h2>";
$institutions_query = "SELECT * FROM vpo WHERE region_id = ? LIMIT 5";
$stmt_institutions = $connection->prepare($institutions_query);
if (!$stmt_institutions) {
    echo "❌ Prepare failed: " . $connection->error . "<br>";
    exit;
}

$stmt_institutions->bind_param("i", $region_id);
if (!$stmt_institutions->execute()) {
    echo "❌ Execute failed: " . $stmt_institutions->error . "<br>";
    exit;
}

$institutions_result = $stmt_institutions->get_result();
echo "✅ Query executed successfully<br>";
echo "Results found: " . $institutions_result->num_rows . "<br>";

if ($institutions_result->num_rows > 0) {
    echo "<h3>Sample VPO records:</h3>";
    while ($row = $institutions_result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['vpo_name'] . ", Region ID: " . $row['region_id'] . "<br>";
    }
} else {
    echo "No VPO institutions found in this region.<br>";
}

echo "<h2>Testing SPO query:</h2>";
$spo_query = "SELECT * FROM spo WHERE region_id = ? LIMIT 5";
$stmt_spo = $connection->prepare($spo_query);
$stmt_spo->bind_param("i", $region_id);
$stmt_spo->execute();
$spo_result = $stmt_spo->get_result();
echo "SPO results found: " . $spo_result->num_rows . "<br>";

echo "<h2>Testing Schools query:</h2>";
$schools_query = "SELECT * FROM schools WHERE region_id = ? LIMIT 5";
$stmt_schools = $connection->prepare($schools_query);
$stmt_schools->bind_param("i", $region_id);
$stmt_schools->execute();
$schools_result = $stmt_schools->get_result();
echo "Schools results found: " . $schools_result->num_rows . "<br>";

// Test if the issue is with schools URL column
echo "<h2>Testing Schools URL field:</h2>";
if ($schools_result->num_rows > 0) {
    $schools_result->data_seek(0); // Reset result pointer
    $school = $schools_result->fetch_assoc();
    echo "School ID field: " . (isset($school['id']) ? "✅ id exists" : "❌ id missing") . "<br>";
    echo "School name: " . $school['school_name'] . "<br>";
}
?>