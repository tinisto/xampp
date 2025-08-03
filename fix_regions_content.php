<?php
// Fix the educational-institutions-all-regions-content.php file
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Fix Regions Content File</h1>";

// Show columns
echo "<h2>Regions Table Columns:</h2>";
$cols = $connection->query("SHOW COLUMNS FROM regions");
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th></tr>";
while ($col = $cols->fetch_assoc()) {
    echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td></tr>";
}
echo "</table>";

echo "<h2>Fix Required:</h2>";
echo "<p>The educational-institutions-all-regions-content.php file is using 'id_region' but the regions table uses 'id' as the primary key.</p>";

if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php';
    $content = file_get_contents($file_path);
    
    // Fix the SQL query
    $content = str_replace(
        "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1",
        "SELECT id, region_name, region_name_en FROM regions WHERE country_id = 1",
        $content
    );
    
    // Fix all references to id_region in the fetched results
    $content = str_replace(
        "\$row['id_region']",
        "\$row['id']",
        $content
    );
    
    // Fix the count query
    $content = str_replace(
        "WHERE \$region_col = {\$row['id_region']}",
        "WHERE \$region_col = {\$row['id']}",
        $content
    );
    
    // Fix data attribute
    $content = str_replace(
        'data-region-id="<?= $row[\'id_region\'] ?>"',
        'data-region-id="<?= $row[\'id\'] ?>"',
        $content
    );
    
    // Fix the displayed_sql query
    $content = str_replace(
        "SELECT COUNT(DISTINCT r.id_region) as total FROM regions r",
        "SELECT COUNT(DISTINCT r.id) as total FROM regions r",
        $content
    );
    
    $content = str_replace(
        "INNER JOIN \$table i ON r.id_region = i.\$region_col",
        "INNER JOIN \$table i ON r.id = i.\$region_col",
        $content
    );
    
    $content = str_replace(
        "WHERE r.id_country = 1",
        "WHERE r.country_id = 1",
        $content
    );
    
    if (file_put_contents($file_path, $content)) {
        echo "<p style='color: green;'>✅ Fixed educational-institutions-all-regions-content.php</p>";
        echo "<p><a href='/vpo-all-regions'>Test VPO Page</a> | <a href='/spo-all-regions'>Test SPO Page</a></p>";
    } else {
        echo "<p style='color: red;'>❌ Could not update file</p>";
    }
} else {
    echo "<p><a href='?fix=yes' style='background: green; color: white; padding: 10px 20px; text-decoration: none;'>Fix Region Column Names in Content File</a></p>";
}

$connection->close();
?>