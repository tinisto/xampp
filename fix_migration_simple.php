<?php
// Simple migration fix with better error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Migration Fix</h1>";

try {
    // Force connection to new database
    $connection = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        'admin_claude',
        'W4eZ!#9uwLmrMay',
        '11klassniki_claude'
    );

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $connection->set_charset("utf8mb4");
    echo "<p>✅ Connected to database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

    // 1. First, let's check what's in the areas table
    echo "<h2>1. Checking Areas Table</h2>";
    $areas_exist = $connection->query("SHOW TABLES LIKE 'areas'");
    if ($areas_exist && $areas_exist->num_rows > 0) {
        echo "<p>✅ Areas table exists</p>";
        
        // Count areas
        $area_count = $connection->query("SELECT COUNT(*) as count FROM areas")->fetch_assoc()['count'];
        echo "<p>Total areas: $area_count</p>";
        
        // Show sample
        echo "<p>Sample areas:</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Region ID</th></tr>";
        $sample = $connection->query("SELECT id, region_id FROM areas LIMIT 5");
        while ($row = $sample->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['region_id']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Areas table not found!</p>";
    }

    // 2. Simple solution - disable foreign key checks temporarily
    echo "<h2>2. Migration with Foreign Key Checks Disabled</h2>";
    
    if (isset($_GET['migrate']) && $_GET['migrate'] == 'yes') {
        echo "<p>Starting migration...</p>";
        
        // Disable foreign key checks
        $connection->query("SET FOREIGN_KEY_CHECKS=0");
        echo "<p>✅ Foreign key checks disabled</p>";
        
        // Migrate VPO
        echo "<h3>Migrating VPO Records</h3>";
        $vpo_query = "
            INSERT INTO universities (
                id, university_name, university_name_genitive, full_name,
                town_id, area_id, region_id, country_id,
                postal_code, street_address, phone, email, website,
                director_name, url_slug, is_approved, is_active
            )
            SELECT 
                v.id_vpo, v.vpo_name, v.vpo_name, v.vpo_name,
                COALESCE(v.id_town, 0), 1, v.id_region, 1,
                v.zip_code, v.street, v.tel, v.email, v.site,
                v.director_name, v.vpo_url, 1, 1
            FROM vpo v
            LEFT JOIN universities u ON v.id_vpo = u.id
            WHERE u.id IS NULL
        ";
        
        if ($connection->query($vpo_query)) {
            $affected = $connection->affected_rows;
            echo "<p>✅ Migrated $affected VPO records</p>";
        } else {
            echo "<p>❌ VPO migration error: " . $connection->error . "</p>";
        }
        
        // Migrate SPO (limit to 50 at a time)
        echo "<h3>Migrating SPO Records</h3>";
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        
        $spo_query = "
            INSERT INTO colleges (
                id, college_name, college_name_genitive, full_name,
                town_id, area_id, region_id, country_id,
                postal_code, street_address, phone, email, website,
                director_name, url_slug, is_approved, is_active
            )
            SELECT 
                s.id_spo, s.spo_name, s.spo_name, s.spo_name,
                COALESCE(s.id_town, 0), 1, s.id_region, 1,
                s.zip_code, s.street, s.tel, s.email, s.site,
                s.director_name, s.spo_url, 1, 1
            FROM spo s
            LEFT JOIN colleges c ON s.id_spo = c.id
            WHERE c.id IS NULL
            LIMIT $offset, 50
        ";
        
        if ($connection->query($spo_query)) {
            $affected = $connection->affected_rows;
            echo "<p>✅ Migrated $affected SPO records (batch starting at $offset)</p>";
            
            // Check if more to migrate
            $remaining = $connection->query("
                SELECT COUNT(*) as count FROM spo s 
                LEFT JOIN colleges c ON s.id_spo = c.id 
                WHERE c.id IS NULL
            ")->fetch_assoc()['count'];
            
            if ($remaining > 0) {
                $new_offset = $offset + 50;
                echo "<p>⚠️ $remaining records remaining</p>";
                echo "<p><a href='?migrate=yes&offset=$new_offset'>Continue Migration (Next Batch)</a></p>";
            } else {
                echo "<p>✅ All SPO records migrated!</p>";
            }
        } else {
            echo "<p>❌ SPO migration error: " . $connection->error . "</p>";
        }
        
        // Re-enable foreign key checks
        $connection->query("SET FOREIGN_KEY_CHECKS=1");
        echo "<p>✅ Foreign key checks re-enabled</p>";
        
    } else {
        echo "<p><a href='?migrate=yes' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Start Migration</a></p>";
    }

    // 3. Show current status
    echo "<h2>3. Current Status</h2>";
    $vpo_count = $connection->query("SELECT COUNT(*) as count FROM vpo")->fetch_assoc()['count'];
    $uni_count = $connection->query("SELECT COUNT(*) as count FROM universities")->fetch_assoc()['count'];
    $spo_count = $connection->query("SELECT COUNT(*) as count FROM spo")->fetch_assoc()['count'];
    $col_count = $connection->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'];
    
    echo "<table border='1'>";
    echo "<tr><th>Old Table</th><th>Count</th><th>New Table</th><th>Count</th><th>Difference</th></tr>";
    echo "<tr><td>vpo</td><td>$vpo_count</td><td>universities</td><td>$uni_count</td><td>" . ($vpo_count - $uni_count) . "</td></tr>";
    echo "<tr><td>spo</td><td>$spo_count</td><td>colleges</td><td>$col_count</td><td>" . ($spo_count - $col_count) . "</td></tr>";
    echo "</table>";

    $connection->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/'>← Back to Home</a></p>";
?>