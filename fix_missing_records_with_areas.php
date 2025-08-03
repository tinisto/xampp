<?php
// Fix missing records with proper area handling
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

echo "<h1>Fixing Missing Records with Area Mapping</h1>";
echo "<p>Database: " . $connection->query("SELECT DATABASE()")->fetch_assoc()['DATABASE()'] . "</p>";

// First, let's understand the area structure
echo "<h2>Understanding Areas Table</h2>";
$areas_check = $connection->query("SELECT * FROM areas LIMIT 5");
if ($areas_check) {
    echo "<p>Sample areas:</p>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Area Name</th><th>Region ID</th></tr>";
    while ($area = $areas_check->fetch_assoc()) {
        echo "<tr><td>{$area['id']}</td><td>{$area['area_name'] ?? 'N/A'}</td><td>{$area['region_id'] ?? 'N/A'}</td></tr>";
    }
    echo "</table>";
}

// Get default area for each region
echo "<h2>Creating Region to Area Mapping</h2>";
$region_area_map = [];
$area_query = "SELECT id, region_id FROM areas WHERE region_id IS NOT NULL";
$area_result = $connection->query($area_query);
if ($area_result) {
    while ($row = $area_result->fetch_assoc()) {
        if (!isset($region_area_map[$row['region_id']])) {
            $region_area_map[$row['region_id']] = $row['id'];
        }
    }
    echo "<p>Found area mappings for " . count($region_area_map) . " regions</p>";
}

// Function to get area_id for a region
function getAreaIdForRegion($region_id, $town_id, $connection, &$region_area_map) {
    // First try to find area by town
    if ($town_id) {
        $town_area = $connection->query("SELECT area_id FROM towns WHERE id = $town_id");
        if ($town_area && $town_area->num_rows > 0) {
            $area_id = $town_area->fetch_assoc()['area_id'];
            if ($area_id) return $area_id;
        }
    }
    
    // If no town area, use region mapping
    if (isset($region_area_map[$region_id])) {
        return $region_area_map[$region_id];
    }
    
    // If still no area, find any area for this region
    $any_area = $connection->query("SELECT id FROM areas WHERE region_id = $region_id LIMIT 1");
    if ($any_area && $any_area->num_rows > 0) {
        $area_id = $any_area->fetch_assoc()['id'];
        $region_area_map[$region_id] = $area_id;
        return $area_id;
    }
    
    // Last resort: create a default area for this region
    $region_name = $connection->query("SELECT region_name FROM regions WHERE id_region = $region_id")->fetch_assoc()['region_name'] ?? "Region $region_id";
    $insert_area = $connection->prepare("INSERT INTO areas (area_name, region_id, country_id) VALUES (?, ?, 1)");
    $default_area_name = "Основной район - " . $region_name;
    $insert_area->bind_param("si", $default_area_name, $region_id);
    if ($insert_area->execute()) {
        $new_area_id = $connection->insert_id;
        $region_area_map[$region_id] = $new_area_id;
        return $new_area_id;
    }
    
    return null;
}

// 1. Find missing VPO records
echo "<h2>1. Migrating Missing VPO Records</h2>";

$missing_vpo_query = "
    SELECT v.* 
    FROM vpo v
    LEFT JOIN universities u ON v.id_vpo = u.id
    WHERE u.id IS NULL
";

$missing_vpo_result = $connection->query($missing_vpo_query);

if ($missing_vpo_result && $missing_vpo_result->num_rows > 0) {
    echo "<p>Found {$missing_vpo_result->num_rows} missing VPO records</p>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Region</th><th>Status</th></tr>";
    
    $insert_count = 0;
    while ($vpo = $missing_vpo_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$vpo['id_vpo']}</td>";
        echo "<td>" . htmlspecialchars($vpo['vpo_name']) . "</td>";
        echo "<td>{$vpo['id_region']}</td>";
        
        // Get proper area_id
        $area_id = getAreaIdForRegion($vpo['id_region'], $vpo['id_town'] ?? null, $connection, $region_area_map);
        
        if (!$area_id) {
            echo "<td style='color: red;'>❌ Could not find/create area for region {$vpo['id_region']}</td>";
            continue;
        }
        
        // Prepare insert query
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
        
        // Map VPO fields
        $genitive_name = $vpo['vpo_name'];
        $full_name = $vpo['vpo_name'];
        $town_id = $vpo['id_town'] ?: 0;
        $country_id = 1;
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
            $vpo['tel'],
            $vpo['email'],
            $vpo['site'],
            $vpo['director_name'],
            $vpo['accreditation'],
            $vpo['accreditation'],
            $vpo['founded_year'],
            $vpo['vpo_url'],
            $view_count,
            $is_approved,
            $is_active
        );
        
        if ($stmt->execute()) {
            echo "<td style='color: green;'>✅ Migrated (area: $area_id)</td>";
            $insert_count++;
        } else {
            echo "<td style='color: red;'>❌ Error: " . $stmt->error . "</td>";
        }
        echo "</tr>";
        
        $stmt->close();
    }
    echo "</table>";
    echo "<p style='color: green;'>Successfully migrated $insert_count VPO records</p>";
} else {
    echo "<p>No missing VPO records found</p>";
}

