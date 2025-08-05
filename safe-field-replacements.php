<?php
// Safe field replacements based on actual migrations completed
echo "<h1>Safe Field Replacements</h1>";

echo "<h2>✅ Fields Successfully Migrated in Database:</h2>";
echo "<pre style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";

// Only the fields we ACTUALLY migrated
$safe_replacements = [
    // Comments table
    "id_entity → entity_id",
    
    // Schools table  
    "id_country → country_id (in schools, spo, vpo tables)",
    "id_rono → rono_id",
    "id_indeks → indeks_id",
    
    // News table
    "id_vpo → vpo_id", 
    "id_spo → spo_id",
    "id_school → school_id"
];

foreach ($safe_replacements as $replacement) {
    echo "✅ $replacement\n";
}

echo "</pre>";

echo "<h2>🔧 Safe Replacement Commands:</h2>";
echo "<pre style='background: #e8f5e9; padding: 15px; border-radius: 5px;'>";

// Safe replacements only
echo "#!/bin/bash\n";
echo "# Safe field replacements for successfully migrated fields only\n\n";

echo "# Create backup first\n";
echo "echo 'Creating backup...'\n";
echo "tar -czf php_files_backup_$(date +%Y%m%d_%H%M%S).tar.gz *.php pages/ includes/ api/ comments/ common-components/\n\n";

echo "# Replace id_entity with entity_id (comments table)\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_entity\\b/entity_id/g' {} +\n\n";

echo "# Replace id_vpo with vpo_id (news table)\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_vpo\\b/vpo_id/g' {} +\n\n";

echo "# Replace id_spo with spo_id (news table)\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_spo\\b/spo_id/g' {} +\n\n";

echo "# Replace id_school with school_id (news table)\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_school\\b/school_id/g' {} +\n\n";

echo "# Replace id_rono with rono_id (schools table)\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_rono\\b/rono_id/g' {} +\n\n";

echo "# Replace id_indeks with indeks_id (schools table)\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_indeks\\b/indeks_id/g' {} +\n\n";

echo "# Replace id_country with country_id (foreign key in schools/spo/vpo tables)\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_country\\b/country_id/g' {} +\n\n";

echo "# Clean up backup files after verifying changes\n";
echo "# find . -name '*.php.bak' -type f -delete\n";

echo "</pre>";

echo "<h2>⚠️ DO NOT Replace These (Primary Keys):</h2>";
echo "<pre style='background: #ffebee; padding: 15px; border-radius: 5px;'>";

$dangerous_replacements = [
    "area_id → id (would break foreign keys!)",
    "country_id → id (would break foreign keys!)", 
    "region_id → id (would break foreign keys!)",
    "town_id → id (would break foreign keys!)"
];

foreach ($dangerous_replacements as $dangerous) {
    echo "❌ $dangerous\n";
}

echo "</pre>";

echo "<h2>📋 Manual Replacement Script:</h2>";
echo "<p>Save this as <code>update_field_names.sh</code> and run it:</p>";
echo "<textarea style='width: 100%; height: 400px; font-family: monospace;'>";
echo "#!/bin/bash\n";
echo "# Safe field replacements for migrated fields\n\n";
echo "echo 'Creating backup...'\n";
echo "tar -czf php_backup_$(date +%Y%m%d_%H%M%S).tar.gz *.php pages/ includes/ api/ comments/ common-components/\n\n";
echo "echo 'Replacing field names...'\n\n";
echo "# Comments table\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_entity\\b/entity_id/g' {} +\n";
echo "echo '✅ Replaced id_entity with entity_id'\n\n";
echo "# News table foreign keys\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_vpo\\b/vpo_id/g' {} +\n";
echo "echo '✅ Replaced id_vpo with vpo_id'\n\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_spo\\b/spo_id/g' {} +\n";
echo "echo '✅ Replaced id_spo with spo_id'\n\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_school\\b/school_id/g' {} +\n";
echo "echo '✅ Replaced id_school with school_id'\n\n";
echo "# Schools table foreign keys\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_rono\\b/rono_id/g' {} +\n";
echo "echo '✅ Replaced id_rono with rono_id'\n\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_indeks\\b/indeks_id/g' {} +\n";
echo "echo '✅ Replaced id_indeks with indeks_id'\n\n";
echo "find . -name '*.php' -type f -exec sed -i.bak 's/\\bid_country\\b/country_id/g' {} +\n";
echo "echo '✅ Replaced id_country with country_id'\n\n";
echo "echo 'Done! Backup files created with .bak extension'\n";
echo "echo 'To remove backups after verification: find . -name \"*.php.bak\" -type f -delete'\n";
echo "</textarea>";

echo "<h2>🎯 Recommended Approach:</h2>";
echo "<ol>";
echo "<li>Create a backup of all PHP files first</li>";
echo "<li>Run the safe replacements only (shown above)</li>";
echo "<li>Test the site thoroughly</li>";
echo "<li>Only remove .bak files after confirming everything works</li>";
echo "</ol>";
?>