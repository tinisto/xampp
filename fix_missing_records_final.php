<?php
// Fix missing records in universities and colleges tables - FINAL VERSION
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

echo "<h1>Fixing Missing Records in New Database</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// 1. Find missing VPO records
echo "<h2>1. Finding Missing VPO Records</h2>";

$missing_vpo_query = "
    SELECT v.* 
    FROM vpo v
    LEFT JOIN universities u ON v.id_vpo = u.id
    WHERE u.id IS NULL
";

$missing_vpo_result = $connection->query($missing_vpo_query);

if (!$missing_vpo_result) {
    echo "<p style='color: red;'>Query error: " . $connection->error . "</p>";
} else {
    $missing_vpo_count = $missing_vpo_result->num_rows;
    echo "<p>Found $missing_vpo_count missing VPO records</p>";

    if ($missing_vpo_count > 0) {
        echo "<h3>Migrating Missing VPO Records:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th></tr>";
        
        $insert_count = 0;
        while ($vpo = $missing_vpo_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$vpo['id_vpo']}</td>";
            echo "<td>" . htmlspecialchars($vpo['vpo_name']) . "</td>";
            
            // Prepare insert query with correct column names
            $insert_query = "INSERT INTO universities (
                id, university_name, university_name_genitive, full_name,
                town_id, area_id, region_id, country_id,
                postal_code, street_address, phone, fax, email, website,
                director_name, accreditation, license, founding_year,
                url_slug, view_count, is_approved, is_active,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $connection->prepare($insert_query);
            
            if (!$stmt) {
                echo "<td style='color: red;'>❌ Prepare error: " . $connection->error . "</td>";
                continue;
            }
            
            // Map VPO fields to Universities fields
            $genitive_name = $vpo['vpo_name'] . ' (род. падеж)'; // Placeholder for genitive
            $full_name = $vpo['vpo_name']; // Use same as name
            $town_id = $vpo['id_town'] ?: 0;
            $area_id = 0; // Will need to be updated
            $country_id = 1; // Russia
            $view_count = 0;
            $is_approved = 1;
            $is_active = 1;
            
            $stmt->bind_param(
                "isssiiiiisssssssssiiii",
                $vpo['id_vpo'],
                $vpo['vpo_name'],
                $genitive_name,
                $full_name,
                $town_id,
                $area_id,
                $vpo['id_region'],
                $country_id,
                $vpo['zip_code'],
                $vpo['street'],
                $vpo['tel'],
                $vpo['tel'], // Use phone as fax
                $vpo['email'],
                $vpo['site'],
                $vpo['director_name'],
                $vpo['accreditation'],
                $vpo['accreditation'], // Use accreditation as license
                $vpo['founded_year'],
                $vpo['vpo_url'],
                $view_count,
                $is_approved,
                $is_active
            );
            
            if ($stmt->execute()) {
                echo "<td style='color: green;'>✅ Migrated</td>";
                $insert_count++;
            } else {
                echo "<td style='color: red;'>❌ Error: " . $stmt->error . "</td>";
            }
            echo "</tr>";
            
            $stmt->close();
        }
        echo "</table>";
        echo "<p style='color: green;'>Successfully migrated $insert_count VPO records</p>";
    }
}

// 2. Find missing SPO records
echo "<h2>2. Finding Missing SPO Records</h2>";

// First get total count
$total_missing_query = "
    SELECT COUNT(*) as count 
    FROM spo s
    LEFT JOIN colleges c ON s.id_spo = c.id
    WHERE c.id IS NULL
";

$total_result = $connection->query($total_missing_query);
if ($total_result) {
    $total_missing_spo = $total_result->fetch_assoc()['count'];
    echo "<p>Found $total_missing_spo total missing SPO records</p>";
}

// Get records to migrate (limit to 50 at a time)
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$missing_spo_query = "
    SELECT s.* 
    FROM spo s
    LEFT JOIN colleges c ON s.id_spo = c.id
    WHERE c.id IS NULL
    LIMIT $offset, 50
";

$missing_spo_result = $connection->query($missing_spo_query);

