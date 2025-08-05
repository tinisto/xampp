<?php
/**
 * Complete Database Standardization Migration
 * This completes the field naming standardization including regions table
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🔧 Complete Field Standardization</h1>";

if (!isset($_POST['run_complete'])) {
    echo "<h2>Additional standardization needed:</h2>";
    echo "<ul>";
    echo "<li>regions table: <code>id</code> → <code>region_id</code></li>";
    echo "<li>towns table: <code>id</code> → <code>town_id</code></li>";
    echo "<li>areas table: <code>id</code> → <code>area_id</code></li>";
    echo "<li>countries table: <code>id</code> → <code>country_id</code></li>";
    echo "</ul>";
    
    echo "<p><strong>This will make ALL primary keys follow the [table]_id pattern for consistency.</strong></p>";
    
    echo "<form method='post'>";
    echo "<p><input type='checkbox' required> I understand this will rename more primary keys</p>";
    echo "<button type='submit' name='run_complete' value='1' style='background: orange; color: white; padding: 15px 30px; font-size: 18px; border: none;'>COMPLETE STANDARDIZATION</button>";
    echo "</form>";
} else {
    echo "<h2>🔄 Running Complete Standardization...</h2>";
    
    $connection->query("SET FOREIGN_KEY_CHECKS = 0");
    
    $queries = [
        // Standardize lookup table primary keys
        "ALTER TABLE regions CHANGE COLUMN id region_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT",
        "ALTER TABLE towns CHANGE COLUMN id town_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT", 
        "ALTER TABLE areas CHANGE COLUMN id area_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT",
        "ALTER TABLE countries CHANGE COLUMN id country_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT",
        
        // Update foreign key references in towns table
        "ALTER TABLE towns CHANGE COLUMN region_id region_ref_id INT(10) UNSIGNED",
        "ALTER TABLE towns CHANGE COLUMN region_ref_id region_id INT(10) UNSIGNED",
        
        // Update foreign key references in areas table  
        "ALTER TABLE areas CHANGE COLUMN region_id region_ref_id INT(10) UNSIGNED",
        "ALTER TABLE areas CHANGE COLUMN region_ref_id region_id INT(10) UNSIGNED",
    ];
    
    $success = 0;
    $errors = 0;
    
    foreach ($queries as $query) {
        if ($connection->query($query)) {
            echo "✅ " . substr($query, 0, 60) . "...<br>";
            $success++;
        } else {
            echo "❌ " . substr($query, 0, 60) . "... ERROR: " . $connection->error . "<br>";
            $errors++;
        }
    }
    
    $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<h2>📊 Results:</h2>";
    echo "<p>✅ Successful: $success</p>";
    echo "<p>❌ Errors: $errors</p>";
    
    if ($errors == 0) {
        echo "<h2>🎉 COMPLETE STANDARDIZATION SUCCESSFUL!</h2>";
        echo "<p>All database fields now follow consistent naming:</p>";
        echo "<ul>";
        echo "<li>Primary keys: [table]_id (region_id, town_id, etc.)</li>";
        echo "<li>Foreign keys: [table]_id (region_id, user_id, etc.)</li>";
        echo "</ul>";
        echo "<p><strong>⚠️ Important:</strong> You'll need to update the region page code to use region_id instead of id!</p>";
    }
}
?>