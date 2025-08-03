<?php
// Force correct database connection by updating db_connections.php

echo "<h1>Forcing Correct Database Connection</h1>";

// Current situation
echo "<h2>Current Situation:</h2>";
echo "<ul>";
echo "<li>.env file: ✅ Has DB_NAME=11klassniki_claude</li>";
echo "<li>PHP constant: ❌ Still shows DB_NAME=11klassniki_ru (cached)</li>";
echo "<li>Actual connection: ❌ Using 11klassniki_ru</li>";
echo "</ul>";

echo "<h2>Solution: Update db_connections.php to force correct database</h2>";

$db_connections_path = $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

if (isset($_GET['apply']) && $_GET['apply'] == 'yes') {
    // Create new db_connections.php that forces the correct database
    $new_content = '<?php
require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/config/loadEnv.php\';

// TEMPORARY FIX: Force new database connection while PHP cache is clearing
// This can be removed once PHP properly reads the new .env values
$force_new_db = true; // Set to false to revert to normal behavior

if ($force_new_db) {
    // Force connection to new database
    $connection = new mysqli(
        \'11klassnikiru67871.ipagemysql.com\',
        \'admin_claude\',
        \'W4eZ!#9uwLmrMay\',
        \'11klassniki_claude\'
    );
} else {
    // Check if the constants are defined
    if (!defined(\'DB_HOST\') || !defined(\'DB_USER\') || !defined(\'DB_PASS\') || !defined(\'DB_NAME\')) {
        header("Location: /error");
        exit();
    }
    
    // Establish the database connection using .env values
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}

// Check the connection
if ($connection->connect_error) {
    header("Location: /error");
    exit();
}

// Set the character set to UTF-8
$connection->set_charset("utf8mb4");
?>';

    if (file_put_contents($db_connections_path, $new_content)) {
        echo "<p style='color: green;'>✅ Updated db_connections.php to force correct database!</p>";
        echo "<p>The force flag has been set to TRUE to use 11klassniki_claude database.</p>";
        
        // Test the connection
        require_once $db_connections_path;
        $current_db = $connection->query("SELECT DATABASE() as db")->fetch_assoc()['db'];
        echo "<p>Now connected to: <strong style='color: " . ($current_db == '11klassniki_claude' ? 'green' : 'red') . ";'>$current_db</strong></p>";
        
        if ($current_db == '11klassniki_claude') {
            echo "<h2 style='color: green;'>✅ SUCCESS! Database connection fixed!</h2>";
            echo "<p>The site is now using the correct database.</p>";
            echo "<p><a href='/site_review.php'>Go back to Site Review</a></p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Could not update db_connections.php</p>";
    }
} else {
    echo "<p>This will temporarily enable the force flag in db_connections.php to use the correct database.</p>";
    echo "<p><a href='?apply=yes' style='background: green; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Apply Fix Now</a></p>";
}

echo "<hr>";
echo "<h3>Note:</h3>";
echo "<p>This is a temporary fix. Once PHP cache clears and properly reads the .env file, you can disable the force flag again.</p>";
?>