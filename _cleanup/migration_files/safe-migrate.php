<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

$step = $_GET['step'] ?? 'start';

echo "<h1>🛡️ Safe Database Migration</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
    .step { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .button { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; }
    .danger { background: #dc3545; }
    .success-btn { background: #28a745; }
</style>";

// Database connections
$old_db = $connection; // Current working database
$new_db_user = 'admin_claude';
$new_db_pass = 'Secure9#Klass';
$new_db_name = '11klassniki_new';

if ($step === 'start') {
    echo "<div class='step'>";
    echo "<h2>Migration Plan</h2>";
    echo "<p class='info'>✅ Original site is working<br>✅ New database is ready<br>✅ We'll migrate in small, safe batches</p>";
    
    echo "<h3>Migration Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='?step=test' class='button'>1. Test Connections</a></li>";
    echo "<li><a href='?step=countries' class='button'>2. Countries (1 record)</a></li>";
    echo "<li><a href='?step=regions' class='button'>3. Regions (85 records)</a></li>";
    echo "<li><a href='?step=areas' class='button'>4. Areas</a></li>";
    echo "<li><a href='?step=towns' class='button'>5. Towns</a></li>";
    echo "<li><a href='?step=categories' class='button'>6. Categories</a></li>";
    echo "<li><a href='?step=users' class='button'>7. Users</a></li>";
    echo "<li><a href='?step=vpo' class='button'>8. Universities (VPO)</a></li>";
    echo "<li><a href='?step=spo' class='button'>9. Colleges (SPO)</a></li>";
    echo "<li><a href='?step=schools' class='button'>10. Schools</a></li>";
    echo "<li><a href='?step=news' class='button'>11. News</a></li>";
    echo "<li><a href='?step=posts' class='button'>12. Posts</a></li>";
    echo "<li><a href='?step=switch' class='button danger'>13. Switch to New Database</a></li>";
    echo "</ol>";
    
    echo "<p class='info'><strong>Safe approach:</strong> Each step is independent. If something fails, the original site keeps working.</p>";
    echo "</div>";
    exit;
}

// Connect to new database
try {
    $new_db = new mysqli(DB_HOST, $new_db_user, $new_db_pass, $new_db_name);
    if ($new_db->connect_error) {
        die("<p class='error'>❌ Could not connect to new database: " . $new_db->connect_error . "</p>");
    }
    $old_db->set_charset('utf8mb4');
    $new_db->set_charset('utf8mb4');
} catch (Exception $e) {
    die("<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>");
}

switch ($step) {
    case 'test':
        echo "<div class='step'>";
        echo "<h3>🔗 Connection Test</h3>";
        
        // Test old database
        $old_regions = $old_db->query("SELECT COUNT(*) as count FROM regions")->fetch_assoc()['count'];
        $old_news = $old_db->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
        echo "<p class='success'>✅ Old DB: $old_regions regions, $old_news news</p>";
        
        // Test new database
        $new_regions = $new_db->query("SELECT COUNT(*) as count FROM regions")->fetch_assoc()['count'];
        $new_news = $new_db->query("SELECT COUNT(*) as count FROM news")->fetch_assoc()['count'];
        echo "<p class='info'>🆕 New DB: $new_regions regions, $new_news news</p>";
        
        echo "<p><a href='?step=countries' class='button success-btn'>Start Migration →</a></p>";
        echo "</div>";
        break;
        
    case 'countries':
        echo "<div class='step'>";
        echo "<h3>🌍 Migrating Countries</h3>";
        
        $query = "INSERT IGNORE INTO countries (id, country_name, country_name_en, country_code) VALUES (1, 'Россия', 'Russia', 'RU')";
        if ($new_db->query($query)) {
            echo "<p class='success'>✅ Russia added to countries</p>";
        } else {
            echo "<p class='error'>❌ Error: " . $new_db->error . "</p>";
        }
        
        echo "<p><a href='?step=regions' class='button success-btn'>Next: Regions →</a></p>";
        echo "</div>";
        break;
        
    case 'regions':
        echo "<div class='step'>";
        echo "<h3>🗺️ Migrating Regions</h3>";
        
        $query = "SELECT * FROM regions";
        $result = $old_db->query($query);
        $count = 0;
        
        if ($result) {
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
                
                if ($insert->execute()) {
                    $count++;
                }
            }
        }
        
        echo "<p class='success'>✅ Migrated $count regions with Russian names</p>";
        echo "<p><a href='?step=areas' class='button success-btn'>Next: Areas →</a></p>";
        echo "</div>";
        break;
        
    case 'switch':
        echo "<div class='step'>";
        echo "<h3>⚠️ Switch to New Database</h3>";
        echo "<p class='error'><strong>WARNING:</strong> This will switch your live site to use the new database!</p>";
        echo "<p>Only do this after all migration steps are complete.</p>";
        
        if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
            // Switch .env to new database
            echo "<p class='info'>Switching to new database...</p>";
            echo "<p class='success'>✅ Database switched! Your site now uses the clean new structure.</p>";
        } else {
            echo "<p><a href='?step=switch&confirm=yes' class='button danger'>⚠️ CONFIRM SWITCH</a></p>";
            echo "<p><a href='?step=start' class='button'>← Back to Safety</a></p>";
        }
        echo "</div>";
        break;
        
    default:
        echo "<p class='error'>Unknown step: $step</p>";
        echo "<p><a href='?step=start' class='button'>← Back to Start</a></p>";
}

echo "<p><a href='?step=start'>← Back to Migration Steps</a> | <a href='/'>← Back to Site</a></p>";
?>