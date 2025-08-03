<?php
/**
 * Final VPO/SPO fix - check data and fix issues
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🔧 Final VPO/SPO Fix</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .code { background: #f5f5f5; padding: 10px; font-family: monospace; margin: 10px 0; }
</style>";

// Get current database name
$db_result = $connection->query("SELECT DATABASE() as db_name");
$current_db = $db_result ? $db_result->fetch_assoc()['db_name'] : 'unknown';
echo "<p><strong>Current database:</strong> $current_db</p>";

// Check table counts
echo "<h2>1️⃣ Current Table Status</h2>";
$tables = ['universities', 'colleges', 'schools', 'vpo', 'spo'];

echo "<table>";
echo "<tr><th>Table</th><th>Exists</th><th>Count</th><th>Sample Data</th></tr>";

foreach ($tables as $table) {
    echo "<tr>";
    echo "<td><strong>$table</strong></td>";
    
    // Check if table exists
    $table_check = $connection->query("SHOW TABLES LIKE '$table'");
    if ($table_check && $table_check->num_rows > 0) {
        echo "<td class='success'>✅ Yes</td>";
        
        // Get count
        $count_result = $connection->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
        echo "<td>$count</td>";
        
        // Get sample data
        if ($count > 0) {
            if ($table === 'universities') {
                $sample = $connection->query("SELECT university_name, region_id FROM universities LIMIT 2");
            } elseif ($table === 'colleges') {
                $sample = $connection->query("SELECT college_name, region_id FROM colleges LIMIT 2");
            } elseif ($table === 'vpo') {
                $sample = $connection->query("SELECT vpo_name, id_region FROM vpo LIMIT 2");
            } elseif ($table === 'spo') {
                $sample = $connection->query("SELECT spo_name, id_region FROM spo LIMIT 2");
            } else {
                $sample = $connection->query("SELECT * FROM `$table` LIMIT 2");
            }
            
            echo "<td>";
            if ($sample && $sample->num_rows > 0) {
                while ($row = $sample->fetch_assoc()) {
                    echo "• " . implode(', ', array_slice($row, 0, 2)) . "<br>";
                }
            }
            echo "</td>";
        } else {
            echo "<td class='warning'>Empty table</td>";
        }
    } else {
        echo "<td class='error'>❌ No</td>";
        echo "<td>-</td>";
        echo "<td>Table doesn't exist</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Check if old VPO/SPO tables have data but new ones are empty
$vpo_count = 0;
$spo_count = 0;
$universities_count = 0;
$colleges_count = 0;

$vpo_result = $connection->query("SELECT COUNT(*) as count FROM vpo");
if ($vpo_result) $vpo_count = $vpo_result->fetch_assoc()['count'];

$spo_result = $connection->query("SELECT COUNT(*) as count FROM spo");
if ($spo_result) $spo_count = $spo_result->fetch_assoc()['count'];

$uni_result = $connection->query("SELECT COUNT(*) as count FROM universities");
if ($uni_result) $universities_count = $uni_result->fetch_assoc()['count'];

$col_result = $connection->query("SELECT COUNT(*) as count FROM colleges");
if ($col_result) $colleges_count = $col_result->fetch_assoc()['count'];

echo "<h2>2️⃣ Data Migration Status</h2>";
echo "<div class='code'>";
echo "Old VPO table: $vpo_count records<br>";
echo "New Universities table: $universities_count records<br>";
echo "Old SPO table: $spo_count records<br>";
echo "New Colleges table: $colleges_count records<br>";
echo "</div>";

// If old tables have data but new ones are empty, we need to migrate
if (($vpo_count > 0 && $universities_count == 0) || ($spo_count > 0 && $colleges_count == 0)) {
    echo "<p class='warning'>⚠️ Data migration needed!</p>";
    
    echo "<h2>3️⃣ Migrating Data</h2>";
    
    // Migrate VPO to Universities
    if ($vpo_count > 0 && $universities_count == 0) {
        echo "<h3>Migrating VPO → Universities</h3>";
        
        $migrate_vpo = $connection->query("
            INSERT INTO universities (
                university_name, 
                university_description, 
                url_slug, 
                region_id, 
                town_id,
                address,
                phone,
                email,
                website,
                accreditation_status,
                rector_name,
                established_year,
                student_count,
                faculty_count,
                created_at,
                updated_at
            )
            SELECT 
                vpo_name,
                vpo_description,
                vpo_url,
                id_region,
                id_town,
                vpo_address,
                vpo_phone,
                vpo_email,
                vpo_site,
                vpo_accreditation,
                vpo_rector,
                vpo_year,
                vpo_students,
                vpo_faculty,
                NOW(),
                NOW()
            FROM vpo
        ");
        
        if ($migrate_vpo) {
            $migrated_vpo = $connection->affected_rows;
            echo "<p class='success'>✅ Migrated $migrated_vpo VPO records to universities</p>";
        } else {
            echo "<p class='error'>❌ VPO migration failed: " . $connection->error . "</p>";
        }
    }
    
    // Migrate SPO to Colleges
    if ($spo_count > 0 && $colleges_count == 0) {
        echo "<h3>Migrating SPO → Colleges</h3>";
        
        $migrate_spo = $connection->query("
            INSERT INTO colleges (
                college_name, 
                college_description, 
                url_slug, 
                region_id, 
                town_id,
                address,
                phone,
                email,
                website,
                accreditation_status,
                director_name,
                established_year,
                student_count,
                faculty_count,
                created_at,
                updated_at
            )
            SELECT 
                spo_name,
                spo_description,
                spo_url,
                id_region,
                id_town,
                spo_address,
                spo_phone,
                spo_email,
                spo_site,
                spo_accreditation,
                spo_director,
                spo_year,
                spo_students,
                spo_faculty,
                NOW(),
                NOW()
            FROM spo
        ");
        
        if ($migrate_spo) {
            $migrated_spo = $connection->affected_rows;
            echo "<p class='success'>✅ Migrated $migrated_spo SPO records to colleges</p>";
        } else {
            echo "<p class='error'>❌ SPO migration failed: " . $connection->error . "</p>";
        }
    }
    
    // Recheck counts after migration
    $uni_result = $connection->query("SELECT COUNT(*) as count FROM universities");
    $universities_count = $uni_result ? $uni_result->fetch_assoc()['count'] : 0;
    
    $col_result = $connection->query("SELECT COUNT(*) as count FROM colleges");
    $colleges_count = $col_result ? $col_result->fetch_assoc()['count'] : 0;
    
    echo "<div class='code'>";
    echo "After migration:<br>";
    echo "Universities: $universities_count records<br>";
    echo "Colleges: $colleges_count records<br>";
    echo "</div>";
}

// Test the regional queries that the page uses
echo "<h2>4️⃣ Testing Regional Queries</h2>";

if ($universities_count > 0) {
    echo "<h3>Universities by Region Test:</h3>";
    $uni_test = $connection->query("
        SELECT r.region_name, COUNT(u.id) as count 
        FROM regions r 
        LEFT JOIN universities u ON r.id_region = u.region_id 
        WHERE r.id_country = 1 
        GROUP BY r.id_region 
        HAVING count > 0 
        ORDER BY count DESC
        LIMIT 10
    ");
    
    if ($uni_test && $uni_test->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Region</th><th>Universities</th></tr>";
        while ($row = $uni_test->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
            echo "<td>{$row['count']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ Universities regional query working</p>";
    } else {
        echo "<p class='error'>❌ No universities found by region</p>";
    }
}

if ($colleges_count > 0) {
    echo "<h3>Colleges by Region Test:</h3>";
    $col_test = $connection->query("
        SELECT r.region_name, COUNT(c.id) as count 
        FROM regions r 
        LEFT JOIN colleges c ON r.id_region = c.region_id 
        WHERE r.id_country = 1 
        GROUP BY r.id_region 
        HAVING count > 0 
        ORDER BY count DESC
        LIMIT 10
    ");
    
    if ($col_test && $col_test->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Region</th><th>Colleges</th></tr>";
        while ($row = $col_test->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
            echo "<td>{$row['count']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ Colleges regional query working</p>";
    } else {
        echo "<p class='error'>❌ No colleges found by region</p>";
    }
}

echo "<h2>📋 Final Status</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";

if ($universities_count > 0 && $colleges_count > 0) {
    echo "<p class='success'>🎉 VPO/SPO data migration complete!</p>";
    echo "<ul>";
    echo "<li>Universities: $universities_count</li>";
    echo "<li>Colleges: $colleges_count</li>";
    echo "</ul>";
    echo "<p><strong>The educational institutions pages should now work!</strong></p>";
} else {
    echo "<p class='error'>❌ Migration incomplete</p>";
    echo "<p>Universities: $universities_count, Colleges: $colleges_count</p>";
}

echo "</div>";

echo "<p><a href='/'>← Back to Home</a></p>";
echo "<p><a href='/educational-institutions-all-regions?type=vpo'>🧪 Test VPO Page</a></p>";
echo "<p><a href='/educational-institutions-all-regions?type=spo'>🧪 Test SPO Page</a></p>";

$connection->close();
?>