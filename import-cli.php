<?php
/**
 * Command-line import script with progress indication
 */

set_time_limit(0);
ini_set('memory_limit', '1G');

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', '11klassniki_claude');

$sqlFile = '/Users/anatolys/Downloads/custsql-ipg117_eigbox_net.sql';

echo "Database Import Script\n";
echo "======================\n\n";

// Check file
if (!file_exists($sqlFile)) {
    die("Error: SQL file not found!\n");
}

$fileSize = filesize($sqlFile);
$fileSizeMB = round($fileSize / 1024 / 1024, 2);
echo "SQL file: " . basename($sqlFile) . " ($fileSizeMB MB)\n\n";

// Connect to MySQL
echo "Connecting to MySQL...\n";
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}

echo "✓ Connected to MySQL\n\n";

// Drop and create database
echo "Creating database...\n";
$mysqli->query("DROP DATABASE IF EXISTS `" . DB_NAME . "`");
$mysqli->query("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$mysqli->select_db(DB_NAME);
$mysqli->set_charset('utf8mb4');
echo "✓ Database created: " . DB_NAME . "\n\n";

// Import SQL file
echo "Starting import...\n";
echo "This may take a few minutes for large files.\n\n";

// Use mysql command line for faster import
$command = sprintf(
    '/Applications/XAMPP/xamppfiles/bin/mysql -h %s -u %s -p%s %s < %s 2>&1',
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    escapeshellarg($sqlFile)
);

echo "Executing import command...\n";
$output = [];
$return_var = 0;
exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "\n✓ Import completed successfully!\n\n";
    
    // Show statistics
    $mysqli->select_db(DB_NAME);
    
    // Count tables
    $result = $mysqli->query("SHOW TABLES");
    $tableCount = $result->num_rows;
    echo "Tables created: $tableCount\n\n";
    
    // Show record counts
    echo "Record counts:\n";
    $tables = ['users', 'posts', 'schools', 'vpo', 'spo', 'comments', 'news'];
    foreach ($tables as $table) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "  - $table: " . number_format($row['count']) . "\n";
        }
    }
    
    echo "\n✓ Database import complete!\n";
    echo "\nYou can now:\n";
    echo "1. Test connection: http://localhost:8000/test-db-connection-new.php\n";
    echo "2. Visit homepage: http://localhost:8000/\n";
    
} else {
    echo "\n✗ Import failed!\n";
    echo "Error output:\n";
    echo implode("\n", $output) . "\n";
}

$mysqli->close();
?>