<?php
/**
 * Import 11klassniki_claude database from SQL dump
 * This script imports the complete database dump into local XAMPP
 */

set_time_limit(0); // No time limit for large import
ini_set('memory_limit', '512M'); // Increase memory for 84MB file

// Database configuration for local XAMPP
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP has no password
define('DB_NAME', '11klassniki_claude');
define('DB_CHARSET', 'utf8mb4');

// SQL file location
$sqlFile = '/Users/anatolys/Downloads/custsql-ipg117_eigbox_net.sql';

// Start import process
echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Import</title>
    <meta charset='UTF-8'>
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
        h1 { color: #333; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        .progress {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<div class='container'>
<h1>Database Import Progress</h1>";

// Check if file exists
if (!file_exists($sqlFile)) {
    die("<p class='error'>Error: SQL file not found at: $sqlFile</p></div></body></html>");
}

$fileSize = filesize($sqlFile);
$fileSizeMB = round($fileSize / 1024 / 1024, 2);
echo "<p class='info'>Importing SQL file: " . basename($sqlFile) . " ($fileSizeMB MB)</p>";

// Create connection to MySQL (without database selected)
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($mysqli->connect_error) {
    die("<p class='error'>Connection failed: " . $mysqli->connect_error . "</p></div></body></html>");
}

echo "<div class='progress'>";
echo "<p class='success'>✓ Connected to MySQL server</p>";

// Drop existing database if exists and create new one
echo "<p>Dropping existing database if exists...</p>";
$mysqli->query("DROP DATABASE IF EXISTS `" . DB_NAME . "`");

echo "<p>Creating fresh database...</p>";
if (!$mysqli->query("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    die("<p class='error'>Error creating database: " . $mysqli->error . "</p></div></body></html>");
}

echo "<p class='success'>✓ Database created: " . DB_NAME . "</p>";

// Select the database
$mysqli->select_db(DB_NAME);
$mysqli->set_charset('utf8mb4');

// Read and execute SQL file
echo "<p>Reading SQL file...</p>";
flush();

$handle = fopen($sqlFile, 'r');
if (!$handle) {
    die("<p class='error'>Error opening SQL file</p></div></body></html>");
}

$query = '';
$lineNum = 0;
$queryCount = 0;
$errorCount = 0;

echo "<p>Executing SQL statements...</p>";
flush();

while (!feof($handle)) {
    $line = fgets($handle);
    $lineNum++;
    
    // Skip comments and empty lines
    if (substr(trim($line), 0, 2) == '--' || trim($line) == '') {
        continue;
    }
    
    $query .= $line;
    
    // If query ends with semicolon, execute it
    if (substr(trim($line), -1, 1) == ';') {
        // Remove any CREATE DATABASE or USE statements as we handle that separately
        if (stripos($query, 'CREATE DATABASE') !== false || stripos($query, 'USE `') !== false) {
            $query = '';
            continue;
        }
        
        if ($mysqli->query($query)) {
            $queryCount++;
            if ($queryCount % 100 == 0) {
                echo "<p>Processed $queryCount queries...</p>";
                flush();
            }
        } else {
            $errorCount++;
            if ($errorCount <= 10) { // Show first 10 errors
                echo "<p class='error'>Error at line $lineNum: " . htmlspecialchars($mysqli->error) . "</p>";
            }
        }
        
        $query = '';
    }
}

fclose($handle);

echo "</div>";

// Get import statistics
$tables = [];
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

echo "<div class='progress'>";
echo "<h2>Import Complete!</h2>";
echo "<p class='success'>✓ Successfully executed $queryCount SQL queries</p>";
if ($errorCount > 0) {
    echo "<p class='error'>✗ Encountered $errorCount errors</p>";
}
echo "<p class='info'>Tables created: " . count($tables) . "</p>";
echo "<details><summary>Show all tables</summary><pre>" . implode("\n", $tables) . "</pre></details>";

// Show some statistics
$stats = [];
foreach (['users', 'posts', 'schools', 'vpo', 'spo', 'comments', 'news'] as $table) {
    if (in_array($table, $tables)) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats[$table] = $row['count'];
        }
    }
}

if (!empty($stats)) {
    echo "<h3>Data Statistics:</h3><ul>";
    foreach ($stats as $table => $count) {
        echo "<li>$table: " . number_format($count) . " records</li>";
    }
    echo "</ul>";
}

echo "</div>";

$mysqli->close();

// Update local database configuration
$configContent = '<?php
// Local database configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "11klassniki_claude");
define("DB_CHARSET", "utf8mb4");

// Set to local environment
$_SERVER["SERVER_NAME"] = "localhost";
define("SITE_URL", "http://localhost/");
define("IS_LOCAL", true);
?>';

file_put_contents(__DIR__ . '/config/database.local.php', $configContent);

echo "<p class='success'>✓ Created local database configuration</p>";
echo "<a href='/' class='btn'>Go to Homepage</a>";
echo "<a href='/test-db-connection.php' class='btn' style='margin-left: 10px;'>Test Database Connection</a>";

echo "</div></body></html>";
?>