if (!$missing_spo_result) {
    echo "<p style='color: red;'>Query error: " . $connection->error . "</p>";
} else {
    $missing_spo_count = $missing_spo_result->num_rows;

    if ($missing_spo_count > 0) {
        echo "<h3>Migrating SPO Records (Records " . ($offset + 1) . " to " . ($offset + $missing_spo_count) . "):</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th></tr>";
        
        $insert_count = 0;
        while ($spo = $missing_spo_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$spo['id_spo']}</td>";
            echo "<td>" . htmlspecialchars($spo['spo_name']) . "</td>";
            
            // Prepare insert query with correct column names
            $insert_query = "INSERT INTO colleges (
                id, college_name, college_name_genitive, full_name,
                town_id, area_id, region_id, country_id,
                postal_code, street_address, phone, fax, email, website,
                director_name, accreditation, license, founding_year,
                url_slug, view_count, is_approved, is_active,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $connection->prepare($insert_query);
            
            if (!$stmt) {
                echo "<td style='color: red;'>❌ Prepare error: " . $connection->error . "</td>";
                continue;
            }
            
            // Map SPO fields to Colleges fields
            $genitive_name = $spo['spo_name'] . ' (род. падеж)'; // Placeholder for genitive
            $full_name = $spo['spo_name']; // Use same as name
            $town_id = $spo['id_town'] ?: 0;
            $area_id = 0; // Will need to be updated
            $country_id = 1; // Russia
            $founded_year = $spo['founded_year'] ?: 0;
            $view_count = 0;
            $is_approved = 1;
            $is_active = 1;
            
            $stmt->bind_param(
                "isssiiiiisssssssssiiii",
                $spo['id_spo'],
                $spo['spo_name'],
                $genitive_name,
                $full_name,
                $town_id,
                $area_id,
                $spo['id_region'],
                $country_id,
                $spo['zip_code'],
                $spo['street'],
                $spo['tel'],
                $spo['tel'], // Use phone as fax
                $spo['email'],
                $spo['site'],
                $spo['director_name'],
                $spo['accreditation'],
                $spo['accreditation'], // Use accreditation as license
                $founded_year,
                $spo['spo_url'],
                $view_count,
                $is_approved,
                $is_active
            );
            
            if ($stmt->execute()) {
                echo "<td style='color: green;'>✅ Migrated</td>";
                $insert_count++;
            } else {
                echo "<td style='color: red;'>❌ Error: " . $stmt->error . "</td>";
            }
            echo "</tr>";
            
            $stmt->close();
        }
        echo "</table>";
        echo "<p style='color: green;'>Successfully migrated $insert_count SPO records</p>";
        
        $new_offset = $offset + 50;
        if (isset($total_missing_spo) && $new_offset < $total_missing_spo) {
            $remaining = $total_missing_spo - $new_offset;
            echo "<p style='color: orange;'>⚠️ There are $remaining more SPO records to migrate.</p>";
            echo "<p><a href='?offset=$new_offset' class='btn btn-primary'>Continue Migration (Next 50 Records)</a></p>";
        } else {
            echo "<p style='color: green;'>✅ All SPO records have been processed!</p>";
        }
    }
}

// 3. Final verification
echo "<h2>3. Final Verification</h2>";
$vpo_count = $connection->query("SELECT COUNT(*) as count FROM vpo")->fetch_assoc()['count'] ?? 0;
$uni_count = $connection->query("SELECT COUNT(*) as count FROM universities")->fetch_assoc()['count'] ?? 0;
$spo_count = $connection->query("SELECT COUNT(*) as count FROM spo")->fetch_assoc()['count'] ?? 0;
$col_count = $connection->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'] ?? 0;

echo "<table border='1'>";
echo "<tr><th>Old Table</th><th>Count</th><th>New Table</th><th>Count</th><th>Status</th></tr>";
echo "<tr>";
echo "<td>vpo</td><td>$vpo_count</td>";
echo "<td>universities</td><td>$uni_count</td>";
echo "<td>" . ($vpo_count == $uni_count ? "✅ Synced" : "⚠️ Not synced (diff: " . ($vpo_count - $uni_count) . ")") . "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>spo</td><td>$spo_count</td>";
echo "<td>colleges</td><td>$col_count</td>";
echo "<td>" . ($spo_count == $col_count ? "✅ Synced" : "⚠️ Not synced (diff: " . ($spo_count - $col_count) . ")") . "</td>";
echo "</tr>";
echo "</table>";

echo "<hr>";
echo "<p><a href='/check_current_database.php'>← Back to Database Analysis</a></p>";

$connection->close();
?>