<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate admin session for testing
$_SESSION['user_id'] = 78;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_email'] = 'admin@11klassniki.ru';

echo "<h1>Admin Dashboard Testing</h1>";
echo "<p>Testing as admin user ID: " . $_SESSION['user_id'] . "</p>";

$dashboards = [
    ['url' => '/dashboard-overview.php', 'name' => 'Overview Dashboard'],
    ['url' => '/dashboard-posts-new.php', 'name' => 'Posts Management'],
    ['url' => '/dashboard-news-new.php', 'name' => 'News Management'],
    ['url' => '/dashboard-users-new.php', 'name' => 'Users Management'],
    ['url' => '/dashboard-moderation.php', 'name' => 'Comment Moderation'],
    ['url' => '/dashboard-analytics.php', 'name' => 'Analytics Dashboard']
];

echo "<h2>Quick Links to Dashboards:</h2>";
echo "<ul>";
foreach ($dashboards as $dashboard) {
    echo "<li><a href='" . $dashboard['url'] . "' target='_blank'>" . $dashboard['name'] . "</a></li>";
}
echo "</ul>";

echo "<h2>Dashboard File Status:</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Dashboard</th><th>File Exists</th><th>Syntax Check</th></tr>";

foreach ($dashboards as $dashboard) {
    $file = $_SERVER['DOCUMENT_ROOT'] . $dashboard['url'];
    echo "<tr>";
    echo "<td>" . $dashboard['name'] . "</td>";
    
    if (file_exists($file)) {
        echo "<td style='color: green;'>✓ Exists</td>";
        
        // Check syntax
        $output = shell_exec("php -l $file 2>&1");
        if (strpos($output, 'No syntax errors detected') !== false) {
            echo "<td style='color: green;'>✓ No syntax errors</td>";
        } else {
            echo "<td style='color: red;'>✗ Syntax error</td>";
        }
    } else {
        echo "<td style='color: red;'>✗ Missing</td>";
        echo "<td>-</td>";
    }
    echo "</tr>";
}
echo "</table>";

// Check database connection
echo "<h2>Database Connection Test:</h2>";
try {
    require_once 'database/db_modern.php';
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test some queries
    echo "<h3>Quick Database Stats:</h3>";
    echo "<ul>";
    
    // Users count
    $result = mysqli_query($db->getConnection(), "SELECT COUNT(*) as count FROM users");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['count'];
        echo "<li>Total Users: " . $count . "</li>";
    }
    
    // Posts count
    $result = mysqli_query($db->getConnection(), "SELECT COUNT(*) as count FROM posts");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['count'];
        echo "<li>Total Posts: " . $count . "</li>";
    }
    
    // News count
    $result = mysqli_query($db->getConnection(), "SELECT COUNT(*) as count FROM news");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['count'];
        echo "<li>Total News: " . $count . "</li>";
    }
    
    // Comments count
    $result = mysqli_query($db->getConnection(), "SELECT COUNT(*) as count FROM comments");
    if ($result) {
        $count = mysqli_fetch_assoc($result)['count'];
        echo "<li>Total Comments: " . $count . "</li>";
    }
    
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> Click on the dashboard links above to test each one individually.</p>";
?>