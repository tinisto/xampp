<?php
/**
 * Debug routing and data issues
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>🐛 Debug Routing and Data Issues</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .code { background: #f5f5f5; padding: 10px; font-family: monospace; }
</style>";

// Check .htaccess rewrite rules
echo "<h2>1️⃣ Checking .htaccess Rules</h2>";
$htaccess_path = $_SERVER['DOCUMENT_ROOT'] . '/.htaccess';
if (file_exists($htaccess_path)) {
    $htaccess_content = file_get_contents($htaccess_path);
    
    // Check for educational-institutions-all-regions rule
    if (strpos($htaccess_content, 'educational-institutions-all-regions') !== false) {
        echo "<p class='success'>✅ Found educational-institutions-all-regions rule</p>";
    } else {
        echo "<p class='error'>❌ Missing educational-institutions-all-regions rule</p>";
    }
    
    // Check for schools-all-regions rule
    if (strpos($htaccess_content, 'schools-all-regions') !== false) {
        echo "<p class='success'>✅ Found schools-all-regions rule</p>";
    } else {
        echo "<p class='error'>❌ Missing schools-all-regions rule</p>";
    }
} else {
    echo "<p class='error'>❌ .htaccess file not found</p>";
}

// Check data counts in new database
echo "<h2>2️⃣ Data Counts in New Database</h2>";
echo "<table>";
echo "<tr><th>Table</th><th>Count</th><th>Sample Records</th></tr>";

$tables_to_check = ['universities', 'colleges', 'schools', 'posts', 'news'];

foreach ($tables_to_check as $table) {
    echo "<tr>";
    echo "<td><strong>$table</strong></td>";
    
    $count_result = $connection->query("SELECT COUNT(*) as count FROM `$table`");
    $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
    echo "<td>$count</td>";
    
    // Get sample records
    if ($count > 0) {
        if ($table === 'universities') {
            $sample = $connection->query("SELECT university_name, url_slug FROM universities LIMIT 3");
        } elseif ($table === 'colleges') {
            $sample = $connection->query("SELECT college_name, url_slug FROM colleges LIMIT 3");
        } elseif ($table === 'posts') {
            $sample = $connection->query("SELECT title_post, url_post FROM posts LIMIT 3");
        } else {
            $sample = $connection->query("SELECT * FROM `$table` LIMIT 3");
        }
        
        echo "<td>";
        if ($sample) {
            while ($row = $sample->fetch_assoc()) {
                if ($table === 'universities') {
                    echo "• " . htmlspecialchars($row['university_name']) . " (/{$row['url_slug']})<br>";
                } elseif ($table === 'colleges') {
                    echo "• " . htmlspecialchars($row['college_name']) . " (/{$row['url_slug']})<br>";
                } elseif ($table === 'posts') {
                    echo "• " . htmlspecialchars($row['title_post']) . " (/{$row['url_post']})<br>";
                } else {
                    echo "• " . implode(', ', array_slice($row, 0, 2)) . "<br>";
                }
            }
        }
        echo "</td>";
    } else {
        echo "<td class='error'>No data</td>";
    }
    
    echo "</tr>";
}
echo "</table>";

// Check posts with missing URL slugs
echo "<h2>3️⃣ Posts Issues</h2>";
$posts_without_url = $connection->query("SELECT COUNT(*) as count FROM posts WHERE url_post IS NULL OR url_post = ''");
$missing_urls = $posts_without_url ? $posts_without_url->fetch_assoc()['count'] : 0;

if ($missing_urls > 0) {
    echo "<p class='error'>❌ Found $missing_urls posts without url_post</p>";
    
    // Show sample
    $sample_missing = $connection->query("SELECT id_post, title_post FROM posts WHERE url_post IS NULL OR url_post = '' LIMIT 5");
    echo "<table>";
    echo "<tr><th>ID</th><th>Title</th></tr>";
    while ($row = $sample_missing->fetch_assoc()) {
        echo "<tr><td>{$row['id_post']}</td><td>" . htmlspecialchars($row['title_post']) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p class='success'>✅ All posts have url_post</p>";
}

// Check regions data for schools
echo "<h2>4️⃣ Regional Data Check</h2>";
$regions_with_universities = $connection->query("SELECT COUNT(DISTINCT region_id) as count FROM universities");
$regions_with_colleges = $connection->query("SELECT COUNT(DISTINCT region_id) as count FROM colleges");
$regions_with_schools = $connection->query("SELECT COUNT(DISTINCT region_id) as count FROM schools");

echo "<table>";
echo "<tr><th>Type</th><th>Regions with Data</th></tr>";
echo "<tr><td>Universities</td><td>" . ($regions_with_universities ? $regions_with_universities->fetch_assoc()['count'] : 0) . "</td></tr>";
echo "<tr><td>Colleges</td><td>" . ($regions_with_colleges ? $regions_with_colleges->fetch_assoc()['count'] : 0) . "</td></tr>";
echo "<tr><td>Schools</td><td>" . ($regions_with_schools ? $regions_with_schools->fetch_assoc()['count'] : 0) . "</td></tr>";
echo "</table>";

// Check specific URL that's 404ing
echo "<h2>5️⃣ Specific URL Tests</h2>";
$test_urls = [
    '/educational-institutions-all-regions?type=vpo',
    '/educational-institutions-all-regions?type=spo', 
    '/schools-all-regions'
];

echo "<table>";
echo "<tr><th>URL</th><th>File Exists</th><th>Rewrite Rule</th></tr>";

foreach ($test_urls as $url) {
    echo "<tr>";
    echo "<td>$url</td>";
    
    // Check if corresponding file exists
    if (strpos($url, 'educational-institutions-all-regions') !== false) {
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
    } elseif (strpos($url, 'schools-all-regions') !== false) {
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/pages/common/educational-institutions-all-regions/educational-institutions-all-regions.php';
    }
    
    if (isset($file_path)) {
        echo "<td>" . (file_exists($file_path) ? "✅ Yes" : "❌ No") . "</td>";
    } else {
        echo "<td>Unknown</td>";
    }
    
    echo "<td>Need to check .htaccess</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>📋 Summary</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>Issues found:</strong></p>";
echo "<ul>";
echo "<li>Educational institutions pages returning 404 - likely missing .htaccess rules</li>";
echo "<li>Schools showing no data - need to check region queries</li>";
echo "<li>Some posts returning 404 - need to check URL routing</li>";
echo "</ul>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Fix .htaccess rewrite rules</li>";
echo "<li>Update regional queries to use new column names</li>";
echo "<li>Fix any missing post URL slugs</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='/'>← Back to Home</a></p>";
?>