<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Database Field Naming Inconsistencies Analysis</h1>";

// Tables to check
$tables = ['users', 'posts', 'news', 'schools', 'vpo', 'spo', 'comments', 'regions', 'towns', 'areas', 'countries'];

$field_patterns = [
    'id' => [],
    'region' => [],
    'town' => [],
    'area' => [],
    'country' => [],
    'user' => [],
    'name' => []
];

echo "<h2>Analyzing ID field patterns:</h2>";
echo "<table border='1'><tr><th>Table</th><th>Primary Key</th><th>Region Field</th><th>Town Field</th><th>Area Field</th><th>User Field</th></tr>";

foreach ($tables as $table) {
    $result = $connection->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) continue;
    
    echo "<tr><td><strong>$table</strong></td>";
    
    // Get primary key
    $pk_query = "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'";
    $pk_result = $connection->query($pk_query);
    $primary_key = $pk_result->fetch_assoc()['Column_name'] ?? 'N/A';
    echo "<td>$primary_key</td>";
    
    // Check for region field
    $region_fields = [];
    $result = $connection->query("SHOW COLUMNS FROM $table LIKE '%region%'");
    while ($row = $result->fetch_assoc()) {
        $region_fields[] = $row['Field'];
    }
    echo "<td>" . implode(', ', $region_fields) . "</td>";
    
    // Check for town field
    $town_fields = [];
    $result = $connection->query("SHOW COLUMNS FROM $table LIKE '%town%'");
    while ($row = $result->fetch_assoc()) {
        $town_fields[] = $row['Field'];
    }
    echo "<td>" . implode(', ', $town_fields) . "</td>";
    
    // Check for area field
    $area_fields = [];
    $result = $connection->query("SHOW COLUMNS FROM $table LIKE '%area%'");
    while ($row = $result->fetch_assoc()) {
        $area_fields[] = $row['Field'];
    }
    echo "<td>" . implode(', ', $area_fields) . "</td>";
    
    // Check for user field
    $user_fields = [];
    $result = $connection->query("SHOW COLUMNS FROM $table LIKE '%user%'");
    while ($row = $result->fetch_assoc()) {
        $user_fields[] = $row['Field'];
    }
    echo "<td>" . implode(', ', $user_fields) . "</td>";
    
    echo "</tr>";
}
echo "</table>";

// Analyze patterns
echo "<h2>Common Patterns Found:</h2>";
echo "<h3>Primary Key Patterns:</h3><ul>";
echo "<li><code>id</code> - Standard pattern (users, comments, etc.)</li>";
echo "<li><code>id_[table]</code> - Table-specific pattern (id_school, id_vpo, id_spo)</li>";
echo "<li><code>[table]_id</code> - Reverse pattern (region_id in regions table)</li>";
echo "</ul>";

echo "<h3>Foreign Key Patterns:</h3><ul>";
echo "<li><code>[table]_id</code> - Standard foreign key (user_id, region_id)</li>";
echo "<li><code>id_[table]</code> - Alternative pattern (id_region, id_town)</li>";
echo "</ul>";

// Proposed standardization
echo "<h2>Proposed Standardization:</h2>";
echo "<table border='1'>";
echo "<tr><th>Current Pattern</th><th>Proposed Standard</th><th>Example</th></tr>";
echo "<tr><td>id_[table] (primary key)</td><td>id</td><td>id_school → id</td></tr>";
echo "<tr><td>id_[table] (foreign key)</td><td>[table]_id</td><td>id_region → region_id</td></tr>";
echo "<tr><td>[table]_name</td><td>name</td><td>school_name → name (context clear from table)</td></tr>";
echo "</table>";

// Check specific inconsistencies
echo "<h2>Specific Issues to Fix:</h2>";
echo "<ul>";
echo "<li>VPO table: id_vpo → id, id_region → region_id, id_town → town_id</li>";
echo "<li>SPO table: id_spo → id, id_region → region_id, id_town → town_id</li>";
echo "<li>Schools table: id_school → id, id_region → region_id, id_town → town_id</li>";
echo "<li>Posts table: id_post → id</li>";
echo "<li>News table: id_news → id</li>";
echo "</ul>";
?>