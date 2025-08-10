<?php
/**
 * Test database connection and show data overview
 */

// Include database configuration
if (file_exists(__DIR__ . '/config/database.local.php')) {
    require_once __DIR__ . '/config/database.local.php';
} else {
    die("Database configuration not found. Please run import-database.php first.");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: -apple-system, Arial, sans-serif; 
            max-width: 1200px; 
            margin: 20px auto; 
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 { color: #333; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Database Connection Test</h1>
    
    <?php
    // Test connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($mysqli->connect_error) {
        echo "<p class='error'>❌ Connection failed: " . $mysqli->connect_error . "</p>";
        echo "</div></body></html>";
        exit;
    }
    
    echo "<p class='success'>✅ Successfully connected to database: " . DB_NAME . "</p>";
    
    // Set charset
    $mysqli->set_charset(DB_CHARSET);
    
    // Get all tables
    $tables = [];
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "<h2>Database Overview</h2>";
    echo "<p class='info'>Total tables: " . count($tables) . "</p>";
    
    // Get statistics for main tables
    $mainTables = ['users', 'posts', 'schools', 'vpo', 'spo', 'comments', 'news', 'categories', 'regions', 'towns'];
    
    echo "<div class='stats-grid'>";
    foreach ($mainTables as $table) {
        if (in_array($table, $tables)) {
            $result = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<div class='stat-box'>";
                echo "<div class='stat-number'>" . number_format($row['count']) . "</div>";
                echo "<div>" . ucfirst($table) . "</div>";
                echo "</div>";
            }
        }
    }
    echo "</div>";
    
    // Show sample data from posts
    echo "<h2>Recent Posts</h2>";
    // First check what columns exist
    $columns = $mysqli->query("SHOW COLUMNS FROM posts");
    $columnNames = [];
    while ($col = $columns->fetch_assoc()) {
        $columnNames[] = $col['Field'];
    }
    
    // Try to find title and date columns
    $titleCol = in_array('post_title', $columnNames) ? 'post_title' : 
                (in_array('title', $columnNames) ? 'title' : 
                (in_array('post_name', $columnNames) ? 'post_name' : null));
    $dateCol = in_array('date_created', $columnNames) ? 'date_created' : 
               (in_array('created_at', $columnNames) ? 'created_at' : 
               (in_array('date', $columnNames) ? 'date' : null));
    
    if ($titleCol && $dateCol) {
        $result = $mysqli->query("SELECT id, $titleCol, $dateCol FROM posts ORDER BY $dateCol DESC LIMIT 5");
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Title</th><th>Created</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row[$titleCol]) . "</td>";
                echo "<td>" . $row[$dateCol] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p>Posts table has different column names. <a href='/check-table-structure.php'>Check structure</a></p>";
    }
    
    // Show sample users
    echo "<h2>Recent Users</h2>";
    $result = $mysqli->query("SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Joined</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No users found</p>";
    }
    
    $mysqli->close();
    ?>
    
    <div style="margin-top: 30px;">
        <a href="/" style="display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;">Go to Homepage</a>
    </div>
</div>
</body>
</html>