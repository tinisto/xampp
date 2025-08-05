<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🚀 Simple Database Migration</h1>";

if (!isset($_POST['run_migration'])) {
    echo "<h2>This will standardize database field names</h2>";
    echo "<p><strong>Changes to be made:</strong></p>";
    echo "<ul>";
    echo "<li>id_post → id (posts table)</li>";
    echo "<li>id_news → id (news table)</li>";
    echo "<li>id_vpo → id (vpo table)</li>";
    echo "<li>id_spo → id (spo table)</li>";
    echo "<li>id_school → id (schools table)</li>";
    echo "<li>id_region → region_id (all tables)</li>";
    echo "<li>id_town → town_id (all tables)</li>";
    echo "</ul>";
    
    echo "<form method='post'>";
    echo "<h3>⚠️ BACKUP YOUR DATABASE FIRST!</h3>";
    echo "<p><input type='checkbox' required> I have backed up my database</p>";
    echo "<p><input type='checkbox' required> I understand this makes permanent changes</p>";
    echo "<button type='submit' name='run_migration' value='1' style='background: red; color: white; padding: 15px 30px; font-size: 18px; border: none;'>RUN MIGRATION NOW</button>";
    echo "</form>";
} else {
    echo "<h2>🔄 Running Migration...</h2>";
    
    $connection->query("SET FOREIGN_KEY_CHECKS = 0");
    
    $queries = [
        "ALTER TABLE posts CHANGE COLUMN id_post id INT(11) NOT NULL AUTO_INCREMENT",
        "ALTER TABLE news CHANGE COLUMN id_news id INT(5) NOT NULL AUTO_INCREMENT",
        "ALTER TABLE vpo CHANGE COLUMN id_vpo id INT(11) NOT NULL AUTO_INCREMENT",
        "ALTER TABLE vpo CHANGE COLUMN id_region region_id INT(11)",
        "ALTER TABLE vpo CHANGE COLUMN id_town town_id INT(11)",
        "ALTER TABLE vpo CHANGE COLUMN id_area area_id INT(11)",
        "ALTER TABLE spo CHANGE COLUMN id_spo id INT(11) NOT NULL AUTO_INCREMENT",
        "ALTER TABLE spo CHANGE COLUMN id_region region_id INT(11)",
        "ALTER TABLE spo CHANGE COLUMN id_town town_id INT(11)",
        "ALTER TABLE spo CHANGE COLUMN id_area area_id INT(11)",
        "ALTER TABLE schools CHANGE COLUMN id_school id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT",
        "ALTER TABLE schools CHANGE COLUMN id_region region_id INT(11)",
        "ALTER TABLE schools CHANGE COLUMN id_town town_id INT(11)",
        "ALTER TABLE schools CHANGE COLUMN id_area area_id INT(11)",
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
        echo "<h2>🎉 MIGRATION SUCCESSFUL!</h2>";
        echo "<p>All database fields have been standardized.</p>";
    } else {
        echo "<h2>⚠️ Migration completed with errors</h2>";
    }
}
?>