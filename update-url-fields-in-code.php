<?php
// Script to update all PHP files from url_post/url_news to url_slug

echo "🚀 Starting PHP code updates for URL field migration...\n";

// Define the replacements
$replacements = [
    // Posts table
    "WHERE url_slug =" => "WHERE url_slug =",
    "url_slug = ?" => "url_slug = ?",
    "url_slug = '$" => "url_slug = '$",
    "url_post = \"$" => "url_slug = \"$",
    "SET url_slug =" => "SET url_slug =",
    
    // News table  
    "WHERE url_slug =" => "WHERE url_slug =",
    "url_slug = ?" => "url_slug = ?",
    "url_slug = '$" => "url_slug = '$",
    "url_news = \"$" => "url_slug = \"$",
    "SET url_slug =" => "SET url_slug =",
];

// Get all PHP files recursively
function getAllPhpFiles($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
}

// Exclude certain directories
$excludeDirs = ['vendor', 'node_modules', '.git', '_cleanup'];
$rootDir = __DIR__;

$allFiles = [];
$phpFiles = getAllPhpFiles($rootDir);

// Filter out excluded directories
foreach ($phpFiles as $file) {
    $skip = false;
    foreach ($excludeDirs as $excludeDir) {
        if (strpos($file, "/$excludeDir/") !== false) {
            $skip = true;
            break;
        }
    }
    if (!$skip) {
        $allFiles[] = $file;
    }
}

echo "📁 Found " . count($allFiles) . " PHP files to process\n";

$updatedFiles = 0;
$totalReplacements = 0;

foreach ($allFiles as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;
    $fileReplacements = 0;
    
    // Apply all replacements
    foreach ($replacements as $search => $replace) {
        $newContent = str_replace($search, $replace, $content);
        $replacementCount = substr_count($content, $search);
        if ($replacementCount > 0) {
            $content = $newContent;
            $fileReplacements += $replacementCount;
            $totalReplacements += $replacementCount;
        }
    }
    
    // Only write if changes were made
    if ($content !== $originalContent) {
        file_put_contents($file, $content);
        $updatedFiles++;
        echo "✅ Updated: " . str_replace($rootDir, '', $file) . " ({$fileReplacements} replacements)\n";
    }
}

echo "\n📊 Update Summary:\n";
echo "✅ Files updated: {$updatedFiles}\n";
echo "✅ Total replacements: {$totalReplacements}\n";

if ($totalReplacements > 0) {
    echo "\n🎉 PHP code migration completed successfully!\n";
    echo "\n⚠️  NEXT STEPS:\n";
    echo "1. Run the database migration script: php fix-url-fields.php\n";
    echo "2. Test post and news pages\n";
    echo "3. Test comment functionality\n";
} else {
    echo "\n✅ No more updates needed - all files already use url_slug!\n";
}
?>