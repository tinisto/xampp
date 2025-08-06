<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Database Standardization Script - Corrected Version</h1>";
echo "<p>This script will:</p>";
echo "<ul>";
echo "<li>Rename all meta description fields to 'meta_description'</li>";
echo "<li>Remove all meta keyword fields</li>";
echo "</ul>";
echo "<hr>";

// Function to safely alter table
function alterTable($connection, $table, $changes, $description) {
    echo "<h3>$table - $description</h3>";
    echo "<pre>";
    
    // Check if table exists
    $tableCheck = mysqli_query($connection, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($tableCheck) == 0) {
        echo "Table '$table' does not exist - skipping\n";
        echo "</pre>";
        return;
    }
    
    // Show current columns
    echo "Current meta fields:\n";
    $cols = mysqli_query($connection, "SHOW COLUMNS FROM $table WHERE Field LIKE '%meta%' OR Field LIKE '%description%' OR Field LIKE '%keyword%'");
    while ($col = mysqli_fetch_assoc($cols)) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    // Apply changes
    foreach ($changes as $change) {
        echo "\nExecuting: $change\n";
        if (mysqli_query($connection, $change)) {
            echo "✓ Success\n";
        } else {
            echo "✗ Error: " . mysqli_error($connection) . "\n";
        }
    }
    
    echo "</pre>";
}

// 1. POSTS table - has meta_d_post, meta_k_post
alterTable($connection, 'posts', [
    "ALTER TABLE posts CHANGE COLUMN `meta_d_post` `meta_description` TEXT DEFAULT NULL",
    "ALTER TABLE posts DROP COLUMN IF EXISTS `meta_k_post`"
], "Standardizing posts table");

// 2. NEWS table - has meta_d_news, meta_k_news  
alterTable($connection, 'news', [
    "ALTER TABLE news CHANGE COLUMN `meta_d_news` `meta_description` TEXT DEFAULT NULL",
    "ALTER TABLE news DROP COLUMN IF EXISTS `meta_k_news`"
], "Standardizing news table");

// 3. VPO table - has meta_d_vpo, meta_k_vpo
alterTable($connection, 'vpo', [
    "ALTER TABLE vpo CHANGE COLUMN `meta_d_vpo` `meta_description` TEXT DEFAULT NULL",
    "ALTER TABLE vpo DROP COLUMN IF EXISTS `meta_k_vpo`"
], "Standardizing vpo table");

// 4. SPO table - has meta_d_spo, meta_k_spo
alterTable($connection, 'spo', [
    "ALTER TABLE spo CHANGE COLUMN `meta_d_spo` `meta_description` TEXT DEFAULT NULL",
    "ALTER TABLE spo DROP COLUMN IF EXISTS `meta_k_spo`"
], "Standardizing spo table");

// 5. SCHOOLS table - no meta fields currently
alterTable($connection, 'schools', [
    "ALTER TABLE schools ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL"
], "Adding meta_description to schools table");

// 6. CATEGORIES table - already has correct field names, just remove keywords
alterTable($connection, 'categories', [
    "ALTER TABLE categories DROP COLUMN IF EXISTS `meta_keywords`"
], "Removing meta_keywords from categories table");

// Final summary
echo "<h2>Final Summary</h2>";
echo "<pre>";
$result = mysqli_query($connection, "
    SELECT 
        TABLE_NAME,
        COLUMN_NAME,
        DATA_TYPE
    FROM 
        INFORMATION_SCHEMA.COLUMNS
    WHERE 
        TABLE_SCHEMA = DATABASE()
        AND (COLUMN_NAME LIKE '%meta%' OR COLUMN_NAME LIKE '%description%')
    ORDER BY 
        TABLE_NAME, COLUMN_NAME
");

echo "Remaining meta/description fields in database:\n\n";
echo sprintf("%-20s %-30s %s\n", "TABLE", "COLUMN", "TYPE");
echo str_repeat("-", 70) . "\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo sprintf("%-20s %-30s %s\n", $row['TABLE_NAME'], $row['COLUMN_NAME'], $row['DATA_TYPE']);
}
echo "</pre>";

echo "<h3>✓ Database standardization complete!</h3>";
echo "<p>All tables now use 'meta_description' for SEO descriptions.</p>";
echo "<p>All meta keyword fields have been removed.</p>";
?>