<?php
/**
 * Test connection to 11klassniki_claude database
 */

echo "<h1>🔌 Testing 11klassniki_claude Database Connection</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
</style>";

// Test connection
try {
    $test_db = new mysqli(
        '11klassnikiru67871.ipagemysql.com',
        'admin_claude',
        'Secure9#Klass',
        '11klassniki_claude'
    );
    
    if ($test_db->connect_error) {
        die("<p class='error'>❌ Connection failed: " . $test_db->connect_error . "</p>");
    }
    
    echo "<p class='success'>✅ Successfully connected to 11klassniki_claude!</p>";
    
    // Quick data check
    $tables = ['universities', 'colleges', 'schools', 'areas', 'towns', 'news', 'posts'];
    
    echo "<h2>📊 Quick Data Check:</h2>";
    echo "<ul>";
    foreach ($tables as $table) {
        $result = $test_db->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $count = $result->fetch_assoc()['count'];
            echo "<li><strong>$table:</strong> $count records</li>";
        } else {
            echo "<li class='error'>$table: Error or doesn't exist</li>";
        }
    }
    echo "</ul>";
    
    // Test a sample query
    echo "<h2>🔍 Sample University Query:</h2>";
    $sample = $test_db->query("SELECT id, university_name, town_id FROM universities LIMIT 1");
    if ($sample && $row = $sample->fetch_assoc()) {
        echo "<p>Sample university:</p>";
        echo "<ul>";
        echo "<li>ID: {$row['id']}</li>";
        echo "<li>Name: {$row['university_name']}</li>";
        echo "<li>Town ID: {$row['town_id']}</li>";
        echo "</ul>";
        echo "<p class='success'>✅ New table structure is working correctly!</p>";
    }
    
    $test_db->close();
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>📝 Next Steps:</h2>";
echo "<ol>";
echo "<li>Update your .env file to use DB_NAME=11klassniki_claude</li>";
echo "<li>Update your code to use new table/column names</li>";
echo "<li>Test thoroughly before switching production</li>";
echo "</ol>";
?>