<?php
// Fix missing records in universities and colleges tables
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

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
$missing_vpo_count = $missing_vpo_result->num_rows;
echo "<p>Found $missing_vpo_count missing VPO records</p>";

if ($missing_vpo_count > 0) {
    echo "<h3>Missing VPO Records:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Action</th></tr>";
    
    $insert_count = 0;
    while ($vpo = $missing_vpo_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$vpo['id_vpo']}</td>";
        echo "<td>" . htmlspecialchars($vpo['vpo_name']) . "</td>";
        
        // Prepare insert query
        $insert_query = "INSERT INTO universities (
            id, name, url, region_id, city, address, 
            phone, website, email, description, 
            rector, accreditation, founded_year,
            students_count, logo_url, type, 
            specialties, rating, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $connection->prepare($insert_query);
        
        // Map VPO fields to Universities fields
        $address = trim($vpo['zip_code'] . ' ' . $vpo['city'] . ' ' . $vpo['street']);
        $type = 'Государственный';
        $specialties = '';
        $rating = 0;
        
        $stmt->bind_param(
            "ississsssssssissis",
            $vpo['id_vpo'],
            $vpo['vpo_name'],
            $vpo['vpo_url'],
            $vpo['id_region'],
            $vpo['city'],
            $address,
            $vpo['tel'],
            $vpo['site'],
            $vpo['email'],
            $vpo['description'],
            $vpo['director_name'],
            $vpo['accreditation'],
            $vpo['founded_year'],
            $vpo['students_count'],
            $vpo['logo'],
            $type,
            $specialties,
            $rating
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

// 2. Find missing SPO records
echo "<h2>2. Finding Missing SPO Records</h2>";
$missing_spo_query = "
    SELECT s.* 
    FROM spo s
    LEFT JOIN colleges c ON s.id_spo = c.id
    WHERE c.id IS NULL
    LIMIT 50
"; // Limit to 50 for testing

$missing_spo_result = $connection->query($missing_spo_query);
$missing_spo_count = $missing_spo_result->num_rows;

// Get total count
$total_missing_spo = $connection->query("
    SELECT COUNT(*) as count 
    FROM spo s
    LEFT JOIN colleges c ON s.id_spo = c.id
    WHERE c.id IS NULL
")->fetch_assoc()['count'];

echo "<p>Found $total_missing_spo total missing SPO records (showing first 50)</p>";

if ($missing_spo_count > 0) {
    echo "<h3>Missing SPO Records (First 50):</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Action</th></tr>";
    
    $insert_count = 0;
    while ($spo = $missing_spo_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$spo['id_spo']}</td>";
        echo "<td>" . htmlspecialchars($spo['spo_name']) . "</td>";
        
        // Prepare insert query
        $insert_query = "INSERT INTO colleges (
            id, name, url, region_id, city, address, 
            phone, website, email, description, 
            director, accreditation, founded_year,
            students_count, logo_url, type, 
            specialties, rating, created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $connection->prepare($insert_query);
        
        // Map SPO fields to Colleges fields
        $address = trim($spo['zip_code'] . ' ' . $spo['city'] . ' ' . $spo['street']);
        $type = 'Государственный';
        $specialties = '';
        $rating = 0;
        $founded_year = $spo['founded_year'] ?: 0;
        $students_count = $spo['students_count'] ?: 0;
        
        $stmt->bind_param(
            "ississsssssssissis",
            $spo['id_spo'],
            $spo['spo_name'],
            $spo['spo_url'],
            $spo['id_region'],
            $spo['city'],
            $address,
            $spo['tel'],
            $spo['site'],
            $spo['email'],
            $spo['description'],
            $spo['director_name'],
            $spo['accreditation'],
            $founded_year,
            $students_count,
            $spo['logo'],
            $type,
            $specialties,
            $rating
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
    
    if ($total_missing_spo > 50) {
        echo "<p style='color: orange;'>⚠️ There are " . ($total_missing_spo - 50) . " more SPO records to migrate. Run this script again to continue.</p>";
    }
}

// 3. Final verification
echo "<h2>3. Final Verification</h2>";
$vpo_count = $connection->query("SELECT COUNT(*) as count FROM vpo")->fetch_assoc()['count'];
$uni_count = $connection->query("SELECT COUNT(*) as count FROM universities")->fetch_assoc()['count'];
$spo_count = $connection->query("SELECT COUNT(*) as count FROM spo")->fetch_assoc()['count'];
$col_count = $connection->query("SELECT COUNT(*) as count FROM colleges")->fetch_assoc()['count'];

echo "<table border='1'>";
echo "<tr><th>Old Table</th><th>Count</th><th>New Table</th><th>Count</th><th>Status</th></tr>";
echo "<tr>";
echo "<td>vpo</td><td>$vpo_count</td>";
echo "<td>universities</td><td>$uni_count</td>";
echo "<td>" . ($vpo_count == $uni_count ? "✅ Synced" : "⚠️ Not synced") . "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>spo</td><td>$spo_count</td>";
echo "<td>colleges</td><td>$col_count</td>";
echo "<td>" . ($spo_count == $col_count ? "✅ Synced" : "⚠️ Not synced") . "</td>";
echo "</tr>";
echo "</table>";

echo "<hr>";
echo "<p><a href='/check_current_database.php'>← Back to Database Analysis</a></p>";

$connection->close();
?>