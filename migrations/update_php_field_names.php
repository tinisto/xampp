<?php
/**
 * Script to update PHP files with new standardized field names
 * Run this AFTER applying the SQL migration
 */

$replacements = [
    // Primary keys
    'id_post' => 'id',
    'id_news' => 'id',
    'id_vpo' => 'id',
    'id_spo' => 'id',
    'id_school' => 'id',
    
    // Foreign keys in VPO/SPO/Schools
    'id_region' => 'region_id',
    'id_town' => 'town_id',
    'id_area' => 'area_id',
    'id_country' => 'country_id',
    'id_rono' => 'rono_id',
    'id_indeks' => 'indeks_id',
    
    // Foreign keys in news
    'id_vpo' => 'vpo_id',
    'id_spo' => 'spo_id',
    'id_school' => 'school_id',
];

echo "<h1>PHP Field Name Update Tool</h1>";
echo "<p>This tool will help identify PHP files that need updating after the database migration.</p>";

// Function to search for field usage in PHP files
function searchFieldUsage($dir, $replacements) {
    $results = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            $matches = [];
            
            foreach ($replacements as $old => $new) {
                // Look for various patterns where field names might appear
                $patterns = [
                    "/['\"]{$old}['\"]/",           // 'id_post' or "id_post"
                    "/\['{$old}'\]/",                // ['id_post']
                    "/\[\"{$old}\"\]/",              // ["id_post"]
                    "/WHERE\s+{$old}\s*=/i",         // WHERE id_post =
                    "/SELECT.*{$old}.*FROM/i",       // SELECT id_post FROM
                    "/\->{$old}/",                   // ->id_post
                    "/\$.*{$old}/",                  // $id_post variable
                ];
                
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $content)) {
                        if (!isset($matches[$old])) {
                            $matches[$old] = [];
                        }
                        $matches[$old][] = $pattern;
                    }
                }
            }
            
            if (!empty($matches)) {
                $results[$file->getPathname()] = $matches;
            }
        }
    }
    
    return $results;
}

// Create a sed script for automatic replacement
function generateSedScript($replacements) {
    $script = "#!/bin/bash\n";
    $script .= "# Backup all PHP files first\n";
    $script .= "find . -name '*.php' -exec cp {} {}.bak \\;\n\n";
    $script .= "# Perform replacements\n";
    
    foreach ($replacements as $old => $new) {
        // Skip if old and new are the same (like id to id)
        if ($old === $new) continue;
        
        $script .= "# Replace {$old} with {$new}\n";
        $script .= "find . -name '*.php' -exec sed -i '' 's/['\"]${old}['\"]/['\"]${new}['\"]/g' {} \\;\n";
        $script .= "find . -name '*.php' -exec sed -i '' 's/\\['{$old}'\\]/['{$new}']/g' {} \\;\n";
        $script .= "find . -name '*.php' -exec sed -i '' 's/\\[\"{$old}\"\\]/[\"{$new}\"]/g' {} \\;\n";
        $script .= "find . -name '*.php' -exec sed -i '' 's/WHERE ${old} /WHERE ${new} /g' {} \\;\n";
        $script .= "find . -name '*.php' -exec sed -i '' 's/->${old}/->${new}/g' {} \\;\n\n";
    }
    
    return $script;
}

// Display summary
echo "<h2>Field Replacements to Apply:</h2>";
echo "<table border='1'>";
echo "<tr><th>Old Field Name</th><th>New Field Name</th><th>Tables Affected</th></tr>";

$tableMapping = [
    'id_post' => 'posts',
    'id_news' => 'news',
    'id_vpo' => 'vpo',
    'id_spo' => 'spo',
    'id_school' => 'schools',
    'id_region' => 'vpo, spo, schools',
    'id_town' => 'vpo, spo, schools',
    'id_area' => 'vpo, spo, schools',
    'id_country' => 'vpo, spo, schools',
];

foreach ($replacements as $old => $new) {
    $tables = $tableMapping[$old] ?? 'various';
    echo "<tr><td><code>{$old}</code></td><td><code>{$new}</code></td><td>{$tables}</td></tr>";
}
echo "</table>";

// Generate the update script
$sedScript = generateSedScript($replacements);
$scriptPath = $_SERVER['DOCUMENT_ROOT'] . '/migrations/update_field_names.sh';
file_put_contents($scriptPath, $sedScript);
chmod($scriptPath, 0755);

echo "<h2>Update Script Generated</h2>";
echo "<p>A bash script has been created at: <code>/migrations/update_field_names.sh</code></p>";
echo "<p>To update all PHP files, run:</p>";
echo "<pre>cd /path/to/your/project\n./migrations/update_field_names.sh</pre>";

echo "<h2>Manual Review Required For:</h2>";
echo "<ul>";
echo "<li>Complex queries with JOINs</li>";
echo "<li>Dynamic field name construction</li>";
echo "<li>Field names in comments or documentation</li>";
echo "<li>JavaScript/AJAX requests that use these field names</li>";
echo "</ul>";

echo "<h2>Testing Checklist:</h2>";
echo "<ol>";
echo "<li>✓ Backup database before migration</li>";
echo "<li>✓ Run SQL migration script</li>";
echo "<li>✓ Backup all PHP files</li>";
echo "<li>✓ Run field name update script</li>";
echo "<li>✓ Test all major features</li>";
echo "<li>✓ Check error logs</li>";
echo "</ol>";
?>