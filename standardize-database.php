<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>Database Standardization Script</h1>";
echo "<p>This script will:</p>";
echo "<ul>";
echo "<li>Standardize all meta description fields to 'meta_description'</li>";
echo "<li>Remove all meta_keywords fields</li>";
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

// 1. Categories table
alterTable($connection, 'categories', [
    "ALTER TABLE categories DROP COLUMN IF EXISTS meta_k_category",
    "ALTER TABLE categories DROP COLUMN IF EXISTS meta_d_category",
    "ALTER TABLE categories DROP COLUMN IF EXISTS meta_keywords"
], "Standardizing categories table");

// 2. Posts table
alterTable($connection, 'posts', [
    "ALTER TABLE posts CHANGE COLUMN `meta_d` `meta_description` TEXT DEFAULT NULL",
    "ALTER TABLE posts DROP COLUMN IF EXISTS meta_k",
    "ALTER TABLE posts DROP COLUMN IF EXISTS meta_keywords"
], "Standardizing posts table");

// 3. News table
alterTable($connection, 'news', [
    "ALTER TABLE news DROP COLUMN IF EXISTS meta_keywords",
    "ALTER TABLE news DROP COLUMN IF EXISTS meta_k",
    "ALTER TABLE news ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL"
], "Standardizing news table");

// 4. VPO table
alterTable($connection, 'vpo', [
    "ALTER TABLE vpo CHANGE COLUMN IF EXISTS `metaD` `meta_description` TEXT DEFAULT NULL",
    "ALTER TABLE vpo DROP COLUMN IF EXISTS metaK",
    "ALTER TABLE vpo DROP COLUMN IF EXISTS meta_keywords"
], "Standardizing vpo table");

// 5. SPO table
alterTable($connection, 'spo', [
    "ALTER TABLE spo CHANGE COLUMN IF EXISTS `metaD` `meta_description` TEXT DEFAULT NULL",
    "ALTER TABLE spo DROP COLUMN IF EXISTS metaK",
    "ALTER TABLE spo DROP COLUMN IF EXISTS meta_keywords"
], "Standardizing spo table");

// 6. Schools table
alterTable($connection, 'schools', [
    "ALTER TABLE schools DROP COLUMN IF EXISTS meta_keywords",
    "ALTER TABLE schools ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL"
], "Standardizing schools table");

// 7. Pages table (if exists)
alterTable($connection, 'pages', [
    "ALTER TABLE pages DROP COLUMN IF EXISTS meta_keywords",
    "ALTER TABLE pages ADD COLUMN IF NOT EXISTS `meta_description` TEXT DEFAULT NULL"
], "Standardizing pages table");

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

echo "Remaining meta fields in database:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo sprintf("  %-20s %-30s %s\n", $row['TABLE_NAME'], $row['COLUMN_NAME'], $row['DATA_TYPE']);
}
echo "</pre>";

echo "<h3>✓ Database standardization complete!</h3>";
?>