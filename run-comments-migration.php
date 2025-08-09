<?php
/**
 * Run Comments System Database Migration
 * 
 * This script executes the database updates needed for the threaded comments system
 * Run this from command line: php run-comments-migration.php
 * Or access via browser (admin only)
 */

// Check if running from CLI or web
$is_cli = php_sapi_name() === 'cli';

if (!$is_cli) {
    session_start();
    // Web access - require admin
    if ((!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') && 
        (!isset($_SESSION['occupation']) || $_SESSION['occupation'] !== 'admin')) {
        die('Access denied. Admin privileges required.');
    }
}

// Database connection
require_once __DIR__ . '/database/db_connections.php';

echo $is_cli ? "\n" : "<pre>";
echo "=== Comments System Database Migration ===\n";
echo "Starting at: " . date('Y-m-d H:i:s') . "\n\n";

// Read the SQL file
$sql_file = __DIR__ . '/database/migrations/update_comments_threaded_system.sql';
if (!file_exists($sql_file)) {
    die("Error: Migration file not found at: $sql_file\n");
}

$sql_content = file_get_contents($sql_file);

// Split by delimiter to handle multiple statements
$queries = array_filter(array_map('trim', explode(';', $sql_content)));

$success_count = 0;
$error_count = 0;
$messages = [];

foreach ($queries as $query) {
    if (empty($query)) continue;
    
    // Skip comments and empty lines
    if (strpos($query, '--') === 0 || strpos($query, '/*') === 0) continue;
    
    try {
        // Execute the query
        if ($connection->query($query . ';')) {
            $success_count++;
            
            // Check if it was a SELECT statement (info message)
            if (stripos($query, 'SELECT') === 0) {
                if ($result = $connection->query($query . ';')) {
                    while ($row = $result->fetch_assoc()) {
                        $messages[] = implode(' ', $row);
                    }
                }
            }
        } else {
            throw new Exception($connection->error);
        }
    } catch (Exception $e) {
        $error_count++;
        echo "Error executing query: " . $e->getMessage() . "\n";
        echo "Query: " . substr($query, 0, 100) . "...\n\n";
    }
}

echo "\n=== Migration Summary ===\n";
echo "Successful operations: $success_count\n";
echo "Errors: $error_count\n";

if (!empty($messages)) {
    echo "\nMessages:\n";
    foreach ($messages as $msg) {
        echo "- $msg\n";
    }
}

// Verify the changes
echo "\n=== Verifying Changes ===\n";

// Check comments table structure
$result = $connection->query("DESCRIBE comments");
echo "\nComments table structure:\n";
$new_columns = ['parent_id', 'email', 'author_ip', 'likes', 'dislikes', 'is_approved', 'edited_at', 'edit_count'];
$found_columns = [];

while ($row = $result->fetch_assoc()) {
    if (in_array($row['Field'], $new_columns)) {
        $found_columns[] = $row['Field'];
        echo "✓ {$row['Field']} - {$row['Type']}\n";
    }
}

$missing = array_diff($new_columns, $found_columns);
if (!empty($missing)) {
    echo "\nMissing columns: " . implode(', ', $missing) . "\n";
}

// Check new tables
$new_tables = ['comment_likes', 'comment_edits', 'comment_reports', 'comment_notifications'];
echo "\n\nNew tables:\n";

foreach ($new_tables as $table) {
    $result = $connection->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "✓ $table created\n";
    } else {
        echo "✗ $table missing\n";
    }
}

// Check indexes
echo "\n\nIndexes on comments table:\n";
$result = $connection->query("SHOW INDEX FROM comments");
$indexes = [];
while ($row = $result->fetch_assoc()) {
    $indexes[$row['Key_name']] = $row['Column_name'];
}

$expected_indexes = ['idx_parent_id', 'idx_entity_type_id', 'idx_date', 'idx_approved'];
foreach ($expected_indexes as $idx) {
    if (isset($indexes[$idx])) {
        echo "✓ $idx on column: {$indexes[$idx]}\n";
    } else {
        echo "✗ $idx missing\n";
    }
}

echo "\n=== Migration completed at: " . date('Y-m-d H:i:s') . " ===\n";
echo $is_cli ? "\n" : "</pre>";

// If web access, provide a nice button to go back
if (!$is_cli) {
    echo '<br><a href="/dashboard" class="btn btn-primary">Back to Dashboard</a>';
}
?>