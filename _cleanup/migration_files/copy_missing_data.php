<?php
/**
 * Copy missing areas and towns from old to new database
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>🔄 Copy Missing Areas and Towns</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
</style>";

if (!isset($_GET['copy']) || $_GET['copy'] !== 'yes') {
    echo "<p>This will copy areas and towns data from the old database.</p>";
    echo "<p><a href='?copy=yes' style='background: blue; color: white; padding: 10px; text-decoration: none;'>START COPY</a></p>";
    exit;
}

try {
    // Connect to old database
    $old_db = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        '11klone_user',
        'K8HqqBV3hTf4mha',
        '11klassniki_ru'
    );
    
    if ($old_db->connect_error) {
        die("<p class='error'>❌ Could not connect to old database: " . $old_db->connect_error . "</p>");
    }
    
    // Connect to new database  
    $new_db = new mysqli(
        DB_HOST,
        'admin_claude',
        'Secure9#Klass',
        '11klassniki_new'
    );
    
    if ($new_db->connect_error) {
        die("<p class='error'>❌ Could not connect to new database: " . $new_db->connect_error . "</p>");
    }
    
    // Set charset
    $old_db->set_charset('utf8mb4');
    $new_db->set_charset('utf8mb4');
    
    echo "<p class='success'>✅ Connected to both databases</p>";
    
    // Copy areas
    echo "<h2>📁 Copying Areas...</h2>";
    $areas_query = "SELECT * FROM areas";
    $areas_result = $old_db->query($areas_query);
    $areas_count = 0;
    
    if ($areas_result) {
        while ($row = $areas_result->fetch_assoc()) {
            $insert = $new_db->prepare("
                INSERT IGNORE INTO areas (id, region_id, area_name, area_name_genitive, description)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $area_name = $row['name'] ?: 'Unknown Area';
            $area_name_genitive = $row['name_rod'] ?: $area_name;
            
            $insert->bind_param("iisss",
                $row['id_area'],
                $row['id_region'],
                $area_name,
                $area_name_genitive,
                $row['description']
            );
            
            if ($insert->execute()) {
                $areas_count++;
            }
        }
    }
    
    echo "<p class='success'>✅ Copied $areas_count areas</p>";
    
    // Copy towns
    echo "<h2>🏘️ Copying Towns...</h2>";
    $towns_query = "SELECT * FROM towns";
    $towns_result = $old_db->query($towns_query);
    $towns_count = 0;
    
    if ($towns_result) {
        while ($row = $towns_result->fetch_assoc()) {
            $insert = $new_db->prepare("
                INSERT IGNORE INTO towns (id, area_id, region_id, country_id, town_name, town_name_genitive, description, image_url, url_slug)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $town_name = $row['name'] ?: 'Unknown Town';
            $town_name_genitive = $row['name_rod'] ?: $town_name;
            $url_slug = $row['url_slug_town'] ?: strtolower(str_replace(' ', '-', $town_name));
            
            $insert->bind_param("iiiisssss",
                $row['id_town'],
                $row['id_area'],
                $row['id_region'],
                $row['id_country'],
                $town_name,
                $town_name_genitive,
                $row['description'],
                $row['img'],
                $url_slug
            );
            
            if ($insert->execute()) {
                $towns_count++;
            }
        }
    }
    
    echo "<p class='success'>✅ Copied $towns_count towns</p>";
    
    // Verify counts
    echo "<h2>📊 Final Counts:</h2>";
    
    $areas_check = $new_db->query("SELECT COUNT(*) as count FROM areas");
    $areas_total = $areas_check->fetch_assoc()['count'];
    
    $towns_check = $new_db->query("SELECT COUNT(*) as count FROM towns");
    $towns_total = $towns_check->fetch_assoc()['count'];
    
    echo "<p>Areas: <strong>$areas_total</strong></p>";
    echo "<p>Towns: <strong>$towns_total</strong></p>";
    
    echo "<p class='success'>✅ Data copy complete!</p>";
    
    $old_db->close();
    $new_db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/'>← Back to Home</a></p>";
?>