// 2. Find missing SPO records
echo "<h2>2. Migrating Missing SPO Records</h2>";

// Get total count
$total_missing_query = "
    SELECT COUNT(*) as count 
    FROM spo s
    LEFT JOIN colleges c ON s.id_spo = c.id
    WHERE c.id IS NULL
";

$total_result = $connection->query($total_missing_query);
$total_missing_spo = $total_result ? $total_result->fetch_assoc()['count'] : 0;
echo "<p>Total missing SPO records: $total_missing_spo</p>";

// Process in batches
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$batch_size = 20; // Smaller batch to avoid timeout

$missing_spo_query = "
    SELECT s.* 
    FROM spo s
    LEFT JOIN colleges c ON s.id_spo = c.id
    WHERE c.id IS NULL
    LIMIT $offset, $batch_size
";

$missing_spo_result = $connection->query($missing_spo_query);

if ($missing_spo_result && $missing_spo_result->num_rows > 0) {
    echo "<p>Processing records " . ($offset + 1) . " to " . ($offset + $missing_spo_result->num_rows) . "</p>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Region</th><th>Status</th></tr>";
    
    $insert_count = 0;
    while ($spo = $missing_spo_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$spo['id_spo']}</td>";
        echo "<td>" . htmlspecialchars($spo['spo_name']) . "</td>";
        echo "<td>{$spo['id_region']}</td>";
        
        // Get proper area_id
        $area_id = getAreaIdForRegion($spo['id_region'], $spo['id_town'] ?? null, $connection, $region_area_map);
        
        if (!$area_id) {
            echo "<td style='color: red;'>❌ Could not find/create area for region {$spo['id_region']}</td>";
            continue;
        }
        
        // Prepare insert query
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
        
        // Map SPO fields
        $genitive_name = $spo['spo_name'];
        $full_name = $spo['spo_name'];
        $town_id = $spo['id_town'] ?: 0;
        $country_id = 1;
        $founded_year = isset($spo['founded_year']) ? $spo['founded_year'] : 0;
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
            $spo['tel'],
            $spo['email'],
            $spo['site'],
            $spo['director_name'],
            $spo['accreditation'],
            $spo['accreditation'],
            $founded_year,
            $spo['spo_url'],
            $view_count,
            $is_approved,
            $is_active
        );
        
        if ($stmt->execute()) {
            echo "<td style='color: green;'>✅ Migrated (area: $area_id)</td>";
            $insert_count++;
        } else {
            echo "<td style='color: red;'>❌ Error: " . $stmt->error . "</td>";
        }
        echo "</tr>";
        
        $stmt->close();
    }
    echo "</table>";
    echo "<p style='color: green;'>Successfully migrated $insert_count SPO records in this batch</p>";
    
    $new_offset = $offset + $batch_size;
    if ($new_offset < $total_missing_spo) {
        $remaining = $total_missing_spo - $new_offset;
        echo "<p style='color: orange;'>⚠️ There are $remaining more SPO records to migrate.</p>";
        echo "<p><a href='?offset=$new_offset' class='btn btn-primary' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Continue Migration (Next $batch_size Records)</a></p>";
    } else {
        echo "<p style='color: green;'>✅ All SPO records have been processed!</p>";
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
$vpo_status = ($vpo_count == $uni_count) ? "<span style='color: green;'>✅ Synced</span>" : "<span style='color: orange;'>⚠️ Not synced (diff: " . ($vpo_count - $uni_count) . ")</span>";
echo "<td>$vpo_status</td>";
echo "</tr>";
echo "<tr>";
echo "<td>spo</td><td>$spo_count</td>";
echo "<td>colleges</td><td>$col_count</td>";
$spo_status = ($spo_count == $col_count) ? "<span style='color: green;'>✅ Synced</span>" : "<span style='color: orange;'>⚠️ Not synced (diff: " . ($spo_count - $col_count) . ")</span>";
echo "<td>$spo_status</td>";
echo "</tr>";
echo "</table>";

echo "<hr>";
echo "<p><a href='/check_current_database.php'>← Back to Database Analysis</a></p>";

$connection->close();
?>