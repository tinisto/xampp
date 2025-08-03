<?php
/**
 * Migrate remaining colleges from SPO table
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>🔄 Migrating Remaining Colleges</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
</style>";

try {
    // Connect to new database only (both tables are there)
    $db = new mysqli(
        DB_HOST,
        'admin_claude',
        'Secure9#Klass',
        '11klassniki_new'
    );
    
    if ($db->connect_error) {
        die("<p class='error'>❌ Could not connect to database: " . $db->connect_error . "</p>");
    }
    
    $db->set_charset('utf8mb4');
    
    echo "<p class='success'>✅ Connected to database</p>";
    
    // Count before
    $before_count = $db->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'];
    echo "<p>Colleges before migration: <strong>$before_count</strong></p>";
    
    // Migrate remaining colleges
    echo "<h2>🚀 Migrating colleges...</h2>";
    
    $migrate_query = "
        INSERT INTO colleges (
            id, user_id, parent_college_id, college_name, college_name_genitive,
            full_name, short_name, old_names, town_id, area_id, region_id, country_id,
            postal_code, street_address, phone, fax, email, website,
            director_name, director_role, director_info, director_email, director_phone,
            accreditation, license, founding_year, meta_description, meta_keywords,
            history, url_slug, image_1, image_2, image_3, vkontakte_url,
            view_count, is_approved, is_active, created_at, updated_at
        )
        SELECT 
            s.id_spo, 
            s.user_id,
            CASE WHEN s.parent_spo_id = 0 THEN NULL ELSE s.parent_spo_id END,
            s.spo_name,
            IFNULL(s.name_rod, s.spo_name),
            s.full_name,
            s.short_name,
            s.old_name,
            s.id_town,
            s.id_area,
            s.id_region,
            s.id_country,
            s.zip_code,
            s.street,
            s.tel,
            s.fax,
            s.email,
            s.site,
            s.director_name,
            s.director_role,
            s.director_info,
            s.director_email,
            s.director_phone,
            s.accreditation,
            s.licence,
            s.year,
            s.meta_d_spo,
            s.meta_k_spo,
            s.history,
            s.spo_url,
            s.image_spo_1,
            s.image_spo_2,
            s.image_spo_3,
            s.vkontakte,
            s.view,
            s.approved,
            1,
            IFNULL(s.updated, NOW()),
            IFNULL(s.updated, NOW())
        FROM spo s
        WHERE s.id_spo NOT IN (SELECT id FROM colleges)
        AND s.id_town IN (SELECT id FROM towns)
        AND s.id_area IN (SELECT id FROM areas)
        AND s.id_region IN (SELECT id FROM regions)
    ";
    
    if ($db->query($migrate_query)) {
        $migrated = $db->affected_rows;
        echo "<p class='success'>✅ Successfully migrated $migrated colleges</p>";
        
        // Count after
        $after_count = $db->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'];
        echo "<p>Colleges after migration: <strong>$after_count</strong></p>";
        echo "<p>Total added: <strong>" . ($after_count - $before_count) . "</strong></p>";
        
        // Check if any were skipped due to invalid foreign keys
        $skipped_query = "
            SELECT COUNT(*) as count 
            FROM spo s
            WHERE s.id_spo NOT IN (SELECT id FROM colleges)
        ";
        
        $skipped_result = $db->query($skipped_query);
        $skipped = $skipped_result->fetch_assoc()['count'];
        
        if ($skipped > 0) {
            echo "<h3 class='warning'>⚠️ Some colleges were skipped</h3>";
            echo "<p>$skipped colleges could not be migrated due to invalid location references.</p>";
            
            // Show details of skipped
            $details_query = "
                SELECT s.id_spo, s.spo_name, s.id_town, s.id_area, s.id_region
                FROM spo s
                WHERE s.id_spo NOT IN (SELECT id FROM colleges)
                LIMIT 20
            ";
            
            $details_result = $db->query($details_query);
            echo "<table style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr style='background: #f0f0f0;'><th style='border: 1px solid #ddd; padding: 8px;'>ID</th><th style='border: 1px solid #ddd; padding: 8px;'>Name</th><th style='border: 1px solid #ddd; padding: 8px;'>Town ID</th><th style='border: 1px solid #ddd; padding: 8px;'>Area ID</th><th style='border: 1px solid #ddd; padding: 8px;'>Region ID</th></tr>";
            while ($row = $details_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$row['id_spo']}</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$row['spo_name']}</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$row['id_town']}</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$row['id_area']}</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>{$row['id_region']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p class='info'>These colleges reference towns/areas/regions that don't exist in the new database.</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Migration failed: " . $db->error . "</p>";
    }
    
    // Final summary
    echo "<h2>📊 Final Summary</h2>";
    
    $final_counts = [
        'regions' => $db->query("SELECT COUNT(*) as count FROM regions")->fetch_assoc()['count'],
        'areas' => $db->query("SELECT COUNT(*) as count FROM areas")->fetch_assoc()['count'],
        'towns' => $db->query("SELECT COUNT(*) as count FROM towns")->fetch_assoc()['count'],
        'universities' => $db->query("SELECT COUNT(*) as count FROM universities")->fetch_assoc()['count'],
        'colleges' => $db->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'],
        'schools' => $db->query("SELECT COUNT(*) as count FROM schools")->fetch_assoc()['count'],
        'news' => $db->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'],
        'posts' => $db->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'],
        'comments' => $db->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count']
    ];
    
    echo "<table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Table</th><th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Record Count</th></tr>";
    foreach ($final_counts as $table => $count) {
        echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'>" . ucfirst($table) . "</td><td style='border: 1px solid #ddd; padding: 8px;'>$count</td></tr>";
    }
    echo "</table>";
    
    echo "<p class='success'>🎉 <strong>Migration process complete!</strong></p>";
    
    $db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/final_db_comparison.php'>← Check Final Comparison</a> | <a href='/'>Home</a></p>";
?>