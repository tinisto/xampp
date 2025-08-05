<?php
/**
 * Database Field Standardization Migration Runner
 * This script performs the complete migration process
 */

set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🚀 Database Field Standardization Migration</h1>";
echo "<p><strong>Starting migration at:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Step 1: Create backup information
echo "<h2>📋 Step 1: Database Backup Information</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>⚠️ IMPORTANT:</strong> Please ensure you have a database backup before proceeding!<br>";
echo "You can create a backup using:<br>";
echo "<code>mysqldump -u username -p database_name > backup_" . date('Y-m-d_H-i-s') . ".sql</code><br>";
echo "Or use your hosting provider's backup tools.";
echo "</div>";

// Step 2: Pre-migration checks
echo "<h2>🔍 Step 2: Pre-Migration Checks</h2>";

$checks = [
    'Database connection' => false,
    'Required tables exist' => false,
    'No active transactions' => false,
];

// Check database connection
if ($connection && !$connection->connect_error) {
    $checks['Database connection'] = true;
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Check required tables
$required_tables = ['posts', 'news', 'vpo', 'spo', 'schools', 'comments'];
$existing_tables = [];
$result = $connection->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $existing_tables[] = $row[0];
}

$missing_tables = array_diff($required_tables, $existing_tables);
if (empty($missing_tables)) {
    $checks['Required tables exist'] = true;
    echo "✅ All required tables exist<br>";
} else {
    echo "❌ Missing tables: " . implode(', ', $missing_tables) . "<br>";
    exit;
}

// Check for active transactions
$result = $connection->query("SELECT COUNT(*) as count FROM information_schema.innodb_trx");
$active_transactions = $result->fetch_assoc()['count'];
if ($active_transactions == 0) {
    $checks['No active transactions'] = true;
    echo "✅ No active transactions<br>";
} else {
    echo "⚠️ Warning: {$active_transactions} active transactions detected<br>";
}

// Step 3: Show what will be changed
echo "<h2>📝 Step 3: Migration Preview</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Table</th><th>Current Field</th><th>New Field</th><th>Type</th></tr>";

$migrations = [
    ['posts', 'id_post', 'id', 'Primary Key'],
    ['news', 'id_news', 'id', 'Primary Key'],
    ['vpo', 'id_vpo', 'id', 'Primary Key'],
    ['spo', 'id_spo', 'id', 'Primary Key'],
    ['schools', 'id_school', 'id', 'Primary Key'],
    ['vpo', 'id_region', 'region_id', 'Foreign Key'],
    ['vpo', 'id_town', 'town_id', 'Foreign Key'],
    ['vpo', 'id_area', 'area_id', 'Foreign Key'],
    ['spo', 'id_region', 'region_id', 'Foreign Key'],
    ['spo', 'id_town', 'town_id', 'Foreign Key'],
    ['spo', 'id_area', 'area_id', 'Foreign Key'],
    ['schools', 'id_region', 'region_id', 'Foreign Key'],
    ['schools', 'id_town', 'town_id', 'Foreign Key'],
    ['schools', 'id_area', 'area_id', 'Foreign Key'],
];

foreach ($migrations as $migration) {
    echo "<tr>";
    echo "<td>{$migration[0]}</td>";
    echo "<td><code>{$migration[1]}</code></td>";
    echo "<td><code>{$migration[2]}</code></td>";
    echo "<td>{$migration[3]}</td>";
    echo "</tr>";
}
echo "</table>";

// Step 4: Confirmation form
echo "<h2>⚡ Step 4: Execute Migration</h2>";

