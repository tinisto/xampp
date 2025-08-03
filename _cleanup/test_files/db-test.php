<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/common-components/check_under_construction.php';

echo "<h1>Database Connection Test</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; }
</style>";

// Test old database connection
echo "<h2>Old Database Test</h2>";
if (isset($connection)) {
    echo "<p class='success'>✅ Old database connection exists</p>";
    
    // Test query
    $test = $connection->query("SELECT COUNT(*) as count FROM regions");
    if ($test) {
        $count = $test->fetch_assoc()['count'];
        echo "<p class='success'>✅ Old database query works: $count regions</p>";
    } else {
        echo "<p class='error'>❌ Old database query failed: " . $connection->error . "</p>";
    }
} else {
    echo "<p class='error'>❌ No old database connection</p>";
}

// Test new database connection
echo "<h2>New Database Test</h2>";
$new_db_name = '11klassniki_new';
$new_db_user = 'admin_claude';
$new_db_pass = 'Secure9#Klass';

try {
    $new_db = new mysqli(DB_HOST, $new_db_user, $new_db_pass, $new_db_name);
    
    if ($new_db->connect_error) {
        echo "<p class='error'>❌ New database connection failed: " . $new_db->connect_error . "</p>";
    } else {
        echo "<p class='success'>✅ New database connection successful</p>";
        
        // Test query
        $test = $new_db->query("SHOW TABLES");
        if ($test) {
            echo "<p class='success'>✅ New database has " . $test->num_rows . " tables</p>";
            
            // List tables
            echo "<p class='info'>Tables in new database:</p><ul>";
            while ($row = $test->fetch_array()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>❌ New database query failed: " . $new_db->error . "</p>";
        }
        
        $new_db->close();
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<h2>Server Info</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>MySQL Client:</strong> " . mysqli_get_client_info() . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

if (defined('DB_HOST')) {
    echo "<p><strong>DB_HOST:</strong> " . DB_HOST . "</p>";
}
if (defined('DB_NAME')) {
    echo "<p><strong>DB_NAME:</strong> " . DB_NAME . "</p>";
}
if (defined('DB_USER')) {
    echo "<p><strong>DB_USER:</strong> " . DB_USER . "</p>";
}
?>