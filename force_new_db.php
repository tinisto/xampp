<?php
/**
 * Force use of new database - temporary override
 */

// OVERRIDE the database connection before anything else loads
$GLOBALS['connection'] = new mysqli(
    '11klassnikiru67871.ipagemysql.com',
    'admin_claude',
    'W4eZ!#9uwLmrMay',
    '11klassniki_claude'
);

if ($GLOBALS['connection']->connect_error) {
    die("Connection failed: " . $GLOBALS['connection']->connect_error);
}

$GLOBALS['connection']->set_charset('utf8mb4');

// Override the connection variable
$connection = $GLOBALS['connection'];

// Now include the test page
?>
<!DOCTYPE html>
<html>
<head>
    <title>Force New Database Test</title>
    <style>
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>🔧 Forcing New Database Connection</h1>
    
    <?php
    // Verify we're using the right database
    $db_result = $connection->query("SELECT DATABASE() as db");
    $current_db = $db_result->fetch_assoc()['db'];
    
    echo "<p>Current database: <strong>$current_db</strong></p>";
    
    if ($current_db === '11klassniki_claude') {
        echo "<p class='success'>✅ Successfully forced connection to new database!</p>";
        
        // Test a university page
        $uni_result = $connection->query("SELECT id, university_name, url_slug FROM universities LIMIT 1");
        if ($uni_result && $uni = $uni_result->fetch_assoc()) {
            echo "<h2>Test Links (using forced connection):</h2>";
            echo "<ul>";
            echo "<li><a href='/force_vpo_test.php?id={$uni['id']}' target='_blank'>Test University: {$uni['university_name']}</a></li>";
            
            // Get a college too
            $col_result = $connection->query("SELECT id, college_name, url_slug FROM colleges LIMIT 1");
            if ($col_result && $col = $col_result->fetch_assoc()) {
                echo "<li><a href='/force_spo_test.php?id={$col['id']}' target='_blank'>Test College: {$col['college_name']}</a></li>";
            }
            
            echo "</ul>";
        }
    }
    ?>
    
    <h2>🚨 Important Note</h2>
    <div style="background: #fff3cd; padding: 20px; border: 1px solid #ffeaa7; border-radius: 5px;">
        <p><strong>This is a temporary override for testing only.</strong></p>
        <p>To make this permanent, you still need to:</p>
        <ol>
            <li>Contact iPage support to restart PHP/Apache</li>
            <li>Or wait for automatic PHP restart (usually within a few hours)</li>
        </ol>
    </div>
    
    <h2>📞 What to Tell iPage Support</h2>
    <div style="background: #f0f0f0; padding: 20px; border-radius: 5px;">
        <p>Call or chat with iPage support and say:</p>
        <p style="font-style: italic; padding: 10px; background: white; border-left: 4px solid #007bff;">
            "Hi, I've updated my .env file to point to a new database, but PHP seems to be caching the old configuration. 
            Could you please restart PHP-FPM or clear the PHP opcache for my hosting account? My domain is 11klassniki.ru"
        </p>
    </div>
    
    <p><a href="/">← Back to Home</a></p>
</body>
</html>