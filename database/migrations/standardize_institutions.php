<?php
/**
 * Institution Fields Standardization Migration
 * Standardizes field names across schools, SPO, and VPO tables
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/loadEnv.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/database/db_connections.php';

echo "<h1>🏫 Institution Fields Standardization</h1>";

if (!isset($_POST['run_institution_standardization'])) {
    echo "<h2>Institution Field Standardization Plan:</h2>";
    echo "<h3>1. Name Fields → 'name'</h3>";
    echo "<ul>";
    echo "<li>schools: <code>school_name</code> → <code>name</code></li>";
    echo "<li>spo: <code>spo_name</code> → <code>name</code></li>";
    echo "<li>vpo: <code>vpo_name</code> → <code>name</code></li>";
    echo "</ul>";
    
    echo "<h3>2. Image Fields → 'image_1/2/3'</h3>";
    echo "<ul>";
    echo "<li>schools: <code>image_school_1/2/3</code> → <code>image_1/2/3</code></li>";
    echo "<li>spo: <code>image_spo_1/2/3</code> → <code>image_1/2/3</code></li>";
    echo "<li>vpo: <code>image_vpo_1/2/3</code> → <code>image_1/2/3</code></li>";
    echo "</ul>";
    
    echo "<h3>3. URL Fields → 'url_slug'</h3>";
    echo "<ul>";
    echo "<li>schools: Add <code>url_slug</code> field (currently uses ID)</li>";
    echo "<li>spo: <code>spo_url</code> → <code>url_slug</code></li>";
    echo "<li>vpo: <code>vpo_url</code> → <code>url_slug</code></li>";
    echo "</ul>";
    
    echo "<h3>4. Other inconsistencies:</h3>";
    echo "<ul>";
    echo "<li>schools: Missing <code>zip_code</code> (add field)</li>";
    echo "<li>schools: <code>logo</code> field (keep as is, unique to schools)</li>";
    echo "</ul>";
    
    echo "<p><strong>This will create consistent field naming across all institution types.</strong></p>";
    
    echo "<form method='post'>";
    echo "<p><input type='checkbox' required> I understand this will rename institution fields for consistency</p>";
    echo "<button type='submit' name='run_institution_standardization' value='1' style='background: orange; color: white; padding: 15px 30px; font-size: 18px; border: none;'>STANDARDIZE INSTITUTION FIELDS</button>";
    echo "</form>";
} else {
    echo "<h2>🔄 Running Institution Standardization...</h2>";
    
    $connection->query("SET FOREIGN_KEY_CHECKS = 0");
    
    $queries = [
        // 1. Standardize name fields
        "ALTER TABLE schools CHANGE COLUMN school_name name VARCHAR(255)",
        "ALTER TABLE spo CHANGE COLUMN spo_name name VARCHAR(255)",
        "ALTER TABLE vpo CHANGE COLUMN vpo_name name VARCHAR(255)",
        
        // 2. Standardize image fields - Schools
        "ALTER TABLE schools CHANGE COLUMN image_school_1 image_1 VARCHAR(255)",
        "ALTER TABLE schools CHANGE COLUMN image_school_2 image_2 VARCHAR(255)",
        "ALTER TABLE schools CHANGE COLUMN image_school_3 image_3 VARCHAR(255)",
        
        // 3. Standardize image fields - SPO
        "ALTER TABLE spo CHANGE COLUMN image_spo_1 image_1 VARCHAR(255)",
        "ALTER TABLE spo CHANGE COLUMN image_spo_2 image_2 VARCHAR(255)", 
        "ALTER TABLE spo CHANGE COLUMN image_spo_3 image_3 VARCHAR(255)",
        
        // 4. Standardize image fields - VPO
        "ALTER TABLE vpo CHANGE COLUMN image_vpo_1 image_1 VARCHAR(255)",
        "ALTER TABLE vpo CHANGE COLUMN image_vpo_2 image_2 VARCHAR(255)",
        "ALTER TABLE vpo CHANGE COLUMN image_vpo_3 image_3 VARCHAR(255)",
        
        // 5. Standardize URL fields
        "ALTER TABLE schools ADD COLUMN url_slug VARCHAR(255) AFTER name",
        "ALTER TABLE spo CHANGE COLUMN spo_url url_slug VARCHAR(255)",
        "ALTER TABLE vpo CHANGE COLUMN vpo_url url_slug VARCHAR(255)",
        
        // 6. Add missing zip_code to schools
        "ALTER TABLE schools ADD COLUMN zip_code INT(11) AFTER short_name",
    ];
    
    $success = 0;
    $errors = 0;
    
    foreach ($queries as $query) {
        if ($connection->query($query)) {
            echo "✅ " . substr($query, 0, 80) . "...<br>";
            $success++;
        } else {
            echo "❌ " . substr($query, 0, 80) . "... ERROR: " . $connection->error . "<br>";
            $errors++;
        }
    }
    
    $connection->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<h2>📊 Results:</h2>";
    echo "<p>✅ Successful: $success</p>";
    echo "<p>❌ Errors: $errors</p>";
    
    if ($errors == 0) {
        echo "<h2>🎉 INSTITUTION STANDARDIZATION SUCCESSFUL!</h2>";
        echo "<p>All institution tables now use consistent field names:</p>";
        echo "<ul>";
        echo "<li><strong>Name:</strong> schools.name, spo.name, vpo.name</li>";
        echo "<li><strong>Images:</strong> image_1, image_2, image_3 (all tables)</li>";
        echo "<li><strong>URL:</strong> url_slug (all tables)</li>";
        echo "<li><strong>Address:</strong> All have zip_code and street</li>";
        echo "</ul>";
        echo "<p><strong>⚠️ Important:</strong> Templates and code need to be updated to use the new field names!</p>";
        
        echo "<h3>Next steps:</h3>";
        echo "<ul>";
        echo "<li>Update institution templates to use new field names</li>";
        echo "<li>Generate URL slugs for existing schools</li>";
        echo "<li>Test all institution pages</li>";
        echo "</ul>";
    }
}
?>