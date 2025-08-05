<?php
// Prioritized database field migration
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Prioritized Database Field Migration</h1>";

try {
    require_once __DIR__ . '/../config/loadEnv.php';
    require_once __DIR__ . '/../database/db_connections.php';
    
    if (!isset($connection) || !$connection) {
        echo "<p>❌ Database connection not available</p>";
        exit;
    }
    
    echo "<p>✅ Database connected</p>";
    
    $errors = 0;
    $success = 0;
    
    // HIGH PRIORITY MIGRATIONS
    echo "<h2>🔴 HIGH PRIORITY MIGRATIONS</h2>";
    
    // 1. Fix regions table primary key
    echo "<h3>1. Regions Table - Primary Key</h3>";
    echo "<p>⚠️ SKIPPED: Regions table primary key change would break many foreign key relationships. Needs comprehensive update.</p>";
    
    // 2. Fix comments.id_entity
    echo "<h3>2. Comments Table - id_entity → entity_id</h3>";
    $check = $connection->query("SHOW COLUMNS FROM comments LIKE 'id_entity'");
    if ($check && $check->num_rows > 0) {
        // Check if entity_id already exists
        $check_new = $connection->query("SHOW COLUMNS FROM comments LIKE 'entity_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE comments CHANGE COLUMN id_entity entity_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_entity to entity_id</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename: " . $connection->error . "</p>";
                $errors++;
            }
        } else {
            echo "<p>⚠️ entity_id already exists or id_entity not found</p>";
        }
    } else {
        echo "<p>⚠️ id_entity column not found</p>";
    }
    
    // 3. Fix schools.id_country
    echo "<h3>3. Schools Table - id_country → country_id</h3>";
    $check = $connection->query("SHOW COLUMNS FROM schools LIKE 'id_country'");
    if ($check && $check->num_rows > 0) {
        $check_new = $connection->query("SHOW COLUMNS FROM schools LIKE 'country_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE schools CHANGE COLUMN id_country country_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_country to country_id in schools</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename: " . $connection->error . "</p>";
                $errors++;
            }
        } else {
            echo "<p>⚠️ country_id already exists or id_country not found</p>";
        }
    } else {
        echo "<p>⚠️ id_country column not found in schools</p>";
    }
    
    // MEDIUM PRIORITY MIGRATIONS
    echo "<h2>🟡 MEDIUM PRIORITY MIGRATIONS</h2>";
    
    // 1. Fix areas table primary key
    echo "<h3>1. Areas Table - Primary Key</h3>";
    echo "<p>⚠️ SKIPPED: Areas table primary key change would affect foreign keys. Needs careful migration.</p>";
    
    // 2. Fix towns table primary key
    echo "<h3>2. Towns Table - Primary Key</h3>";
    echo "<p>⚠️ SKIPPED: Towns table primary key change would affect many foreign keys. Needs careful migration.</p>";
    
    // 3. Fix news foreign keys
    echo "<h3>3. News Table - Foreign Keys</h3>";
    
    // Fix id_vpo → vpo_id
    $check = $connection->query("SHOW COLUMNS FROM news LIKE 'id_vpo'");
    if ($check && $check->num_rows > 0) {
        $check_new = $connection->query("SHOW COLUMNS FROM news LIKE 'vpo_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE news CHANGE COLUMN id_vpo vpo_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_vpo to vpo_id</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename id_vpo: " . $connection->error . "</p>";
                $errors++;
            }
        }
    }
    
    // Fix id_spo → spo_id
    $check = $connection->query("SHOW COLUMNS FROM news LIKE 'id_spo'");
    if ($check && $check->num_rows > 0) {
        $check_new = $connection->query("SHOW COLUMNS FROM news LIKE 'spo_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE news CHANGE COLUMN id_spo spo_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_spo to spo_id</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename id_spo: " . $connection->error . "</p>";
                $errors++;
            }
        }
    }
    
    // Fix id_school → school_id
    $check = $connection->query("SHOW COLUMNS FROM news LIKE 'id_school'");
    if ($check && $check->num_rows > 0) {
        $check_new = $connection->query("SHOW COLUMNS FROM news LIKE 'school_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE news CHANGE COLUMN id_school school_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_school to school_id</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename id_school: " . $connection->error . "</p>";
                $errors++;
            }
        }
    }
    
    // Fix schools table foreign keys
    echo "<h3>4. Schools Table - Other Foreign Keys</h3>";
    
    // Fix id_rono → rono_id
    $check = $connection->query("SHOW COLUMNS FROM schools LIKE 'id_rono'");
    if ($check && $check->num_rows > 0) {
        $check_new = $connection->query("SHOW COLUMNS FROM schools LIKE 'rono_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE schools CHANGE COLUMN id_rono rono_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_rono to rono_id</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename id_rono: " . $connection->error . "</p>";
                $errors++;
            }
        }
    }
    
    // Fix id_indeks → indeks_id
    $check = $connection->query("SHOW COLUMNS FROM schools LIKE 'id_indeks'");
    if ($check && $check->num_rows > 0) {
        $check_new = $connection->query("SHOW COLUMNS FROM schools LIKE 'indeks_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE schools CHANGE COLUMN id_indeks indeks_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_indeks to indeks_id</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename id_indeks: " . $connection->error . "</p>";
                $errors++;
            }
        }
    }
    
    // Fix SPO and VPO tables
    echo "<h3>5. SPO/VPO Tables - id_country → country_id</h3>";
    
    // Fix SPO
    $check = $connection->query("SHOW COLUMNS FROM spo LIKE 'id_country'");
    if ($check && $check->num_rows > 0) {
        $check_new = $connection->query("SHOW COLUMNS FROM spo LIKE 'country_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE spo CHANGE COLUMN id_country country_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_country to country_id in SPO</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename in SPO: " . $connection->error . "</p>";
                $errors++;
            }
        }
    }
    
    // Fix VPO
    $check = $connection->query("SHOW COLUMNS FROM vpo LIKE 'id_country'");
    if ($check && $check->num_rows > 0) {
        $check_new = $connection->query("SHOW COLUMNS FROM vpo LIKE 'country_id'");
        if ($check_new && $check_new->num_rows === 0) {
            $sql = "ALTER TABLE vpo CHANGE COLUMN id_country country_id INT(11)";
            if ($connection->query($sql)) {
                echo "<p>✅ Renamed id_country to country_id in VPO</p>";
                $success++;
            } else {
                echo "<p>❌ Failed to rename in VPO: " . $connection->error . "</p>";
                $errors++;
            }
        }
    }
    
    // LOW PRIORITY MIGRATIONS
    echo "<h2>🟢 LOW PRIORITY MIGRATIONS</h2>";
    echo "<p>⚠️ Countries table primary key change skipped - rarely changes and would affect many foreign keys.</p>";
    
    // Summary
    echo "<h2>📊 Migration Summary</h2>";
    echo "<p>✅ Successful migrations: $success</p>";
    echo "<p>❌ Failed migrations: $errors</p>";
    
    if ($errors === 0 && $success > 0) {
        echo "<p style='color: green; font-weight: bold;'>🎉 Migration completed successfully!</p>";
        
        // Create marker file
        file_put_contents(__DIR__ . '/prioritized_migration_completed.txt', date('Y-m-d H:i:s') . " - Prioritized migration completed\n");
    } else if ($errors > 0) {
        echo "<p style='color: red; font-weight: bold;'>⚠️ Migration completed with errors. Please review and fix.</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>