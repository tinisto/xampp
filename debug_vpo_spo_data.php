<?php
/**
 * Debug VPO/SPO data issues
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🔍 Debug VPO/SPO Data Issues</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

// Check database tables exist
echo "<h2>1️⃣ Database Tables Check</h2>";
$tables_to_check = ['universities', 'colleges', 'regions'];

echo "<table>";
echo "<tr><th>Table</th><th>Exists</th><th>Record Count</th><th>Sample Data</th></tr>";

foreach ($tables_to_check as $table) {
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
        if ($table === 'universities') {
            $sample = $connection->query("SELECT university_name, region_id FROM universities LIMIT 3");
        } elseif ($table === 'colleges') {
            $sample = $connection->query("SELECT college_name, region_id FROM colleges LIMIT 3");
        } else {
            $sample = $connection->query("SELECT * FROM `$table` LIMIT 3");
        }
        
        echo "<td>";
        if ($sample && $sample->num_rows > 0) {
            while ($row = $sample->fetch_assoc()) {
                if ($table === 'universities') {
                    echo "• " . htmlspecialchars($row['university_name']) . " (region: {$row['region_id']})<br>";
                } elseif ($table === 'colleges') {
                    echo "• " . htmlspecialchars($row['college_name']) . " (region: {$row['region_id']})<br>";
                } else {
                    echo "• " . implode(', ', array_slice($row, 0, 2)) . "<br>";
                }
            }
        }
        echo "</td>";
    } else {
        echo "<td class='error'>❌ No</td>";
        echo "<td colspan='2'>Table not found</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Check regional distribution
echo "<h2>2️⃣ Regional Distribution</h2>";
echo "<table>";
echo "<tr><th>Table</th><th>Regions with Data</th><th>Total Institutions</th><th>Sample Regions</th></tr>";

// Universities
$uni_regions = $connection->query("
    SELECT r.region_name, COUNT(u.id) as count 
    FROM regions r 
    LEFT JOIN universities u ON r.id_region = u.region_id 
    WHERE r.id_country = 1 
    GROUP BY r.id_region 
    HAVING count > 0 
    ORDER BY count DESC 
    LIMIT 5
");

echo "<tr>";
echo "<td><strong>Universities</strong></td>";

$total_uni_regions = $connection->query("SELECT COUNT(DISTINCT region_id) as count FROM universities");
$uni_region_count = $total_uni_regions ? $total_uni_regions->fetch_assoc()['count'] : 0;
echo "<td>$uni_region_count</td>";

$total_universities = $connection->query("SELECT COUNT(*) as count FROM universities");
$total_uni_count = $total_universities ? $total_universities->fetch_assoc()['count'] : 0;
echo "<td>$total_uni_count</td>";

echo "<td>";
if ($uni_regions && $uni_regions->num_rows > 0) {
    while ($row = $uni_regions->fetch_assoc()) {
        echo "• " . htmlspecialchars($row['region_name']) . " ({$row['count']})<br>";
    }
} else {
    echo "No data found";
}
echo "</td>";
echo "</tr>";

// Colleges  
$college_regions = $connection->query("
    SELECT r.region_name, COUNT(c.id) as count 
    FROM regions r 
    LEFT JOIN colleges c ON r.id_region = c.region_id 
    WHERE r.id_country = 1 
    GROUP BY r.id_region 
    HAVING count > 0 
    ORDER BY count DESC 
    LIMIT 5
");

echo "<tr>";
echo "<td><strong>Colleges</strong></td>";

$total_college_regions = $connection->query("SELECT COUNT(DISTINCT region_id) as count FROM colleges");
$college_region_count = $total_college_regions ? $total_college_regions->fetch_assoc()['count'] : 0;
echo "<td>$college_region_count</td>";

$total_colleges = $connection->query("SELECT COUNT(*) as count FROM colleges");
$total_college_count = $total_colleges ? $total_colleges->fetch_assoc()['count'] : 0;
echo "<td>$total_college_count</td>";

echo "<td>";
if ($college_regions && $college_regions->num_rows > 0) {
    while ($row = $college_regions->fetch_assoc()) {
        echo "• " . htmlspecialchars($row['region_name']) . " ({$row['count']})<br>";
    }
} else {
    echo "No data found";
}
echo "</td>";
echo "</tr>";

echo "</table>";

// Test the exact query used by the educational institutions page
echo "<h2>3️⃣ Test Page Queries</h2>";

// Test VPO query
echo "<h3>VPO (Universities) Query Test:</h3>";
$vpo_test_sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
$vpo_regions = $connection->query($vpo_test_sql);

if ($vpo_regions && $vpo_regions->num_rows > 0) {
    echo "<p>Found {$vpo_regions->num_rows} regions</p>";
    echo "<table>";
    echo "<tr><th>Region</th><th>Universities Count</th><th>Query Used</th></tr>";
    
    $displayed_count = 0;
    while ($row = $vpo_regions->fetch_assoc()) {
        $count_sql = "SELECT COUNT(*) AS count FROM universities WHERE region_id = {$row['id_region']}";
        $count_result = $connection->query($count_sql);
        
        if ($count_result) {
            $count_row = $count_result->fetch_assoc();
            $institution_count = $count_row['count'];
            
            if ($institution_count > 0) {
                $displayed_count++;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
                echo "<td class='success'>$institution_count</td>";
                echo "<td><code>$count_sql</code></td>";
                echo "</tr>";
                
                if ($displayed_count >= 5) break; // Show only first 5 for brevity
            }
        }
    }
    echo "</table>";
    
    if ($displayed_count == 0) {
        echo "<p class='error'>❌ No regions with universities found!</p>";
    } else {
        echo "<p class='success'>✅ Found $displayed_count regions with universities</p>";
    }
} else {
    echo "<p class='error'>❌ No regions found</p>";
}

// Test SPO query
echo "<h3>SPO (Colleges) Query Test:</h3>";
$spo_test_sql = "SELECT id_region, region_name, region_name_en FROM regions WHERE id_country = 1 ORDER BY region_name ASC";
$spo_regions = $connection->query($spo_test_sql);

if ($spo_regions && $spo_regions->num_rows > 0) {
    echo "<p>Found {$spo_regions->num_rows} regions</p>";
    echo "<table>";
    echo "<tr><th>Region</th><th>Colleges Count</th><th>Query Used</th></tr>";
    
    $displayed_count = 0;
    while ($row = $spo_regions->fetch_assoc()) {
        $count_sql = "SELECT COUNT(*) AS count FROM colleges WHERE region_id = {$row['id_region']}";
        $count_result = $connection->query($count_sql);
        
        if ($count_result) {
            $count_row = $count_result->fetch_assoc();
            $institution_count = $count_row['count'];
            
            if ($institution_count > 0) {
                $displayed_count++;
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
                echo "<td class='success'>$institution_count</td>";
                echo "<td><code>$count_sql</code></td>";
                echo "</tr>";
                
                if ($displayed_count >= 5) break; // Show only first 5 for brevity
            }
        }
    }
    echo "</table>";
    
    if ($displayed_count == 0) {
        echo "<p class='error'>❌ No regions with colleges found!</p>";
    } else {
        echo "<p class='success'>✅ Found $displayed_count regions with colleges</p>";
    }
} else {
    echo "<p class='error'>❌ No regions found</p>";
}

// Check specific posts issue
echo "<h2>4️⃣ Missing Post Investigation</h2>";

// Search for posts with similar titles
$post_search = $connection->query("
    SELECT id_post, title_post, url_post 
    FROM posts 
    WHERE title_post LIKE '%побла%' OR title_post LIKE '%благо%' OR url_post LIKE '%hochu%'
    ORDER BY created_at DESC
");

if ($post_search && $post_search->num_rows > 0) {
    echo "<p>Found similar posts:</p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Title</th><th>URL Slug</th></tr>";
    while ($row = $post_search->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id_post']}</td>";
        echo "<td>" . htmlspecialchars($row['title_post']) . "</td>";
        echo "<td>" . htmlspecialchars($row['url_post']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='warning'>⚠️ No posts found with similar titles</p>";
}

echo "<h2>📋 Summary</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>Key Findings:</strong></p>";
echo "<ul>";
echo "<li>Universities in database: $total_uni_count</li>";
echo "<li>Colleges in database: $total_college_count</li>";
echo "<li>Regions with universities: $uni_region_count</li>";
echo "<li>Regions with colleges: $college_region_count</li>";
echo "</ul>";

if ($total_uni_count > 0 && $total_college_count > 0) {
    echo "<p class='success'>✅ Data exists - the issue is likely in the page logic</p>";
} else {
    echo "<p class='error'>❌ Missing data in universities/colleges tables</p>";
}
echo "</div>";

echo "<p><a href='/'>← Back to Home</a></p>";

$connection->close();
?>