if (!isset($_POST['confirm_migration'])) {
    echo "<form method='post' style='background: #f8f9fa; padding: 20px; border-radius: 5px;'>";
    echo "<h3>⚠️ Final Confirmation Required</h3>";
    echo "<p>This migration will make <strong>irreversible changes</strong> to your database structure.</p>";
    echo "<label><input type='checkbox' name='backup_confirmed' required> I confirm I have a database backup</label><br><br>";
    echo "<label><input type='checkbox' name='understand_risks' required> I understand this will change database field names</label><br><br>";
    echo "<label><input type='checkbox' name='confirm_migration' value='1' required> I want to proceed with the migration</label><br><br>";
    echo "<button type='submit' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px;'>🚀 START MIGRATION</button>";
    echo "</form>";
} else {
    // Execute the migration
    echo "<h3>🔄 Executing Migration...</h3>";
    
    // Disable foreign key checks
    $connection->query("SET FOREIGN_KEY_CHECKS = 0");
    echo "✅ Foreign key checks disabled<br>";
    
    $migration_queries = [
        // Posts table
        "ALTER TABLE posts CHANGE COLUMN id_post id INT(11) NOT NULL AUTO_INCREMENT",
        
        // News table
        "ALTER TABLE news CHANGE COLUMN id_news id INT(5) NOT NULL AUTO_INCREMENT",
        "ALTER TABLE news CHANGE COLUMN id_vpo vpo_id INT(11)",
        "ALTER TABLE news CHANGE COLUMN id_spo spo_id INT(11)",
        "ALTER TABLE news CHANGE COLUMN id_school school_id INT(11)",
        
        // VPO table
        "ALTER TABLE vpo CHANGE COLUMN id_vpo id INT(11) NOT NULL AUTO_INCREMENT",
        "ALTER TABLE vpo CHANGE COLUMN id_region region_id INT(11)",
        "ALTER TABLE vpo CHANGE COLUMN id_town town_id INT(11)",
        "ALTER TABLE vpo CHANGE COLUMN id_area area_id INT(11)",
        "ALTER TABLE vpo CHANGE COLUMN id_country country_id INT(11)",
        
        // SPO table
        "ALTER TABLE spo CHANGE COLUMN id_spo id INT(11) NOT NULL AUTO_INCREMENT",
        "ALTER TABLE spo CHANGE COLUMN id_region region_id INT(11)",
        "ALTER TABLE spo CHANGE COLUMN id_town town_id INT(11)",
        "ALTER TABLE spo CHANGE COLUMN id_area area_id INT(11)",
        "ALTER TABLE spo CHANGE COLUMN id_country country_id INT(11)",
        
        // Schools table
        "ALTER TABLE schools CHANGE COLUMN id_school id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT",
        "ALTER TABLE schools CHANGE COLUMN id_region region_id INT(11)",
        "ALTER TABLE schools CHANGE COLUMN id_town town_id INT(11)",
        "ALTER TABLE schools CHANGE COLUMN id_area area_id INT(11)",
        "ALTER TABLE schools CHANGE COLUMN id_country country_id INT(11)",
        "ALTER TABLE schools CHANGE COLUMN id_rono rono_id INT(11)",
        "ALTER TABLE schools CHANGE COLUMN id_indeks indeks_id INT(11)",
    ];
    
    $success_count = 0;
    $error_count = 0;
    $errors = [];
    
    foreach ($migration_queries as $query) {
        if ($connection->query($query)) {
            $success_count++;
            echo "✅ Executed: " . substr($query, 0, 50) . "...<br>";
        } else {
            $error_count++;
            $error = $connection->error;
            $errors[] = $error;
            echo "❌ Failed: " . substr($query, 0, 50) . "... Error: {$error}<br>";
        }
    }
    
    // Re-enable foreign key checks
    $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    echo "✅ Foreign key checks re-enabled<br>";
    
    echo "<h3>📊 Migration Results</h3>";
    echo "<p><strong>Successful queries:</strong> {$success_count}</p>";
    echo "<p><strong>Failed queries:</strong> {$error_count}</p>";
    
    if ($error_count == 0) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; color: #155724;'>";
        echo "<h3>🎉 Migration Completed Successfully!</h3>";
        echo "<p>All database field names have been standardized.</p>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ol>";
        echo "<li>Update PHP files to use new field names</li>";
        echo "<li>Test all functionality</li>";
        echo "<li>Check error logs</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; color: #721c24;'>";
        echo "<h3>⚠️ Migration Completed with Errors</h3>";
        echo "<p>Some queries failed. Review the errors above.</p>";
        echo "<p>You may need to rollback and fix issues before retrying.</p>";
        echo "</div>";
    }
}

echo "<p><strong>Migration completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>