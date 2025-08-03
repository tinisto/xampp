<?php
// Check what's happening with the remaining 100 records
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

echo "<h1>Checking Remaining Records</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// 1. Check the missing SPO records
echo "<h2>1. Missing SPO Records Analysis</h2>";

$missing_query = "
    SELECT s.id_spo, s.spo_name, s.id_region, s.id_town 
    FROM spo s
    LEFT JOIN colleges c ON s.id_spo = c.id
    WHERE c.id IS NULL
    ORDER BY s.id_spo
    LIMIT 20
";

$result = $connection->query($missing_query);
if ($result) {
    echo "<p>First 20 missing records:</p>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Region</th><th>Town</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id_spo']}</td>";
        echo "<td>" . htmlspecialchars($row['spo_name']) . "</td>";
        echo "<td>{$row['id_region']}</td>";
        echo "<td>{$row['id_town']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 2. Check for duplicate IDs or other issues
echo "<h2>2. Checking for Issues</h2>";

// Check max ID in SPO
$max_spo = $connection->query("SELECT MAX(id_spo) as max_id FROM spo")->fetch_assoc()['max_id'];
echo "<p>Maximum ID in SPO table: $max_spo</p>";

// Check max ID in colleges
$max_col = $connection->query("SELECT MAX(id) as max_id FROM colleges")->fetch_assoc()['max_id'];
echo "<p>Maximum ID in colleges table: $max_col</p>";

// Check for duplicate IDs in SPO
$dup_query = "SELECT id_spo, COUNT(*) as count FROM spo GROUP BY id_spo HAVING count > 1";
$dup_result = $connection->query($dup_query);
if ($dup_result && $dup_result->num_rows > 0) {
    echo "<p style='color: red;'>⚠️ Found duplicate IDs in SPO table:</p>";
    while ($row = $dup_result->fetch_assoc()) {
        echo "<p>ID {$row['id_spo']} appears {$row['count']} times</p>";
    }
} else {
    echo "<p>✅ No duplicate IDs in SPO table</p>";
}

// 3. Try to migrate with explicit values
echo "<h2>3. Manual Migration Option</h2>";

if (isset($_GET['force']) && $_GET['force'] == 'yes') {
    echo "<p>Attempting force migration...</p>";
    
    // Disable foreign key checks
    $connection->query("SET FOREIGN_KEY_CHECKS=0");
    
    // Get all missing records
    $missing_all = "
        SELECT s.* 
        FROM spo s
        LEFT JOIN colleges c ON s.id_spo = c.id
        WHERE c.id IS NULL
    ";
    
    $all_result = $connection->query($missing_all);
    if ($all_result) {
        $success = 0;
        $failed = 0;
        
        while ($spo = $all_result->fetch_assoc()) {
            // Build insert query with minimal required fields
            $id = $spo['id_spo'];
            $name = $connection->real_escape_string($spo['spo_name']);
            $region = $spo['id_region'] ?: 1;
            $town = $spo['id_town'] ?: 0;
            
            $insert = "INSERT IGNORE INTO colleges 
                (id, college_name, college_name_genitive, full_name, 
                 region_id, town_id, area_id, country_id, is_approved, is_active) 
                VALUES 
                ($id, '$name', '$name', '$name', 
                 $region, $town, 1, 1, 1, 1)";
            
            if ($connection->query($insert)) {
                if ($connection->affected_rows > 0) {
                    $success++;
                }
            } else {
                $failed++;
                echo "<p style='color: red;'>Failed to insert ID $id: " . $connection->error . "</p>";
            }
        }
        
        echo "<p>✅ Successfully migrated: $success records</p>";
        echo "<p>❌ Failed: $failed records</p>";
    }
    
    // Re-enable foreign key checks
    $connection->query("SET FOREIGN_KEY_CHECKS=1");
    
} else {
    echo "<p><a href='?force=yes' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Force Migrate All Remaining Records</a></p>";
    echo "<p style='color: orange;'>⚠️ This will attempt to migrate all remaining records at once with minimal validation</p>";
}

// 4. Final status
echo "<h2>4. Final Status</h2>";
$spo_count = $connection->query("SELECT COUNT(*) as count FROM spo")->fetch_assoc()['count'];
$col_count = $connection->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'];
$missing_count = $spo_count - $col_count;

echo "<p>SPO records: $spo_count</p>";
echo "<p>Colleges records: $col_count</p>";
echo "<p>Missing: $missing_count</p>";

$connection->close();

echo "<hr>";
echo "<p><a href='/fix_migration_simple.php'>← Back to Migration Script</a></p>";
?>