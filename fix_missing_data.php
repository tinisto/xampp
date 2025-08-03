<?php
/**
 * Fix missing data in new database
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>🔧 Fix Missing Data</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

if (!isset($_GET['action'])) {
    echo "<h2>Choose Action:</h2>";
    echo "<p><a href='?action=copy_areas_towns' class='button' style='background: blue; color: white; padding: 10px; text-decoration: none; margin: 5px; display: inline-block;'>1. Copy Missing Areas & Towns</a></p>";
    echo "<p><a href='?action=check_missing_vpo_spo' class='button' style='background: green; color: white; padding: 10px; text-decoration: none; margin: 5px; display: inline-block;'>2. Check Missing Universities & Colleges</a></p>";
    echo "<p><a href='?action=full_copy' class='button' style='background: red; color: white; padding: 10px; text-decoration: none; margin: 5px; display: inline-block;'>3. Full Copy All Missing Data</a></p>";
    exit;
}

try {
    // Connect to both databases
    $old_db = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        '11klone_user',
        'K8HqqBV3hTf4mha',
        '11klassniki_ru'
    );
    
    if ($old_db->connect_error) {
        die("<p class='error'>❌ Could not connect to old database: " . $old_db->connect_error . "</p>");
    }
    
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
    
    $action = $_GET['action'];
    
    switch($action) {
        case 'copy_areas_towns':
            echo "<h2>📁 Copying Missing Areas & Towns...</h2>";
            
            // First, clear existing data to avoid duplicates
            echo "<p>Clearing existing areas and towns...</p>";
            $new_db->query("SET FOREIGN_KEY_CHECKS = 0");
            $new_db->query("TRUNCATE TABLE towns");
            $new_db->query("TRUNCATE TABLE areas");
            $new_db->query("SET FOREIGN_KEY_CHECKS = 1");
            
            // Copy ALL areas
            echo "<h3>Copying Areas...</h3>";
            $areas_query = "SELECT * FROM areas ORDER BY id_area";
            $areas_result = $old_db->query($areas_query);
            $areas_count = 0;
            
            while ($row = $areas_result->fetch_assoc()) {
                $insert = $new_db->prepare("
                    INSERT INTO areas (id, region_id, area_name, area_name_genitive, description)
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
            echo "<p class='success'>✅ Copied $areas_count areas</p>";
            
            // Copy ALL towns
            echo "<h3>Copying Towns...</h3>";
            $towns_query = "SELECT * FROM towns ORDER BY id_town";
            $towns_result = $old_db->query($towns_query);
            $towns_count = 0;
            
            while ($row = $towns_result->fetch_assoc()) {
                $insert = $new_db->prepare("
                    INSERT INTO towns (id, area_id, region_id, country_id, town_name, town_name_genitive, description, image_url, url_slug)
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
            echo "<p class='success'>✅ Copied $towns_count towns</p>";
            break;
            
        case 'check_missing_vpo_spo':
            echo "<h2>🔍 Checking Missing Universities & Colleges...</h2>";
            
            // Find missing VPO/Universities
            echo "<h3>Missing Universities (VPO):</h3>";
            $missing_vpo = $old_db->query("
                SELECT v.id_vpo, v.vpo_name, v.id_town, t.name as town_name
                FROM vpo v
                LEFT JOIN towns t ON v.id_town = t.id_town
                WHERE v.id_vpo NOT IN (SELECT id FROM 11klassniki_new.universities)
                ORDER BY v.id_vpo
            ");
            
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Town ID</th><th>Town Name</th></tr>";
            while ($row = $missing_vpo->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id_vpo']}</td>";
                echo "<td>{$row['vpo_name']}</td>";
                echo "<td>{$row['id_town']}</td>";
                echo "<td>{$row['town_name']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Find missing SPO/Colleges
            echo "<h3>Missing Colleges (SPO):</h3>";
            $missing_spo = $old_db->query("
                SELECT s.id_spo, s.spo_name, s.id_town, t.name as town_name
                FROM spo s
                LEFT JOIN towns t ON s.id_town = t.id_town
                WHERE s.id_spo NOT IN (SELECT id FROM 11klassniki_new.colleges)
                ORDER BY s.id_spo
                LIMIT 20
            ");
            
            echo "<table>";
            echo "<tr><th>ID</th><th>Name</th><th>Town ID</th><th>Town Name</th></tr>";
            while ($row = $missing_spo->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id_spo']}</td>";
                echo "<td>{$row['spo_name']}</td>";
                echo "<td>{$row['id_town']}</td>";
                echo "<td>{$row['town_name']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p class='info'>Showing first 20 missing colleges...</p>";
            break;
            
        case 'full_copy':
            echo "<h2>🔄 Full Copy of All Missing Data...</h2>";
            
            // This will copy everything that's missing
            echo "<p class='warning'>⚠️ This is a comprehensive operation...</p>";
            
            // Re-run the migration for missing universities
            echo "<h3>Copying missing universities...</h3>";
            $result = $new_db->query("
                INSERT IGNORE INTO universities (
                    id, user_id, parent_university_id, university_name, university_name_genitive,
                    full_name, short_name, old_names, town_id, area_id, region_id, country_id,
                    postal_code, street_address, phone, fax, email, website,
                    director_name, director_role, director_info, director_email, director_phone,
                    accreditation, license, founding_year, meta_description, meta_keywords,
                    history, url_slug, image_1, image_2, image_3, vkontakte_url,
                    view_count, is_approved, is_active, created_at, updated_at
                )
                SELECT 
                    id_vpo, user_id,
                    CASE WHEN parent_vpo_id = 0 THEN NULL ELSE parent_vpo_id END,
                    vpo_name, name_rod, full_name, short_name, old_name,
                    id_town, id_area, id_region, id_country,
                    zip_code, street, tel, fax, email, site,
                    director_name, director_role, director_info, director_email, director_phone,
                    accreditation, licence, year, meta_d_vpo, meta_k_vpo,
                    history, vpo_url, image_vpo_1, image_vpo_2, image_vpo_3, vkontakte,
                    view, approved, 1, updated, updated
                FROM 11klassniki_new.vpo
                WHERE id_vpo NOT IN (SELECT id FROM universities)
            ");
            echo "<p>Added " . $new_db->affected_rows . " missing universities</p>";
            
            // Re-run the migration for missing colleges
            echo "<h3>Copying missing colleges...</h3>";
            $result = $new_db->query("
                INSERT IGNORE INTO colleges (
                    id, user_id, parent_college_id, college_name, college_name_genitive,
                    full_name, short_name, old_names, town_id, area_id, region_id, country_id,
                    postal_code, street_address, phone, fax, email, website,
                    director_name, director_role, director_info, director_email, director_phone,
                    accreditation, license, founding_year, meta_description, meta_keywords,
                    history, url_slug, image_1, image_2, image_3, vkontakte_url,
                    view_count, is_approved, is_active, created_at, updated_at
                )
                SELECT 
                    id_spo, user_id,
                    CASE WHEN parent_spo_id = 0 THEN NULL ELSE parent_spo_id END,
                    spo_name, name_rod, full_name, short_name, old_name,
                    id_town, id_area, id_region, id_country,
                    zip_code, street, tel, fax, email, site,
                    director_name, director_role, director_info, director_email, director_phone,
                    accreditation, licence, year, meta_d_spo, meta_k_spo,
                    history, spo_url, image_spo_1, image_spo_2, image_spo_3, vkontakte,
                    view, approved, 1, updated, updated
                FROM 11klassniki_new.spo
                WHERE id_spo NOT IN (SELECT id FROM colleges)
            ");
            echo "<p>Added " . $new_db->affected_rows . " missing colleges</p>";
            
            echo "<p class='success'>✅ Full copy complete!</p>";
            break;
    }
    
    // Show final counts
    echo "<h2>📊 Final Verification:</h2>";
    $tables = [
        'areas' => 'Areas',
        'towns' => 'Towns', 
        'universities' => 'Universities',
        'colleges' => 'Colleges'
    ];
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Count</th></tr>";
    foreach ($tables as $table => $name) {
        $result = $new_db->query("SELECT COUNT(*) as count FROM $table");
        $count = $result->fetch_assoc()['count'];
        echo "<tr><td>$name</td><td>$count</td></tr>";
    }
    echo "</table>";
    
    $old_db->close();
    $new_db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/fix_missing_data.php'>← Back to Actions</a> | <a href='/final_db_comparison.php'>Check Comparison</a></p>";
?>