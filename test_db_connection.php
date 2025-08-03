<?php
/**
 * Quick test of database connection and data
 */

echo "<h1>🔍 Database Connection Test</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>";

// Test database connection
try {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';
    
    if (isset($connection) && $connection->ping()) {
        echo "<p class='success'>✅ Database connection successful</p>";
        
        // Get database name
        $db_name_result = $connection->query("SELECT DATABASE() as db_name");
        if ($db_name_result) {
            $db_name = $db_name_result->fetch_assoc()['db_name'];
            echo "<p><strong>Connected to database:</strong> $db_name</p>";
        }
        
        // Test table counts
        $tables = ['universities', 'colleges', 'schools', 'regions', 'posts'];
        
        echo "<table>";
        echo "<tr><th>Table</th><th>Count</th><th>Status</th></tr>";
        
        foreach ($tables as $table) {
            echo "<tr>";
            echo "<td>$table</td>";
            
            $count_result = $connection->query("SELECT COUNT(*) as count FROM `$table`");
            if ($count_result) {
                $count = $count_result->fetch_assoc()['count'];
                echo "<td>$count</td>";
                
                if ($count > 0) {
                    echo "<td class='success'>✅ Has data</td>";
                } else {
                    echo "<td class='error'>❌ Empty</td>";
                }
            } else {
                echo "<td>Error</td>";
                echo "<td class='error'>❌ Query failed: " . $connection->error . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Test specific VPO/SPO queries
        echo "<h2>VPO/SPO Query Tests</h2>";
        
        // Test universities by region
        $uni_test = $connection->query("
            SELECT r.region_name, COUNT(u.id) as count 
            FROM regions r 
            LEFT JOIN universities u ON r.id_region = u.region_id 
            WHERE r.id_country = 1 
            GROUP BY r.id_region 
            HAVING count > 0 
            LIMIT 5
        ");
        
        echo "<h3>Universities by Region (Top 5):</h3>";
        if ($uni_test && $uni_test->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Region</th><th>Count</th></tr>";
            while ($row = $uni_test->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
                echo "<td>{$row['count']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ No universities found by region</p>";
        }
        
        // Test colleges by region  
        $college_test = $connection->query("
            SELECT r.region_name, COUNT(c.id) as count 
            FROM regions r 
            LEFT JOIN colleges c ON r.id_region = c.region_id 
            WHERE r.id_country = 1 
            GROUP BY r.id_region 
            HAVING count > 0 
            LIMIT 5
        ");
        
        echo "<h3>Colleges by Region (Top 5):</h3>";
        if ($college_test && $college_test->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Region</th><th>Count</th></tr>";
            while ($row = $college_test->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['region_name']) . "</td>";
                echo "<td>{$row['count']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>❌ No colleges found by region</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Connection error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='/'>← Back to Home</a></p>";
?>