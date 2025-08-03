<?php
/**
 * Simple Database Migration - Step by Step
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

// Get step parameter
$step = $_GET['step'] ?? 'start';

echo "<h1>🚀 Simple Database Migration</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    .step { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
    .button { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
</style>";

// Database connections
$old_db = $connection; // Current database
$new_db_name = '11klassniki_new';
$new_db_user = 'admin_claude';
$new_db_pass = 'Secure9#Klass';

if ($step === 'start') {
    echo "<div class='step'>";
    echo "<h2>Migration Steps</h2>";
    echo "<p>This migration will copy data in small batches to avoid timeouts.</p>";
    echo "<ol>";
    echo "<li><a href='?step=test_connection' class='button'>Test Connection</a></li>";
    echo "<li><a href='?step=countries' class='button'>Migrate Countries</a></li>";
    echo "<li><a href='?step=regions' class='button'>Migrate Regions</a></li>";
    echo "<li><a href='?step=areas' class='button'>Migrate Areas</a></li>";
    echo "<li><a href='?step=towns' class='button'>Migrate Towns</a></li>";
    echo "<li><a href='?step=categories' class='button'>Migrate Categories</a></li>";
    echo "<li><a href='?step=users' class='button'>Migrate Users</a></li>";
    echo "<li><a href='?step=vpo' class='button'>Migrate Universities (VPO)</a></li>";
    echo "<li><a href='?step=spo' class='button'>Migrate Colleges (SPO)</a></li>";
    echo "<li><a href='?step=schools' class='button'>Migrate Schools</a></li>";
    echo "<li><a href='?step=news' class='button'>Migrate News</a></li>";
    echo "<li><a href='?step=posts' class='button'>Migrate Posts</a></li>";
    echo "</ol>";
    echo "</div>";
    exit;
}

try {
    // Connect to new database
    $new_db = new mysqli(DB_HOST, $new_db_user, $new_db_pass, $new_db_name);
    if ($new_db->connect_error) {
        die("<p class='error'>❌ Could not connect to new database: " . $new_db->connect_error . "</p>");
    }
    
    // Set UTF8MB4 charset
    $old_db->set_charset('utf8mb4');
    $new_db->set_charset('utf8mb4');
    
    echo "<p class='success'>✅ Connected to both databases</p>";

    switch ($step) {
        case 'test_connection':
            echo "<div class='step'>";
            echo "<h3>🔗 Testing Database Connections</h3>";
            
            // Test old database
            $old_test = $old_db->query("SELECT COUNT(*) as count FROM regions");
            if ($old_test) {
                $old_count = $old_test->fetch_assoc()['count'];
                echo "<p class='success'>✅ Old database: $old_count regions found</p>";
            } else {
                echo "<p class='error'>❌ Old database connection failed</p>";
            }
            
            // Test new database
            $new_test = $new_db->query("SELECT COUNT(*) as count FROM regions");
            if ($new_test) {
                $new_count = $new_test->fetch_assoc()['count'];
                echo "<p class='success'>✅ New database: $new_count regions currently</p>";
            } else {
                echo "<p class='error'>❌ New database connection failed</p>";
            }
            
            echo "<p><a href='?step=countries' class='button'>Continue to Countries</a></p>";
            echo "</div>";
            break;
            
        case 'countries':
            echo "<div class='step'>";
            echo "<h3>🌍 Migrating Countries</h3>";
            $query = "INSERT IGNORE INTO countries (id, country_name, country_name_en, country_code) VALUES (1, 'Россия', 'Russia', 'RU')";
            $new_db->query($query);
            echo "<p class='success'>✅ Countries migrated</p>";
            echo "<p><a href='?step=regions' class='button'>Continue to Regions</a></p>";
            echo "</div>";
            break;
            
        case 'regions':
            echo "<div class='step'>";
            echo "<h3>🗺️ Migrating Regions</h3>";
            
            $query = "SELECT * FROM regions LIMIT 50";
            $result = $old_db->query($query);
            $count = 0;
            
            while ($row = $result->fetch_assoc()) {
                $insert = $new_db->prepare("
                    INSERT IGNORE INTO regions (id, country_id, region_name, region_name_en, region_name_genitive, region_name_locative, region_name_locative_en, region_image) 
                    VALUES (?, 1, ?, ?, ?, ?, ?, ?)
                ");
                
                $region_name = $row['region_name'] ?: 'Unknown';
                $region_name_en = $row['region_name_en'] ?: $region_name;
                $region_name_genitive = $row['region_name_rod'] ?: $region_name;
                $region_name_locative = $row['region_where'] ?: $region_name;
                $region_name_locative_en = $row['region_where_en'] ?: $region_name_en;
                $region_image = $row['region_img'];
                
                $insert->bind_param("issssss", 
                    $row['id_region'], 
                    $region_name, 
                    $region_name_en, 
                    $region_name_genitive, 
                    $region_name_locative, 
                    $region_name_locative_en, 
                    $region_image
                );
                $insert->execute();
                $count++;
            }
            
            echo "<p class='success'>✅ Migrated $count regions</p>";
            echo "<p><a href='?step=areas' class='button'>Continue to Areas</a></p>";
            echo "</div>";
            break;
            
        case 'areas':
            echo "<div class='step'>";
            echo "<h3>🏘️ Migrating Areas</h3>";
            
            $query = "SELECT * FROM areas LIMIT 100";
            $result = $old_db->query($query);
            $count = 0;
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $insert = $new_db->prepare("
                        INSERT IGNORE INTO areas (id, region_id, area_name, area_name_genitive, description) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    $area_name = $row['name'] ?: 'Unknown Area';
                    $area_name_genitive = $row['name_rod'] ?: $area_name;
                    $description = $row['description'];
                    
                    $insert->bind_param("iisss", 
                        $row['id_area'], 
                        $row['id_region'], 
                        $area_name, 
                        $area_name_genitive, 
                        $description
                    );
                    $insert->execute();
                    $count++;
                }
            }
            
            echo "<p class='success'>✅ Migrated $count areas</p>";
            echo "<p><a href='?step=towns' class='button'>Continue to Towns</a></p>";
            echo "</div>";
            break;
            
        case 'towns':
            echo "<div class='step'>";
            echo "<h3>🏘️ Migrating Towns</h3>";
            
            $query = "SELECT * FROM towns LIMIT 200";
            $result = $old_db->query($query);
            $count = 0;
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $insert = $new_db->prepare("
                        INSERT IGNORE INTO towns (id, area_id, region_id, country_id, town_name, town_name_en, town_name_genitive, description, image_url, url_slug) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $town_name = $row['name'] ?: 'Unknown Town';
                    $town_name_en = null;
                    $town_name_genitive = $row['name_rod'] ?: $town_name;
                    $description = $row['description'];
                    $image_url = $row['img'];
                    $url_slug = $row['url_slug_town'] ?: strtolower(str_replace(' ', '-', $town_name));
                    
                    $insert->bind_param("iiiissssss", 
                        $row['id_town'], 
                        $row['id_area'], 
                        $row['id_region'], 
                        $row['id_country'], 
                        $town_name, 
                        $town_name_en, 
                        $town_name_genitive, 
                        $description, 
                        $image_url, 
                        $url_slug
                    );
                    $insert->execute();
                    $count++;
                }
            }
            
            echo "<p class='success'>✅ Migrated $count towns</p>";
            echo "<p><a href='?step=categories' class='button'>Continue to Categories</a></p>";
            echo "</div>";
            break;

        case 'categories':
            echo "<div class='step'>";
            echo "<h3>📂 Migrating Categories</h3>";
            
            $query = "SELECT * FROM categories";
            $result = $old_db->query($query);
            $count = 0;
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $insert = $new_db->prepare("
                        INSERT IGNORE INTO categories (id, category_name, meta_description, meta_keywords, category_description, url_slug) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    
                    $insert->bind_param("isssss", 
                        $row['id_category'], 
                        $row['title_category'], 
                        $row['meta_d_category'], 
                        $row['meta_k_category'], 
                        $row['text_category'], 
                        $row['url_category']
                    );
                    $insert->execute();
                    $count++;
                }
            }
            
            echo "<p class='success'>✅ Migrated $count categories</p>";
            echo "<p><a href='?step=users' class='button'>Continue to Users</a></p>";
            echo "</div>";
            break;
            
        default:
            echo "<p class='error'>Unknown step: $step</p>";
            echo "<p><a href='?step=start' class='button'>Back to Start</a></p>";
    }

} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='?step=start'>← Back to Migration Steps</a></p>";
?>