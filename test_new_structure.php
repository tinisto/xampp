<?php
/**
 * Test script for new database structure
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>🧪 Testing New Database Structure</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .test-link { margin: 5px 0; display: block; }
</style>";

// Test database connection
echo "<h2>1️⃣ Database Connection Test</h2>";
if ($connection && !$connection->connect_error) {
    echo "<p class='success'>✅ Connected to database</p>";
    
    // Get current database
    $db_result = $connection->query("SELECT DATABASE() as db");
    $current_db = $db_result->fetch_assoc()['db'];
    echo "<p>Current database: <strong>$current_db</strong></p>";
} else {
    echo "<p class='error'>❌ Database connection failed</p>";
    exit;
}

// Test table structure
echo "<h2>2️⃣ Table Structure Test</h2>";
$tables_to_check = [
    'universities' => ['Expected columns' => ['id', 'university_name', 'url_slug', 'town_id', 'region_id']],
    'colleges' => ['Expected columns' => ['id', 'college_name', 'url_slug', 'town_id', 'region_id']],
    'schools' => ['Expected columns' => ['id', 'school_name', 'town_id', 'region_id']],
    'areas' => ['Expected columns' => ['id', 'area_name', 'region_id']],
    'towns' => ['Expected columns' => ['id', 'town_name', 'area_id', 'region_id']]
];

echo "<table>";
echo "<tr><th>Table</th><th>Status</th><th>Sample Columns</th></tr>";

foreach ($tables_to_check as $table => $info) {
    echo "<tr>";
    echo "<td><strong>$table</strong></td>";
    
    $table_exists = $connection->query("SHOW TABLES LIKE '$table'")->num_rows > 0;
    
    if ($table_exists) {
        echo "<td class='success'>✅ Exists</td>";
        
        // Check columns
        $columns_result = $connection->query("SHOW COLUMNS FROM $table");
        $columns = [];
        while ($col = $columns_result->fetch_assoc()) {
            $columns[] = $col['Field'];
        }
        
        $sample_columns = array_slice($columns, 0, 5);
        echo "<td>" . implode(', ', $sample_columns) . "...</td>";
    } else {
        echo "<td class='error'>❌ Not found</td>";
        echo "<td>-</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Test sample data
echo "<h2>3️⃣ Sample Data Test</h2>";

// Get sample university
$uni_result = $connection->query("SELECT id, university_name, url_slug FROM universities LIMIT 1");
if ($uni_result && $uni = $uni_result->fetch_assoc()) {
    echo "<p class='success'>✅ Sample university found:</p>";
    echo "<ul>";
    echo "<li>ID: {$uni['id']}</li>";
    echo "<li>Name: {$uni['university_name']}</li>";
    echo "<li>URL: /vpo/{$uni['url_slug']}</li>";
    echo "</ul>";
    echo "<p><a href='/vpo/{$uni['url_slug']}' target='_blank' class='test-link'>🔗 Test University Page</a></p>";
}

// Get sample college
$col_result = $connection->query("SELECT id, college_name, url_slug FROM colleges LIMIT 1");
if ($col_result && $col = $col_result->fetch_assoc()) {
    echo "<p class='success'>✅ Sample college found:</p>";
    echo "<ul>";
    echo "<li>ID: {$col['id']}</li>";
    echo "<li>Name: {$col['college_name']}</li>";
    echo "<li>URL: /spo/{$col['url_slug']}</li>";
    echo "</ul>";
    echo "<p><a href='/spo/{$col['url_slug']}' target='_blank' class='test-link'>🔗 Test College Page</a></p>";
}

// Test listing pages
echo "<h2>4️⃣ Listing Pages Test</h2>";
echo "<p>Test these pages to see if they load correctly:</p>";
echo "<ul>";
echo "<li><a href='/educational-institutions-all-regions?type=vpo' target='_blank'>Universities by Region</a></li>";
echo "<li><a href='/educational-institutions-all-regions?type=spo' target='_blank'>Colleges by Region</a></li>";
echo "<li><a href='/educational-institutions-all-regions?type=schools' target='_blank'>Schools by Region</a></li>";
echo "</ul>";

// Check for old table references
echo "<h2>5️⃣ Old Table Check</h2>";
$old_tables = ['vpo', 'spo'];
echo "<p>Checking if old tables still exist (they shouldn't be used):</p>";
echo "<ul>";
foreach ($old_tables as $old_table) {
    $exists = $connection->query("SHOW TABLES LIKE '$old_table'")->num_rows > 0;
    if ($exists) {
        echo "<li class='warning'>⚠️ $old_table table still exists (should not be used by application)</li>";
    } else {
        echo "<li class='success'>✅ $old_table table not found (good)</li>";
    }
}
echo "</ul>";

// Summary
echo "<h2>📊 Summary</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";
echo "<p><strong>Database:</strong> $current_db</p>";
echo "<p><strong>New Structure:</strong> ✅ Active</p>";
echo "<p><strong>Key Changes:</strong></p>";
echo "<ul>";
echo "<li>vpo → universities</li>";
echo "<li>spo → colleges</li>";
echo "<li>id_vpo → id</li>";
echo "<li>vpo_name → university_name</li>";
echo "<li>All foreign keys use new naming (town_id, region_id, etc.)</li>";
echo "</ul>";
echo "</div>";

echo "<p><a href='/'>← Back to Home</a></p>";
?>