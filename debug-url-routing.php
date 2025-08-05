<?php
echo "<h1>Debug URL Routing</h1>";

echo "<h2>Current URL and Parameters:</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>GET parameters:</strong></p>";
echo "<pre>" . print_r($_GET, true) . "</pre>";

echo "<h2>Testing Database Queries:</h2>";

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

// Test school ID 2718
echo "<h3>School ID 2718:</h3>";
$school_result = $connection->query("SELECT id, name, url_slug FROM schools WHERE id = 2718");
if ($school_result && $school_result->num_rows > 0) {
    $school = $school_result->fetch_assoc();
    echo "✅ Found: " . $school['name'] . "<br>";
    echo "URL Slug: " . ($school['url_slug'] ?: 'NULL') . "<br>";
} else {
    echo "❌ School ID 2718 not found<br>";
}

// Test SPO
echo "<h3>SPO: belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti</h3>";
$spo_slug = 'belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti';
$spo_result = $connection->query("SELECT id, name, url_slug FROM spo WHERE url_slug = '$spo_slug'");
if ($spo_result && $spo_result->num_rows > 0) {
    $spo = $spo_result->fetch_assoc();
    echo "✅ Found: " . $spo['name'] . "<br>";
    echo "URL Slug: " . $spo['url_slug'] . "<br>";
} else {
    echo "❌ SPO with slug '$spo_slug' not found<br>";
}

// Test VPO
echo "<h3>VPO: amijt</h3>";
$vpo_slug = 'amijt';
$vpo_result = $connection->query("SELECT id, name, url_slug FROM vpo WHERE url_slug = '$vpo_slug'");
if ($vpo_result && $vpo_result->num_rows > 0) {
    $vpo = $vpo_result->fetch_assoc();
    echo "✅ Found: " . $vpo['name'] . "<br>";
    echo "URL Slug: " . $vpo['url_slug'] . "<br>";
} else {
    echo "❌ VPO with slug '$vpo_slug' not found<br>";
}

echo "<h2>Check .htaccess Rules:</h2>";
echo "<p>If you see GET parameters above, the .htaccess rules are working.</p>";
echo "<p>If no GET parameters, the .htaccess rules may not be active.</p>";

// Check field names after migration
echo "<h2>Verify Field Names After Migration:</h2>";
echo "<h3>Schools table structure:</h3>";
$schools_cols = $connection->query("SHOW COLUMNS FROM schools");
$school_fields = [];
while ($col = $schools_cols->fetch_assoc()) {
    $school_fields[] = $col['Field'];
}
echo "<p>" . implode(', ', $school_fields) . "</p>";

echo "<h3>SPO table structure:</h3>";
$spo_cols = $connection->query("SHOW COLUMNS FROM spo");
$spo_fields = [];
while ($col = $spo_cols->fetch_assoc()) {
    $spo_fields[] = $col['Field'];
}
echo "<p>" . implode(', ', $spo_fields) . "</p>";

echo "<h3>VPO table structure:</h3>";
$vpo_cols = $connection->query("SHOW COLUMNS FROM vpo");
$vpo_fields = [];
while ($col = $vpo_cols->fetch_assoc()) {
    $vpo_fields[] = $col['Field'];
}
echo "<p>" . implode(', ', $vpo_fields) . "</p>";

echo "<h2>Test Direct Template Access:</h2>";
echo "<p>Try accessing templates directly:</p>";
echo "<ul>";
echo "<li><a href='/pages/school/school-single-simplified.php?id_school=2718'>School template with ID</a></li>";
echo "<li><a href='/pages/common/vpo-spo/single-simplified.php?url_slug=amijt&type=vpo'>VPO template direct</a></li>";
echo "<li><a href='/pages/common/vpo-spo/single-simplified.php?url_slug=belogorskiy-tehnologicheskiy-tehnikum-pischevoy-promyishlennosti&type=spo'>SPO template direct</a></li>";
echo "</ul>";
?>