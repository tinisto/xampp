<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Debug Institution Pages</h1>";

// Test school
echo "<h2>Testing School ID 2718</h2>";
$school_result = $connection->query("SELECT * FROM schools WHERE id = 2718");
if ($school_result && $school_result->num_rows > 0) {
    $school = $school_result->fetch_assoc();
    echo "✅ School found: " . $school['school_name'] . "<br>";
    echo "Fields: " . implode(', ', array_keys($school)) . "<br>";
} else {
    echo "❌ School not found with id = 2718<br>";
    
    // Check with old field name
    $school_result2 = $connection->query("SELECT * FROM schools WHERE id_school = 2718");
    if ($school_result2 && $school_result2->num_rows > 0) {
        echo "⚠️ School found with old field name id_school<br>";
    }
}

// Test SPO
echo "<h2>Testing SPO: belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti</h2>";
$spo_url = 'belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti';
$spo_result = $connection->query("SELECT * FROM spo WHERE spo_url = '$spo_url'");
if ($spo_result && $spo_result->num_rows > 0) {
    $spo = $spo_result->fetch_assoc();
    echo "✅ SPO found: " . $spo['spo_name'] . "<br>";
    echo "Fields: " . implode(', ', array_keys($spo)) . "<br>";
} else {
    echo "❌ SPO not found with spo_url = '$spo_url'<br>";
}

// Test VPO
echo "<h2>Testing VPO: amijt</h2>";
$vpo_url = 'amijt';
$vpo_result = $connection->query("SELECT * FROM vpo WHERE vpo_url = '$vpo_url'");
if ($vpo_result && $vpo_result->num_rows > 0) {
    $vpo = $vpo_result->fetch_assoc();
    echo "✅ VPO found: " . $vpo['vpo_name'] . "<br>";
    echo "Fields: " . implode(', ', array_keys($vpo)) . "<br>";
} else {
    echo "❌ VPO not found with vpo_url = '$vpo_url'<br>";
}

// Check table structures
echo "<h2>Table Structures</h2>";

echo "<h3>Schools table fields:</h3>";
$schools_cols = $connection->query("SHOW COLUMNS FROM schools");
echo "<ul>";
while ($col = $schools_cols->fetch_assoc()) {
    echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
}
echo "</ul>";

echo "<h3>SPO table fields:</h3>";
$spo_cols = $connection->query("SHOW COLUMNS FROM spo");
echo "<ul>";
while ($col = $spo_cols->fetch_assoc()) {
    echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
}
echo "</ul>";

echo "<h3>VPO table fields:</h3>";
$vpo_cols = $connection->query("SHOW COLUMNS FROM vpo");
echo "<ul>";
while ($col = $vpo_cols->fetch_assoc()) {
    echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
}
echo "</ul>";
?>