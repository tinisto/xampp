<?php
// Script to fix regions table column references from 'id' to 'id_region'

echo "🚀 Starting regions ID field fix...\n";

$files = [
    'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content-cached.php',
    'vpo-all-regions-standalone.php',
    'vpo-all-regions-direct.php', 
    'vpo-all-regions-fixed.php',
    '_cleanup/test_files/spo-test-direct.php',
    '_cleanup/test_files/vpo-test-standalone.php',
    'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-content.php',
    'pages/common/educational-institutions-all-regions/educational-institutions-all-regions-old.php'
];

$replacements = [
    "SELECT id, region_name, region_name_en FROM regions" => "SELECT id_region, region_name, region_name_en FROM regions",
    "WHERE country_id = 1" => "WHERE id_country = 1",
    "\$row['id']" => "\$row['id_region']"
];

$updatedFiles = 0;
$totalReplacements = 0;

foreach ($files as $file) {
    $fullPath = __DIR__ . '/' . $file;
    
    if (!file_exists($fullPath)) {
        echo "⚠️  File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($fullPath);
    $originalContent = $content;
    $fileReplacements = 0;
    
    foreach ($replacements as $search => $replace) {
        $newContent = str_replace($search, $replace, $content);
        $replacementCount = substr_count($content, $search);
        if ($replacementCount > 0) {
            $content = $newContent;
            $fileReplacements += $replacementCount;
            $totalReplacements += $replacementCount;
        }
    }
    
    if ($content !== $originalContent) {
        file_put_contents($fullPath, $content);
        $updatedFiles++;
        echo "✅ Updated: $file ({$fileReplacements} replacements)\n";
    }
}

echo "\n📊 Regions ID Fix Summary:\n";
echo "✅ Files updated: {$updatedFiles}\n";
echo "✅ Total replacements: {$totalReplacements}\n";

if ($totalReplacements > 0) {
    echo "\n🎉 Regions ID field migration completed successfully!\n";
} else {
    echo "\n✅ No updates needed - all files already use correct field names!\n";
}
?>